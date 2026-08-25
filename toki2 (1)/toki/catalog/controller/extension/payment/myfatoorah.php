<?php

require_once DIR_SYSTEM . 'library/myfatoorah/PaymentMyfatoorahApiV2.php';

class ControllerExtensionPaymentMyfatoorah extends Controller {

    private $mfCode;
    private $ocCode;
    private $orderId;
    private $mfObj;

//---------------------------------------------------------------------------------------------------------------------------------------------------
    private function registerData() {

        //get order info
        $this->load->model('checkout/order');
        $order = $this->model_checkout_order->getOrder($this->orderId);

        //get payment info
        $this->mfCode = $order['payment_code'];
        $this->ocCode = (version_compare(VERSION, '3.0.0', '<')) ? $this->mfCode : 'payment_' . $this->mfCode;

        //get MyFatoorah object
        $logger = new Log($this->mfCode . '.log');
        $isLog  = $this->config->get($this->ocCode . '_debug') === '1' ? true : false;

        $apiKey      = $this->config->get($this->ocCode . '_apiKey');
        $isTest      = $this->config->get($this->ocCode . '_test') === '1' ? true : false;
        $countryMode = $this->config->get($this->ocCode . '_countryMode');

        $this->mfObj = ($isLog) ? new PaymentMyfatoorahApiV2($apiKey, $countryMode, $isTest, $logger, 'write') : new PaymentMyfatoorahApiV2($apiKey, $countryMode, $isTest);
    }

//---------------------------------------------------------------------------------------------------------------------------------------------------

    public function webhook() {

        //allow the callback code run 1st. 
        sleep(30);

        //get MyFatoorah-Signature from request headers
        $request_headers     = apache_request_headers();
        $myFatoorahSignature = (empty($request_headers['MyFatoorah-Signature'])) ? exit() : $request_headers['MyFatoorah-Signature'];

        //get webhook data content
        $body    = (file_get_contents("php://input"));
        $webhook = json_decode($body, true);
        if (!isset($webhook['EventType']) || $webhook['EventType'] != 1) {
            exit();
        }

        //get order info
        $this->orderId = $webhook['Data']['CustomerReference'];
        $this->registerData();

        try {
            //validate signature
            $secretKey = $this->config->get($this->ocCode . '_webhook_secret_key') ?? exit();
            $this->mfObj->validateSignature($webhook['Data'], $secretKey, $myFatoorahSignature);

            //update order info
            $this->setOrderHistory($webhook['Data']['InvoiceId'], 'InvoiceId', '-Webhook');
        } catch (Exception $ex) {
            $this->mfObj->log('In Webhook Exception Block: ' . $ex->getMessage());
        }
        exit();
    }

//---------------------------------------------------------------------------------------------------------------------------------------------------
    public function callback() {
        $paymentId     = $this->request->get['paymentId'];
        $this->orderId = base64_decode($this->request->get['orid']);

        if (empty($paymentId) || empty($this->orderId)) {
            $this->redirectToErrorPage('Ops, you are accessing wrong data');
        }

        //get order info
        $this->registerData();

        try {
            //update order info
            $this->setOrderHistory($paymentId, 'PaymentId');
            $this->response->redirect($this->url->link('checkout/success', '', true));
        } catch (Exception $ex) {
            $this->redirectToErrorPage("Order #$this->orderId: " . $ex->getMessage());
        }
    }

//---------------------------------------------------------------------------------------------------------------------------------------------------
    function redirectToErrorPage($errMessage) {
        $this->session->data['error'] = $errMessage;

        if ($this->config->get('quickcheckout_status') == 1) {
            $this->response->redirect($this->url->link('checkout/cart', '', true));
        }
        $this->response->redirect($this->url->link('checkout/checkout', '', true));
    }

//---------------------------------------------------------------------------------------------------------------------------------------------------
    public function setOrderHistory($keyId, $keyType, $source = '') {

        $json = $this->mfObj->getPaymentStatus($keyId, $keyType, $this->orderId);

        if (isset($json->focusTransaction)) {


            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($this->orderId);

            if (empty($order_info['payment_custom_field']['paymentId']) || $order_info['payment_custom_field']['paymentId'] != $json->focusTransaction->PaymentId) {

                //update the gateway
                $default = ($this->mfCode == 'myfatoorah_pg') ? 'MyFatoorah' : $this->mfCode;
                $name    = $default . ' (' . $json->focusTransaction->PaymentGateway . ')';
                $this->db->query("UPDATE `" . DB_PREFIX . "order` SET payment_method = '" . $this->db->escape($name) . "', payment_custom_field = '" . $this->db->escape(json_encode(array('paymentId' => $json->focusTransaction->PaymentId))) . "' WHERE order_id = '" . (int) $this->orderId . "'");

                //add the history
                $newStatus = ($json->InvoiceStatus == 'Paid') ? '' : '_failed';
                $msg       = $this->getOrderHistoryNote($json->focusTransaction, $source);
                $this->model_checkout_order->addOrderHistory($this->orderId, $this->config->get($this->ocCode . $newStatus . '_order_status_id'), $msg, true, true);
            }
        }

        if ($json->InvoiceStatus != 'Paid') {

            throw new Exception($json->InvoiceError);
        }
    }

//---------------------------------------------------------------------------------------------------------------------------------------------------
    public function getOrderHistoryNote($data, $source = '') {
        $note = "<b>MyFatoorah$source Payment  Details:</b><br>";
        $note .= ' Gateway: ' . $data->PaymentGateway . '<br>';
        $note .= ' Transaction Status: ' . $data->TransactionStatus . '<br>';
        $note .= ' PaymentId: ' . $data->PaymentId . '<br>';
        $note .= ' AuthorizationId: ' . $data->AuthorizationId . '<br>';
        $note .= ' PaidCurrency: ' . $data->PaidCurrency . '<br>';
        $note .= empty($data->Error) ? '' : ' Error: ' . $data->Error . '<br>';
        return $note;
    }

//---------------------------------------------------------------------------------------------------------------------------------------------------
}
