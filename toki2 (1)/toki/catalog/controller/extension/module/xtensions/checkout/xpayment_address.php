<?php
class ControllerExtensionModuleXtensionsCheckoutXPaymentAddress extends Controller {
	public $data = array();
	
	public function __construct($registry){
		parent::__construct($registry);
		$this->data = $this->load->language('checkout/checkout');
		$this->data = array_merge($this->data,$this->load->language($this->config->get('xtensions_language_path')));
		$this->data += $this->xtensions_checkout->getXtensionsData($this->config->get('config_store_id'), 'xtensions_best_checkout');
		$this->customer_group = $this->customer->getGroupId();
	}

	public function index() {
		$this->xtensions_checkout->addEventActivity(array('entered_step_2'));
		$this->response->setOutput($this->matter());
	}

	public function matter() {
		if(!$this->customer->isLogged()){
			return '<script type="text/javascript">location = "' . $this->url->link('checkout/checkout','',true) . '"; </script>';
		}		
		$xconfig = $this->data['xconfig'];
		$this->data['misc_options'] = $misc_options = $xconfig['options'];
		$this->data['same_shipping'] = isset($misc_options['same_shipping']) && $misc_options['same_shipping']?true:false;	
		$this->data['isMobile'] = $this->xtensions_checkout->isMobile();
		
		$account_custom_fields = $this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->getAccountCustomFields($this->customer->getId());
		$this->session->data['customer']['custom_field'] = $this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->getCustomFieldByIdentifier($account_custom_fields, 'account', $this->customer_group, '');	
				
		$this->data['shipping_required'] = $hasShipping = $this->cart->hasShipping();	
		
		foreach ($this->config->get('xtensions_checkout_checkout_address') as $key) {
			// @formatter:off
			if (!in_array($key, array('firstname','lastname','country_id','zone_id'))) {
				$field_value[$key] = '';
			}else if(in_array($key, array('country_id','zone_id'))){
				if(isset($xconfig['options'][$key]) && $xconfig['options'][$key]) {
					$field_value[$key] =  $xconfig['options'][$key];
				} else if (isset($this->session->data['payment_address'][$key])) {
					$field_value[$key] = $this->session->data['payment_address'][$key];
				}  else {
					$field_value[$key] = $this->config->get('config_'.$key);
				}
			}
			// @formatter:on
		}
		
		$this->data['zone_id'] = $field_value['zone_id'];
		$field_value['firstname'] = $this->customer->getFirstName();
		$field_value['lastname'] = $this->customer->getLastName();
		$field_value['value_custom_field'] = array();	
		
		$custom_fields = $this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->getCustomFields($this->customer_group);
		$field_data = $this->xtensions_checkout->getRefinedFields($xconfig, 'xtensions_checkout_checkout_address', 'checkout', 'address', $field_value, array());
		foreach ($custom_fields as $custom_field) {
			if ($custom_field['location'] == 'address') {
				$field_data[$custom_field['identifier']] = $custom_field;
				$field_data[$custom_field['identifier']]['custom_field'] = true;
				$field_data[$custom_field['identifier']]['show'] = true;
				$field_data[$custom_field['identifier']]['section'] = $custom_field['location'];
			}
		}
		$this->xtensions_checkout->generateForm($this,$field_data,$field_value);		
		$this->data['text_save'] = $this->language->get('text_address_new');
		
		$this->data['button_continue'] = $this->language->get('button_continue');
		$this->data['button_upload'] = $this->language->get('button_upload');
		$this->data['text_loading'] = $this->language->get('text_loading');
		$this->data['addresses'] = array();
		
		$results = $this->xtensions_checkout->getAddresses($this);
		$sort_order = array();
		foreach ($results as $key => $value) {
			$sort_order[$key] = $value['address_id'];
		}
		array_multisort($sort_order, SORT_ASC, $results);
		
		$edit_address = '<a class="editAddress" inline="' . ($misc_options['address_form']=='inline'?'yes':'no') . '" link="%s" alt="%s">%s</a>';
		$remove_address = '<a class="removeAddress" link="%s" alt="%s">%s</a>';		
		$this->load->model('account/customer');		
		$customer = $this->model_account_customer->getCustomer($this->customer->getId());
		
		foreach ($results as $result) {
			$edit = sprintf($edit_address, $this->url->link($this->config->get('xtensions_controller_path').'xpayment_address/editAddress', 'address_id=' . $result['address_id'], 'SSL'), $this->language->get('text_edit_address'), $this->language->get('text_edit_address'));
			$delete = sprintf($remove_address, $this->url->link($this->config->get('xtensions_controller_path').'xpayment_address/deleteAddress', 'address_id=' . $result['address_id'], 'SSL'), $this->language->get('button_delete'), $this->language->get('button_delete'));
			$default = $customer['address_id'] == $result['address_id'];
			$this->data['addresses'][$result['address_id']] = array(
				'address_id' => $result['address_id'],
				'address' => $result['formatted_address'],
				'linear_address' => $result['linear_address'],
				'edit' => $edit,
				'default' => $default,
				'delete' => $delete 
			);
		}
		
		if (isset($this->session->data['payment_address']['address_id']) && isset($this->data['addresses'][$this->session->data['payment_address']['address_id']])) {
			$this->data['address_id'] = $this->session->data['payment_address']['address_id'];
		} else if ($customer['address_id'] && isset($this->data['addresses'][$customer['address_id']])) {
			$this->data['address_id'] = $customer['address_id'];
		} else if ($results) {
			foreach ($results as $result) {
				$this->data['address_id'] = $result['address_id'];
				break;
			}
		} else {
			$this->data['address_id'] = 0;
		}
		
		if ($this->data['address_id']) {
			$this->session->data['payment_address'] = $this->xtensions_checkout->getAddress($this, $this->data['address_id']);
			$this->tax->unsetRates();
			$this->tax->setPaymentAddress($this->session->data['payment_address']['country_id'], $this->session->data['payment_address']['zone_id']);
			$this->tax->setStoreAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
		}
		
		if ($hasShipping) {
			if (isset($this->session->data['shipping_address']['address_id']) && isset($this->data['addresses'][$this->session->data['shipping_address']['address_id']])) {
				$this->data['shipping_address_id'] = $this->session->data['shipping_address']['address_id'];
			} else if ($customer['address_id'] && isset($this->data['addresses'][$customer['address_id']])) {
				$this->data['shipping_address_id'] = $customer['address_id'];
			} else if ($results) {
				foreach ($results as $result) {
					$this->data['shipping_address_id'] = $result['address_id'];
					break;
				}
			} else {
				$this->data['shipping_address_id'] = 0;
			}
			if ($this->data['shipping_address_id']) {
				$this->session->data['shipping_address'] = $this->xtensions_checkout->getAddress($this, $this->data['shipping_address_id']);
				$this->tax->setShippingAddress($this->session->data['shipping_address']['country_id'], $this->session->data['shipping_address']['zone_id']);
			}
		}
		
		if (isset($this->session->data['sameAddress']) && $this->session->data['sameAddress']) {
			$this->data['same_address'] = true;
		} else if (isset($this->session->data['sameAddress']) && !$this->session->data['sameAddress']) {
			$this->data['same_address'] = false;
		} else {
			$this->data['same_address'] = (isset($misc_options['default_same_address'])?false:true);
		}
		
		$this->data['text_agree'] = '';
		$this->data['info_title'] = '';
		$this->data['agree_content'] = '';
		$this->data['agree_href'] = '';
		if ($this->config->get('config_checkout_id')) {
			$this->load->model('catalog/information');			
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));			
			if ($information_info) {
				$agree_text = '<a class="agree" href="%s" alt="%s"><b>%s</b></a>';
				$this->data['agree_href'] = $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_checkout_id'), 'SSL');
				$this->data['text_agree_alternate'] = sprintf($agree_text, $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_checkout_id'), 'SSL'), $information_info['title'], $information_info['title']);				
				$agree_text = '<a class="agree pointer" href="%s" alt="%s">%s</a>';
				$this->data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_checkout_id'), 'SSL'), $information_info['title'], $information_info['title']);
				$this->data['info_title'] = $information_info['title'];
				$this->data['agree_content'] = sprintf($this->language->get('agree_content'), $information_info['title']);
			}
		}
		
		$this->data['comment'] =  isset($this->session->data['comment'])?$this->session->data['comment']:'';
		$this->data['agree']   =  isset($this->session->data['agree'])?$this->session->data['agree']:'';		
		
		if ($hasShipping && $this->data['shipping_address_id'] && !isset($this->data['addresses'][$this->data['shipping_address_id']])) {
			$this->data['shipping_address_id'] = '';
		}
		// @formatter:off
		$children = array($this->config->get('xtensions_controller_path').'xcvc/xoptions');
		if ($hasShipping && $this->data['shipping_address_id'] && isset($this->data['addresses'][$this->data['shipping_address_id']])) {
			array_push($children, array('key'=>$this->config->get('xtensions_controller_path').'xshipping_method/validate','data'=>array('direct'=>true)));
			array_push($children, $this->config->get('xtensions_controller_path').'xshipping_method');
		} else {
			$this->data['xshipping_method'] = '';
		}
		// @formatter:on	
		
		$this->data['shipping_error'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));		
		array_push($children, array('key'=>$this->config->get('xtensions_controller_path').'xcart','data'=>array('section'=>'address')));	
		array_push($children, $this->config->get('xtensions_controller_path').'xcvc/xtotals');		
			
		$this->data += $this->xtensions_checkout->getChildren($children);
		$this->data['force_redirect'] = false;
		if($this->data['xcart'] == 'redirect'){
			$this->data['force_redirect'] = $this->url->link('checkout/cart');
		}
		
		$this->data['display_country'] = (isset($field_data['country_id']['show']) && $field_data['country_id']['show']) ? true : false;
		$this->data['is_edit'] = false;
		$this->data['address_modal'] = $misc_options['address_form']=='modal'?true:false;
		$this->data['display_comments'] = isset($misc_options['display_comments']);
		if($misc_options['address_form'] == 'modal'){
			$this->data['modal_form'] = $this->load->view($this->config->get('xtensions_view_path').'address_form', $this->data);
			$this->data['inline_form'] = '';
		}else{
			$this->data['modal_form'] = '';
			$this->data['inline_form'] = $this->load->view($this->config->get('xtensions_view_path').'address_form', $this->data);
		}	
		$this->data += $this->cartValid();
		$this->data['address_block'] = $misc_options['address_type']=='block'?true:false;
		if ($hasShipping && $this->data['shipping_address_id'] && isset($this->data['addresses'][$this->data['shipping_address_id']])) {
			if(!$this->data['xshipping_method']){
				$this->xtensions_checkout->addEventActivity(array('no_shipping_method'));
			}else{
				$this->xtensions_checkout->addEventActivity(array('shipping_method_changed'));
			}
		}
		$this->xtensions_checkout->addActivity(array('payment_address','shipping_address','shipping_method'));		
		$this->template = $this->config->get('xtensions_view_path').'xpayment_address';
		return $this->xtensions_checkout->renderView($this);
	}
	
	public function validate() {		
		$json = array();
		$json1 = array();
		$json['error'] = array();
		$json1['error'] = array();
		$existing = true;
		$existing1 = true;
		$shipping_same = false;
		$hasShipping = $this->cart->hasShipping();
		if (isset($this->request->post['comment'])) {
			$this->session->data['comment'] = strip_tags($this->request->post['comment']);
			if(utf8_strlen($this->request->post['comment'])>0){
				$this->xtensions_checkout->addEventActivity(array('order_comment_added'));
			}
		}
		if (isset($this->request->post['xshipping_address_check']) && !empty($this->request->post['xshipping_address_check'])) {
			$shipping_same = true;
			$this->session->data['sameAddress'] = true;
		} else {
			$this->session->data['sameAddress'] = false;
		}
		
		// Validate if customer is logged in.
		if (!$this->customer->isLogged()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		}
		
		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');
		}
		
		// Validate minimum quantity requirments.
		$products = $this->cart->getProducts();
		
		foreach ($products as $product) {
			$product_total = 0;
			
			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}
			
			if ($product['minimum'] > $product_total) {
				$json['redirect'] = $this->url->link('checkout/cart');
				
				break;
			}
		}
		$json1 = $json;
		if (!$json['error']) {
			if (empty($this->request->post['address_id'])) {
				$json['error']['warning'] = $this->language->get('error_address');
			} elseif (!in_array($this->request->post['address_id'], array_keys($this->xtensions_checkout->getAddresses($this)))) {
				$json['error']['warning'] = $this->language->get('error_address');
			}
			
			if (!$json['error']) {
				$this->session->data['payment_address'] = $this->xtensions_checkout->getAddress($this, $this->request->post['address_id']);
				$this->session->data['payment']['custom_field'] = $this->session->data['payment_address']['identified_data'];
				$this->tax->unsetRates();
				$this->tax->setPaymentAddress($this->session->data['payment_address']['country_id'], $this->session->data['payment_address']['zone_id']);
				$this->tax->setStoreAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
				if (isset($this->request->post['agree'])) {
					$this->session->data['agree'] = true;
				}
				$existing = false;
				if ($shipping_same && $hasShipping) {
					$this->session->data['shipping_address'] = $this->session->data['payment_address'];
					$this->session->data['shipping']['custom_field'] = $this->session->data['payment']['custom_field'];
					$this->tax->setShippingAddress($this->session->data['shipping_address']['country_id'], $this->session->data['shipping_address']['zone_id']);
				}
				unset($this->session->data['payment_method']);
				unset($this->session->data['payment_methods']);
			}
			// Xtensions Shipping Address
			if ($hasShipping && !$shipping_same) {
				if (empty($this->request->post['saddress_id'])) {
					$json1['error']['swarning'] = $this->language->get('error_address');
				} elseif (!in_array($this->request->post['saddress_id'], array_keys($this->xtensions_checkout->getAddresses($this)))) {
					$json1['error']['swarning'] = $this->language->get('error_address');
				}
				if (!$json1['error']) {
					$this->session->data['shipping_address'] = $this->xtensions_checkout->getAddress($this, $this->request->post['saddress_id']);
					$this->session->data['shipping']['custom_field'] = $this->session->data['shipping_address']['identified_data'];
					$this->tax->setShippingAddress($this->session->data['shipping_address']['country_id'], $this->session->data['shipping_address']['zone_id']);
				}
				$json['error'] = array_merge($json['error'], $json1['error']);
			}
		}
		if (!$json['error'] && isset($this->request->get['showpayment'])) {
			$this->session->data['agree'] = true;
			$json = $this->xtensions_checkout->getChildren(array($this->config->get('xtensions_controller_path').'xpayment_method'));
		} else if(!isset($this->request->get['showpayment']) && !$this->cart->hasShipping()){
	  		$json += $this->xtensions_checkout->getChildren(array($this->config->get('xtensions_controller_path').'xcvc/xtotals'));
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function validateForm($data = array()) {
		$json = array();		
		$xconfig = $this->xtensions_checkout->getXtensionsData($this->config->get('config_store_id'), 'xtensions_best_checkout');
		$field_data = $this->xtensions_checkout->getRefinedFields($xconfig['xconfig'], 'xtensions_checkout_checkout_address', 'checkout', 'address');
		
		foreach ($field_data as $key => $field) {
			if ($field['show'] && $field['required']) {
				// @formatter:off
				if(in_array($field['section'], array('address')) || $field['location'] == 'both'){
					$field_error = $this->xtensions_checkout->validateFields($key,$field,$this->request->post[$key]);
					if($field_error){
						$json['error'][$key] = $field_error;
					}
				}
				// @formatter:on
			}
		}
		// Custom field validation
		$customer_group =  isset($data['customer_group'])?$data['customer_group']:$this->customer_group;
		$custom_fields = $this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->getCustomFields($customer_group);
		foreach ($custom_fields as $custom_field) {
			if ($custom_field['location'] == 'address') {
				$custom_field_post = isset($this->request->post['custom_field'][$custom_field['custom_field_id']])?$this->request->post['custom_field'][$custom_field['custom_field_id']]:null;				
				$custom_field_error = $this->xtensions_checkout->validateCustomField($custom_field,$custom_field_post);
				if($custom_field_error){
					$json['error']['custom_field' . $custom_field['custom_field_id']] = $custom_field_error;
				}
			}
		}
		//Validate EU VAT Number Country
		if(isset($this->session->data['validate_country'])){
			$this->load->model('localisation/country');
			$country_data = $this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->getCountryByIsoCode2($this->session->data['validate_country']);
			if(!$country_data || ($country_data['country_id'] != $this->request->post['country_id'])){
				$json['error']['country_id'] = $this->language->get('error_eu_vat_country_mismatch');
			}
			unset($this->session->data['validate_country']);
		}	
		return $json;
	}

	public function addAddress() {		
		$hasShipping = $this->cart->hasShipping();
		$json = array();
		if (!$this->customer->isLogged()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');			
		}
		if(!$json){
			$json = $this->validateForm();
		}
		
		if (!$json) {
			$this->load->model('account/address');			
			$address_data = $this->request->post;
			$address_data['custom_field'] = array();	
			if (isset($this->request->post['custom_field'])) {
				$address_data['custom_field']['address'] = $this->request->post['custom_field'];
			}
			if (!isset($address_data['default'])) {
				array_push($address_data, array('default' => 0));
			}
			$address_id = $this->model_account_address->addAddress($this->customer->getId(),$address_data);
			$this->session->data['payment_address'] = $this->xtensions_checkout->getAddress($this, $address_id);
			if ($this->config->get('config_customer_activity')) {
				$this->load->model('account/activity');
				$activity_data = array(
					'customer_id' => $this->customer->getId(),
					'name' => $this->customer->getFirstName() . ' ' . $this->customer->getLastName() 
				);
				$this->model_account_activity->addActivity('address_add', $activity_data);
			}
			if ($hasShipping) {
				$this->session->data['shipping_address'] = $this->session->data['payment_address'];
			}
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
		}
		if (!$json) {
			$json['xpayment_address'] = $this->matter();
			$this->xtensions_checkout->addEventActivity(array('add_address_successful'=>array('address_id'=>$address_id)));
			//$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		}else{
			$this->xtensions_checkout->addEventActivity(array('add_address_unsuccessful'=>$json));
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}	

	public function editAddress() {
		$xconfig = $this->data['xconfig'];
		$this->data['misc_options'] = $misc_options = $xconfig['options'];
		$json = array();
		$this->data['addresses'] = $this->xtensions_checkout->getAddresses($this);
		$this->data['is_edit'] = true;
		if (!$this->customer->isLogged()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		}
		if (!$json && !($this->request->server['REQUEST_METHOD'] == 'POST')) {		
			
			$this->data['shipping_required'] = $this->cart->hasShipping();		
			
			$this->data['text_save'] = $this->language->get('text_edit_address');
			
			$this->data['button_continue'] = $this->language->get('button_continue');
			$this->data['button_upload'] = $this->language->get('button_upload');
			$this->data['text_loading'] = $this->language->get('text_loading');
			
			$address_info = $this->xtensions_checkout->getAddress($this, $this->request->get['address_id']);
			
			if (!empty($address_info)) {
				$this->data['address'] = $address_info;
				$this->data['addaddress'] = $address_info;
				$this->data['address_custom_field'] = $address_info['custom_field'];
				$this->load->model('account/customer');
				// fix
				$customer = $this->model_account_customer->getCustomer($this->customer->getId());
				$this->data['default'] = $customer['address_id'] == $address_info['address_id'];
				$this->load->model('localisation/country');

				// Custom fields
				foreach ($this->config->get('xtensions_checkout_checkout_address') as $key) {
					$field_value[$key] = isset($address_info[$key])?$address_info[$key]:'';
				}
				$field_value['value_custom_field'] = $address_info['custom_field'];
				$this->data['country_id'] = $address_info['country_id'];
				$this->data['zone_id'] = $address_info['zone_id'];
				$this->load->model('localisation/country');				
				
				$custom_fields = $this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->getCustomFields($this->customer_group);
				$field_data = $this->xtensions_checkout->getRefinedFields($xconfig, 'xtensions_checkout_checkout_address', 'checkout', 'address', $field_value, array());
				foreach ($custom_fields as $custom_field) {
					if ($custom_field['location'] == 'address') {
						$field_data[$custom_field['identifier']] = $custom_field;
						$field_data[$custom_field['identifier']]['custom_field'] = true;
						$field_data[$custom_field['identifier']]['show'] = true;
						$field_data[$custom_field['identifier']]['section'] = $custom_field['location'];
					}
				}
				$this->xtensions_checkout->generateForm($this,$field_data,$field_value);				
				$this->data['display_country'] = (isset($field_data['country_id']['show']) && $field_data['country_id']['show']) ? true : false;
			} else {
				$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
			}			
			if (!$json) {
				$this->data['address_modal'] = $misc_options['address_form']=='modal'?true:false;
				$this->template = $this->config->get('xtensions_view_path').'address_form';
				$json['editAddress'] = $this->xtensions_checkout->renderView($this);
			}
		} else if(!$json){			
			$json = $this->validateForm();
			if (!$json) {
				$this->load->model('account/address');
				$this->request->post['address_id'] = $this->request->post['edit_address_id'];
				$address_data = $this->request->post;
				$address_data['custom_field'] = array();	
				if (isset($this->request->post['custom_field'])) {
					$address_data['custom_field']['address'] = $this->request->post['custom_field'];
				}				
				$this->model_account_address->editAddress($this->request->post['address_id'], $address_data);
				$hasShipping = $this->cart->hasShipping();
				// Default Shipping Address
				if ($hasShipping && isset($this->session->data['shipping_address']['address_id']) && ($this->request->post['address_id'] == $this->session->data['shipping_address']['address_id'])) {
					$this->session->data['shipping_address'] = $this->xtensions_checkout->getAddress($this, $this->request->post['address_id']);
					unset($this->session->data['shipping_method']);
					unset($this->session->data['shipping_methods']);
				}
				// Default Payment Address
				if (isset($this->session->data['payment_address']['address_id']) && ($this->request->post['address_id'] == $this->session->data['payment_address']['address_id'])) {
					$this->session->data['payment_address'] = $this->xtensions_checkout->getAddress($this, $this->request->post['address_id']);
					unset($this->session->data['payment_method']);
					unset($this->session->data['payment_methods']);
				}
				// Add to activity log
				if ($this->config->get('config_customer_activity')) {
					$this->load->model('account/activity');
					$activity_data = array(
						'customer_id' => $this->customer->getId(),
						'name' => $this->customer->getFirstName() . ' ' . $this->customer->getLastName() 
					);
					$this->model_account_activity->addActivity('address_edit', $activity_data);
				}
				if (!$json) {
					$json['xpayment_address'] = $this->matter();
					$this->xtensions_checkout->addEventActivity(array('edit_address_successful'=>array('address_id'=>$this->request->post['address_id'])));
					//$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');					
				}else{
					$this->xtensions_checkout->addEventActivity(array('edit_address_unsuccessful'=>$json));
				}
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function deleteAddress() {
		$json = array();
		if (!$this->customer->isLogged()) {
			$json['redirect'] = $this->url->link('account/login', '', 'SSL');
		}
		if (isset($this->request->get['address_id'])) {
			$this->load->model('account/address');
			$this->model_account_address->deleteAddress($this->request->get['address_id']);
			$this->xtensions_checkout->addEventActivity(array('address_deleted'=>array('address_id'=>$this->request->get['address_id'])));
			// Default Shipping Address
			if (isset($this->session->data['shipping_address']['address_id']) && ($this->request->get['address_id'] == $this->session->data['shipping_address']['address_id'])) {
				unset($this->session->data['shipping_address']);
				unset($this->session->data['shipping_method']);
				unset($this->session->data['shipping_methods']);
			}
			// Default Payment Address
			if (isset($this->session->data['payment_address']['address_id']) && ($this->request->get['address_id'] == $this->session->data['payment_address']['address_id'])) {
				unset($this->session->data['payment_address']);
				unset($this->session->data['payment_method']);
				unset($this->session->data['payment_methods']);
			}
			// Add to activity log
			if ($this->config->get('config_customer_activity')) {
				$this->load->model('account/activity');
				$activity_data = array(
					'customer_id' => $this->customer->getId(),
					'name' => $this->customer->getFirstName() . ' ' . $this->customer->getLastName() 
				);
				$this->model_account_activity->addActivity('address_delete', $activity_data);
			}
		} else {
			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		}
		//$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		$json['xpayment_address'] = $this->matter();
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function cartValid(){
		$error = array();
		$this->load->language('checkout/cart');
		if (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout'))) {
			$error['error_stock_warning'] = $this->language->get('error_stock');
		}
		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$error['error_minimum_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
			}
		}
		if(isset($error['error_stock_warning'])){
			$this->xtensions_checkout->addEventActivity(array('error_stock_warning'));
		}else{
			$this->xtensions_checkout->addEventActivity(array('no_error_stock_warning'));
		}
		if(isset($error['error_minimum_warning'])){
			$this->xtensions_checkout->addEventActivity(array('error_minimum_warning'));
		}else{
			$this->xtensions_checkout->addEventActivity(array('no_error_minimum_warning'));
		}
		return $error;
	}
}
?>
