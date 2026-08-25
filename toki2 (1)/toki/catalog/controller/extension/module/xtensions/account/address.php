<?php
class ControllerExtensionModuleXtensionsAccountAddress extends Controller {
	private $error = array();
	public $data = array();

	public function add() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/address', '', 'SSL');
			$this->xtensions_checkout->redirect($this->url->link('account/login', '', 'SSL'));
		}
		
		$this->data = $this->load->language('account/address');
		$this->data += $this->load->language($this->config->get('xtensions_language_path'));
		$this->data += $this->xtensions_checkout->getXtensionsData($this->config->get('config_store_id'), 'xtensions_best_checkout');
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('account/address');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {			
			$this->model_account_address->addAddress($this->customer->getId(),$this->request->post);
			// Add to activity log
			if ($this->config->get('config_customer_activity')) {
				$this->load->model('account/activity');
				
				$activity_data = array(
					'customer_id' => $this->customer->getId(),
					'name' => $this->customer->getFirstName() . ' ' . $this->customer->getLastName() 
				);
				
				$this->model_account_activity->addActivity('address_add', $activity_data);
			}
			$this->session->data['success'] = $this->language->get('text_add');
			$this->xtensions_checkout->redirect($this->url->link('account/address', '', 'SSL'));
		}
		
		return $this->getForm();
	}

	public function edit() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/address', '', 'SSL');
			$this->xtensions_checkout->redirect($this->url->link('account/login', '', 'SSL'));
		}
		$this->data = $this->load->language('account/address');
		$this->data += $this->load->language($this->config->get('xtensions_language_path'));
		$this->data += $this->xtensions_checkout->getXtensionsData($this->config->get('config_store_id'), 'xtensions_best_checkout');
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('account/address');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {			
			$this->model_account_address->editAddress($this->request->get['address_id'], $this->request->post);
			
			// Default Shipping Address
			if (isset($this->session->data['shipping_address']['address_id']) && ($this->request->get['address_id'] == $this->session->data['shipping_address']['address_id'])) {
				$this->session->data['shipping_address'] = $this->xtensions_checkout->getAddress($this, $this->request->get['address_id']);
				
				unset($this->session->data['shipping_method']);
				unset($this->session->data['shipping_methods']);
			}
			
			// Default Payment Address
			if (isset($this->session->data['payment_address']['address_id']) && ($this->request->get['address_id'] == $this->session->data['payment_address']['address_id'])) {
				$this->session->data['payment_address'] = $this->xtensions_checkout->getAddress($this, $this->request->get['address_id']);
				
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
			$this->session->data['success'] = $this->language->get('text_edit');
			
			$this->xtensions_checkout->redirect($this->url->link('account/address', '', 'SSL'));
		}
		$this->document->setTitle($this->language->get('heading_title'));
		
		return $this->getForm();
	}

	protected function getForm() {				
		$this->data += $this->load->language($this->config->get('xtensions_language_path'));
		$xconfig = $this->data['xconfig'];
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');
		$this->data['text_address'] = !isset($this->request->get['address_id']) ? $this->language->get('text_address_add') : $this->language->get('text_address_edit');
		$this->data['breadcrumbs'] = array();
		
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home'),
			'separator' => false 
		);
		
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', 'SSL'),
			'separator' => $this->language->get('text_separator') 
		);
		
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/address', '', 'SSL'),
			'separator' => $this->language->get('text_separator') 
		);
		
		if (!isset($this->request->get['address_id'])) {
			$this->data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_edit_address'),
				'href' => $this->url->link('account/address/add', '', 'SSL'),
				'separator' => $this->language->get('text_separator') 
			);
		} else {
			$this->data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_edit_address'),
				'href' => $this->url->link('account/address/edit', 'address_id=' . $this->request->get['address_id'], 'SSL'),
				'separator' => $this->language->get('text_separator') 
			);
		}
		
		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['text_edit_address'] = $this->language->get('text_edit_address');
		
		$this->data['button_continue'] = $this->language->get('button_continue');
		$this->data['button_back'] = $this->language->get('button_back');
		$this->data['button_upload'] = $this->language->get('button_upload');
		$this->data['back'] = $this->url->link('account/address', '', 'SSL');
		
		if (isset($this->error['custom_field'])) {
			$this->data['error_custom_field'] = $this->error['custom_field'];
		} else {
			$this->data['error_custom_field'] = array();
		}
		
		if (!isset($this->request->get['address_id'])) {
			$this->data['action'] = $this->url->link('account/address/add', '', 'SSL');
		} else {
			$this->data['action'] = $this->url->link('account/address/edit', 'address_id=' . $this->request->get['address_id'], 'SSL');
		}		
		
		if (isset($this->request->get['address_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$address_info = $this->xtensions_checkout->getAddress($this, $this->request->get['address_id']);
		}
		
		foreach ($this->config->get('xtensions_checkout_account_address') as $key) {
			// @formatter:off			
			if (isset($this->request->post[$key])) {
				$field_value[$key] = $this->request->post[$key];
			} else if(isset($address_info[$key])){
				$field_value[$key] = $address_info[$key];
			} else if(isset($xconfig['options'][$key])){
				$field_value[$key] = $xconfig['options'][$key];
			} else if (in_array($key, array('postcode','country_id','zone_id')) && isset($this->session->data['shipping_address'][$key])) {
				$field_value[$key] = $this->session->data['shipping_address'][$key];
			} else {				
				$field_value[$key] = '';
			}			
			// @formatter:on
		}
		$this->data['country_id'] = $field_value['country_id'];
		
		$this->data['zone_id'] = $field_value['zone_id'];
		
		$this->load->model('localisation/country');
		
		$this->data['countries'] = $this->model_localisation_country->getCountries();
		
		// Custom fields
		$custom_fields = $this->data['custom_fields'] = $this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->getCustomFields($this->customer->getGroupId());
		
		if (isset($this->request->post['custom_field']['address'])) {
			$this->data['register_custom_field'] = $this->request->post['custom_field']['address'];
		} elseif (isset($address_info['custom_field'])) {
			$this->data['register_custom_field'] = $address_info['custom_field'];
		} else {
			$this->data['register_custom_field'] = array();
		}
		
		$field_data = $this->xtensions_checkout->getRefinedFields($xconfig, 'xtensions_checkout_account_address','edit', 'address', $field_value, $this->error);
		$this->data['display_country'] = (isset($field_data['country_id']['show']) && $field_data['country_id']['show'])?true:false;
		foreach ($custom_fields as $custom_field) {
			if($custom_field['location']=='address'){
				$field_data[$custom_field['identifier']] = $custom_field;
				$field_data[$custom_field['identifier']]['custom_field'] = true;
				$field_data[$custom_field['identifier']]['section'] = $custom_field['location'];
			}
		}
		$this->data['mask'] = array();
		$sort_order = array();
		foreach ($field_data as $key => $value) {
			if($value['is_mask']){
				if(isset($value['custom_field'])){
					$this->data['mask']['input-custom-field'.$value['custom_field_id']] = $value['mask'];
				}else{
					$this->data['mask']['input-'.$key] = $value['mask'];
				}
			}
			$sort_order[$key] = $value['sort_order'];
		}
		array_multisort($sort_order, SORT_ASC, $field_data);
		$this->data['field_data'] = $field_data;
		$this->data['fields'] = array();
		foreach ($this->data['field_data'] as $key => $field) {
			// @formatter:off
			$field_view = array('key' => $key,'field' => $field,'text_select' => $this->language->get('text_select'),'button_upload' => $this->language->get('button_upload'),'text_loading' => $this->language->get('text_loading'),'error_custom_field'=>$this->data['error_custom_field'],'register_custom_field'=>$this->data['register_custom_field']);			
			$this->data['fields']['address'][] = $this->load->view($this->config->get('xtensions_account_view_path').'xtensions_form', $field_view);			
			// @formatter:on
		}
		
		if (isset($this->request->post['default'])) {
			$this->data['default'] = $this->request->post['default'];
		} elseif (isset($this->request->get['address_id'])) {
			$this->data['default'] = $this->customer->getAddressId() == $this->request->get['address_id'];
		} else {
			$this->data['default'] = false;
		}	
		
		$children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header' 
		);
		$this->data += $this->xtensions_checkout->getChildren($children);
		
		$this->template = $this->config->get('xtensions_account_view_path').'address_form';
		return $this->xtensions_checkout->renderView($this);
	}

	protected function validateForm() {
		$field_data = $this->xtensions_checkout->getRefinedFields($this->data['xconfig'], 'xtensions_checkout_account_address','edit','address');		
		foreach ($field_data as $key => $field) {
			if ($field['show'] && $field['required']) {
				$field_error = $this->xtensions_checkout->validateFields($key,$field,$this->request->post[$key]);
				if($field_error){
					$this->error[$key] = $field_error;
				}
			}
		}	
		
		// Custom field validation
		// @formatter:off
		$custom_fields = $this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->getCustomFields($this->customer->getGroupId());		
		foreach ($custom_fields as $custom_field) {
			if ($custom_field['location'] == 'address') {
				$custom_field_post = isset($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])?$this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']]:null;
				$custom_field_error = $this->xtensions_checkout->validateCustomField($custom_field,$custom_field_post);
				if($custom_field_error){
					$this->error['custom_field'][$custom_field['custom_field_id']] = $custom_field_error;
				}
			}
		}	
		//Validate EU VAT Number Country
		if(isset($this->session->data['validate_country'])){
			$this->load->model('localisation/country');
			$country_data = $this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->getCountryByIsoCode2($this->session->data['validate_country']);
			if(!$country_data || ($country_data['country_id'] != $this->request->post['country_id'])){
				$this->error['country_id'] = $this->language->get('error_eu_vat_country_mismatch');
			}
			unset($this->session->data['validate_country']);
		}
		// @formatter:on
		return !$this->error;
	}	
	
	public function addresses(){
		$data['addresses'] = array();

		$results = $this->xtensions_checkout->getAddresses($this);
		foreach ($results as $result) {
			$data['addresses'][] = array(
				'address_id' => $result['address_id'],
				'address'    => $result['formatted_address'],
				'update'     => $this->url->link('account/address/edit', 'address_id=' . $result['address_id'], true),
				'delete'     => $this->url->link('account/address/delete', 'address_id=' . $result['address_id'], true)
			);
		}
		return $data['addresses'];
	}
}
