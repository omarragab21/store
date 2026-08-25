<?php

class ModelExtensionShippingAramex extends Model {

    function getQuote($address) {
        $this->load->language('extension/shipping/fedex');
        $this->load->model('extension/shipping/aramexmodel');
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int) $this->config->get('shipping_aramex_geo_zone_id') . "' AND country_id = '" . (int) $address['country_id'] . "' AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = '0')");

        if (!$this->config->get('shipping_aramex_geo_zone_id')) {

            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $error = '';

        $quote_data = array();

        if ($status) {

            $chk_live_rate = ($this->config->get('shipping_aramex_live_rate_calculation')) ? $this->config->get('shipping_aramex_live_rate_calculation') : '';

            if ($chk_live_rate == 1) {

                $weight = $this->weight->convert($this->cart->getWeight(), $this->config->get('shipping_config_weight_class_id'), $this->config->get('shipping_aramex_weight_class_id'));
                $weight_code = strtoupper($this->weight->getUnit($this->config->get('shipping_aramex_weight_class_id')));

                $this->load->model('localisation/country');

                $country_info = $this->model_localisation_country->getCountry($this->config->get('shipping_config_country_id'));
                $this->load->model('localisation/zone');

                $zone_info = $this->model_localisation_zone->getZone($this->config->get('shipping_config_zone_id'));
                $clientInfo = $this->model_extension_shipping_aramexmodel->getClientInfo();

                ##################### config shipper details ################
                $origin_country = ($this->config->get('shipping_aramex_shipper_country_code')) ? $this->config->get('shipping_aramex_shipper_country_code') : '';
                $origin_city = ($this->config->get('shipping_aramex_shipper_city')) ? $this->config->get('shipping_aramex_shipper_city') : '';
                $origin_zipcode = ($this->config->get('shipping_aramex_shipper_postal_code')) ? $this->config->get('shipping_aramex_shipper_postal_code') : '';
                $origin_state = ($this->config->get('shipping_aramex_shipper_state')) ? $this->config->get('shipping_aramex_shipper_state') : '';
                $destination_city = ($address['zone']) ? $address['zone'] : '';
                ##################### config default service type ################

                if (strtolower($address['iso_code_2']) == strtolower($origin_country)) {
                    $ProductGroup = 'DOM';
                    $ProductType = ($this->config->get('shipping_aramex_default_allowed_domestic_methods')) ? $this->config->get('shipping_aramex_default_allowed_domestic_methods') : '';
                } else {
                    $ProductGroup = 'EXP';
                    $ProductType = ($this->config->get('shipping_aramex_default_allowed_international_methods')) ? $this->config->get('shipping_aramex_default_allowed_international_methods') : '';

                }
                $cart_count = ($this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0));

                try {
                    $params = array(
                        'ClientInfo' => $clientInfo,
                        'Transaction' => array(
                            'Reference1' => '001',
                        ),
                        'OriginAddress' => array(
                            'StateOrProvinceCode' => $origin_state,
                            'City' => $origin_city,
                            'PostCode' => $origin_zipcode,
                            'CountryCode' => $origin_country
                        ),
                        'DestinationAddress' => array(
                            'StateOrProvinceCode' => ($address['zone']) ? $address['zone'] : '',
                            'City' => $destination_city,
                            'PostCode' => ($address['postcode']) ? $address['postcode'] : '',
                            'CountryCode' => ($address['iso_code_2']) ? $address['iso_code_2'] : ''
                        ),
                        'ShipmentDetails' => array(
                            'PaymentType' => 'P',
                            'ProductGroup' => $ProductGroup,
                            'ProductType' => $ProductType,
                            'ActualWeight' => array('Value' => $weight, 'Unit' => $weight_code),
                            'ChargeableWeight' => array('Value' => $weight, 'Unit' => $weight_code),
                            'NumberOfPieces' => $cart_count,
                        ),
                        'PreferredCurrencyCode' => ($this->config->get('config_currency')) ? $this->config->get('config_currency') : '',
                    );
                    if ($ProductType == "CDA") {
                        $params['ShipmentDetails']['Services'] = "CODS";
                    } else {
                        $params['ShipmentDetails']['Services'] = "";
                    }


                    $baseUrl = $this->model_extension_shipping_aramexmodel->getWsdlPath();
                    $soapClient = new SoapClient($baseUrl . '/aramex-rates-calculator-wsdl.wsdl', array('trace' => 1));

                    try {
                        $results = $soapClient->CalculateRate($params);
                        $error = "";
                        if ($results->HasErrors) {
                            if (count($results->Notifications->Notification) > 1) {
                                foreach ($results->Notifications->Notification as $notify_error) {
                                    $error .= 'Aramex: ' . $notify_error->Code . ' - ' . $notify_error->Message . "<br>";
                                }
                            } else {
                                $error .= 'Aramex: ' . $results->Notifications->Notification->Code . ' - ' . $results->Notifications->Notification->Message;
                            }
                        } else {
                            $cost = $results->TotalAmount->Value;
                            $currency = $results->TotalAmount->CurrencyCode;

                            if (strtolower(trim($this->config->get('config_currency'))) == strtolower(trim($currency))) {
                                //all currencies work
                                $quote_data['aramex'] = array(
                                    'code' => 'aramex.aramex',
                                    'title' => 'Aramex shipping charges',
                                    'cost' => $this->currency->convert($cost, $currency, $this->config->get('config_currency')),
                                    'tax_class_id' => $this->config->get('aramex_tax_class_id'),
                                    'text' => $this->currency->format($this->tax->calculate($this->currency->convert($cost, $currency, $this->session->data['currency']), $this->config->get('shipping_aramex_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'], 1.0000000)
                                );
                            } elseif (strtolower(trim($this->session->data['currency'])) == strtolower(trim($currency))) {
                                //one currency works
                                $quote_data['aramex'] = array(
                                    'code' => 'aramex.aramex',
                                    'title' => 'Aramex shipping charges',
                                    'cost' => $this->currency->convert($cost, $currency, $this->config->get('config_currency')),
                                    'tax_class_id' => $this->config->get('shipping_aramex_tax_class_id'),
                                    'text' => $this->currency->format($this->tax->calculate($this->currency->convert($cost, $currency, $this->config->get('config_currency')), $this->config->get('shipping_aramex_tax_class_id'), $this->config->get('config_tax')), $this->config->get('config_currency'), 1.0000000)
                                );
                            } else {
                                //no one currency works
                                $quote_data['aramex'] = array(
                                    'code' => 'aramex.aramex',
                                    'title' => 'Currently  ' . $this->session->data['currency'] . " currency is not supported by Aramex server.",
                                    'cost' => "",
                                    'tax_class_id' => "",
                                    'text' => ""
                                );
                            }
                        }
                    } catch (Exception $e) {

                        $error .= $e->getMessage();
                    }
                } catch (Exception $e) {

                    $error .= $e->getMessage();
                }
            } else {

                $cost = ($this->config->get('shipping_aramex_default_rate')) ? $this->config->get('shipping_aramex_default_rate') : '';
                $currency = $this->config->get('config_currency');
                $quote_data['aramex'] = array(
                    'code' => 'aramex.aramex',
                    'title' => 'Aramex shipping charges',
                    'cost' => $this->currency->convert($cost, $currency, $this->config->get('config_currency')),
                    'tax_class_id' => $this->config->get('shipping_aramex_tax_class_id'),
                    'text' => $this->currency->format($this->tax->calculate($this->currency->convert($cost, $currency, $this->config->get('config_currency')), $this->config->get('shipping_aramex_tax_class_id'), $this->config->get('config_tax')), $this->config->get('config_currency'), 1.0000000)
                );
            }
        } // end of status if 

        $method_data = array();

        if ($quote_data || $error) {
            $title = 'Aramex';

            if ($this->config->get('fedex_display_weight')) {
                $title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('fedex_weight_class_id')) . ')';
            }

            $method_data = array(
                'code' => 'fedex',
                'title' => $title,
                'quote' => $quote_data,
                'sort_order' => $this->config->get('fedex_sort_order'),
                'error' => $error
            );
        }

        return $method_data;
    }

}

?>