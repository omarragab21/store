<?php

class ModelExtensionShippingApilocationvalidator extends Model {
    
    public function fetchCities($CountryCode, $NameStartsWith = NULL) {
        $this->load->model('extension/shipping/aramex');
        $clientInfo = $this->model_extension_shipping_aramex->getClientInfo();
        $this->load->model('localisation/country');
        $params = array(
            'ClientInfo' => $clientInfo,
            'Transaction' => array(
                'Reference1' => '001',
                'Reference2' => '002',
                'Reference3' => '003',
                'Reference4' => '004',
                'Reference5' => '005'
            ),
            'CountryCode' => $CountryCode,
            'State' => NULL,
            'NameStartsWith' => $NameStartsWith,
        );
        $baseUrl = $this->model_extension_shipping_aramex->getWsdlPath();
        //SOAP object
        $soapClient = new SoapClient($baseUrl . '/Location-API-WSDL.wsdl', array('cache_wsdl' => WSDL_CACHE_NONE));
        try {
            $results = $soapClient->FetchCities($params);
            if (is_object($results)) {
                if (!$results->HasErrors) {
                    $cities = isset($results->Cities->string) ? $results->Cities->string : false;
                    return $cities;
                }
            }
        } catch (SoapFault $fault) {
            die('Error : ' . $fault->faultstring);
        }
    }
}

?>