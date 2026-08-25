<?php
class ControllerExtensionModuleXtensionsCheckoutXGuest extends Controller {
	public $data=array();	
	
	public function __construct($registry){
		parent::__construct($registry);
		$this->data = $this->load->language('checkout/checkout');
		$this->data = array_merge($this->data,$this->load->language($this->config->get('xtensions_language_path')));
		$this->data += $this->xtensions_checkout->getXtensionsData($this->config->get('store_id'), 'xtensions_best_checkout');
		if (isset($this->session->data['guest']['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->session->data['guest']['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$this->customer_group_id = $this->session->data['guest']['customer_group_id'];
		} else {
			$this->customer_group_id = $this->config->get('config_customer_group_id');
		}		
	}
	
	public function index() {
		$this->response->setOutput($this->matter());
	}

	public function matter($data=array()) {
		if(isset($data['direct'])){
			$this->xtensions_checkout->addActivity(array('name','email'));
			$this->xtensions_checkout->addEventActivity(array('entered_guest_step_2'));
		}
		$xconfig = $this->data['xconfig'];
		$this->data['misc_options'] = $misc_options = $xconfig['options'];
		$this->data['same_shipping'] = isset($misc_options['same_shipping']) && $misc_options['same_shipping']?true:false;		
		$this->data['isMobile'] = $this->xtensions_checkout->isMobile();
		
		$this->session->data['customer']['custom_field'] = $this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->getCustomFieldByIdentifier($this->session->data['guest']['custom_field'],'account',$this->customer_group_id);		

		$this->data['shipping_required'] = $hasShipping = $this->cart->hasShipping();
		
		if(isset($this->session->data['payment_address'])){
			$payment_address = $this->session->data['payment_address'];
			$this->tax->unsetRates();
			$this->tax->setPaymentAddress($this->session->data['payment_address']['country_id'], $this->session->data['payment_address']['zone_id']);
			$this->tax->setStoreAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
		}else{
			$payment_address = array();
		}		
		$this->data['fields']['payment'] = $this->generateForm('payment', $payment_address,$xconfig);
		$this->data['shipping_address1'] =  '';
		if($hasShipping){		
			if($this->data['same_shipping'] || (isset($this->session->data['shipping_same_guest']) && $this->session->data['shipping_same_guest'])){
				$this->data['shipping_same'] = true;
			} else if(isset($this->session->data['shipping_same_guest']) && !$this->session->data['shipping_same_guest']){
				$this->data['shipping_same'] = false;
			} else{
				$this->data['shipping_same'] =  (isset($misc_options['default_same_address'])?false:true);
			}
			if($this->data['shipping_same']){
				$shipping_address = $payment_address;
			} else if(isset($this->session->data['shipping_address'])){
				$shipping_address = $this->session->data['shipping_address'];
			}else{
				$shipping_address = array();
			}		
			if($shipping_address){
				$this->tax->setShippingAddress($shipping_address['country_id'], $shipping_address['zone_id']);
			}
			$this->data['shipping_address1'] ='';
			if(isset($shipping_address['formatted_address'])){
				$this->data['shipping_address1'] = $shipping_address['formatted_address'];
			}
			if (isset($shipping_address['custom_field'])) {
				$this->data['guest_shipping_custom_field'] = $shipping_address['custom_field'];
			} else {
				$this->data['guest_shipping_custom_field'] = array();
			}
			if($this->data['shipping_same']){
				if (isset($this->session->data['payment_address'])) {
					$this->session->data['shipping_address'] = $this->session->data['payment_address'];
				}
			}
			if (isset($this->session->data['guest']['shipping_address'])) {
				$this->data['shipping_address'] = $this->session->data['guest']['shipping_address'];
			} else {
				$this->data['shipping_address'] = true;
			}			
			$this->data['fields']['shipping'] = $this->generateForm('shipping', $shipping_address,$xconfig);
		}
		$this->data['text_save'] = $this->language->get('text_address_new');
			
		$this->data['button_continue'] = $this->language->get('button_continue');
		$this->data['button_upload'] = $this->language->get('button_upload');
		$this->data['text_loading'] = $this->language->get('text_loading');
		
		$this->data['payment_address'] ='';
		
		if(isset($payment_address['formatted_address'])){
			$this->data['payment_address'] = $payment_address['formatted_address'];
		}		

		if (isset($payment_address['custom_field'])) {
			$this->data['guest_custom_field'] = $payment_address['custom_field'];
		} else {
			$this->data['guest_custom_field'] = array();
		}		

		$this->data['text_agree'] = '';
		$this->data['info_title'] = '';
		$this->data['agree_content'] = '';
		if ($this->config->get('config_checkout_id')) {
			$this->load->model('catalog/information');			
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));			
			if ($information_info) {
				$agree_text = '<a class="agree" href="%s" alt="%s"><b>%s</b></a>';
				$this->data['text_agree'] = sprintf($agree_text, $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_checkout_id'), 'SSL'), $information_info['title'], $information_info['title']);
				$this->data['agree_href'] = $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_checkout_id'), 'SSL');
				$this->data['text_agree_alternate'] = sprintf($agree_text, $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_checkout_id'), 'SSL'), $information_info['title'], $information_info['title']);
				$agree_text = '<a class="agree pointer" href="%s" alt="%s">%s</a>';
				$this->data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_checkout_id'), 'SSL'), $information_info['title'], $information_info['title']);
				$this->data['info_title'] = $information_info['title'];
				$this->data['agree_content'] = sprintf($this->language->get('agree_content'), $information_info['title']);
			}
		}
		$this->data['display_comments'] = isset($misc_options['display_comments']);
		$this->data['comment'] =  isset($this->session->data['comment'])?$this->session->data['comment']:'';
		$this->data['agree']   =  isset($this->session->data['agree'])?$this->session->data['agree']:'';

		$children = array($this->config->get('xtensions_controller_path').'xcvc/xoptions');
		if($hasShipping && $shipping_address){
			array_push($children, array('key'=>$this->config->get('xtensions_controller_path').'xshipping_method/validate','data'=>array('direct'=>true)));
			array_push($children, $this->config->get('xtensions_controller_path').'xshipping_method');
		}else{
			$this->data['xshipping_method']  = '';
		}
		
		$this->data['shipping_error'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
		array_push($children,$this->config->get('xtensions_controller_path').'xcvc/xtotals');
		if(count($payment_address)){
			if(!$hasShipping || count($shipping_address)){
				$this->data['payment_next'] = true;
			}else{
				$this->data['payment_next'] = '';
			}
		}else{
			$this->data['payment_next'] = '';
		}
		
		array_push($children, array('key'=>$this->config->get('xtensions_controller_path').'xcart','data'=>array('section'=>'address')));
		$this->data += $this->xtensions_checkout->getChildren($children);
		$this->data['force_redirect'] = false;
		if($this->data['xcart'] == 'redirect'){
			$this->data['force_redirect'] = $this->url->link('checkout/cart');
		}

		$this->data['display_country'] = (isset($this->data['field_data']['country_id']['show']) && $this->data['field_data']['country_id']['show']) ? true : false;
		$this->data['is_edit'] = false;
		$this->data['addresses'] = array();
		$this->data['address_modal'] = $misc_options['address_form']=='modal'?true:false;
		$this->data['shipping_modal_form'] = '';
		$this->data['shipping_inline_form'] = '';
		if($misc_options['address_form'] == 'modal'){
			$this->data['section'] = 'payment';
			$this->data['addresses'] = $payment_address;
			$this->data['payment_modal_form'] = $this->load->view($this->config->get('xtensions_view_path').'guest_address_form', $this->data);
			$this->data['payment_inline_form'] = '';
			if($hasShipping && !$this->data['same_shipping']){
				$this->data['addresses'] = $shipping_address;
				$this->data['section'] = 'shipping';
				$this->data['shipping_modal_form'] = $this->load->view($this->config->get('xtensions_view_path').'guest_address_form', $this->data);
			 	$this->data['shipping_inline_form'] = '';
			}
		}else{
			$this->data['payment_modal_form'] = '';
			$this->data['addresses'] = $payment_address;
			$this->data['section'] = 'payment';
			$this->data['payment_inline_form'] = $this->load->view($this->config->get('xtensions_view_path').'guest_address_form', $this->data);
			if($hasShipping && !$this->data['same_shipping']){
				$this->data['addresses'] = $payment_address?$payment_address:$shipping_address;
				$this->data['section'] = 'shipping';
				$this->data['shipping_inline_form'] = $this->load->view($this->config->get('xtensions_view_path').'guest_address_form', $this->data);
				$this->data['shipping_modal_form'] = '';
			}
		}	
		$this->data += $this->load->controller($this->config->get('xtensions_controller_path').'xpayment_address/cartValid');
		$this->data['address_block'] = $misc_options['address_type']=='block'?true:false;
		if($hasShipping && $shipping_address){
			if(!$this->data['xshipping_method']){
				$this->xtensions_checkout->addEventActivity(array('no_shipping_method'));
			}else{
				$this->xtensions_checkout->addEventActivity(array('shipping_method_changed'));
			}
		}
		$this->template = $this->config->get('xtensions_view_path').'xguest';
		return $this->xtensions_checkout->renderView($this);
	}

	private function generateForm($section,$section_address,$xconfig){
		foreach ($this->config->get('xtensions_checkout_checkout_address') as $key) {
			// @formatter:off
			if(isset($section_address[$key])){
				$field_value[$section][$key] = $section_address[$key];
			}else if (!in_array($key, array('firstname','lastname','country_id','zone_id'))) {
				$field_value[$section][$key] = '';
			}else if(in_array($key, array('country_id','zone_id'))){
				if(isset($xconfig['options'][$key]) && $xconfig['options'][$key]) {
					$field_value[$section][$key] =  $xconfig['options'][$key];
				} else if (isset($section_address[$key])) {
					$field_value[$section][$key] = $section_address[$key];
				}  else {
					$field_value[$section][$key] = $this->config->get('config_'.$key);
				}
			}			
			// @formatter:on
		}
			
		$this->data['zone_id'][$section] = $field_value[$section]['zone_id'];
		$field_value[$section]['firstname'] = (isset($section_address['firstname'])?$section_address['firstname']:(isset($this->session->data['guest']['firstname'])?$this->session->data['guest']['firstname']:''));
		$field_value[$section]['lastname'] = (isset($section_address['lastname'])?$section_address['lastname']:(isset($this->session->data['guest']['lastname'])?$this->session->data['guest']['lastname']:''));
		$field_value[$section]['value_custom_field'] = isset($section_address['custom_field'])?$section_address['custom_field']:array();
		
		$custom_fields = $this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->getCustomFields($this->customer_group_id);
		$field_data = $this->xtensions_checkout->getRefinedFields($xconfig, 'xtensions_checkout_checkout_address', 'checkout', 'address', $field_value[$section], array());
		foreach ($custom_fields as $custom_field) {
			if ($custom_field['location'] == 'address') {
				$field_data[$custom_field['identifier']] = $custom_field;
				$field_data[$custom_field['identifier']]['custom_field'] = true;
				$field_data[$custom_field['identifier']]['show'] = true;
				$field_data[$custom_field['identifier']]['section'] = $custom_field['location'];
			}
		}
		$this->data['field_data'] = $field_data;
		return  $this->xtensions_checkout->generateForm($this,$field_data,$field_value,'guest_address',$section);;
	}
	
	
	
	public function addAddress(){		
		$json = array();
		$shipping_same = false;
		$hasShipping = $this->cart->hasShipping();		
		
		// Validate if customer is logged in.
		if ($this->customer->isLogged()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		}

		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');
		}

		// Check if guest checkout is avaliable.
		if ($this->config->get('config_customer_price') || $this->cart->hasDownload()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		}
		if (!$json) {
			$customer_group = isset($this->session->data['guest']['customer_group_id'])?$this->session->data['guest']['customer_group_id']:$this->config->get('config_customer_group_id');
			$json = $this->load->controller($this->config->get('xtensions_controller_path').'xpayment_address/validateForm',array('customer_group'=>$customer_group));
		}
			
		if (!$json) {
			$section = isset($this->request->post['section'])?$this->request->post['section']:'payment';
			$address['firstname'] = $this->request->post['firstname'];
			$address['lastname'] = $this->request->post['lastname'];
			$address['company'] = $this->request->post['company'];
			$address['address_1'] = $this->request->post['address_1'];
			$address['address_2'] = $this->request->post['address_2'];
			$address['postcode'] = $this->request->post['postcode'];
			$address['city'] = $this->request->post['city'];
			$address['country_id'] = $this->request->post['country_id'];
			$address['zone_id'] = $this->request->post['zone_id'];

			$this->load->model('localisation/country');

			$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

			if ($country_info) {
				$address['country'] = $country_info['name'];
				$address['iso_code_2'] = $country_info['iso_code_2'];
				$address['iso_code_3'] = $country_info['iso_code_3'];
				$address['address_format'] = $country_info['address_format'];
			} else {
				$address['country'] = '';
				$address['iso_code_2'] = '';
				$address['iso_code_3'] = '';
				$address['address_format'] = '';
			}
			if (isset($this->request->post['custom_field'])) {
				$address['custom_field'] = $this->request->post['custom_field'];
			} else {
				$address['custom_field'] = array();
			}
			$this->load->model('localisation/zone');

			$zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);

			if ($zone_info) {
				$address['zone'] = $zone_info['name'];
				$address['zone_code'] = $zone_info['code'];
			} else {
				$address['zone'] = '';
				$address['zone_code'] = '';
			}

			$result = $address;
			
			$result['identified_data'] = $this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->getCustomFieldByIdentifier($address['custom_field'],'address');
			$address['formatted_address'] = $this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->getFormattedAddress($result);
			$this->session->data[$section.'_address'] = $address;
			$this->session->data[$section]['custom_field'] = $result['identified_data'];		

			// Default Payment Address
			$this->session->data[$section.'_country_id'] = $this->request->post['country_id'];
			$this->session->data[$section.'_zone_id'] = $this->request->post['zone_id'];
			$this->session->data[$section.'_postcode'] = $this->request->post['postcode'];

			if ($section=='payment' && $shipping_same && $hasShipping) {
				$shipping_address = $address;		
				$this->session->data['shipping_address'] = $shipping_address;
				$this->session->data['shipping']['custom_field'] = $this->session->data['payment']['custom_field'];
				// Default Shipping Address
				$this->session->data['shipping_country_id'] = $this->request->post['country_id'];
				$this->session->data['shipping_zone_id'] = $this->request->post['zone_id'];
				$this->session->data['shipping_postcode'] = $this->request->post['postcode'];
			}
		}
		if(!$json){
			$child = $this->xtensions_checkout->getChildren(array($this->config->get('xtensions_controller_path').'xguest/matter'));
			$json['xguest'] = $child['matter'];
			$this->xtensions_checkout->addEventActivity(array('add_guest_'.$section.'_address_successful'));
		}else{
			$section = isset($this->request->post['section'])?$this->request->post['section']:'payment';
			$this->xtensions_checkout->addEventActivity(array('add_guest_'.$section.'_address_unsuccessful'=>$json));
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function sameAddress(){
		$json = array();
		$shipping_same = true;
		$this->session->data['shipping_same_guest'] = true;
		if(isset($this->request->post['comment'])){
			$this->session->data['comment'] = strip_tags($this->request->post['comment']);
			if(utf8_strlen($this->request->post['comment'])>0){
				$this->xtensions_checkout->addEventActivity(array('order_comment_added'));
			}
		}
		
		if(isset($this->request->post['agree'])){
			$this->session->data['agree'] = true;
		}
		$hasShipping = $this->cart->hasShipping();
		
		// Validate if customer is logged in.
		if ($this->customer->isLogged()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		}

		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');
		}

		// Check if guest checkout is avaliable.
		if ($this->config->get('config_customer_price') || $this->cart->hasDownload()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		}
			
		if (!$json) {
			if (!empty($this->request->post['shipping_address'])) {
				$this->session->data['guest']['shipping_address'] = true;
			} else {
				$this->session->data['guest']['shipping_address'] = false;
			}
			if (isset($this->session->data['payment_address']) && $shipping_same && $hasShipping) {
				$this->session->data['shipping_address'] = $this->session->data['payment_address'];				
				$this->session->data['shipping_address']['formatted_address']=$this->session->data['payment_address']['formatted_address'];
				$this->session->data['shipping']['custom_field'] = $this->session->data['payment']['custom_field'];
			}
		}
		if(!$json){
			$child = $this->xtensions_checkout->getChildren(array($this->config->get('xtensions_controller_path').'xguest/matter'));
			$json['xguest'] = $child['matter'];
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}
	
	public function notSameAddress(){
		$json = array();
		if ($this->customer->isLogged()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		}

		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');
		}

		// Check if guest checkout is avaliable.
		if ($this->config->get('config_customer_price') || $this->cart->hasDownload()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		}
		if(!$json){
			$this->session->data['shipping_same_guest'] = false;
			$child = $this->xtensions_checkout->getChildren(array($this->config->get('xtensions_controller_path').'xguest/matter'));
			$json['xguest'] = $child['matter'];
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
?>
