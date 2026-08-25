<?php

class ControllerExtensionShippingAramexAramexSchedulePickup extends Controller {

    private $error = array();

    public function index() {
        $this->language->load('extension/shipping/aramex');
        $this->document->setTitle($this->language->get('heading_title_shedule'));
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

            $this->document->setTitle($this->language->get('heading_title_shedule'));

            ############### label #############
            $data['text_back_to_order'] = $this->language->get('text_back_to_order');
            $data['text_create_sipment'] = $this->language->get('text_create_sipment');
            $data['text_rate_calculator'] = $this->language->get('text_rate_calculator');
            $data['text_schedule_pickup'] = $this->language->get('text_schedule_pickup');
            $data['text_print_label'] = $this->language->get('text_print_label');
            $data['text_track'] = $this->language->get('text_track');

            $data['heading_title'] = $this->language->get('heading_title_shedule');


            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true),
                'separator' => false
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title_shedule'),
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

            ############ autocities ##########
            $data['aramex_searchautocities'] = htmlspecialchars_decode($this->url->link('extension/shipping/aramex/aramex_searchautocities', 'user_token=' . $this->session->data['user_token'], true));
            $this->document->addScript('../../catalog/view/javascript/aramex/jqueryui/jquery-ui.js');
            $this->document->addStyle('../../catalog/view/javascript/aramex/jqueryui/jquery-ui.css');
            $data['aramex_loader'] = "../../catalog/view/javascript/aramex/jqueryui/aramex_loader.gif";
            $data['aramex_allow'] = $this->config->get('shipping_aramex_api_location_validator');
            ##################### config shipper details ################

            if (isset($this->request->post['contact']) && !empty($this->request->post['contact'])) {
                $data['contact'] = $this->request->post['contact'];
            } else {
                $data['contact'] = ($this->config->get('shipping_aramex_shipper_name')) ? $this->config->get('shipping_aramex_shipper_name') : '';
            }

            if (isset($this->request->post['company']) && !empty($this->request->post['company'])) {
                $data['company'] = $this->request->post['company'];
            } else {
                $data['company'] = ($this->config->get('shipping_aramex_shipper_company')) ? $this->config->get('shipping_aramex_shipper_company') : '';
            }

            if (isset($this->request->post['phone']) && !empty($this->request->post['phone'])) {
                $data['phone'] = $this->request->post['phone'];
            } else {
                $data['phone'] = ($this->config->get('shipping_aramex_shipper_phone')) ? $this->config->get('shipping_aramex_shipper_phone') : '';
            }

            if (isset($this->request->post['address']) && !empty($this->request->post['address'])) {
                $data['address'] = $this->request->post['address'];
            } else {
                $data['address'] = ($this->config->get('shipping_aramex_shipper_address')) ? $this->config->get('shipping_aramex_shipper_address') : '';
            }

            if (isset($this->request->post['country']) && !empty($this->request->post['country'])) {
                $data['country'] = $this->request->post['country'];
            } else {
                $data['country'] = ($this->config->get('shipping_aramex_shipper_country_code')) ? $this->config->get('shipping_aramex_shipper_country_code') : '';
            }

            if (isset($this->request->post['city']) && !empty($this->request->post['city'])) {
                $data['city'] = $this->request->post['city'];
            } else {
                $data['city'] = ($this->config->get('shipping_aramex_shipper_city')) ? $this->config->get('shipping_aramex_shipper_city') : '';
            }

            if (isset($this->request->post['zip']) && !empty($this->request->post['zip'])) {
                $data['zip'] = $this->request->post['zip'];
            } else {
                $data['zip'] = ($this->config->get('shipping_aramex_shipper_postal_code')) ? $this->config->get('shipping_aramex_shipper_postal_code') : '';
            }

            if (isset($this->request->post['state']) && !empty($this->request->post['state'])) {
                $data['state'] = $this->request->post['state'];
            } else {
                $data['state'] = ($this->config->get('shipping_aramex_shipper_state')) ? $this->config->get('shipping_aramex_shipper_state') : '';
            }

            if (isset($this->request->post['email']) && !empty($this->request->post['email'])) {
                $data['email'] = $this->request->post['email'];
            } else {
                $data['email'] = ($this->config->get('shipping_aramex_shipper_email')) ? $this->config->get('shipping_aramex_shipper_email') : '';
            }

            if (isset($this->request->post['date']) && !empty($this->request->post['date'])) {
                $data['date'] = $this->request->post['date'];
            } else {

                // CODE HERE IF HIGHER
                $data['date'] = date('Y-m-d');

            }

            ##################### customer shipment details ################

            $shipment_receiver_name = '';
            $shipment_receiver_street = '';


            $data['destination_country'] = ($order_info['shipping_iso_code_2']) ? $order_info['shipping_iso_code_2'] : '';
            $data['destination_city'] = ($order_info['shipping_city']) ? $order_info['shipping_city'] : '';
            $data['destination_zipcode'] = ($order_info['shipping_postcode']) ? $order_info['shipping_postcode'] : '';
            $data['destination_state'] = ($order_info['shipping_zone']) ? $order_info['shipping_zone'] : '';


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



            if (isset($this->request->post['product_group']) && !empty($this->request->post['product_group'])) {
                $data['group'] = $this->request->post['product_group'];
            } else {
                $data['group'] = "";
            }
            if (isset($this->request->post['product_type']) && !empty($this->request->post['product_type'])) {
                $data['type'] = $this->request->post['product_type'];
            } else {
                $data['type'] = "";
            }
            if (isset($this->request->post['payment_type']) && !empty($this->request->post['payment_type'])) {
                $data['pay_type'] = $this->request->post['payment_type'];
            } else {
                $data['pay_type'] = '';
            }
            if (isset($this->request->post['comments']) && !empty($this->request->post['comments'])) {
                $data['comments'] = $this->request->post['comments'];
            } else {
                $data['comments'] = '';
            }
            if (isset($this->request->post['mobile']) && !empty($this->request->post['mobile'])) {
                $data['mobile'] = $this->request->post['mobile'];
            } else {
                $data['mobile'] = '';
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

                    if (version_compare(VERSION, '2.0.3.1', '>=')) {
                        // CODE HERE IF HIGHER
                        $date = ($this->request->post['date']) ? $this->request->post['date'] : '';
                    } else {
                        // CODE HERE IF LOWER
                        $date = ($this->request->post['date']) ? $this->request->post['date'] : '';
                        $dateExploded = explode("/", $date);
                        $date = $dateExploded[2] . "-" . $dateExploded[0] . "-" . $dateExploded[1];
                    }


                    $pickupDate = strtotime($date);
                    $readyTimeH = ($this->request->post['ready_hour']) ? $this->request->post['ready_hour'] : '';
                    $readyTimeM = ($this->request->post['ready_minute']) ? $this->request->post['ready_minute'] : '';
                    //$readyTime=mktime(($readyTimeH-2),$readyTimeM,0,date("m",$pickupDate),date("d",$pickupDate),date("Y",$pickupDate));

                    $readyTime = gmmktime(($readyTimeH), $readyTimeM, 0, date("m", $pickupDate), date("d", $pickupDate), date("Y", $pickupDate));

                    $closingTimeH = ($this->request->post['latest_hour']) ? $this->request->post['latest_hour'] : '';
                    $closingTimeM = ($this->request->post['latest_minute']) ? $this->request->post['latest_minute'] : '';
                    //$closingTime=mktime(($closingTimeH-2),$closingTimeM,0,date("m",$pickupDate),date("d",$pickupDate),date("Y",$pickupDate));

                    $closingTime = gmmktime(($closingTimeH), $closingTimeM, 0, date("m", $pickupDate), date("d", $pickupDate), date("Y", $pickupDate));

                    $text_weight = ($this->request->post['text_weight']) ? $this->request->post['text_weight'] : '';
                    $weight_unit = ($this->request->post['weight_unit']) ? $this->request->post['weight_unit'] : '';

                    $params = array(
                        'ClientInfo' => $clientInfo,
                        'Transaction' => array(
                            'Reference1' => ($this->request->post['reference']) ? $this->request->post['reference'] : '',
                        ),
                        'Pickup' => array(
                            'PickupContact' => array(
                                'PersonName' => ($this->request->post['contact']) ? html_entity_decode($this->request->post['contact']) : '',
                                'CompanyName' => ($this->request->post['company']) ? html_entity_decode($this->request->post['company']) : '',
                                'PhoneNumber1' => ($this->request->post['phone']) ? html_entity_decode($this->request->post['phone']) : '',
                                'PhoneNumber1Ext' => ($this->request->post['ext']) ? html_entity_decode($this->request->post['ext']) : '',
                                'CellPhone' => ($this->request->post['mobile']) ? html_entity_decode($this->request->post['mobile']) : '',
                                'EmailAddress' => ($this->request->post['email']) ? html_entity_decode($this->request->post['email']) : ''
                            ),
                            'PickupAddress' => array(
                                'Line1' => ($this->request->post['address']) ? html_entity_decode($this->request->post['address']) : '',
                                'City' => ($this->request->post['city']) ? html_entity_decode($this->request->post['city']) : '',
                                'StateOrProvinceCode' => ($this->request->post['state']) ? html_entity_decode($this->request->post['state']) : '',
                                'PostCode' => ($this->request->post['zip']) ? html_entity_decode($this->request->post['zip']) : '',
                                'CountryCode' => ($this->request->post['country']) ? $this->request->post['country'] : '',
                            ),
                            'PickupLocation' => ($this->request->post['location']) ? html_entity_decode($this->request->post['location']) : '',
                            'PickupDate' => $readyTime,
                            'ReadyTime' => $readyTime,
                            'LastPickupTime' => $closingTime,
                            'ClosingTime' => $closingTime,
                            'Comments' => ($this->request->post['comments']) ? html_entity_decode($this->request->post['comments']) : '',
                            'Reference1' => ($this->request->post['reference']) ? $this->request->post['reference'] : '',
                            'Reference2' => '',
                            'Vehicle' => ($this->request->post['vehicle']) ? $this->request->post['vehicle'] : '',
                            'Shipments' => array(
                                'Shipment' => array()
                            ),
                            'PickupItems' => array(
                                'PickupItemDetail' => array(
                                    'ProductGroup' => ($this->request->post['product_group']) ? $this->request->post['product_group'] : '',
                                    'ProductType' => ($this->request->post['product_type']) ? $this->request->post['product_type'] : '',
                                    'Payment' => ($this->request->post['payment_type']) ? $this->request->post['payment_type'] : '',
                                    'NumberOfShipments' => ($this->request->post['no_shipments']) ? $this->request->post['no_shipments'] : '',
                                    'NumberOfPieces' => ($this->request->post['total_count']) ? $this->request->post['total_count'] : '',
                                    'ShipmentWeight' => array('Value' => $text_weight, 'Unit' => $weight_unit),
                                ),
                            ),
                            'Status' => ($this->request->post['status']) ? $this->request->post['status'] : '',
                        )
                    );



                    $baseUrl = $this->model_extension_shipping_aramex->getWsdlPath();
                    $soapClient = new SoapClient($baseUrl . '/shipping.wsdl', array('trace' => 1));

                    try {
                        $results = $soapClient->CreatePickup($params);

                        if ($results->HasErrors) {
                            if (count($results->Notifications->Notification) > 1) {
                                $error = "";
                                foreach ($results->Notifications->Notification as $notify_error) {
                                    $data['eRRORS'][] = 'Aramex: ' . $notify_error->Code . ' - ' . $notify_error->Message . "<br>";
                                }
                            } else {
                                $data['eRRORS'][] = 'Aramex: ' . $results->Notifications->Notification->Code . ' - ' . $results->Notifications->Notification->Message;
                            }
                        } else {
                            $notify = false;
                            $visible = false;
                            $comment = "Pickup reference number ( <strong>" . $results->ProcessedPickup->ID . "</strong> ).";
                            //change status
                            $order_status = 15; //Processed
                            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int) $order_status . "', date_modified = NOW() WHERE order_id = '" . (int) $this->request->post['order_id'] . "'");

                            if (isset($this->request->post['aramex_email_customer']) && $this->request->post['aramex_email_customer'] == 'yes') {
                                $is_email = 1;
                            } else {
                                $is_email = 0;
                            }
                            $message = array(
                                'notify' => $is_email,
                                'comment' => $comment
                            );

                            $this->model_extension_shipping_aramex->addOrderHistory($this->request->get['order_id'], $message);
                            $shipmenthistory = "<p class='amount'>Pickup reference number ( <strong>" . $results->ProcessedPickup->ID . "</strong> ).</p>";
                            $this->session->data['success_html'] = $shipmenthistory;
                            $this->response->redirect($this->url->link('extension/shipping/aramex/aramex_schedule_pickup', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_id, true));
                        }
                    } catch (Exception $e) {

                        $data['eRRORS'][] = $e->getMessage();
                    }
                } catch (Exception $e) {
                    $data['eRRORS'][] = $e->getMessage();
                }
            }



            ################## create shipment end ###########

            $data['is_shipment'] = $this->model_extension_shipping_aramex->checkAWB($this->request->get['order_id']);
            $data['is_pickup_created'] = $this->model_extension_shipping_aramex->checkShedulePickup($this->request->get['order_id']);

            //echo '<pre>';
            //print_r($data['order_products']);
            if (isset($this->session->data['success_html'])) {
                $data['success_html'] = $this->session->data['success_html'];

                unset($this->session->data['success_html']);
            } else {
                $data['success_html'] = '';
            }

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('extension/shipping/aramex_schedule_pickup', $data));
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