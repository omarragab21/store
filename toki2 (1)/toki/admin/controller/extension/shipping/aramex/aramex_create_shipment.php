<?php

class ControllerExtensionShippingAramexAramexCreateshipment extends Controller {

    private $error = array();

    public function index() {


        $this->language->load('extension/shipping/aramex');
        $this->document->setTitle($this->language->get('heading_title1'));
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


            $this->document->setTitle($this->language->get('heading_title1'));

            ############### label #############
            $data['text_back_to_order'] = $this->language->get('text_back_to_order');
            $data['text_create_sipment'] = $this->language->get('text_create_sipment');
            $data['text_rate_calculator'] = $this->language->get('text_rate_calculator');
            $data['text_schedule_pickup'] = $this->language->get('text_schedule_pickup');
            $data['text_print_label'] = $this->language->get('text_print_label');
            $data['text_track'] = $this->language->get('text_track');

            $data['heading_title'] = $this->language->get('heading_title1');


            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
                'separator' => false
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title1'),
                'href' => $this->url->link('sale/order', 'user_token=' . $this->session->data['user_token'], 'SSL'),
                'separator' => ' :: '
            );

            $data['order_id'] = $this->request->get['order_id'];

            ############ button ########## schedule_pickup
            $data['back_to_order'] = $this->url->link('sale/order/info', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_id, true);
            $data['aramex_create_sipment'] = $this->url->link('extension/shipping/aramex/aramex_create_shipment', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_id, 'SSL');
            $data['aramex_rate_calculator'] = $this->url->link('extension/shipping/aramex/aramex_rate_calculator', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_id, 'SSL');
            $data['aramex_schedule_pickup'] = $this->url->link('extension/shipping/aramex/aramex_schedule_pickup', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_id, 'SSL');
            $data['aramex_print_label'] = $this->url->link('extension/shipping/aramex/aramex_create_shipment/lable', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_id, 'SSL');
            $data['aramex_traking'] = $this->url->link('extension/shipping/aramex/aramex_traking', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_id, 'SSL');
            $data['aramex_searchautocities'] = htmlspecialchars_decode ($this->url->link('extension/shipping/aramex/aramex_searchautocities',  'user_token='.$this->session->data['user_token'], 'SSL'));
            $this->document->addScript('../../catalog/view/javascript/aramex/jqueryui/jquery-ui.js');
            $this->document->addStyle('../../catalog/view/javascript/aramex/jqueryui/jquery-ui.css');
            $data['aramex_loader'] = "../../catalog/view/javascript/aramex/jqueryui/aramex_loader.gif";
            $data['aramex_allow'] = $this->config->get('shipping_aramex_api_location_validator');
            ############ button ##########
            //$data['amazon_order_id'] = $order_info['amazon_order_id'];
            $data['store_name'] = $order_info['store_name'];
            $data['store_url'] = $order_info['store_url'];
            $data['firstname'] = $order_info['firstname'];
            $data['lastname'] = $order_info['lastname'];

            if ($order_info['customer_id']) {
                $data['customer'] = $this->url->link('extension/shipping/customer/update', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $order_info['customer_id'], 'SSL');
            } else {
                $data['customer'] = '';
            }

                // CODE HERE IF HIGHER
                $this->load->model('customer/customer_group');
                $customer_group_info = $this->model_customer_customer_group->getCustomerGroup($order_info['customer_group_id']);


            if ($customer_group_info) {
                $data['customer_group'] = $customer_group_info['name'];
            } else {
                $data['customer_group'] = '';
            }


            ##################### config shipper details ################
            if (isset($this->request->post['aramex_shipment_shipper_name'])) {
                $data['aramex_shipment_shipper_name'] = $this->request->post['aramex_shipment_shipper_name'];
            } else {
                $data['aramex_shipment_shipper_name'] = ($this->config->get('shipping_aramex_shipper_name')) ? $this->config->get('shipping_aramex_shipper_name') : '';
            }

            if (isset($this->request->post['aramex_shipment_shipper_email'])) {
                $data['aramex_shipment_shipper_email'] = $this->request->post['aramex_shipment_shipper_email'];
            } else {
                $data['aramex_shipment_shipper_email'] = ($this->config->get('shipping_aramex_shipper_email')) ? $this->config->get('shipping_aramex_shipper_email') : '';
            }

            if (isset($this->request->post['aramex_shipment_shipper_company'])) {
                $data['aramex_shipment_shipper_company'] = $this->request->post['aramex_shipment_shipper_company'];
            } else {
                $data['aramex_shipment_shipper_company'] = ($this->config->get('shipping_aramex_shipper_company')) ? $this->config->get('shipping_aramex_shipper_company') : '';
            }

            if (isset($this->request->post['aramex_shipment_shipper_street'])) {
                $data['aramex_shipment_shipper_street'] = $this->request->post['aramex_shipment_shipper_street'];
            } else {
                $data['aramex_shipment_shipper_street'] = ($this->config->get('shipping_aramex_shipper_address')) ? $this->config->get('shipping_aramex_shipper_address') : '';
            }

            if (isset($this->request->post['aramex_shipment_shipper_country'])) {
                $data['aramex_shipment_shipper_country'] = $this->request->post['aramex_shipment_shipper_country'];
            } else {
                $data['aramex_shipment_shipper_country'] = ($this->config->get('shipping_aramex_shipper_country_code')) ? $this->config->get('shipping_aramex_shipper_country_code') : '';
            }

            if (isset($this->request->post['aramex_shipment_shipper_city'])) {
                $data['aramex_shipment_shipper_city'] = $this->request->post['aramex_shipment_shipper_city'];
            } else {
                $data['aramex_shipment_shipper_city'] = ($this->config->get('shipping_aramex_shipper_city')) ? $this->config->get('shipping_aramex_shipper_city') : '';
            }

            if (isset($this->request->post['aramex_shipment_shipper_postal'])) {
                $data['aramex_shipment_shipper_postal'] = $this->request->post['aramex_shipment_shipper_postal'];
            } else {
                $data['aramex_shipment_shipper_postal'] = ($this->config->get('shipping_aramex_shipper_postal_code')) ? $this->config->get('shipping_aramex_shipper_postal_code') : '';
            }

            if (isset($this->request->post['aramex_shipment_shipper_state'])) {
                $data['aramex_shipment_shipper_state'] = $this->request->post['aramex_shipment_shipper_state'];
            } else {
                $data['aramex_shipment_shipper_state'] = ($this->config->get('shipping_aramex_shipper_state')) ? $this->config->get('shipping_aramex_shipper_state') : '';
            }

            if (isset($this->request->post['aramex_shipment_shipper_phone'])) {
                $data['aramex_shipment_shipper_phone'] = $this->request->post['aramex_shipment_shipper_phone'];
            } else {
                $data['aramex_shipment_shipper_phone'] = ($this->config->get('shipping_aramex_shipper_phone')) ? $this->config->get('shipping_aramex_shipper_phone') : '';
            }

            ##################### customer shipment details ################

            $shipment_receiver_name = '';
            $shipment_receiver_street = '';
            if (isset($order_info['shipping_firstname']) && !empty($order_info['shipping_firstname'])) {
                $shipment_receiver_name .= $order_info['shipping_firstname'];
            }
            if (isset($order_info['shipping_lastname']) && !empty($order_info['shipping_lastname'])) {
                $shipment_receiver_name .= " " . $order_info['shipping_lastname'];
            }
            if (isset($order_info['shipping_address_1']) && !empty($order_info['shipping_address_1'])) {
                $shipment_receiver_street .= $order_info['shipping_address_1'];
            }
            if (isset($order_info['shipping_address_2']) && !empty($order_info['shipping_address_2'])) {
                $shipment_receiver_street .= " " . $order_info['shipping_address_2'];
            }

            if (isset($this->request->post['aramex_shipment_receiver_name']) && !empty($this->request->post['aramex_shipment_receiver_name'])) {
                $data['aramex_shipment_receiver_name'] = $this->request->post['aramex_shipment_receiver_name'];
            } else {
                $data['aramex_shipment_receiver_name'] = $shipment_receiver_name;
            }

            if (isset($this->request->post['aramex_shipment_receiver_email'])) {
                $data['aramex_shipment_receiver_email'] = $this->request->post['aramex_shipment_receiver_email'];
            } else {
                $data['aramex_shipment_receiver_email'] = ($order_info['email']) ? $order_info['email'] : '';
            }

            if (isset($this->request->post['aramex_shipment_receiver_company'])) {
                $data['aramex_shipment_receiver_company'] = $this->request->post['aramex_shipment_receiver_company'];
            } else {
                $data['aramex_shipment_receiver_company'] = ($order_info['shipping_company']) ? $order_info['shipping_company'] : '';
            }

            if (isset($this->request->post['aramex_shipment_receiver_street'])) {
                $data['aramex_shipment_receiver_street'] = $this->request->post['aramex_shipment_receiver_street'];
            } else {
                $data['aramex_shipment_receiver_street'] = $shipment_receiver_street;
            }

            if (isset($this->request->post['aramex_shipment_receiver_country'])) {
                $data['aramex_shipment_receiver_country'] = $this->request->post['aramex_shipment_receiver_country'];
            } else {
                $data['aramex_shipment_receiver_country'] = ($order_info['shipping_iso_code_2']) ? $order_info['shipping_iso_code_2'] : '';
            }

            if (isset($this->request->post['aramex_shipment_receiver_city'])) {
                $data['aramex_shipment_receiver_city'] = $this->request->post['aramex_shipment_receiver_city'];
            } else {
                $data['aramex_shipment_receiver_city'] = ($order_info['shipping_city']) ? $order_info['shipping_city'] : '';
            }

            if (isset($this->request->post['aramex_shipment_receiver_postal'])) {
                $data['aramex_shipment_receiver_postal'] = $this->request->post['aramex_shipment_receiver_postal'];
            } else {
                $data['aramex_shipment_receiver_postal'] = ($order_info['shipping_postcode']) ? $order_info['shipping_postcode'] : '';
            }

            if (isset($this->request->post['aramex_shipment_receiver_state'])) {
                $data['aramex_shipment_receiver_state'] = $this->request->post['aramex_shipment_receiver_state'];
            } else {
                $data['aramex_shipment_receiver_state'] = ($order_info['shipping_zone']) ? $order_info['shipping_zone'] : '';
            }

            if (isset($this->request->post['aramex_shipment_receiver_phone'])) {
                $data['aramex_shipment_receiver_phone'] = $this->request->post['aramex_shipment_receiver_phone'];
            } else {
                $data['aramex_shipment_receiver_phone'] = ($order_info['telephone']) ? $order_info['telephone'] : '';
            }
            #################

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


            if (isset($this->request->post['aramex_shipment_info_billing_account'])) {
                $data['aramex_shipment_info_billing_account'] = $this->request->post['aramex_shipment_info_billing_account'];
            } else {
                $data['aramex_shipment_info_billing_account'] = "";
            }
            if (isset($this->request->post['aramex_shipment_info_product_group'])) {
                $data['group'] = $this->request->post['aramex_shipment_info_product_group'];
            } else {
                $data['group'] = "";
            }
            if (isset($this->request->post['aramex_shipment_info_product_type'])) {
                $data['type'] = $this->request->post['aramex_shipment_info_product_type'];
            } else {
                $data['type'] = "";
            }
			
            if (isset($this->request->post['aramex_shipment_info_service_type'])) {
                $data['stype'] = $this->request->post['aramex_shipment_info_service_type'][0];
            } else {
                $data['stype'] = $this->config->get('shipping_aramex_default_allowed_domestic_additional_services');
            }
            if (isset($this->request->post['aramex_shipment_info_service_type'])) {
                $data['stype1'] = $this->request->post['aramex_shipment_info_service_type'][0];
            } else {
                $data['stype1'] = $this->config->get('shipping_aramex_default_allowed_international_additional_services');
            }
            if($order_info['payment_code'] === "cod"){
                $data['payment_method'] = 'CODS';
            }else{
                $data['payment_method'] = '';
            }
			
            if (isset($this->request->post['aramex_shipment_info_payment_type'])) {
                $data['pay_type'] = $this->request->post['aramex_shipment_info_payment_type'];
            } else {
                $data['pay_type'] = '';
            }
            if (isset($this->request->post['aramex_shipment_info_payment_option'])) {
                $data['pay_option'] = $this->request->post['aramex_shipment_info_payment_option'];
            } else {
                $data['pay_option'] = '';
            }

            if (isset($this->request->post['aramex_shipment_currency_code'])) {
                $data['currency_code'] = $this->request->post['aramex_shipment_currency_code'];
            } else {
                $data['currency_code'] = ($order_info['currency_code']) ? $order_info['currency_code'] : '';
                ;
            }

            if (isset($this->request->post['aramex_shipment_info_cod_amount'])) {
                $data['cod_value'] = $this->request->post['aramex_shipment_info_cod_amount'];
            } else {
                $data['cod_value'] = ($order_info['total']) ? number_format($order_info['total'], 2) : '';
                ;
            }
////////////////
            $data['cod_value'] = $this->currency->format(str_replace(',', '', $data['cod_value']), $order_info['currency_code'], $order_info['currency_value'], false);

            if (isset($this->request->post['aramex_shipment_info_custom_amount'])) {
                $data['custom_amount'] = $this->request->post['aramex_shipment_info_custom_amount'];
            } else {
                $data['custom_amount'] = '';
                ;
            }
            if (isset($this->request->post['aramex_shipment_info_comment'])) {
                $data['aramex_shipment_info_comment'] = $this->request->post['aramex_shipment_info_comment'];
            } else {
                $data['aramex_shipment_info_comment'] = '';
                ;
            }
            if (isset($this->request->post['aramex_shipment_info_foreignhawb'])) {
                $data['aramex_shipment_info_foreignhawb'] = $this->request->post['aramex_shipment_info_foreignhawb'];
            } else {
                $data['aramex_shipment_info_foreignhawb'] = '';
                ;
            }
            if (isset($this->request->post['weight_unit'])) {
                $getunit_classid = $this->model_extension_shipping_aramex->getWeightClassId($this->request->post['weight_unit']);
                $data['weight_unit'] = $getunit_classid->row['unit'];
                $config_weight_class_id = $getunit_classid->row['weight_class_id'];
            } else {
                $data['weight_unit'] = $this->weight->getUnit($this->config->get('config_weight_class_id'));
                $config_weight_class_id = $this->config->get('config_weight_class_id');
            }

            if ($order_info['payment_method'] != null) {
                if(stripos($order_info['payment_method'],"smart") !== false)
                {
                    $data['aramex_payment_method'] = "payment_smartaramex";
                }
            }

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
                $data['product_arr'][] = $order_product['name'];
                $data['order_products'][] = array(
                    'order_product_id' => $order_product['order_product_id'],
                    'product_id' => $order_product['product_id'],
                    'name' => $order_product['name'],
                    'model' => $order_product['model'],
                    'option' => $order_option,
                    'quantity' => $order_product['quantity'],
                    'weight' => number_format($this->weight->convert($product_weight_query->row['weight'], $product_weight_query->row['weight_class_id'], $config_weight_class_id), 2),
                    'weight_class' => $weight_class_query->row['unit'],
                    'price' => number_format($order_product['price'], 2),
                    'total' => $order_product['total'],
                    'tax' => $order_product['tax'],
                    'reward' => $order_product['reward']
                );
            }
            $data['weighttot'] = number_format($weighttot, 2);
            $data['total'] = number_format($order_info['total'], 2);
            $data['total'] = $this->currency->format(str_replace(',', '',$data['total']), $order_info['currency_code'], $order_info['currency_value'], false);

            ################## create shipment ###########
            if ($this->request->post) {
                $baseUrl = $this->model_extension_shipping_aramex->getWsdlPath();
                //SOAP object
                $soapClient = new SoapClient($baseUrl . '/shipping.wsdl');
                $aramex_errors = false;
                $flag = true;
                $error = "";
                try {
                    $totalWeight = 0;
                    $totalItems = 0;
                    $aramex_items_counter = 0;
                    foreach ($this->request->post['aramex_items'] as $key => $value) {
                        $aramex_items_counter++;
                        if ($value != 0) {
                            //itrating order items
                            foreach ($order_products as $item) {
                                if ($item['order_product_id'] == $key) {
                                    //get weight
                                    $weight = $this->weight->convert($product_weight_query->row['weight'], $product_weight_query->row['weight_class_id'], $config_weight_class_id);
                                    $weight = ($weight * $item['quantity']);

                                    $order_product_id = $item['order_product_id'];
                                    // collect items for aramex
                                    $aramex_items[] = array(
                                        'PackageType' => 'Box',
                                        'Quantity' => $this->request->post[$order_product_id],
                                        'Weight' => array(
                                            'Value' => $weight,
                                            'Unit' => 'Kg'
                                        ),
                                        'Comments' => $item['order_product_id'], //'',
                                        'Reference' => ''
                                    );

                                    $totalWeight += $weight;
                                    $totalItems += $this->request->post[$order_product_id];
                                }
                            }
                        }
                    }

                    $aramex_atachments = array();
                    //attachment
                    for ($i = 1; $i <= 3; $i++) {
                        if (isset($fileName) != '') {
                            $fileName = explode('.', $fileName);
                            $fileName = $fileName[0]; //filename without extension
                            $fileData = '';
                            if ($_FILES['file' . $i]['tmp_name'] != '')
                                $fileData = file_get_contents($_FILES['file' . $i]['tmp_name']);
                            $ext = pathinfo($_FILES['file' . $i]['name'], PATHINFO_EXTENSION); //file extension
                            if ($fileName && $ext && $fileData)
                                $aramex_atachments[] = array(
                                    'FileName' => $fileName,
                                    'FileExtension' => $ext,
                                    'FileContents' => $fileData
                                );
                        }
                    }
                    $params = array();

                    //shipper parameters
                    $params['Shipper'] = array(
                        'Reference1' => ($this->request->post['aramex_shipment_shipper_reference']) ? $this->request->post['aramex_shipment_shipper_reference'] : '', //'ref11111',
                        'Reference2' => '',
                        'AccountNumber' => ($this->request->post['aramex_shipment_shipper_account']) ? $this->request->post['aramex_shipment_shipper_account'] : '', //'43871',
                        //Party Address
                        'PartyAddress' => array(
                            'Line1' => ($this->request->post['aramex_shipment_shipper_street']) ? addslashes($this->request->post['aramex_shipment_shipper_street']) : '', //'13 Mecca St',
                            'Line2' => '',
                            'Line3' => '',
                            'City' => ($this->request->post['aramex_shipment_shipper_city']) ? $this->request->post['aramex_shipment_shipper_city'] : '', //'Dubai',
                            'StateOrProvinceCode' => ($this->request->post['aramex_shipment_shipper_state']) ? $this->request->post['aramex_shipment_shipper_state'] : '', //'',
                            'PostCode' => ($this->request->post['aramex_shipment_shipper_postal']) ? $this->request->post['aramex_shipment_shipper_postal'] : '',
                            'CountryCode' => ($this->request->post['aramex_shipment_shipper_country']) ? $this->request->post['aramex_shipment_shipper_country'] : '', //'AE'
                        ),
                        //Contact Info
                        'Contact' => array(
                            'Department' => '',
                            'PersonName' => ($this->request->post['aramex_shipment_shipper_name']) ? $this->request->post['aramex_shipment_shipper_name'] : '', //'Suheir',
                            'Title' => '',
                            'CompanyName' => ($this->request->post['aramex_shipment_shipper_company']) ? $this->request->post['aramex_shipment_shipper_company'] : '', //'Aramex',
                            'PhoneNumber1' => ($this->request->post['aramex_shipment_shipper_phone']) ? $this->request->post['aramex_shipment_shipper_phone'] : '', //'55555555',
                            'PhoneNumber1Ext' => '',
                            'PhoneNumber2' => '',
                            'PhoneNumber2Ext' => '',
                            'FaxNumber' => '',
                            'CellPhone' => ($this->request->post['aramex_shipment_shipper_phone']) ? $this->request->post['aramex_shipment_shipper_phone'] : '',
                            'EmailAddress' => ($this->request->post['aramex_shipment_shipper_email']) ? $this->request->post['aramex_shipment_shipper_email'] : '', //'',
                            'Type' => ''
                        ),
                    );

                    //consinee parameters
                    $params['Consignee'] = array(
                        'Reference1' => ($this->request->post['aramex_shipment_receiver_reference']) ? $this->request->post['aramex_shipment_receiver_reference'] : '', //'',
                        'Reference2' => '',
                        'AccountNumber' => ($this->request->post['aramex_shipment_info_billing_account'] == 2) ? $this->request->post['aramex_shipment_shipper_account'] : '',
                        //Party Address
                        'PartyAddress' => array(
                            'Line1' => ($this->request->post['aramex_shipment_receiver_street']) ? $this->request->post['aramex_shipment_receiver_street'] : '', //'15 ABC St',
                            'Line2' => '',
                            'Line3' => '',
                            'City' => ($this->request->post['aramex_shipment_receiver_city']) ? $this->request->post['aramex_shipment_receiver_city'] : '', //'Amman',
                            'StateOrProvinceCode' => '',
                            'PostCode' => ($this->request->post['aramex_shipment_receiver_postal']) ? $this->request->post['aramex_shipment_receiver_postal'] : '',
                            'CountryCode' => ($this->request->post['aramex_shipment_receiver_country']) ? $this->request->post['aramex_shipment_receiver_country'] : '', //'JO'
                        ),
                        //Contact Info
                        'Contact' => array(
                            'Department' => '',
                            'PersonName' => ($this->request->post['aramex_shipment_receiver_name']) ? $this->request->post['aramex_shipment_receiver_name'] : '', //'Mazen',
                            'Title' => '',
                            'CompanyName' => ($this->request->post['aramex_shipment_receiver_company']) ? $this->request->post['aramex_shipment_receiver_company'] : 'individual', //'Aramex',
                            'PhoneNumber1' => ($this->request->post['aramex_shipment_receiver_phone']) ? $this->request->post['aramex_shipment_receiver_phone'] : '', //'6666666',
                            'PhoneNumber1Ext' => '',
                            'PhoneNumber2' => '',
                            'PhoneNumber2Ext' => '',
                            'FaxNumber' => '',
                            'CellPhone' => ($this->request->post['aramex_shipment_receiver_phone']) ? $this->request->post['aramex_shipment_receiver_phone'] : '',
                            'EmailAddress' => ($this->request->post['aramex_shipment_receiver_email']) ? $this->request->post['aramex_shipment_receiver_email'] : '', //'mazen@aramex.com',
                            'Type' => ''
                        )
                    );

                    //new

                    if ($this->request->post['aramex_shipment_info_billing_account'] == 3) {
                        $params['ThirdParty'] = array(
                            'Reference1' => ($this->request->post['aramex_shipment_shipper_reference']) ? $this->request->post['aramex_shipment_shipper_reference'] : '', //'ref11111',
                            'Reference2' => '',
                            'AccountNumber' => ($this->request->post['aramex_shipment_shipper_account']) ? $this->request->post['aramex_shipment_shipper_account'] : '', //'43871',
                            //Party Address
                            'PartyAddress' => array(
                                'Line1' => ($this->config->get('shipping_aramex_shipper_address')) ? $this->config->get('shipping_aramex_shipper_address') : '', //'13 Mecca St',
                                'Line2' => '',
                                'Line3' => '',
                                'City' => ($this->config->get('shipping_aramex_shipper_city')) ? $this->config->get('shipping_aramex_shipper_city') : '', //'Dubai',
                                'StateOrProvinceCode' => ($this->config->get('shipping_aramex_shipper_state')) ? $this->config->get('shipping_aramex_shipper_state') : '', //'',
                                'PostCode' => ($this->config->get('shipping_aramex_shipper_postal_code')) ? $this->config->get('shipping_aramex_shipper_postal_code') : '',
                                'CountryCode' => ($this->config->get('shipping_aramex_shipper_country_code')) ? $this->config->get('shipping_aramex_shipper_country_code') : '', //'AE'
                            ),
                            //Contact Info
                            'Contact' => array(
                                'Department' => '',
                                'PersonName' => ($this->config->get('shipping_aramex_shipper_name')) ? $this->config->get('shipping_aramex_shipper_name') : '', //'Suheir',
                                'Title' => '',
                                'CompanyName' => ($this->config->get('shipping_aramex_shipper_company')) ? $this->config->get('shipping_aramex_shipper_company') : '', //'Aramex',
                                'PhoneNumber1' => ($this->config->get('shipping_aramex_shipper_phone')) ? $this->config->get('shipping_aramex_shipper_phone') : '', //'55555555',
                                'PhoneNumber1Ext' => '',
                                'PhoneNumber2' => '',
                                'PhoneNumber2Ext' => '',
                                'FaxNumber' => '',
                                'CellPhone' => ($this->config->get('shipping_aramex_shipper_phone')) ? $this->config->get('shipping_aramex_shipper_phone') : '',
                                'EmailAddress' => ($this->config->get('shipping_aramex_shipper_email')) ? $this->config->get('shipping_aramex_shipper_email') : '', //'',
                                'Type' => ''
                            ),
                        );
                    }

                    // Other Main Shipment Parameters
                    $params['Reference1'] = ($this->request->post['aramex_shipment_info_reference']) ? $this->request->post['aramex_shipment_info_reference'] : ''; //'Shpt0001';
                    $params['Reference2'] = '';
                    $params['Reference3'] = '';
                    $params['ForeignHAWB'] = ($this->request->post['aramex_shipment_info_foreignhawb']) ? $this->request->post['aramex_shipment_info_foreignhawb'] : '';

                    $params['TransportType'] = 0;
                    $params['ShippingDateTime'] = time(); //date('m/d/Y g:i:sA');
                    $params['DueDate'] = time() + (7 * 24 * 60 * 60); //date('m/d/Y g:i:sA');
                    $params['PickupLocation'] = 'Reception';
                    $params['PickupGUID'] = '';
                    $params['Comments'] = ($this->request->post['aramex_shipment_info_comment']) ? $this->request->post['aramex_shipment_info_comment'] : '';
                    $params['AccountingInstrcutions'] = '';
                    $params['OperationsInstructions'] = '';
////// add COD
                    $services = array();
                    if(!isset($this->request->post['aramex_shipment_info_service_type'])){ $this->request->post['aramex_shipment_info_service_type'] = null; };
                    if($this->request->post['aramex_shipment_info_product_type'] == "CDA"){
                        if( $this->request->post['aramex_shipment_info_service_type'] == null ){
                            array_push($services, "CODS");
                        }elseif ( !in_array("CODS", $this->request->post['aramex_shipment_info_service_type'])){
                            $services = array_merge($services, $this->request->post['aramex_shipment_info_service_type']);
                            array_push($services, "CODS");
                        }else{
                            $services = array_merge($services, $this->request->post['aramex_shipment_info_service_type']);
                        }
                    }else{
                        if($this->request->post['aramex_shipment_info_service_type'] == null){
                            $this->request->post['aramex_shipment_info_service_type'] = array();
                        }

                        $services = array_merge($services, $this->request->post['aramex_shipment_info_service_type']);
                    }
                    $services = implode(',', $services);

                    $AdditionalPropertyDetails = array();
        if (isset($this->request->post['aramex_shipment_shipper_country']) && $this->request->post['aramex_shipment_shipper_country'] == 'IN') {
            if ($this->request->post['ShipperTaxIdVATEINNumber'] != null)
            {
                array_push($AdditionalPropertyDetails,[
                    'CategoryName' => "CustomsClearance",
                    'Name' => "ShipperTaxIdVATEINNumber",
                    'Value' => $this->request->post['ShipperTaxIdVATEINNumber']
                ]);
            }
            if ($this->request->post['ConsigneeTaxIdVATEINNumber'] != null)
            {
                array_push($AdditionalPropertyDetails,[
                    'CategoryName' => "CustomsClearance",
                    'Name' => "ConsigneeTaxIdVATEINNumber",
                    'Value' => $this->request->post['ConsigneeTaxIdVATEINNumber']
                ]);
            }
            if ($this->request->post['TaxPaid'] != null)
            {
                array_push($AdditionalPropertyDetails,[
                    'CategoryName' => "CustomsClearance",
                    'Name' => "TaxPaid",
                    'Value' => $this->request->post['TaxPaid']
                ]);
            }
            if ($this->request->post['InvoiceDate'] != null)
            {
                $orgDate = $this->request->post['InvoiceDate'];  
                $newDate = date("m/d/Y", strtotime($orgDate));  
                array_push($AdditionalPropertyDetails,[
                    'CategoryName' => "CustomsClearance",
                    'Name' => "InvoiceDate",
                    'Value' => $newDate
                ]);
            }
            if ($this->request->post['InvoiceNumber'] != null)
            {
                array_push($AdditionalPropertyDetails,[
                    'CategoryName' => "CustomsClearance",
                    'Name' => "InvoiceNumber",
                    'Value' => $this->request->post['InvoiceNumber']
                ]);
            }
            if ($this->request->post['TaxAmount'] != null)
            {
                array_push($AdditionalPropertyDetails,[
                    'CategoryName' => "CustomsClearance",
                    'Name' => "TaxAmount",
                    'Value' => $this->request->post['TaxAmount']
                ]);
            }
            if ($this->request->post['ExporterType'] != null)
            {
                array_push($AdditionalPropertyDetails,[
                    'CategoryName' => "CustomsClearance",
                    'Name' => "ExporterType",
                    'Value' => $this->request->post['ExporterType']
                ]);
            }
        }
                    
                    ///// add COD and

                    $itemNumbers = explode(',',$this->request->post['item_details']);
                    $itemDetails = array();
            
                    $isTrue = true;
                    foreach ($itemNumbers as $val)
                    {
                        if ($isTrue)
                        { 
                            $isTrue = false;
                            continue;
                        }
                        $itemTitle = "aramex_items_Title_".$val;
                        $itemQuantity = "aramex_items_total_".$val;
                        $itemPrice = "aramex_items_base_price_".$val;
                        $itemWeight = "aramex_items_base_weight_".$val;
                        array_push($itemDetails,array(
                            'Quantity' => $this->request->post[$itemQuantity],
                            'Weight' => array(
                                'Value' => $this->request->post[$itemWeight],
                                'Unit' => $this->request->post['weight_unit']
                            ),
                        'GoodsDescription' => $this->request->post[$itemTitle],
                        'CustomsValue' => array(
                            'Value' => $this->request->post[$itemPrice],
                            'CurrencyCode' => $this->request->post['aramex_shipment_currency_code_hidden']
                            )
                    ));
                }

                    $orderTotalWeight = 0;
                    if ($this->request->post['order_weight'] != ''){
                        $orderTotalWeight = $this->request->post['order_weight'];
                    }
                    else {
                        $orderTotalWeight = $this->request->post['order_weight_hidden'];
                    }
                    $totalItems = (trim($this->request->post['number_pieces']) == '') ? 1 : (int)$this->request->post['number_pieces'];
                    $params['Details'] = array(
                        'Dimensions' => array(
                            'Length' => '0',
                            'Width' => '0',
                            'Height' => '0',
                            'Unit' => 'cm'
                        ),
                        'ActualWeight' => array(
                            'Value' => $orderTotalWeight,
                            'Unit' => $this->request->post['weight_unit']
                        ),
                        'ProductGroup' => ($this->request->post['aramex_shipment_info_product_group']) ? $this->request->post['aramex_shipment_info_product_group'] : '', //'EXP',
                        'ProductType' => ($this->request->post['aramex_shipment_info_product_type']) ? $this->request->post['aramex_shipment_info_product_type'] : '', //,'PDX'
                        'PaymentType' => ($this->request->post['aramex_shipment_info_payment_type']) ? $this->request->post['aramex_shipment_info_payment_type'] : '',
                        'PaymentOptions' => ($this->request->post['aramex_shipment_info_payment_option']) ? $this->request->post['aramex_shipment_info_payment_option'] : '', //$post['aramex_shipment_info_payment_option']
                        'Services' => $services,
                        'NumberOfPieces' => $totalItems,
                        'DescriptionOfGoods' => ($this->request->post['aramex_shipment_description']) ? $this->request->post['aramex_shipment_description'] : '',
                        'GoodsOriginCountry' => ($this->request->post['aramex_shipment_shipper_country']) ? $this->request->post['aramex_shipment_shipper_country'] : '', //'JO',
                        'Items' => array(
                            'ShipmentItem' => $itemDetails
                        ),
                        'AdditionalProperties' => [
                            'AdditionalProperty' => $AdditionalPropertyDetails
                        ]
                    );
                    if (count($aramex_atachments)) {
                        $params['Attachments'] = $aramex_atachments;
                    }
                    if ($this->request->post['aramex_shipment_info_service_type'] != null)
                    {
                        $hasCOD = strval(array_search("CODS",$this->request->post['aramex_shipment_info_service_type'],false));
                        if ($hasCOD == "")
                        {                    
                            $params['Details']['CashOnDeliveryAmount'] = null ;
                        }
                        else
                        {
                            $params['Details']['CashOnDeliveryAmount'] = array(
                                'Value' => ($this->request->post['aramex_shipment_info_cod_amount']) ? $this->request->post['aramex_shipment_info_cod_amount'] : '',
                                'CurrencyCode' => ($this->request->post['aramex_shipment_currency_code']) ? $this->request->post['aramex_shipment_currency_code'] : ''
                            );
                        }
                    }
                    else
                    {
                        $params['Details']['CashOnDeliveryAmount'] = null ;       
                    }
                    if ($this->request->post['aramex_shipment_info_product_group'] === 'DOM') {
                        $params['Details']['CustomsValueAmount'] = array(
                            'Value' => ($this->request->post['aramex_shipment_info_custom_amount_hidden']) ? $this->request->post['aramex_shipment_info_custom_amount_hidden'] : '',
                            'CurrencyCode' => ($this->request->post['aramex_shipment_currency_code_hidden']) ? $this->request->post['aramex_shipment_currency_code_hidden'] : ''
                        );
                    }
                    else {
                        $params['Details']['CustomsValueAmount'] = array(
                            'Value' => ($this->request->post['aramex_shipment_info_custom_amount']) ? $this->request->post['aramex_shipment_info_custom_amount'] : '',
                            'CurrencyCode' => ($this->request->post['aramex_shipment_currency_code']) ? $this->request->post['aramex_shipment_currency_code'] : ''
                        );
                    }
                    
                    $major_par['Shipments'][] = $params;
                    $clientInfo = $this->model_extension_shipping_aramex->getClientInfo();

                    // Source
                    $payment_method_type = $order_info['payment_method'];
                    if(stripos($payment_method_type,"smart") !== false)
                    {
                        $clientInfo['Source'] = 47;
                    } 

                    $major_par['ClientInfo'] = $clientInfo;
                    $report_id = ($this->config->get('shipping_aramex_report_id')) ? $this->config->get('shipping_aramex_report_id') : '9729';

                    $major_par['LabelInfo'] = array(
                        'ReportID' => $report_id, //'9201',
                        'ReportType' => 'URL'
                    );

                    try {
                        //create shipment call
                        $auth_call = $soapClient->CreateShipments($major_par);

                        if ($auth_call->HasErrors) {
                            if (empty($auth_call->Shipments)) {
                                if (count($auth_call->Notifications->Notification) > 1) {
                                    foreach ($auth_call->Notifications->Notification as $notify_error) {

                                        $data['eRRORS'][] = 'Aramex: ' . $notify_error->Code . ' - ' . $notify_error->Message;
                                    }
                                } else {
                                    $data['eRRORS'][] = 'Aramex: ' . $auth_call->Notifications->Notification->Code . ' - ' . $auth_call->Notifications->Notification->Message;
                                }
                            } else {

                                if (isset($auth_call->Notifications->Notification)) {
                                    if (is_countable($auth_call->Notifications->Notification) && count($auth_call->Notifications->Notification) > 1) {
                                        $notification_string = '';
                                        foreach ($auth_call->Notifications->Notification as $notification_error) {
                                            $notification_string .= $notification_error->Code . ' - ' . $notification_error->Message . ' <br />';
                                        }
                                        $data['eRRORS'][] = $notification_string;
                                    } else {

                                        $data['eRRORS'][] = 'Aramex: ' . $auth_call->Notifications->Notification->Code . ' - ' . $auth_call->Notifications->Notification->Message;
                                    }
                                } else {
                                    if (is_countable($auth_call->Shipments->ProcessedShipment->Notifications->Notification) && count($auth_call->Shipments->ProcessedShipment->Notifications->Notification) > 1) {
                                        $notification_string = '';
                                        foreach ($auth_call->Shipments->ProcessedShipment->Notifications->Notification as $notification_error) {
                                            $notification_string .= $notification_error->Code . ' - ' . $notification_error->Message . ' <br />';
                                        }
                                        $data['eRRORS'][] = $notification_string;
                                    } else {

                                        $data['eRRORS'][] = 'Aramex: ' . $auth_call->Shipments->ProcessedShipment->Notifications->Notification->Code . ' - ' . $auth_call->Shipments->ProcessedShipment->Notifications->Notification->Message;
                                    }
                                }
                            }
                        } else {
                            if (isset($this->request->post['aramex_return_shipment'])) {
                                $shipmenthistory = "Return shipment AWB No. " . $auth_call->Shipments->ProcessedShipment->ID . " - Order No. " . $auth_call->Shipments->ProcessedShipment->Reference1;
                                $is_email = 0;
                                $message = array(
                                    'notify' => $is_email,
                                    'comment' => $shipmenthistory
                                );
                                $this->model_extension_shipping_aramex->addOrderHistory($this->request->get['order_id'], $message);
                                $this->session->data['success_html'] = $shipmenthistory;
                                $this->response->redirect($this->url->link('extension/shipping/aramex/aramex_create_shipment', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_id, 'SSL'));
                            } else {

                                $shipmenthistory = "AWB No. " . $auth_call->Shipments->ProcessedShipment->ID . " - Order No. " . $auth_call->Shipments->ProcessedShipment->Reference1;

                                //change status
                                $order_status = 2; //Processing
                                $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int) $order_status . "', date_modified = NOW() WHERE order_id = '" . (int) $this->request->post['order_id'] . "'");

                                if (isset($this->request->post['aramex_email_customer']) && $this->request->post['aramex_email_customer'] == 'yes') {

                                    $is_email = 1;
                                } else {
                                    $is_email = 0;
                                }
                                $message = array(
                                    'notify' => $is_email,
                                    'comment' => $shipmenthistory
                                );
                                $this->model_extension_shipping_aramex->addOrderHistory($this->request->get['order_id'], $message);
                                $this->session->data['success_html'] = $shipmenthistory;
                                $this->response->redirect($this->url->link('extension/shipping/aramex/aramex_create_shipment', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_id, 'SSL'));
                            }
                        }
                    } catch (Exception $e) {
                        $aramex_errors = true;
                        $data['eRRORS'][] = $e->getMessage();
                    }
                } catch (Exception $e) {
                    $data['eRRORS'][] = $e->getMessage();
                }
            }



            ################## create shipment end ###########

            $data['is_shipment'] = $this->model_extension_shipping_aramex->checkAWB($this->request->get['order_id']);
            if (isset($this->session->data['success_html'])) {
                $data['success_html'] = $this->session->data['success_html'];

                unset($this->session->data['success_html']);
            } else {
                $data['success_html'] = '';
            }

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('extension/shipping/aramex_create_shipment', $data));
        } else {
            $this->language->load('error/not_found');

            $this->document->setTitle($this->language->get('heading_title1'));

            $data['heading_title'] = $this->language->get('heading_title1');

            $data['text_not_found'] = $this->language->get('text_not_found');

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], 'SSL'),
                'separator' => false
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title1'),
                'href' => $this->url->link('error/not_found', 'user_token=' . $this->session->data['user_token'], 'SSL'),
                'separator' => ' :: '
            );

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('error/not_found', $data));
        }
    }

    public function lable() {
        $this->load->model('sale/order');
        $this->load->model('extension/shipping/aramex');
        $this->load->model('extension/shipping/aramexsettings');

        if (isset($this->request->get['order_id'])) {
            $order_id = $this->request->get['order_id'];
        } else {
            $order_id = 0;
        }


        if ($order_id) {
            $shipments = $this->model_extension_shipping_aramex->getOrderHistoriesAWB($this->request->get['order_id'], 0, 100);
            $baseUrl = $this->model_extension_shipping_aramex->getWsdlPath();
            $soapClient = new SoapClient($baseUrl . '/shipping.wsdl');
            $clientInfo = $this->model_extension_shipping_aramex->getClientInfo();

            if ($shipments) {

                foreach ($shipments as $key => $comment) {
                    $cmnt_txt = ($comment['comment']) ? $comment['comment'] : '';
                    if (version_compare(PHP_VERSION, '5.3.0') <= 0) {
                        $awbno = substr($cmnt_txt, 0, strpos($cmnt_txt, "- Order No"));
                    } else {
                        $awbno = strstr($cmnt_txt, "- Order No", true);
                    }
                    $awbno = trim($awbno, "AWB No.");
                    break;
                }


                $report_id = ($this->config->get('shipping_aramex_report_id')) ? $this->config->get('shipping_aramex_report_id') : 0;
                if (!$report_id) {
                    $report_id = 9729;
                }

                $params = array(
                    'ClientInfo' => $clientInfo,
                    'Transaction' => array(
                        'Reference1' => $order_id,
                        'Reference2' => '',
                        'Reference3' => '',
                        'Reference4' => '',
                        'Reference5' => '',
                    ),
                    'LabelInfo' => array(
                        'ReportID' => $report_id,
                        'ReportType' => 'URL',
                    ),
                );
                $params['ShipmentNumber'] = $awbno;

                try {
                    $auth_call = $soapClient->PrintLabel($params);

                    /* bof  PDF demaged Fixes debug */
                    if ($auth_call->HasErrors) {
                        if (count($auth_call->Notifications->Notification) > 1) {
                            foreach ($auth_call->Notifications->Notification as $notify_error) {
                                $data['eRRORS'][] = 'Aramex: ' . $notify_error->Code . ' - ' . $notify_error->Message;
                            }
                        } else {
                            $data['eRRORS'][] = 'Aramex: ' . $auth_call->Notifications->Notification->Code . ' - ' . $auth_call->Notifications->Notification->Message;
                        }
                    }
                    /* eof  PDF demaged Fixes */
                    $filepath = $auth_call->ShipmentLabel->LabelURL;
                    $name = "{$order_id}-shipment-label.pdf";
                    //header('Content-type: application/pdf');
                    //header('Content-Disposition: attachment; filename="' . $name . '"');

                    header( "HTTP/1.1 301 Moved Permanently" );
                    header('Location: ' . $filepath);


                    // readfile($filepath);
                    exit();
                } catch (SoapFault $fault) {
                    $data['eRRORS'][] = 'Error : ' . $fault->faultstring;
                } catch (Exception $e) {

                    $data['eRRORS'][] = $e->getMessage();
                }
            } else {
                $data['eRRORS'][] = 'Shipment is empty or not created yet.';
            }
        } else {
            $data['eRRORS'][] = 'This order no longer exists.';
        }
    }
}