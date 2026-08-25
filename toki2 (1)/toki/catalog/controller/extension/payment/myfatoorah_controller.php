<?php
define('MYFATOORAH_OC_PLUGIN_VERSION', '2.0.0.2');

class MyfatoorahController extends Controller {

    protected $id;
    protected $path;
    protected $ocCode;
    protected $orderId;
    protected $mfObj;

//---------------------------------------------------------------------------------------------------------------------------------------------------
    public function __construct($registry) {
        parent::__construct($registry);

        $this->path   = 'extension/payment/' . $this->id;
        $this->ocCode = (version_compare(VERSION, '3.0.0', '<')) ? $this->id : 'payment_' . $this->id;

        //get MyFatoorah object
        $logger = new Log($this->id . '.log');
        $isLog  = $this->config->get($this->ocCode . '_debug') === '1' ? true : false;

        $apiKey      = $this->config->get($this->ocCode . '_apiKey');
        $isTest      = $this->config->get($this->ocCode . '_test') === '1' ? true : false;
        $countryMode = $this->config->get($this->ocCode . '_countryMode');

        require_once DIR_SYSTEM . 'library/myfatoorah/PaymentMyfatoorahApiV2.php';
        $this->mfObj = ($isLog) ? new PaymentMyfatoorahApiV2($apiKey, $countryMode, $isTest, $logger, 'write') : new PaymentMyfatoorahApiV2($apiKey, $countryMode, $isTest);
    }

//---------------------------------------------------------------------------------------------------------------------------------------------------
    protected function getPayload($order_info) {

        $extension = 'extension/';
        
        if ($this->currency->getValue($this->config->get('config_currency')) != 1) {
            $this->load->language($extension . 'shipping/myfatoorah');
            throw new Exception($this->language->get('error_defult_currency_rate'));
        }

        $orderId = $this->orderId;

        $gFname = isset($this->session->data['guest']['firstname']) ? $this->session->data['guest']['firstname'] : '';
        $gLname = isset($this->session->data['guest']['lastname']) ? $this->session->data['guest']['lastname'] : '';
        $gEmail = isset($this->session->data['guest']['email']) ? $this->session->data['guest']['email'] : null;
        $gPhone = isset($this->session->data['guest']['telephone']) ? $this->session->data['guest']['telephone'] : '';

        $fname = $this->customer->getFirstName() ?: $gFname;
        $lname = $this->customer->getLastName() ?: $gLname;
        $email = $this->customer->getEmail() ?: $gEmail;
        $phone = $this->customer->getTelephone() ?: $gPhone;

        $phoneArr = $this->mfObj->getPhone($phone);

        $userDefinedField = ($this->config->get($this->ocCode . '_saveCard') === '1' && $this->customer->getId()) ? 'CK-' . $this->customer->getId() : '';

        $returnUrl = $this->url->link($extension . 'payment/myfatoorah/callback', '', true) . '&orid=' . base64_encode($orderId);

        $shippingMethod    = null;
        $shippingAddress   = !empty($this->session->data['shipping_address']['address_1']) ? $this->session->data['shipping_address']['address_1'] . ' ' . $this->session->data['shipping_address']['address_2'] : '';
        $shippingConsignee = null;
        if (isset($this->session->data['shipping_method']['mfid'])) {
            $shippingMethod = $this->session->data['shipping_method']['mfid'];

            $shippingConsignee = [
                'PersonName'   => $fname . ' ' . $lname,
                'Mobile'       => $phoneArr[1],
                'EmailAddress' => $email,
                'LineAddress'  => $shippingAddress,
                'CityName'     => !empty($this->session->data['shipping_address']['city']) ? $this->session->data['shipping_address']['city'] : $this->session->data['shipping_address']['zone'],
                'PostalCode'   => $this->session->data['shipping_address']['postcode'],
                'CountryCode'  => $this->session->data['shipping_address']['iso_code_2']
            ];
        }

//        $amount = $this->convert($order_info['total']);
//        $invoiceItems = null;
//        $amount       = 0;
//        $invoiceItems = $this->getInvoiceItems($order_info, $amount, $shippingMethod);

        $cartData     = $this->getCartData($shippingMethod);
        $amount       = $cartData['amount'];
        $invoiceItems = $cartData['invoiceItems'];

        $data = [
            'CustomerName'       => $fname . ' ' . $lname,
            'DisplayCurrencyIso' => $order_info['currency_code'],
            'MobileCountryCode'  => $phoneArr[0],
            'CustomerMobile'     => $phoneArr[1],
            'CustomerEmail'      => $email,
            'InvoiceValue'       => "$amount",
            'CallBackUrl'        => $returnUrl,
            'ErrorUrl'           => $returnUrl,
            'Language'           => $this->language->get('code'),
            'CustomerReference'  => $orderId,
            'ShippingConsignee'  => $shippingConsignee,
            'CustomerCivilId'    => '',
            'UserDefinedField'   => $userDefinedField,
            'ExpiryDate'         => '',
            'CustomerAddress'    => [
                'Block'               => '',
                'Street'              => '',
                'HouseBuildingNo'     => '',
                'Address'             => $this->session->data['payment_address']['city'] . ', ' . $this->session->data['payment_address']['zone'] . ', ' . $this->session->data['payment_address']['postcode'] . ', ' . $this->session->data['payment_address']['country'],
                'AddressInstructions' => $shippingAddress,
            ],
            'InvoiceItems'       => $invoiceItems,
            'ShippingMethod'     => $shippingMethod,
            'SourceInfo'         => 'OpenCart ' . VERSION . ' - ' . $this->id . ' ' . MYFATOORAH_OC_PLUGIN_VERSION,
        ];
        return $data;
    }

//---------------------------------------------------------------------------------------------------------------------------------------------------
    public function getCartData($shippingMethod = null) {

        $amount          = 0;
        $mfShipping      = 0;
        $invoiceItemsArr = [];

        //---------------------product items
        $products = $this->cart->getProducts();
        foreach ($products as $product) {
            $weightRate    = ($shippingMethod) ? $this->mfObj->getWeightRate($this->weight->getUnit($product['weight_class_id'])) : 1;
            $dimensionRate = ($shippingMethod) ? $this->mfObj->getDimensionRate($this->length->getUnit($product['length_class_id'])) : 1;

            $unitPrice = $this->convert($product['price']);

            $amount += $unitPrice * $product['quantity'];

            $invoiceItemsArr[] = [
                'ItemName'  => $product['name'],
                'Quantity'  => $product['quantity'],
                'UnitPrice' => $unitPrice,
                'weight'    => $product['weight'] * $weightRate,
                'Width'     => $product['width'] * $dimensionRate,
                'Height'    => $product['length'] * $dimensionRate,
                'Depth'     => $product['height'] * $dimensionRate,
            ];
        }

        //---------------------Gift User Created Vouchers 
        if (!empty($this->session->data['vouchers'])) {
            foreach ($this->session->data['vouchers'] as $voucher) {

                $unitPrice = $this->convert($voucher['amount']);

                $amount += $unitPrice;

                $invoiceItemsArr[] = array('ItemName' => $voucher['description'], 'Quantity' => 1, 'UnitPrice' => "$unitPrice", 'Weight' => '0', 'Width' => '0', 'Height' => '0', 'Depth' => '0');
            }
        }

        //---------------------shipping, tax, credit, handling, low_order_fee, coupon, reward, voucher, sub_total and total prices
        $items = $this->getCartItems();
        //other items in cart
        foreach ($items as $item) {
            if ($item['code'] != 'sub_total' && $item['code'] != 'total' && ($item['code'] != 'shipping' || ($shippingMethod == null && $item['code'] == 'shipping' ))) {

                $unitPrice = $this->convert($item['value']);

                $amount += $unitPrice;

                $invoiceItemsArr[] = array('ItemName' => $item['title'], 'Quantity' => 1, 'UnitPrice' => "$unitPrice", 'Weight' => '0', 'Width' => '0', 'Height' => '0', 'Depth' => '0');
            }

            if ($item['code'] == 'shipping' && $shippingMethod) {
                $mfShipping = $this->currency->convert($item['value'], $this->config->get('config_currency'), $this->session->data['currency']);
            }
        }


        return [
            'amount'       => $amount,
            'mfShipping'   => $mfShipping,
            'invoiceItems' => $invoiceItemsArr,
        ];
    }

//---------------------------------------------------------------------------------------------------------------------------------------------------
    private function getCartItems() {

        $verTotalKey   = (version_compare(VERSION, '3.0.0', '<')) ? '' : 'total_';
        $verSettingKey = (version_compare(VERSION, '3.0.0', '<')) ? 'extension' : 'setting';
        $model         = "model_{$verSettingKey}_extension";

        $items = array();
        $taxes = $this->cart->getTaxes();
        $total = 0;

        $this->load->model("$verSettingKey/extension");
        $results = $this->{$model}->getExtensions('total');

        //to sort data and recalc them right very imp
        $sort_order = array();
        foreach ($results as $key => $value) {
            $sort_order[$key] = $this->config->get($verTotalKey . $value['code'] . '_sort_order');
        }
        array_multisort($sort_order, SORT_ASC, $results);

        foreach ($results as $result) {
            if ($this->config->get($verTotalKey . $result['code'] . '_status')) {
                $this->load->model('extension/total/' . $result['code']);

                // We have to put the totals in an array so that they pass by reference.
                $this->{'model_extension_total_' . $result['code']}->getTotal(['totals' => &$items, 'taxes' => &$taxes, 'total' => &$total]);
            }
        }

//        $sort_order = array();
//        foreach ($totals as $key => $value) {
//            $sort_order[$key] = $value['sort_order'];
//        }
//        array_multisort($sort_order, SORT_ASC, $totals);

        return $items;
    }

//---------------------------------------------------------------------------------------------------------------------------------------------------
    private function convert($value) {

//        $unitPrice = $this->currency->format($value, $this->session->data['currency'], '', false);
        $unitPrice = $this->currency->convert($value, $this->config->get('config_currency'), $this->session->data['currency']);
        return round($unitPrice, 3);
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
}
