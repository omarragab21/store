<?php

require_once 'myfatoorah_controller.php';

class ControllerExtensionPaymentMyfatoorahPG extends MyfatoorahController {

    protected $id = 'myfatoorah_pg';

//---------------------------------------------------------------------------------------------------------------------------------------------------
    public function __construct($registry) {
        parent::__construct($registry);
    }

//---------------------------------------------------------------------------------------------------------------------------------------------------
    public function index() {

        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET payment_method='MyFatoorah' WHERE order_id='" . (int) $this->session->data['order_id'] . "'");
//        $this->db->query("UPDATE `" . DB_PREFIX . "order` a INNER JOIN `" . DB_PREFIX . "order` b ON a.order_id = b.order_id SET a.payment_method='MyFatoorah' where b.payment_method like '<div%' ");

        $data = $this->language->load($this->path);

        if (($this->config->get($this->ocCode . '_test') != '1') && (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on')) {
            $data['error'] = $data['error_enable_ssl'];
        } else if ($this->currency->getValue($this->config->get('config_currency')) != 1) {
            $this->load->language('extension/shipping/myfatoorah');
            $data['error'] = $this->language->get('error_defult_currency_rate');
        } else {

            $data['action']       = 'index.php?route=' . $this->path . '/confirm';
            $data['displayTypes'] = $this->config->get($this->ocCode . '_payment_type');
            if ($data['displayTypes'] == 'multigateways') {
                $data = $this->fillMultigatewaysData($data);
            }
        }

        return $this->load->view($this->path, $data);
    }

//---------------------------------------------------------------------------------------------------------------------------------------------------
    public function confirm() {

        if (!isset($this->session->data['order_id'])) {
            return $this->redirectToErrorPage('Session has been expired please try again later');
        }

        $this->load->model('checkout/order');

        $this->orderId = $this->session->data['order_id'];
        $order_info    = $this->model_checkout_order->getOrder($this->orderId);

        try {
            $curlData = $this->getPayload($order_info);

            $gatewayId = (empty($this->request->post['mfCardData'])) ? 'myfatoorah' : $this->request->post['mfCardData'];
            $sessionId = (empty($this->request->post['mfFormData'])) ? '' : $this->request->post['mfFormData'];

            $data = $this->mfObj->getInvoiceURL($curlData, $gatewayId, $this->orderId, $sessionId);

            $msg = '<b>MyFatoorah Invoice Details:</b><br> Invoice ID ' . $data['invoiceId'] . '<br>';
            $msg .= ($sessionId) ? 'Payment URL <a href ="' . $data['invoiceURL'] . '" target="_blank">' . $data['invoiceURL'] . '</a>' : '';
            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get($this->ocCode . '_initial_order_status_id'), $msg, false); //We don't need to send a pending email to the customer.

            $this->response->redirect($data['invoiceURL']);
        } catch (Exception $ex) {
            return $this->redirectToErrorPage($ex->getMessage());
        }
    }

//---------------------------------------------------------------------------------------------------------------------------------------------------
    private function fillMultigatewaysData($data) {
        try {
            $data['language'] = $this->language->get('code');

            $this->load->model('checkout/order');
            $this->orderId = $this->session->data['order_id'];

            $order_info     = $this->model_checkout_order->getOrder($this->orderId);
            $shippingMethod = isset($this->session->data['shipping_method']['mfid']) ? $this->session->data['shipping_method']['mfid'] : null;

            $cartData = $this->getCartData($order_info, $shippingMethod);

            $data['paymentMethods'] = $this->mfObj->getPaymentMethodsForDisplay(($cartData['amount'] + $cartData['mfShipping']), $order_info['currency_code']);

            $viewObj = ($this->journal3) ? $this->journal3 : $this;

            //add style
            $viewObj->document->addStyle('catalog/view/theme/default/stylesheet/myfatoorah.css');
            $data['styles'] = ($this->journal3) ? [] : $this->document->getStyles();

            $data['height']   = ($this->config->get($this->ocCode . '_saveCard') === '1' && $this->customer->getId()) ? '180' : '130';
            $userDefinedField = ($this->config->get($this->ocCode . '_saveCard') === '1' && $this->customer->getId()) ? 'CK-' . $this->customer->getId() : '';
            $data['session']  = $this->mfObj->getEmbeddedSession($userDefinedField);
        } catch (Exception $ex) {
            $data['error'] = $ex->getMessage();
        }
        return $data;
    }

//---------------------------------------------------------------------------------------------------------------------------------------------------
}
