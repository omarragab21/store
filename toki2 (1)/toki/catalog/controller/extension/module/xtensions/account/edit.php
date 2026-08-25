<?php
class ControllerExtensionModuleXtensionsAccountEdit extends Controller {
	private $error = array();
	public $data = array();

	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/edit', '', 'SSL');
			$this->xtensions_checkout->redirect($this->url->link('account/login', '', 'SSL'));			
		}
		
		$this->data = $this->load->language('account/edit');
		$this->data += $this->load->language($this->config->get('xtensions_language_path'));
		
		$this->document->setTitle($this->language->get('heading_title'));
		$this->data += $this->xtensions_checkout->getXtensionsData($this->config->get('config_store_id'), 'xtensions_best_checkout');
		$xconfig = $this->data['xconfig'];
		
		$this->load->model('account/customer');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate($xconfig)) {
			$before_customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
			if(isset($xconfig['options']['show_country_code']) && $xconfig['options']['show_country_code']){
				if(isset($xconfig['options']['country_code_location']) && $xconfig['options']['country_code_location'] == 'customer'){
					$before_customer_info['telephone'] = $before_customer_info['telephone'];	
				}else{
					$country_code = $this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->getCountryCode($this->customer->getId());
					$before_customer_info['telephone'] = $country_code?$country_code.' '.$before_customer_info['telephone']:$before_customer_info['telephone'];
				}					
			}
			$customer_data = $this->request->post;			
			$this->model_account_customer->editCustomer($this->customer->getId(),$customer_data);
			if(isset($xconfig['options']['show_country_code']) && $xconfig['options']['show_country_code']){
				$this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->editTelephone($this->customer->getId(),$this->request->post['telephone'],$this->request->post['country_code']);
			}
			if($this->xtensions_checkout->configExists('xtensions/xsmspro')){
				$this->load->config('xtensions/xsmspro');
				if($this->config->get($this->config->get('xsmspro_config_pfx').'status') && $this->config->get($this->config->get('xsmspro_config_pfx').'edit_confirmation')){
					if(($before_customer_info['telephone'] != ((isset($this->request->post['country_code']) && $this->request->post['country_code'])?'+'.$this->request->post['country_code'].' ':'').$this->request->post['telephone'])){
						$this->load->controller($this->config->get('xsmspro_path').'/confirm',array('type'=>'edit_confirmation','message'=>'edit_confirmation'));
					}
				}
			}
			
			$this->session->data['success'] = $this->language->get('text_success');
			// Add to activity log
			if ($this->config->get('config_customer_activity')) {
				$this->load->model('account/activity');
				
				$activity_data = array(
					'customer_id' => $this->customer->getId(),
					'name' => $this->customer->getFirstName() . ' ' . $this->customer->getLastName() 
				);
				
				$this->model_account_activity->addActivity('edit', $activity_data);
			}		
			if(!isset($this->session->data['xsmspro']['edit']['otpdata']['do_not_redirect'])){
				$this->xtensions_checkout->redirect($this->url->link('account/account', '', 'SSL'));
			}else{
				unset($this->session->data['xsmspro']['edit']['otpdata']['do_not_redirect']);
				return ;
			}
		}
		$this->data['xsmspro'] = '';
		if($this->xtensions_checkout->configExists('xtensions/xsmspro')){
			$this->load->config('xtensions/xsmspro');
			if($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->error['different_email']) && $this->config->get($this->config->get('xsmspro_config_pfx').'status') && $this->config->get($this->config->get('xsmspro_config_pfx').'email_edit')){							
				$this->data['xsmspro'] = $this->load->controller($this->config->get('xsmspro_path').'/editEmail',array('page'=>'edit'));			
			}
			if(!$this->data['xsmspro'] && $this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->error['different_mobile']) && $this->config->get($this->config->get('xsmspro_config_pfx').'status') && $this->config->get($this->config->get('xsmspro_config_pfx').'edit')){							
				$this->data['xsmspro'] = $this->load->controller($this->config->get('xsmspro_path').'/edit',array('page'=>'edit'));			
			}
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
		
		if ($this->request->server['REQUEST_METHOD'] != 'POST') {
			$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
		}
		if(isset($xconfig['options']['show_country_code']) && $xconfig['options']['show_country_code']){
			if ($this->request->server['REQUEST_METHOD'] != 'POST') {
				if(isset($xconfig['options']['country_code_location']) && $xconfig['options']['country_code_location'] == 'customer'){
					$customer_info['telephone'] = $customer_info['telephone'];	
				}else{
					$country_code = $this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->getCountryCode($this->customer->getId());
					$customer_info['telephone'] = $country_code?$country_code.$customer_info['telephone']:$customer_info['telephone'];
				}
			}else{
				$country_code = (!empty($this->request->post['country_code']) && isset($this->request->post['country_code']))?'+'.$this->request->post['country_code']:'';
				$this->request->post['telephone'] = $country_code?$country_code.$this->request->post['telephone']:$this->request->post['telephone'];
			}
		}
		 
		foreach ($this->config->get('xtensions_checkout_account_edit') as $key) {
			if (isset($this->request->post[$key])) {
				$field_value[$key] = $this->request->post[$key];
			} else if(isset($customer_info[$key])){
				$field_value[$key] = $customer_info[$key];
			} else {				
				$field_value[$key] = '';
			}
		}
		if ($this->request->server['REQUEST_METHOD'] != 'POST') {
			$field_value['confirm_email'] = $customer_info['email'];
			$field_value['confirm_telephone'] = $customer_info['telephone'];
		}
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');		
		
		$this->data['breadcrumbs'] = array();
		
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home') 
		);
		
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', 'SSL') 
		);
		
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_edit'),
			'href' => $this->url->link('account/edit', '', 'SSL') 
		);
		
		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['button_continue'] = $this->language->get('button_continue');
		$this->data['button_back'] = $this->language->get('button_back');
		$this->data['button_upload'] = $this->language->get('button_upload');
		
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
		
		$this->data['action'] = $this->url->link('account/edit', '', 'SSL');
		
		// Custom Fields		
		$custom_fields = $this->data['custom_fields'] = $this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->getCustomFields($this->customer->getGroupId());
		
		if (isset($this->request->post['custom_field']['account'])) {
			$this->data['register_custom_field'] = $this->request->post['custom_field']['account'];
		} elseif (isset($customer_info)) {
			$this->data['register_custom_field'] = $this->xtensions_checkout->unserialize($customer_info['custom_field']);
		} else {
			$this->data['register_custom_field'] = array();
		}
		
		$field_data = $this->xtensions_checkout->getRefinedFields($xconfig, 'xtensions_checkout_account_edit','edit', 'account', $field_value, $this->error);
		foreach ($custom_fields as $custom_field) {
			$field_data[$custom_field['identifier']] = $custom_field;
			$field_data[$custom_field['identifier']]['custom_field'] = true;
			$field_data[$custom_field['identifier']]['section'] = $custom_field['location'];
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
			$this->data['fields'][$field['section']][] = $this->load->view($this->config->get('xtensions_account_view_path').'xtensions_form', $field_view);			
			// @formatter:on
		}
		$this->data['back'] = $this->url->link('account/account', '', 'SSL');
		
		$children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header' 
		);
		$this->data += $this->xtensions_checkout->getChildren($children);
		
		$this->template = $this->config->get('xtensions_account_view_path').'edit';
		return $this->xtensions_checkout->renderView($this);
	}

	protected function validate($xconfig) {
		$field_data = $this->xtensions_checkout->getRefinedFields($xconfig, 'xtensions_checkout_account_edit','edit', 'account');
		foreach ($field_data as $key => $field) {
			if ($field['show'] && $field['required']) {
				$field_error = $this->xtensions_checkout->validateFields($key,$field,$this->request->post[$key]);
				if($field_error){
					$this->error[$key] = $field_error;
				}
			}
		}
		if (($this->customer->getEmail() != $this->request->post['email']) && $this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$this->error['warning'] = $this->language->get('error_exists');
		}
		// Customer Group
		$custom_fields = $this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->getCustomFields($this->customer->getGroupId());
		// @formatter:off
		foreach ($custom_fields as $custom_field) {
			if ($custom_field['location'] == 'account') {
				$custom_field_post = isset($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])?$this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']]:null;
				$custom_field_error = $this->xtensions_checkout->validateCustomField($custom_field,$custom_field_post);
				if($custom_field_error){
					$this->error['custom_field'][$custom_field['custom_field_id']] = $custom_field_error;
				}
			}
		}
		if(isset($xconfig['options']['show_country_code']) && $xconfig['options']['show_country_code'] && empty($this->request->post['country_code'])){
			$this->load->language($this->config->get('xtensions_language_path_countrycode'));
			$this->error['telephone'] = $this->language->get('error_countrycode');
		}
		if($this->xtensions_checkout->configExists('xtensions/xsmspro')){
			$this->load->config('xtensions/xsmspro');
			if($this->config->get($this->config->get('xsmspro_config_pfx').'status') && $this->config->get($this->config->get('xsmspro_config_pfx').'restrict_duplicate')){							
				$xsmserror = $this->load->controller($this->config->get('xsmspro_path').'/isEditDuplicate');
				if($xsmserror){
					$this->error['telephone'] = $xsmserror;
				}
			}
			if(!$this->error && $this->config->get($this->config->get('xsmspro_config_pfx').'status') && $this->config->get($this->config->get('xsmspro_config_pfx').'email_edit')){
				if(($this->customer->getEmail() != $this->request->post['email']) && !$this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])){
					if(!isset($this->session->data['xsmspro']['edit_otp_verfied_email'][$this->request->post['email']])){
						$this->error['different_email'] = true;
						$this->session->data['xsmspro']['edit']['otpdata'] = $this->request->post;
					}else{
						$this->session->data['xsmspro']['edit']['otpdata']['do_not_redirect'] = true;
						if(!$this->config->get($this->config->get('xsmspro_config_pfx').'edit')){
							unset($this->session->data['xsmspro']['edit_otp_verfied_email']);
							unset($this->session->data['xsmspro']['editemailsent']);
						}
					}	
				}				
			}
			if(!$this->error && $this->config->get($this->config->get('xsmspro_config_pfx').'status') && $this->config->get($this->config->get('xsmspro_config_pfx').'edit')){
				$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
				if(isset($xconfig['options']['show_country_code']) && $xconfig['options']['show_country_code']){
					if(isset($xconfig['options']['country_code_location']) && $xconfig['options']['country_code_location'] == 'customer'){
						$customer_info['telephone'] = $customer_info['telephone'];	
					}else{
						$country_code = $this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->getCountryCode($this->customer->getId());
						$customer_info['telephone'] = $country_code?$country_code.' '.$customer_info['telephone']:$customer_info['telephone'];
					}					
				}
				$this->load->model($this->config->get('xsmspro_path'));
				if($this->{$this->config->get('xsmspro_model')}->isPemittedCountry((isset($this->request->post['country_code'])?$this->request->post['country_code']:''))){
					if(($customer_info['telephone'] != ((isset($this->request->post['country_code']) && $this->request->post['country_code'])?'+'.$this->request->post['country_code'].' ':'').$this->request->post['telephone'])){
						if(!isset( $this->session->data['xsmspro']['edit_otp_verfied'][$this->request->post['telephone']])){
							$this->error['different_mobile'] = true;
							unset($this->session->data['xsmspro']['edit']['otpdata']['do_not_redirect']);
							$this->session->data['xsmspro']['edit']['otpdata'] = $this->request->post;
						}else{
							unset($this->session->data['xsmspro']['edit_otp_verfied']);
							$this->session->data['xsmspro']['edit']['otpdata']['do_not_redirect'] = true;
							if($this->config->get($this->config->get('xsmspro_config_pfx').'email_edit')){
								unset($this->session->data['xsmspro']['edit_otp_verfied_email']);
								unset($this->session->data['xsmspro']['editemailsent']);
							}
						}
					}
				}
			}
		}
		// @formatter:on
		return !$this->error;
	}
}
