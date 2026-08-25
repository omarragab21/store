<?php 

class ControllerExtensionShippingSmsa extends Controller {
    public $_apiUrl = "http://track.smsaexpress.com/SECOM/SMSAwebService.asmx";
    
    public function trackShipment(){
        $this->load->language('account/smsa');

        $data['result'] = array();

        if($this->config->get('shipping_smsa_status') && (isset($this->request->get['awb_number']) || $this->request->get['awb_number'])){
            $args = array(
                'awbNo'     => $this->request->get['awb_number'],
                'passkey'   => $this->config->get('shipping_smsa_passkey'),
            );

            $xml = $this->createXml('http://track.smsaexpress.com/secom/getTracking', 'getTracking', $args);

            $result = $this->send($xml);       

            if(isset($result['soapBody']['getTrackingResponse']['getTrackingResult']['diffgrdiffgram']) && $result['soapBody']['getTrackingResponse']['getTrackingResult']['diffgrdiffgram']){
                $response = $result['soapBody']['getTrackingResponse']['getTrackingResult']['diffgrdiffgram'];
                
                if(isset($response['NewDataSet']['Tracking'][0])){
                    $data['awbNo'] = $response['NewDataSet']['Tracking'][0]['awbNo'];
                    $data['status'] = $response['NewDataSet']['Tracking'][0]['Activity'];
                    unset($response['NewDataSet']['Tracking'][0]);
                    $data['result'] = $response['NewDataSet']['Tracking']; 
                } else {
                    $data['awbNo'] = $response['NewDataSet']['Tracking']['awbNo'];
                    $data['status'] = $response['NewDataSet']['Tracking']['Activity'];
                    $data['result'][] = $response['NewDataSet']['Tracking'];
                }
               
            }
        }

        $this->response->setOutput($this->load->view('extension/shipping/smsa_track', $data));
       
    }
    
    public function createXml($SOAPAction, $method, $variables){
        $xmlcontent = '<?xml version="1.0" encoding="utf-8"?>
            <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
              <soap:Body>
                <'.$method.' xmlns="http://track.smsaexpress.com/secom/">';
                  if(count($variables)){
                    foreach($variables As $key=>$val){
                        $xmlcontent .= '<'.$key.'>'.$val.'</'.$key.'>';
                    }
                  }
        $xmlcontent .= '</'.$method.'>
              </soap:Body>
            </soap:Envelope>';
            
        $headers = array(
            "POST /SECOM/SMSAwebService.asmx HTTP/1.1",
            "Host: track.smsaexpress.com",
            "Content-Type: text/xml; charset=utf-8",
            "Content-Length: ".strlen($xmlcontent),
            "SOAPAction: ".$SOAPAction
        );
        
        return array(
            'xml'       => $xmlcontent,
            'header'    => $headers
        );
    }
    
    public function send($arg=array()){
        $ch = curl_init();
        //curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $arg['header']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_URL, $this->_apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arg['xml']);
        $content=curl_exec($ch);
        
        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $content);
        $xml = new SimpleXMLElement($response);
        $array = json_decode(json_encode((array)$xml), TRUE);
        return $array;
    }
    public function getShipCharge(){
          $result = array();
          $cust_address = $this->getAddress();
          if($this->config->get('shipping_smsa_mode')){
                $passKey = $this->config->get('shipping_smsa_passkey');
            }else{
                $passKey = 'Testing1';
            }
          $args = array(
            'passKey'  => $passKey,
            'shipCity' => $this->config->get('shipping_smsa_city'),
            'shipCntry'  => $this->config->get('shipping_smsa_country'),
            'destCity' => $cust_address['zone'],
             'destCntry'  => $cust_address['country'],
            'shipType' => 'DLV',
            'codAmt'  => $this->cart->getTotal(),
            'weight' => $this->cart->getWeight(), 
         );
          $xml = $this->createXml("http://track.smsaexpress.com/secom/getShipCharges","getShipCharges",$args);
          $results = $this->send($xml); 
          $result = $results['soapBody']['getShipChargesResponse']['getShipChargesResult'];

          return $result;
    }
    public function getAddress() {
        $address_query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$this->customer->getId() . "'");

        if ($address_query->num_rows) {
            $country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$address_query->row['country_id'] . "'");

            if ($country_query->num_rows) {
                $country = $country_query->row['name'];
                $iso_code_2 = $country_query->row['iso_code_2'];
                $iso_code_3 = $country_query->row['iso_code_3'];
                $address_format = $country_query->row['address_format'];
            } else {
                $country = '';
                $iso_code_2 = '';
                $iso_code_3 = '';
                $address_format = '';
            }

            $zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$address_query->row['zone_id'] . "'");

            if ($zone_query->num_rows) {
                $zone = $zone_query->row['name'];
                $zone_code = $zone_query->row['code'];
            } else {
                $zone = '';
                $zone_code = '';
            }

            $address_data = array(
                'address_id'     => $address_query->row['address_id'],
                'firstname'      => $address_query->row['firstname'],
                'lastname'       => $address_query->row['lastname'],
                'company'        => $address_query->row['company'],
                'address_1'      => $address_query->row['address_1'],
                'address_2'      => $address_query->row['address_2'],
                'postcode'       => $address_query->row['postcode'],
                'city'           => $address_query->row['city'],
                'zone_id'        => $address_query->row['zone_id'],
                'zone'           => $zone,
                'zone_code'      => $zone_code,
                'country_id'     => $address_query->row['country_id'],
                'country'        => $country,
                'iso_code_2'     => $iso_code_2,
                'iso_code_3'     => $iso_code_3,
                'address_format' => $address_format,
                'custom_field'   => json_decode($address_query->row['custom_field'], true)
            );

            return $address_data;
        } else {
            return false;
        }
    }
}