<?php

class ModelExtensionShippingApilocationvalidator extends Model {

    private $backendInstance;

    private function fromFront() {
        require('admin/model/extension/shipping/aramex.php');
        if (!$this->backendInstance) {
            $this->backendInstance = new ModelExtensionShippingAramex($this->registry);
        }
        return $this->backendInstance;
    }

    public function fetchCities($CountryCode, $NameStartsWith = NULL) {
        $clientInfo = $this->fromFront()->getClientInfo();
        $this->load->model('localisation/country');
        $CountryCode = $this->model_localisation_country->getCountry($CountryCode);
        $params = array(
            'ClientInfo' => $clientInfo,
            'Transaction' => array(
                'Reference1' => '001',
                'Reference2' => '002',
                'Reference3' => '003',
                'Reference4' => '004',
                'Reference5' => '005'
            ),
            'CountryCode' => $CountryCode['iso_code_2'],
            'State' => NULL,
            'NameStartsWith' => $NameStartsWith,
        );

        $baseUrl = $this->getWsdlPath();
        //SOAP object
        $soapClient = new SoapClient($baseUrl . '/Location-API-WSDL.wsdl');

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

    public function validateAddress($address) {
        $clientInfo = $this->fromFront()->getClientInfo();
        $this->load->model('localisation/country');
        $address['country_code'] = $this->model_localisation_country->getCountry($address['country_code']);
        $params = array(
            'ClientInfo' => $clientInfo,
            'Transaction' => array(
                'Reference1' => '001',
                'Reference2' => '002',
                'Reference3' => '003',
                'Reference4' => '004',
                'Reference5' => '005'
            ),
            'Address' => array(
                'Line1' => '001',
                'Line2' => '',
                'Line3' => '',
                'City' => $address['city'],
                'StateOrProvinceCode' => '',
                'PostCode' => $address['post_code'],
                'CountryCode' => $address['country_code']['iso_code_2']
            )
        );

        $baseUrl = $this->getWsdlPath();
        //SOAP object
        $soapClient = new SoapClient($baseUrl . '/Location-API-WSDL.wsdl');
        $reponse = array();

        try {
            $results = $soapClient->ValidateAddress($params);
            if (is_object($results)) {
                if ($results->HasErrors) {
                    $suggestedAddresses = (isset($results->SuggestedAddresses->Address)) ? $results->SuggestedAddresses->Address : "";
                    $message = $results->Notifications->Notification->Message;
                    $reponse = array('is_valid' => false, 'suggestedAddresses' => $suggestedAddresses, 'message' => $message);
                } else {
                    $reponse = array('is_valid' => true);
                }
            }
        } catch (SoapFault $fault) {
            die('Error : ' . $fault->faultstring);
        }
        return $reponse;
    }

    private function getWsdlPath() {

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $base = HTTPS_SERVER;
        } else {
            $base = HTTP_SERVER;
        }
        $base = rtrim($base, "/");
        $wsdlBasePath = $base . '/admin/model/extension/shipping/aramex/wsdl';
        if ($this->config->get('shipping_aramex_test') == 1) {
            $wsdlBasePath .='/TestMode';
        }
        return $wsdlBasePath;
    }

}

?>