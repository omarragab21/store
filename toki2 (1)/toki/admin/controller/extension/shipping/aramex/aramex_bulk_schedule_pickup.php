<?php
class ControllerExtensionShippingAramexAramexBulkSchedulePickup extends Controller {
    private $error = array();

    public function index() {
        $this->language->load('sale/order');
        $this->language->load('extension/shipping/aramex');

        $this->document->setTitle($this->language->get('heading_title_bulk_shedule'));

        $this->load->model('sale/order');
        $this->load->model('extension/shipping/aramex');
        $this->load->model('extension/shipping/aramexsettings');

        $this->getList();
    }
    protected function getList() {
        if (isset($this->request->get['filter_order_id'])) {
            $filter_order_id = $this->request->get['filter_order_id'];
        } else {
            $filter_order_id = null;
        }

        if (isset($this->request->get['filter_customer'])) {
            $filter_customer = $this->request->get['filter_customer'];
        } else {
            $filter_customer = null;
        }

        if (isset($this->request->get['filter_order_status'])) {
            $filter_order_status = $this->request->get['filter_order_status'];
        } else {
            $filter_order_status = null;
        }

        if (isset($this->request->get['filter_total'])) {
            $filter_total = $this->request->get['filter_total'];
        } else {
            $filter_total = null;
        }

        if (isset($this->request->get['filter_date_added'])) {
            $filter_date_added = $this->request->get['filter_date_added'];
        } else {
            $filter_date_added = null;
        }

        if (isset($this->request->get['filter_date_modified'])) {
            $filter_date_modified = $this->request->get['filter_date_modified'];
        } else {
            $filter_date_modified = null;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'o.order_id';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }

        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_order_status'])) {
            $url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
        }

        if (isset($this->request->get['filter_total'])) {
            $url .= '&filter_total=' . $this->request->get['filter_total'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['filter_date_modified'])) {
            $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/shipping/aramex/aramex_bulk_schedule_pickup', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['invoice'] = $this->url->link('sale/order/invoice', 'user_token=' . $this->session->data['user_token'], true);
        $data['shipping'] = $this->url->link('sale/order/shipping', 'user_token=' . $this->session->data['user_token'], true);
        $data['add'] = $this->url->link('sale/order/add', 'user_token=' . $this->session->data['user_token'], true);
        $data['create'] = $this->url->link('extension/shipping/aramex/aramex_bulk_schedule_pickup/create', 'user_token=' . $this->session->data['user_token'], true);

        $data['orders'] = array();

        $AllAWB = $this->model_extension_shipping_aramex->getAllAWB();
        $AllPickup = $this->model_extension_shipping_aramex->getAllPickup();



        if(isset($AllAWB) & !empty($AllAWB))
        {

            if(isset($AllPickup) & !empty($AllPickup))
            {
                $tobegenrate = array_diff($AllAWB,$AllPickup);
            }else{
                $tobegenrate = $AllAWB;
            }
        }else{
            $tobegenrate = array();
        }

        $filter_data = array(
            'filter_order_id'      => $filter_order_id,
            'filter_customer'	   => $filter_customer,
            'filter_order_status'  => $filter_order_status,
            'filter_total'         => $filter_total,
            'filter_date_added'    => $filter_date_added,
            'filter_date_modified' => $filter_date_modified,
            'sort'                 => $sort,
            'order'                => $order,
            'tobegenrate'		   => $tobegenrate,
            'start'                => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'                => $this->config->get('config_limit_admin')
        );

        $order_total = $this->model_extension_shipping_aramex->getTotalOrders($filter_data);


        $results = $this->model_sale_order->getOrders($filter_data);

        foreach ($results as $result) {
            if(isset($result['order_status'])){
                $order_status =  $result['order_status'];
            }else{
                $order_status =  $result['status'];
            }

            if(in_array($result['order_id'],$tobegenrate))
            {

                $data['orders'][] = array(
                    'order_id'      => $result['order_id'],
                    'customer'      => $result['customer'],
                    'status'        => $order_status,
                    'total'         => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
                    'date_added'    => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                    'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
                    'shipping_code' => $result['shipping_code'],
                    'view'          => $this->url->link('sale/order/info', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'] . $url, true),
                    //	'edit'          => $this->url->link('sale/order/edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'] . $url, true),
                    //	'delete'        => $this->url->link('sale/order/delete', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'] . $url, true)
                );
            }
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');
        $data['text_missing'] = $this->language->get('text_missing');
        $data['text_create'] = $this->language->get('text_create');

        $data['column_order_id'] = $this->language->get('column_order_id');
        $data['column_customer'] = $this->language->get('column_customer');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_total'] = $this->language->get('column_total');
        $data['column_date_added'] = $this->language->get('column_date_added');
        $data['column_date_modified'] = $this->language->get('column_date_modified');
        $data['column_action'] = $this->language->get('column_action');

        $data['entry_return_id'] = $this->language->get('entry_return_id');
        $data['entry_order_id'] = $this->language->get('entry_order_id');
        $data['entry_customer'] = $this->language->get('entry_customer');
        $data['entry_order_status'] = $this->language->get('entry_order_status');
        $data['entry_total'] = $this->language->get('entry_total');
        $data['entry_date_added'] = $this->language->get('entry_date_added');
        $data['entry_date_modified'] = $this->language->get('entry_date_modified');

        $data['button_invoice_print'] = $this->language->get('button_invoice_print');
        $data['button_shipping_print'] = $this->language->get('button_shipping_print');
        $data['button_add'] = $this->language->get('button_add');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_filter'] = $this->language->get('button_filter');
        $data['button_view'] = $this->language->get('button_view');


        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }

        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_order_status'])) {
            $url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
        }

        if (isset($this->request->get['filter_total'])) {
            $url .= '&filter_total=' . $this->request->get['filter_total'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['filter_date_modified'])) {
            $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_order'] = $this->url->link('extension/shipping/aramex/aramex_bulk_schedule_pickup', 'user_token=' . $this->session->data['user_token'] . '&sort=o.order_id' . $url, true);
        $data['sort_customer'] = $this->url->link('extension/shipping/aramex/aramex_bulk_schedule_pickup', 'user_token=' . $this->session->data['user_token'] . '&sort=customer' . $url, true);
        $data['sort_status'] = $this->url->link('extension/shipping/aramex/aramex_bulk_schedule_pickup', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
        $data['sort_total'] = $this->url->link('extension/shipping/aramex/aramex_bulk_schedule_pickup', 'user_token=' . $this->session->data['user_token'] . '&sort=o.total' . $url, true);
        $data['sort_date_added'] = $this->url->link('extension/shipping/aramex/aramex_bulk_schedule_pickup', 'user_token=' . $this->session->data['user_token'] . '&sort=o.date_added' . $url, true);
        $data['sort_date_modified'] = $this->url->link('extension/shipping/aramex/aramex_bulk_schedule_pickup', 'user_token=' . $this->session->data['user_token'] . '&sort=o.date_modified' . $url, true);

        $url = '';

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }

        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_order_status'])) {
            $url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
        }

        if (isset($this->request->get['filter_total'])) {
            $url .= '&filter_total=' . $this->request->get['filter_total'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['filter_date_modified'])) {
            $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $order_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('extension/shipping/aramex/aramex_bulk_schedule_pickup', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

        $data['filter_order_id'] = $filter_order_id;
        $data['filter_customer'] = $filter_customer;
        $data['filter_order_status'] = $filter_order_status;
        $data['filter_total'] = $filter_total;
        $data['filter_date_added'] = $filter_date_added;
        $data['filter_date_modified'] = $filter_date_modified;

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/shipping/aramex_bulk_schedule_pickup', $data));
    }

    public function create() {

//print_r($_POST);
        $this->language->load('extension/shipping/aramex');
        $this->load->model('sale/order');
        $this->load->model('extension/shipping/aramex');
        $this->load->model('extension/shipping/aramexsettings');
        $this->document->addScript('view/javascript/aramex/jquery.chained.js');

        if (isset($this->request->post['selected'])) {
            $orders = $this->request->post['selected'];
        } elseif (isset($this->request->post['order_id'])) {
            $orders = $this->request->post['order_id'];
        }



        ############ button ##########
        $data['text_back_to_order'] = $this->language->get('text_back_to_order');
        $data['back_to_order'] = $this->url->link('extension/shipping/aramex/aramex_bulk_schedule_pickup', 'user_token=' . $this->session->data['user_token'] , true);
        ############### label #############



        $data['heading_title'] = $this->language->get('heading_title_shedule');


        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title_shedule'),
            'href'      => $this->url->link('extension/shipping/aramex/aramex_bulk_schedule_pickup', 'user_token=' . $this->session->data['user_token'], true),
            'separator' => ' :: '
        );


        ############ button ##########
        $AllAWB = $this->model_extension_shipping_aramex->getAllAWB();
        $AllPickup = $this->model_extension_shipping_aramex->getAllPickup();


        if(isset($orders) && !empty($orders))
        {

            if(isset($AllAWB) & !empty($AllAWB))
            {
                if(isset($AllPickup) & !empty($AllPickup))
                {
                    $tobegenrate = array_diff($AllAWB,$AllPickup);
                }else{
                    $tobegenrate = $AllAWB;
                }
            }else{
                $tobegenrate = array();
            }

            foreach($orders as $id)
            {

                if(in_array($id,$tobegenrate))
                {

                    $order_id = $id;
                    $order_info = $this->model_sale_order->getOrder($order_id);

                    if ($order_info) {

                        $data['firstname'][$order_id] = $order_info['firstname'];
                        $data['lastname'][$order_id] = $order_info['lastname'];
                        $this->document->setTitle($this->language->get('heading_title_shedule'));


                        ##################### config shipper details ################

                        if(isset($this->request->post['contact']) && !empty($this->request->post['contact'])) {
                            $data['contact'] = $this->request->post['contact'];
                        }else{
                            $data['contact'] = ($this->config->get('shipping_aramex_shipper_name'))?$this->config->get('shipping_aramex_shipper_name'):'';
                        }

                        if(isset($this->request->post['company']) && !empty($this->request->post['company'])) {
                            $data['company'] = $this->request->post['company'];
                        }else{
                            $data['company'] = ($this->config->get('shipping_aramex_shipper_company'))?$this->config->get('shipping_aramex_shipper_company'):'';
                        }

                        if(isset($this->request->post['phone']) && !empty($this->request->post['phone'])) {
                            $data['phone'] = $this->request->post['phone'];
                        }else{
                            $data['phone']   = ($this->config->get('shipping_aramex_shipper_phone'))?$this->config->get('shipping_aramex_shipper_phone'):'';
                        }

                        if(isset($this->request->post['address']) && !empty($this->request->post['address'])) {
                            $data['address'] = $this->request->post['address'];
                        }else{
                            $data['address'] = ($this->config->get('shipping_aramex_shipper_address'))?$this->config->get('shipping_aramex_shipper_address'):'';
                        }

                        if(isset($this->request->post['country']) && !empty($this->request->post['country'])) {
                            $data['country'] = $this->request->post['country'];
                        }else{
                            $data['country'] = ($this->config->get('shipping_aramex_shipper_country_code'))?$this->config->get('shipping_aramex_shipper_country_code'):'';
                        }

                        if(isset($this->request->post['city']) && !empty($this->request->post['city'])) {
                            $data['city'] = $this->request->post['city'];
                        }else{
                            $data['city']    = ($this->config->get('shipping_aramex_shipper_city'))?$this->config->get('shipping_aramex_shipper_city'):'';
                        }

                        if(isset($this->request->post['zip']) && !empty($this->request->post['zip'])) {
                            $data['zip'] = $this->request->post['zip'];
                        }else{
                            $data['zip']     = ($this->config->get('shipping_aramex_shipper_postal_code'))?$this->config->get('shipping_aramex_shipper_postal_code'):'';
                        }

                        if(isset($this->request->post['state']) && !empty($this->request->post['state'])) {
                            $data['state'] = $this->request->post['state'];
                        }else{
                            $data['state']   = ($this->config->get('shipping_aramex_shipper_state'))?$this->config->get('shipping_aramex_shipper_state'):'';
                        }

                        if(isset($this->request->post['email']) && !empty($this->request->post['email'])) {
                            $data['email'] = $this->request->post['email'];
                        }else{
                            $data['email']   = ($this->config->get('shipping_aramex_shipper_email'))?$this->config->get('shipping_aramex_shipper_email'):'';
                        }

                        if(isset($this->request->post['date']) && !empty($this->request->post['date'])) {
                            $data['date'] = $this->request->post['date'];
                        }else{
                            // CODE HERE IF HIGHER
                            $data['date']   = date('Y-m-d');
                        }

                        ##################### customer shipment details ################

                        $shipment_receiver_name ='';
                        $shipment_receiver_street ='';


                        $data['destination_country'] = ($order_info['shipping_iso_code_2'])?$order_info['shipping_iso_code_2']:'';
                        $data['destination_city']    = ($order_info['shipping_city'])?$order_info['shipping_city']:'';
                        $data['destination_zipcode']  = ($order_info['shipping_postcode'])?$order_info['shipping_postcode']:'';
                        $data['destination_state']   = ($order_info['shipping_zone'])?$order_info['shipping_zone']:'';


                        ##################  Additional ###########

                        $this->load->model('localisation/country');
                        $data['countries'] = $this->model_localisation_country->getCountries();
                        $data['reference'] = $order_id;

                        $data['aramex_shipment_shipper_account'] = ($this->config->get('shipping_aramex_account_number'))?$this->config->get('shipping_aramex_account_number'):'';


                        $data['aramex_allowed_domestic_methods'] = ($this->config->get('shipping_aramex_allowed_domestic_methods'))?$this->config->get('shipping_aramex_allowed_domestic_methods'):'';
                        $data['aramex_allowed_domestic_additional_services'] = ($this->config->get('shipping_aramex_allowed_domestic_additional_services'))?$this->config->get('shipping_aramex_allowed_domestic_additional_services'):'';
                        $data['aramex_allowed_international_methods'] = ($this->config->get('shipping_aramex_allowed_international_methods'))?$this->config->get('shipping_aramex_allowed_international_methods'):'';
                        $data['aramex_allowed_international_additional_services'] = ($this->config->get('shipping_aramex_allowed_international_additional_services'))?$this->config->get('shipping_aramex_allowed_international_additional_services'):'';


                        $data['all_allowed_domestic_methods'] = $this->model_extension_shipping_aramexsettings->domesticmethods();
                        $data['all_allowed_domestic_additional_services'] = $this->model_extension_shipping_aramexsettings->domesticAdditionalServices();
                        $data['all_allowed_international_methods'] = $this->model_extension_shipping_aramexsettings->internationalmethods();
                        $data['all_allowed_international_additional_services'] = $this->model_extension_shipping_aramexsettings->internationalAdditionalServices();



                        if(isset($this->request->post['product_group']) && !empty($this->request->post['product_group'])) {
                            $data['group'] = $this->request->post['product_group'];
                        }else{
                            $data['group'] = "";
                        }
                        if(isset($this->request->post['product_type']) && !empty($this->request->post['product_type'])) {
                            $data['type'] = $this->request->post['product_type'];
                        }else{
                            $data['type'] = "";
                        }
                        if(isset($this->request->post['payment_type']) && !empty($this->request->post['payment_type'])) {
                            $data['pay_type'] = $this->request->post['payment_type'];
                        }else{
                            $data['pay_type'] = '';
                        }
                        if(isset($this->request->post['comments']) && !empty($this->request->post['comments'])) {
                            $data['comments'] = $this->request->post['comments'];
                        }else{
                            $data['comments'] = '';
                        }
                        if(isset($this->request->post['mobile']) && !empty($this->request->post['mobile'])) {
                            $data['mobile'] = $this->request->post['mobile'];
                        }else{
                            $data['mobile'] = '';
                        }



                        if(isset($this->request->post['weight_unit']) && !empty($this->request->post['weight_unit'])) {
                            $getunit_classid = $this->model_extension_shipping_aramex->getWeightClassId($this->request->post['weight_unit'][$order_id]);
                            $data['weight_unit'][$order_id] = $getunit_classid->row['unit'];
                            $config_weight_class_id = $getunit_classid->row['weight_class_id'];
                        }else{
                            $data['weight_unit'][$order_id] = $this->weight->getUnit($this->config->get('config_weight_class_id'));
                            $config_weight_class_id = $this->config->get('config_weight_class_id');
                        }
                        ##################

                        $data['total'] = ($order_info['total'])?number_format($order_info['total'],2):'';

                        ########### product list ##########
                        if (isset($this->request->post['order_product'])) {
                            $order_products = $this->request->post['order_product'];
                        } elseif (isset($order_id)) {
                            $order_products = $this->model_sale_order->getOrderProducts($order_id);
                        } else {
                            $order_products = array();
                        }
                        $data['order_products'] = array();
                        $weighttot = 0;
                        $i = 0;
                        foreach ($order_products as $order_product) {
                            if (isset($order_product['order_option'])) {
                                $order_option = $order_product['order_option'];
                            } elseif (isset($order_id)) {
                                $order_option = $this->model_sale_order->getOrderOptions($order_id, $order_product['order_product_id']);
                                $product_weight_query = $this->db->query("SELECT weight, weight_class_id FROM " . DB_PREFIX . "product WHERE product_id = '" . $order_product['product_id'] . "'");
                                $weight_class_query = $this->db->query("SELECT wcd.unit FROM " . DB_PREFIX . "weight_class wc LEFT JOIN " . DB_PREFIX . "weight_class_description wcd ON (wc.weight_class_id = wcd.weight_class_id) WHERE wcd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND wc.weight_class_id = '" . $product_weight_query->row['weight_class_id'] . "'");
                            } else {
                                $order_option = array();
                            }

                            $prodweight = $this->weight->convert($product_weight_query->row['weight'], $product_weight_query->row['weight_class_id'], $config_weight_class_id);
                            $prodweight = ($prodweight * $order_product['quantity']);
                            $weighttot = ($weighttot + $prodweight);
                            $i = $i + $order_product['quantity'];
                        }
                        $data['create_order_id'][] = $order_id;
                        $data['no_of_item'][$order_id] = $i;
                        $data['weighttot'][$order_id] = number_format($weighttot,2);
                        $data['total'][$order_id] = number_format($order_info['total'],2);

                    }
                } // if end
            } // if foreach end


            ################## create shipment ###########
            $shedule_order_id = (isset($this->request->post['order_id']))?$this->request->post['order_id']:'';
            if ($this->request->post && $shedule_order_id)
            {


                $account=($this->config->get('shipping_aramex_account_number'))?$this->config->get('shipping_aramex_account_number'):'';
                $country_code=($this->config->get('shipping_aramex_account_country_code'))?$this->config->get('shipping_aramex_account_country_code'):'';
                $response=array();

                $clientInfo = $this->model_extension_shipping_aramex->getClientInfo();


                // CODE HERE IF HIGHER
                $date = ($this->request->post['date'])?$this->request->post['date']:'';


                $pickupDate=strtotime($date);
                $readyTimeH=(isset($this->request->post['ready_hour']))?$this->request->post['ready_hour']:'';
                $readyTimeM=(isset($this->request->post['ready_minute']))?$this->request->post['ready_minute']:'';
                //$readyTime=mktime(($readyTimeH-2),$readyTimeM,0,date("m",$pickupDate),date("d",$pickupDate),date("Y",$pickupDate));
                $readyTime=gmmktime(($readyTimeH),$readyTimeM,0,date("m",$pickupDate),date("d",$pickupDate),date("Y",$pickupDate));

                $closingTimeH=(isset($this->request->post['latest_hour']))?$this->request->post['latest_hour']:'';
                $closingTimeM=(isset($this->request->post['latest_minute']))?$this->request->post['latest_minute']:'';
                //$closingTime=mktime(($closingTimeH-2),$closingTimeM,0,date("m",$pickupDate),date("d",$pickupDate),date("Y",$pickupDate));

                $closingTime=gmmktime(($closingTimeH),$closingTimeM,0,date("m",$pickupDate),date("d",$pickupDate),date("Y",$pickupDate));

                $text_weight = (isset($this->request->post['text_weight']))?$this->request->post['text_weight']:'';
                $weight_unit = (isset($this->request->post['weight_unit']))?$this->request->post['weight_unit']:'';

                $contact =(isset($this->request->post['contact']))?html_entity_decode($this->request->post['contact']):'';
                $company =(isset($this->request->post['company']))?html_entity_decode($this->request->post['company']):'';
                $phone =(isset($this->request->post['phone']))?html_entity_decode($this->request->post['phone']):'';
                $ext =(isset($this->request->post['ext']))?html_entity_decode($this->request->post['ext']):'';
                $mobile =(isset($this->request->post['mobile']))?html_entity_decode($this->request->post['mobile']):'';
                $email =(isset($this->request->post['email']))?html_entity_decode($this->request->post['email']):'';
                $address =(isset($this->request->post['address']))?html_entity_decode($this->request->post['address']):'';$city = (isset($this->request->post['city']))?html_entity_decode($this->request->post['city']):'';
                $state = (isset($this->request->post['state']))?html_entity_decode($this->request->post['state']):'';
                $zip = (isset($this->request->post['zip']))?html_entity_decode($this->request->post['zip']):'';
                $CountryCode = (isset($this->request->post['country']))?html_entity_decode($this->request->post['country']):'';
                $location = (isset($this->request->post['location']))?html_entity_decode($this->request->post['location']):'';
                $comments = (isset($this->request->post['comments']))?html_entity_decode($this->request->post['comments']):'';
                $reference = (isset($this->request->post['reference']))?$this->request->post['reference']:'';
                $vehicle = (isset($this->request->post['vehicle']))?$this->request->post['vehicle']:'';
                $product_group = (isset($this->request->post['product_group']))?$this->request->post['product_group']:'';
                $product_type = (isset($this->request->post['product_type']))?$this->request->post['product_type']:'';$payment_type = (isset($this->request->post['payment_type']))?$this->request->post['payment_type']:'';$total_count = (isset($this->request->post['total_count']))?$this->request->post['total_count']:'';
                $total_count = (isset($this->request->post['total_count']))?$this->request->post['total_count']:'';
                $status = (isset($this->request->post['status']))?$this->request->post['status']:'';
                $no_shipments = (isset($this->request->post['no_shipments']))?$this->request->post['no_shipments']:'';


                foreach($shedule_order_id as $id)
                {
                    $lreference  = $reference[$id];
                    $lstatus 	= $status[$id];
                    $items[] = array(
                        'ProductGroup'	=>$product_group[$id],
                        'ProductType'	=>$product_type[$id],
                        'Payment'		=>$payment_type[$id],
                        'NumberOfShipments'=>$no_shipments[$id],
                        'NumberOfPieces'=>$total_count[$id],
                        'ShipmentWeight'=>array('Value'=>$text_weight[$id],'Unit'=>$weight_unit[$id]),

                    );
                } // foreach end

                try {

                    $params = array(
                        'ClientInfo'  	=> $clientInfo,

                        'Transaction' 	=> array(
                            'Reference1'			=> $lreference ,
                        ),

                        'Pickup'		=>array(
                            'PickupContact'				=>array(
                                'PersonName'			=>$contact,
                                'CompanyName'			=>$company,
                                'PhoneNumber1'			=>$phone,
                                'PhoneNumber1Ext'		=>$ext,
                                'CellPhone'				=>$mobile,
                                'EmailAddress'			=>$email
                            ),
                            'PickupAddress'				=>array(
                                'Line1'					=>$address,
                                'City'					=>$city,
                                'StateOrProvinceCode'	=>$state,
                                'PostCode'				=>$zip,
                                'CountryCode'			=>$CountryCode,
                            ),

                            'PickupLocation'		=>$location,
                            'PickupDate'			=>$readyTime,
                            'ReadyTime'				=>$readyTime,
                            'LastPickupTime'		=>$closingTime,
                            'ClosingTime'			=>$closingTime,
                            'Comments'				=>$comments,
                            'Reference1'			=>$lreference,
                            'Reference2'			=>'',
                            'Vehicle'				=>$vehicle,
                            'Shipments'				=>array(
                                'Shipment'					=>array()
                            ),
                            'PickupItems'			=>array(
                                'PickupItemDetail'=>array(
                                    'ProductGroup'	=>$product_group[$id],
                                    'ProductType'	=>$product_type[$id],
                                    'Payment'		=>$payment_type[$id],
                                    'NumberOfShipments'=>$no_shipments[$id],
                                    'NumberOfPieces'=>$total_count[$id],
                                    'ShipmentWeight'=>array('Value'=>$text_weight[$id],'Unit'=>$weight_unit[$id]),

                                ),
                            ),
                            'Status' =>$lstatus,
                        )
                    );


                    $baseUrl = $this->model_extension_shipping_aramex->getWsdlPath();
                    $soapClient = new SoapClient($baseUrl.'/shipping.wsdl', array('trace' => 1));

                    try{
                        $results = $soapClient->CreatePickup($params);

                        //print_r($results);
                        if($results->HasErrors){
                            if(count($results->Notifications->Notification) > 1){
                                $error="";
                                foreach($results->Notifications->Notification as $notify_error){
                                    $data['eRRORS'][0] = 'Aramex: ' . $notify_error->Code .' - '. $notify_error->Message."<br>";
                                }
                            }else{
                                $data['eRRORS'][0] = 'Aramex: ' . $results->Notifications->Notification->Code . ' - '. $results->Notifications->Notification->Message;
                            }
                            $flag = false;

                        }else{


                            $comment="Pickup reference number ( <strong>".$results->ProcessedPickup->ID."</strong> ).";
                            $message = array(
                                'notify' => 1,
                                'comment' => $comment
                            );
                            $flag = true;
                            foreach($shedule_order_id as $id)
                            {
                                $this->model_extension_shipping_aramex->addOrderHistory($id, $message);
                                if(($key = array_search($id, $data['create_order_id'])) !== false)
                                {
                                    unset($data['create_order_id'][$key]);
                                }
                            }
                            $shipmenthistory = "<p class='amount'>Pickup reference number ( <strong>".$results->ProcessedPickup->ID."</strong> ).</p>";
                            $this->session->data['success_html'][0] = $shipmenthistory;

                        }
                    } catch (Exception $e) {

                        $data['eRRORS'][0] = $e->getMessage();
                        $flag = false;
                    }
                }
                catch (Exception $e) {
                    $data['eRRORS'][0] = $e->getMessage();
                    $flag = false;
                }



            } // post end here



            ################## create shipment end ###########


            if (isset($this->session->data['success_html'])) {
                $data['success_html'] = $this->session->data['success_html'];

                unset($this->session->data['success_html']);
            } else {
                $data['success_html'] = '';
            }

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->load->view('extension/shipping/aramex_create_bulk_schedule_pickup', $data));
        }
        else {


            $this->language->load('error/not_found');

            $this->document->setTitle($this->language->get('heading_title'));

            $data['heading_title'] = $this->language->get('heading_title');

            $data['text_not_found'] = $this->language->get('text_not_found');

            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text'      => $this->language->get('text_home'),
                'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true),
                'separator' => false
            );

            $data['breadcrumbs'][] = array(
                'text'      => $this->language->get('heading_title'),
                'href'      => $this->url->link('error/not_found', 'user_token=' . $this->session->data['user_token'], true),
                'separator' => ' :: '
            );

            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
             $this->response->setOutput($this->load->view('error/not_found', $data));

        }
    }

}
?>