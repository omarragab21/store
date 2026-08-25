<?php

class ModelExtensionShippingAramexcalculator extends Model {

    private $backendInstance;
    private $fromShipping;

    private function fromFront() {
        require('admin/model/extension/shipping/aramex.php');
        if (!$this->backendInstance) {
            $this->backendInstance = new ModelExtensionShippingAramex($this->registry);
        }
        return $this->backendInstance;
    }
    private function fromShipping() {
        require('admin/model/extension/shipping/aramexsettings.php');
        if (!$this->fromShipping) {
            $this->fromShipping = new ModelExtensionShippingAramexsettings($this->registry);
        }
        return $this->fromShipping;
    }

    public function calculateRate() {
        $this->load->model('localisation/country');
        $destinationCountry = $this->model_localisation_country->getCountry($this->request->post['country_code']);
        $destinationCity = isset($this->request->post['city'])? $this->request->post['city']: "";
        $destinationZipcode = isset($this->request->post['post_code']) ? $this->request->post['post_code'] : "";

        $productId = $this->request->post['product_id'];
        $this->load->model('catalog/product');
        $product = $this->model_catalog_product->getProduct($productId);
        $weight = $product['weight'];
        $currency = $this->session->data['currency'];
        $allowed_methods = array();
        $all_domestic_methods = $this->fromShipping()->domesticmethods();
        $temporary_domestic = array();
        foreach ($all_domestic_methods as $domestic) {
            $temporary_domestic[$domestic['value']] = $domestic['label'];
        }
        $all_international_methods = $this->fromShipping->internationalmethods();
        $temporary_international = array();
        foreach ($all_international_methods as $international) {
            $temporary_international[$international['value']] = $international['label'];
        }

        if ($this->config->get('shipping_aramex_shipper_country_code') == $destinationCountry['iso_code_2']) {
            $product_group = 'DOM';
            $domestic_methods = $this->config->get('shipping_aramex_allowed_domestic_methods');
            if($domestic_methods){
                foreach ($domestic_methods as $cod){
                    $allowed_methods[$cod] = $temporary_domestic[$cod];
                }}
        }else{
            $product_group = 'EXP';
            $international_methods = $this->config->get('shipping_aramex_allowed_international_methods');
            if($international_methods){
                foreach ($international_methods as $cod){
                            $allowed_methods[$cod] = $temporary_international[$cod];
                }}

        }

        $response = array();
        $OriginAddress = array(
            'StateOrProvinceCode' => $this->config->get('shipping_aramex_shipper_state'),
            'City' => $this->config->get('shipping_aramex_shipper_city'),
            'PostCode' => $this->config->get('shipping_aramex_shipper_postal_code'),
            'CountryCode' =>  $this->config->get('shipping_aramex_shipper_country_code'),
        );
        $DestinationAddress = array(
            'StateOrProvinceCode' => "",
            'City' => $destinationCity,
            'PostCode' => $destinationZipcode,
            'CountryCode' => $destinationCountry['iso_code_2'],
        );
        $ShipmentDetails = array(
            'PaymentType' => 'P',
            'ProductGroup' => $product_group,
            'ProductType' => '',
            'ActualWeight' => array('Value' => $weight, 'Unit' => $this->weight->getUnit($this->config->get('config_weight_class_id'))),
            'ChargeableWeight' => array('Value' => $weight, 'Unit' => $this->weight->getUnit($this->config->get('config_weight_class_id'))),
            'NumberOfPieces' => 1
        );
        $clientInfo = $this->fromFront()->getClientInfo();
        $params = array(
            'ClientInfo' => $clientInfo,
            'OriginAddress' => $OriginAddress,
            'DestinationAddress' => $DestinationAddress,
            'ShipmentDetails' => $ShipmentDetails,
            'PreferredCurrencyCode' => $currency
        );

        $baseUrl = $this->getWsdlPath();
        //SOAP object
        $soapClient = new SoapClient($baseUrl . '/aramex-rates-calculator-wsdl.wsdl');
        $priceArr = array();
        foreach ($allowed_methods as $m_value => $m_title) {
            $params['ShipmentDetails']['ProductType'] = $m_value;
            if ($m_value == "CDA") {
                $params['ShipmentDetails']['Services'] = "CODS";
            } else {
                $params['ShipmentDetails']['Services'] = "";
            }
            try {

                $results = $soapClient->CalculateRate($params);
                if ($results->HasErrors) {
                    if (count($results->Notifications->Notification) > 1) {
                        foreach ($results->Notifications->Notification as $notify_error) {
                            $priceArr[$m_value] = ('Aramex: ' . $notify_error->Code . ' - ' . $notify_error->Message) . ' ';
                        }
                    } else {
                        $priceArr[$m_value] = ('Aramex: ' . $results->Notifications->Notification->Code . ' - ' . $results->Notifications->Notification->Message) . ' ';
                    }
                    $response['type'] = 'error';
                } else {
                    $response['type'] = 'success';
                    $priceArr[$m_value] = array('label' => $m_title, 'amount' => $results->TotalAmount->Value, 'currency' => $results->TotalAmount->CurrencyCode);
                }
            } catch (Exception $e) {
                $response['type'] = 'error';
                $priceArr[$m_value] = $e->getMessage();
            }
        }

        print json_encode($priceArr);
        die();
    }

    private function getWsdlPath() {

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $base = HTTPS_SERVER;
        } else {
            $base = HTTP_SERVER;
        }
        $base = rtrim($base, "/");
        $wsdlBasePath = $base . '/admin/model/extension/shipping/aramex/wsdl';
        if ($this->config->get('aramex_test') == 1) {
            $wsdlBasePath .='/TestMode';
        }
        return $wsdlBasePath;
    }

}

?>