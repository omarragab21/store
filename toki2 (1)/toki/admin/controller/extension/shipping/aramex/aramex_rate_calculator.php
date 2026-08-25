<?php

class ControllerExtensionShippingAramexAramexRateCalculator extends Controller {

    private $error = array();
    public function index() {
        $this->language->load('extension/shipping/aramex');
        $this->document->setTitle($this->language->get('heading_title_rate'));
        $this->load->model('sale/order');
        $this->getForm();
    }

    public function getForm() {
        $this->load->model('sale/order');
        $this->load->model('extension/shipping/aramex');
        $this->load->model('extension/shipping/aramexsettings');
        $this->document->addScript('view/javascript/aramex/jquery.chained.js');

        if (isset($this->request->get['order_id'])) {
            $order_id = $this->request->get['order_id'];
        } else {
            $order_id = 0;
        }
        $order_info = $this->model_sale_order->getOrder($order_id);
        if ($order_info) {
            $this->document->setTitle($this->language->get('heading_title_rate'));
            ############### label #############
            $data['text_back_to_order'] = $this->language->get('text_back_to_order');
            $data['text_create_sipment'] = $this->language->get('text_create_sipment');
            $data['text_rate_calculator'] = $this->language->get('text_rate_calculator');
            $data['text_schedule_pickup'] = $this->language->get('text_schedule_pickup');
            $data['text_print_label'] = $this->language->get('text_print_label');
            $data['text_track'] = $this->language->get('text_track');
            $data['heading_title'] = $this->language->get('heading_title_rate');


            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true),
                'separator' => false
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title_rate'),
                'href' => $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'], true),
                'separator' => ' :: '
            );

            $data['order_id'] = $this->request->get['order_id'];

            ############ button ##########
            $data['back_to_order'] = $this->url->link('sale/order/info', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_id, true);
            $data['aramex_create_sipment'] = $this->url->link('extension/shipping/aramex/aramex_create_shipment', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_id, true);
            $data['aramex_rate_calculator'] = $this->url->link('extension/shipping/aramex/aramex_rate_calculator', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_id, true);
            $data['aramex_schedule_pickup'] = $this->url->link('extension/shipping/aramex/aramex_schedule_pickup', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_id, true);
            $data['aramex_print_label'] = $this->url->link('extension/shipping/aramex/aramex_create_shipment/lable', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_id, true);
            $data['aramex_traking'] = $this->url->link('extension/shipping/aramex/aramex_traking', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_id, true);
            ############ button ##########
            ##################### config shipper details ################
            $data['origin_country'] = ($this->config->get('shipping_aramex_shipper_country_code')) ? $this->config->get('shipping_aramex_shipper_country_code') : '';
            $data['origin_city'] = ($this->config->get('shipping_aramex_shipper_city')) ? $this->config->get('shipping_aramex_shipper_city') : '';
            $data['origin_zipcode'] = ($this->config->get('shipping_aramex_shipper_postal_code')) ? $this->config->get('shipping_aramex_shipper_postal_code') : '';
            $data['origin_state'] = ($this->config->get('shipping_aramex_shipper_state')) ? $this->config->get('shipping_aramex_shipper_state') : '';

            ##################### customer shipment details ################

            $shipment_receiver_name = '';
            $shipment_receiver_street = '';

            $data['destination_country'] = ($order_info['shipping_iso_code_2']) ? $order_info['shipping_iso_code_2'] : '';
            $data['destination_city'] = ($order_info['shipping_city']) ? $order_info['shipping_city'] : '';
            $data['destination_zipcode'] = ($order_info['shipping_postcode']) ? $order_info['shipping_postcode'] : '';
            $data['destination_state'] = ($order_info['shipping_zone']) ? $order_info['shipping_zone'] : '';

            ############ autocities ##########
            $data['aramex_searchautocities'] = htmlspecialchars_decode ($this->url->link('extension/shipping/aramex/aramex_searchautocities',  'user_token='.$this->session->data['user_token'], true));
            $this->document->addScript('../../catalog/view/javascript/aramex/jqueryui/jquery-ui.js');
            $this->document->addStyle('../../catalog/view/javascript/aramex/jqueryui/jquery-ui.css');
            $data['aramex_loader'] = "../../catalog/view/javascript/aramex/jqueryui/aramex_loader.gif";
            $data['aramex_allow'] = $this->config->get('shipping_aramex_api_location_validator');
            ##################  Additional ###########

            $this->load->model('localisation/country');
            $data['countries'] = $this->model_localisation_country->getCountries();
            $data['reference'] = $order_id;
            $data['aramex_shipment_shipper_account'] = ($this->config->get('shipping_aramex_account_number')) ? $this->config->get('shipping_aramex_account_number') : '';
            $data['aramex_allowed_domestic_methods'] = ($this->config->get('shipping_aramex_allowed_domestic_methods')) ? $this->config->get('shipping_aramex_allowed_domestic_methods') : '';
            $data['aramex_allowed_domestic_additional_services'] = ($this->config->get('shipping_aramex_allowed_domestic_additional_services')) ? $this->config->get('shipping_aramex_allowed_domestic_additional_services') : '';
            $data['aramex_allowed_international_methods'] = ($this->config->get('shipping_aramex_allowed_international_methods')) ? $this->config->get('shipping_aramex_allowed_international_methods') : '';
            $data['aramex_allowed_international_additional_services'] = ($this->config->get('shipping_aramex_allowed_international_additional_services')) ? $this->config->get('shipping_aramex_allowed_international_additional_services') : '';


            $data['all_allowed_domestic_methods'] = $this->model_extension_shipping_aramexsettings->domesticmethods();
            $data['all_allowed_domestic_additional_services'] = $this->model_extension_shipping_aramexsettings->domesticAdditionalServices();
            $data['all_allowed_international_methods'] = $this->model_extension_shipping_aramexsettings->internationalmethods();
            $data['all_allowed_international_additional_services'] = $this->model_extension_shipping_aramexsettings->internationalAdditionalServices();
            $data['currency_code'] = ($order_info['currency_code']) ? $order_info['currency_code'] : '';

            if (isset($this->request->post['payment_type']) && !empty($this->request->post['payment_type'])) {
                $data['pay_type'] = $this->request->post['payment_type'];
            } else {
                $data['pay_type'] = '';
            }
            if (isset($this->request->post['product_group']) && !empty($this->request->post['product_group'])) {
                $data['group'] = $this->request->post['product_group'];
            } else {
                $data['group'] = "";
            }
            if (isset($this->request->post['service_type']) && !empty($this->request->post['service_type'])) {
                $data['type'] = $this->request->post['service_type'];
            } else {
                $data['type'] = "";
            }
            if (isset($this->request->post['weight_unit']) && !empty($this->request->post['weight_unit'])) {
                $getunit_classid = $this->model_extension_shipping_aramex->getWeightClassId($this->request->post['weight_unit']);
                $data['weight_unit'] = $getunit_classid->row['unit'];
                $config_weight_class_id = $getunit_classid->row['weight_class_id'];
            } else {
                $data['weight_unit'] = $this->weight->getUnit($this->config->get('config_weight_class_id'));
                $config_weight_class_id = $this->config->get('config_weight_class_id');
            }
            ##################

            $data['total'] = ($order_info['total']) ? number_format($order_info['total'], 2) : '';

            ########### product list ##########
            if (isset($this->request->post['order_product'])) {
                $order_products = $this->request->post['order_product'];
            } elseif (isset($this->request->get['order_id'])) {
                $order_products = $this->model_sale_order->getOrderProducts($this->request->get['order_id']);
            } else {
                $order_products = array();
            }
            $data['order_products'] = array();
            $weighttot = 0;
            $i = 1;
            foreach ($order_products as $order_product) {
                if (isset($order_product['order_option'])) {
                    $order_option = $order_product['order_option'];
                } elseif (isset($this->request->get['order_id'])) {
                    $order_option = $this->model_sale_order->getOrderOptions($this->request->get['order_id'], $order_product['order_product_id']);
                    $product_weight_query = $this->db->query("SELECT weight, weight_class_id FROM " . DB_PREFIX . "product WHERE product_id = '" . $order_product['product_id'] . "'");
                    $weight_class_query = $this->db->query("SELECT wcd.unit FROM " . DB_PREFIX . "weight_class wc LEFT JOIN " . DB_PREFIX . "weight_class_description wcd ON (wc.weight_class_id = wcd.weight_class_id) WHERE wcd.language_id = '" . (int) $this->config->get('config_language_id') . "' AND wc.weight_class_id = '" . $product_weight_query->row['weight_class_id'] . "'");
                } else {
                    $order_option = array();
                }


                $prodweight = $this->weight->convert($product_weight_query->row['weight'], $product_weight_query->row['weight_class_id'], $config_weight_class_id);
                $prodweight = ($prodweight * $order_product['quantity']);
                $weighttot = ($weighttot + $prodweight);
                $i = $i + 1;
            }
            $data['no_of_item'] = $i;
            $data['weighttot'] = number_format($weighttot, 2);
            $data['total'] = number_format($order_info['total'], 2);

            ################## create shipment ###########
            if ($this->request->post) {
                $account = ($this->config->get('shipping_aramex_account_number')) ? $this->config->get('shipping_aramex_account_number') : '';
                $country_code = ($this->config->get('shipping_aramex_account_country_code')) ? $this->config->get('shipping_aramex_account_country_code') : '';
                $response = array();
                $clientInfo = $this->model_extension_shipping_aramex->getClientInfo();
                try {

                    $text_weight = ($this->request->post['text_weight']) ? $this->request->post['text_weight'] : '';
                    $weight_unit = ($this->request->post['weight_unit']) ? $this->request->post['weight_unit'] : '';
                    if($this->request->post['service_type'] == "CDA"){ $aramex_services = "CODS"; }else{ $aramex_services = ""; }
                    $params = array(
                        'ClientInfo' => $clientInfo,
                        'Transaction' => array(
                            'Reference1' => ($this->request->post['reference']) ? $this->request->post['reference'] : '',
                        ),
                        'OriginAddress' => array(
                            'StateOrProvinceCode' => ($this->request->post['origin_state']) ? html_entity_decode($this->request->post['origin_state']) : '',
                            'City' => ($this->request->post['origin_city']) ? html_entity_decode($this->request->post['origin_city']) : '',
                            'PostCode' => ($this->request->post['origin_zipcode']) ? $this->request->post['origin_zipcode'] : '',
                            'CountryCode' => ($this->request->post['origin_country']) ? $this->request->post['origin_country'] : ''
                        ),
                        'DestinationAddress' => array(
                            'StateOrProvinceCode' => ($this->request->post['destination_state']) ? html_entity_decode($this->request->post['destination_state']) : '',
                            'City' => ($this->request->post['destination_city']) ? html_entity_decode($this->request->post['destination_city']) : '',
                            'PostCode' => ($this->request->post['destination_zipcode']) ? $this->request->post['destination_zipcode'] : '',
                            'CountryCode' => ($this->request->post['destination_country']) ? $this->request->post['destination_country'] : ''
                        ),
                        'ShipmentDetails' => array(
                            'PaymentType' => ($this->request->post['payment_type']) ? $this->request->post['payment_type'] : '',
                            'ProductGroup' => ($this->request->post['product_group']) ? $this->request->post['product_group'] : '',
                            'ProductType' => ($this->request->post['service_type']) ? $this->request->post['service_type'] : '',
                            'ActualWeight' => array('Value' => $text_weight, 'Unit' => $weight_unit),
                            'ChargeableWeight' => array('Value' => $text_weight, 'Unit' => $weight_unit),
                            'NumberOfPieces' => ($this->request->post['total_count']) ? $this->request->post['total_count'] : '',
                            'Services'  => $aramex_services
                        ),
                        'PreferredCurrencyCode' => ($this->request->post['currency_code']) ? $this->request->post['currency_code'] : '',
                    );


                    $baseUrl = $this->model_extension_shipping_aramex->getWsdlPath();
                    $soapClient = new SoapClient($baseUrl . '/aramex-rates-calculator-wsdl.wsdl', array('trace' => 1));

                    try {
                        $results = $soapClient->CalculateRate($params);

                        if ($results->HasErrors) {
                            if (is_countable($results->Notifications->Notification) && count($results->Notifications->Notification) > 1) {
                                $error = "";
                                foreach ($results->Notifications->Notification as $notify_error) {
                                    $data['eRRORS'][] = 'Aramex: ' . $notify_error->Code . ' - ' . $notify_error->Message . "<br>";
                                }
                            } else {
                                $data['eRRORS'][] = 'Aramex: ' . $results->Notifications->Notification->Code . ' - ' . $results->Notifications->Notification->Message;
                            }
                        } else {

                            $amount = "<p class='amount'>" . $results->TotalAmount->Value . " " . $results->TotalAmount->CurrencyCode . "</p>";
                            $text = "Local taxes - if any - are not included. Rate is based on account number $account in " . $country_code;
                            $this->session->data['rate_html'] = $amount . $text;
                            $this->response->redirect($this->url->link('extension/shipping/aramex/aramex_rate_calculator', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_id, true));
                        }
                    } catch (Exception $e) {
                        $response['type'] = 'error';
                        $response['error'] = $e->getMessage();
                    }
                } catch (Exception $e) {
                    $response['type'] = 'error';
                    $response['error'] = $e->getMessage();
                }
            }

            ################## create shipment end ###########
            $data['is_shipment'] = $this->model_extension_shipping_aramex->checkAWB($this->request->get['order_id']);
            if (isset($this->session->data['rate_html'])) {
                $data['rate_html'] = $this->session->data['rate_html'];
                unset($this->session->data['rate_html']);
            } else {
                $data['rate_html'] = '';
            }
            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('extension/shipping/aramex_rate_calculator', $data));
        } else {
            $this->language->load('error/not_found');

            $this->document->setTitle($this->language->get('heading_title'));

            $data['heading_title'] = $this->language->get('heading_title');

            $data['text_not_found'] = $this->language->get('text_not_found');

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true),
                'separator' => false
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('error/not_found', 'user_token=' . $this->session->data['user_token'], true),
                'separator' => ' :: '
            );


            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
            $this->response->setOutput($this->load->view('error/not_found', $data));
        }
    }

}