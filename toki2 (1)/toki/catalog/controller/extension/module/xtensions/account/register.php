<?php
class ControllerExtensionModuleXtensionsAccountRegister extends Controller {
	private $error = array();
	public $data = array();

	public function index() {
		if ($this->customer->isLogged()) {
			$this->xtensions_checkout->redirect($this->url->link('account/account', '', true));
		}
		$this->data = $this->load->language('account/register');
		$this->data += $this->load->language($this->config->get('xtensions_language_path'));
		
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('account/customer');
		$this->data += $this->xtensions_checkout->getXtensionsData($this->config->get('config_store_id'), 'xtensions_best_checkout');
		$xconfig = $this->data['xconfig'];	
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate($xconfig)) {
			$this->submit($xconfig);
		}
		
		if(isset($xconfig['options']['show_country_code']) && $xconfig['options']['show_country_code']){
			$this->document->addStyle('catalog/view/javascript/xtensions/countrycode/css/intlTelInput.css');
			$this->document->addScript('catalog/view/javascript/xtensions/countrycode/js/intlTelInput.js');				
		}
		$this->data['isMobile'] = $this->xtensions_checkout->isMobile();
		$this->data['show_country_code'] = false;
		if(isset($xconfig['options']['show_country_code']) && $xconfig['options']['show_country_code']){			
			$this->data['show_country_code'] = true;
			$this->data['only_top_countries'] = isset($xconfig['options']['country_code_only_top']) && $xconfig['options']['country_code_only_top'];
			$this->data['top_countries'] =  '';
			if($xconfig['options']['country_code_top']){
				$top_countries = explode(',',$xconfig['options']['country_code_top']);
				$this->data['top_countries'] = implode('","', $top_countries);
				$this->data['top_countries'] = str_replace(' ', '', '"'.$this->data['top_countries'].'"');
			}
			$this->data['all_countries'] =  '';
			if($xconfig['options']['country_code_all']){
				$all_countries = explode(',',$xconfig['options']['country_code_all']);
				$this->data['all_countries'] = implode('","', $all_countries);
				$this->data['all_countries'] = str_replace(' ', '', '"'.$this->data['all_countries'].'"');
			}			
		}		
		
		foreach ($this->config->get('xtensions_checkout_account_register') as $key) {
			// @formatter:off
			if (isset($this->request->post[$key])) {
				$field_value[$key] = $this->request->post[$key];
			} else {
				$field_value[$key] = '';
			}		
			// @formatter:off
		}		
		if(isset($xconfig['options']['show_country_code']) && $xconfig['options']['show_country_code']){
			if ($this->request->server['REQUEST_METHOD'] == 'POST') {
				$country_code = (!empty($this->request->post['country_code']) && isset($this->request->post['country_code']))?'+'.$this->request->post['country_code']:'';
				$field_value['telephone'] = $country_code?$country_code.$this->request->post['telephone']:$this->request->post['telephone'];
			}
		}
		$this->data['breadcrumbs'] = array();
		
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);
		
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);
		
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_register'),
			'href' => $this->url->link('account/register', '', true)
		);
		
		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['text_account_already'] = sprintf($this->language->get('text_account_already'), $this->url->link('account/login', '', true));
		
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');		
		
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->error['custom_field'])) {
			$this->data['error_custom_field'] = $this->error['custom_field'];
		} else {
			$this->data['error_custom_field'] = array();
		}		
		$this->data['customer_groups'] = array();
		
		if (is_array($this->config->get('config_customer_group_display'))) {
			$this->load->model('account/customer_group');
			
			$customer_groups = $this->model_account_customer_group->getCustomerGroups();
			
			foreach ($customer_groups as $customer_group) {
				if (in_array($customer_group['customer_group_id'], $this->config->get('config_customer_group_display'))) {
					$this->data['customer_groups'][] = $customer_group;
				}
			}
		}
		
		if (isset($this->request->post['customer_group_id'])) {
			$this->data['customer_group_id'] = $this->request->post['customer_group_id'];
		} else {
			$this->data['customer_group_id'] = $this->config->get('config_customer_group_id');
		}
		
		$custom_fields = $this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->getCustomFields();
		
		if (isset($this->request->post['custom_field'])) {
			if (isset($this->request->post['custom_field']['account'])) {
				$account_custom_field = $this->request->post['custom_field']['account'];
			} else {
				$account_custom_field = array();
			}
			
			$this->data['register_custom_field'] = $account_custom_field;
		} else {
			$this->data['register_custom_field'] = array();
		}
		
		$this->data['single_box'] = $single_box = isset($xconfig['options']['single_box']) ? $xconfig['options']['single_box'] : false;				
		
		$field_data = $this->xtensions_checkout->getRefinedFields($xconfig,'xtensions_checkout_account_register', 'register', '', $field_value, $this->error);
		foreach ($custom_fields as $custom_field) {
			if($custom_field['location'] == 'account'){
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
			$field_view = array('key' => $key,'field' => $field,'customer_group_id' => $this->data['customer_group_id'],'customer_groups' => $this->data['customer_groups'],'entry_customer_group' => $this->language->get('entry_customer_group'),'text_select' => $this->language->get('text_select'),'button_upload' => $this->language->get('button_upload'),'text_loading' => $this->language->get('text_loading'),'error_custom_field'=>$this->data['error_custom_field'],'register_custom_field'=>$this->data['register_custom_field']);
			if ($single_box) {
				if ($key != 'newsletter') {					
					$this->data['fields']['all'][] = $this->load->view($this->config->get('xtensions_account_view_path').'xtensions_form', $field_view);
				}
			} else {
				if (in_array($field['section'], array('account','password'))) {
					$this->data['fields'][$field['section']][] = $this->load->view($this->config->get('xtensions_account_view_path').'xtensions_form', $field_view);
				} 
			}
			// @formatter:on
		}
		
		$this->data['action'] = $this->url->link('account/register', '', true);
		
		$this->data['display_newsletter'] = $field_data['newsletter']['show'];
		
		if (isset($this->request->post['newsletter'])) {
			$this->data['newsletter'] = $this->request->post['newsletter'];
		} else {
			$this->data['newsletter'] = ((isset($xconfig['options']['newsletter_default']) && $xconfig['options']['newsletter_default']=='checked')?1:0);
		}
		
		$this->data['captcha'] = '';
		// Captcha
		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
			$this->data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'), $this->error);
		}
		
		if ($this->config->get('config_account_id')) {
			$this->load->model('catalog/information');
			
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
			
			if ($information_info) {
				$this->data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_account_id'), true), $information_info['title'], $information_info['title']);
			} else {
				$this->data['text_agree'] = '';
			}
		} else {
			$this->data['text_agree'] = '';
		}
		
		if (isset($this->request->post['agree'])) {
			$this->data['agree'] = $this->request->post['agree'];
		} else {
			$this->data['agree'] = false;
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
		
		$this->template = $this->config->get('xtensions_account_view_path').'register';
		
		return $this->xtensions_checkout->renderView($this);
	}

	private function submit($xconfig) {
		$customer_id = $this->model_account_customer->addCustomer($this->xtensions_checkout->getRegisterPostData($this->request->post));
		if(isset($xconfig['options']['show_country_code']) && $xconfig['options']['show_country_code']){
			$this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->editTelephone($customer_id,$this->request->post['telephone'],$this->request->post['country_code']);
		}
		$this->customer->login($this->request->post['email'], $this->request->post['password']);
		
		unset($this->session->data['guest']);
		
		// Clear any previous login attempts for unregistered accounts.
		if (method_exists($this->model_account_customer, 'deleteLoginAttempts')) {
			$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
		}
		// Add to activity log
		if ($this->config->get('config_customer_activity')) {
			$this->load->model('account/activity');
			if (isset($xconfig['register']['firstname']['show']) || isset($xconfig['register']['lastname']['show'])) {
				$name = (isset($this->request->post['firstname']) ? $this->request->post['firstname'] . ' ' : '') . '' . (isset($this->request->post['lastname']) ? $this->request->post['lastname'] : '');
			} else {
				$name = $this->request->post['email'];
			}
			$activity_data = array(
				'customer_id' => $customer_id,
				'name' => $name 
			);
			$this->model_account_activity->addActivity('register', $activity_data);
		}
		$this->xtensions_checkout->redirect($this->url->link('account/success'));
	}

	public function validate($xconfig) {
		$field_data = $this->xtensions_checkout->getRefinedFields($xconfig, 'xtensions_checkout_account_register','register');		
		foreach ($field_data as $key => $field) {
			if ($field['show'] && $field['required']) {
				// @formatter:off
				if($field['type'] != 'checkbox'){
					$field_error = $this->xtensions_checkout->validateFields($key,$field,$this->request->post[$key]);
					if($field_error){
						$this->error[$key] = $field_error;
					}
				}
				// @formatter:on
			}
		}
		
		if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$this->error['warning'] = $this->language->get('error_exists');
		}
		if ($this->config->get('config_account_id')) {
			$this->load->model('catalog/information');
			
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
			
			if ($information_info && !isset($this->request->post['agree'])) {
				$this->error['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
			}
		}
		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
			$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');
			
			if ($captcha) {
				$this->error['captcha'] = $captcha;
			}
		}
		
		// Customer Group
		if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->post['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}
		// Custom field validation
		// @formatter:off
		$custom_fields = $this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->getCustomFields($customer_group_id);
		foreach ($custom_fields as $custom_field) {
			if (($custom_field['location'] == 'account')) {
				$custom_field_post = isset($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])?$this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']]:null;				
				$custom_field_error = $this->xtensions_checkout->validateCustomField($custom_field,$custom_field_post);
				if($custom_field_error){
					$this->error['custom_field'][$custom_field['custom_field_id']] = $custom_field_error;
				}
			}
		}		
		// @formatter:on
		return !$this->error;
	}

	public function customfield() {
		$json = array();
		
		// Customer Group
		if (isset($this->request->get['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->get['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->get['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}
		
		$custom_fields = $this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->getCustomFields($customer_group_id);
		
		foreach ($custom_fields as $custom_field) {
			$json[] = array(
				'custom_field_id' => $custom_field['custom_field_id'],
				'required' => $custom_field['required'] 
			);
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
