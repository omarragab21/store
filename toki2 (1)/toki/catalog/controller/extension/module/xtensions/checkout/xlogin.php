<?php
class ControllerExtensionModuleXtensionsCheckoutXLogin extends Controller {
	public $data = array();

	public function index() {
		$this->data = $this->load->language('checkout/checkout');
		$this->data = array_merge($this->data,$this->load->language($this->config->get('xtensions_language_path')));
		$this->data += $this->xtensions_checkout->getXtensionsData($this->config->get('config_store_id'), 'xtensions_best_checkout');
		$xconfig = $this->data['xconfig'];
		$this->data['misc_options'] = $misc_options = $xconfig['options'];
		$this->data['clean_login'] = $misc_options['login_type']=='clean'?true:false;
		$this->data['account_sort'] = isset($xconfig['sort_order']['account'])?$xconfig['sort_order']['account']:array('login'=>1,'register'=>2,'guest'=>3);
		
		$this->data['text_select'] = $this->language->get('text_select');
		$this->data['text_none'] = $this->language->get('text_none');
		
		$this->data['button_continue'] = $this->language->get('button_continue');
		$this->data['button_upload'] = $this->language->get('button_upload');
		$this->data['text_loading'] = $this->language->get('text_loading');
		$this->data['button_login'] = $this->language->get('button_login');
		$this->data['checkout_guest'] = ($this->config->get('config_checkout_guest') && !$this->config->get('config_customer_price') && !$this->cart->hasDownload());
		$this->data['only_guest_checkout'] = false;
		if (isset($xconfig['options']['only_guest_checkout']) && $xconfig['options']['only_guest_checkout']) {
			$this->data['account'] = 'guest';
			$this->data['only_guest_checkout'] = true;
		}else if (isset($xconfig['options']['step_default_use_session']) && isset($this->session->data['account'])) {
			$this->data['account'] = $this->session->data['account'];
		} else {
			$this->data['account'] = isset($xconfig['options']['step_default']) ? $xconfig['options']['step_default'] : 'login';
		}
		foreach ($this->config->get('xtensions_checkout_checkout_register_account') as $key) {
			if (isset($this->session->data['guest'][$key])) {
				$field_value[$key] = $this->session->data['guest'][$key];
			} else {
				$field_value[$key] = '';
			}
		}	
		if(isset($xconfig['options']['show_country_code']) && $xconfig['options']['show_country_code'] && isset($this->session->data['guest']['country_code'])){
			$field_value['telephone'] = $this->session->data['guest']['country_code'].$field_value['telephone'];
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
		
		$this->data['customer_group_id'] = $this->config->get('config_customer_group_id');
		if ($this->config->get('config_account_id')) {
			$this->load->model('catalog/information');
			
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
			
			if ($information_info) {
				$this->data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_account_id'), 'SSL'), $information_info['title'], $information_info['title']);
			} else {
				$this->data['text_agree'] = '';
			}
		} else {
			$this->data['text_agree'] = '';
		}
		
		$custom_fields = $this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->getCustomFields();
		
		if (isset($this->session->data['guest']['custom_field'])) {
			$field_value['value_custom_field'] = $this->session->data['guest']['custom_field'];
		} else {
			$field_value['value_custom_field'] = array();
		}
		$this->data['captcha'] = '';		
		$this->data['register_captcha'] = in_array('register', (array)$this->config->get('config_captcha_page'))?true:false;
		$this->data['guest_captcha'] = in_array('guest', (array)$this->config->get('config_captcha_page'))?true:false;
		// Captcha
		if ($this->config->get('captcha_'.$this->config->get('config_captcha') . '_status') && ($this->data['register_captcha'] || $this->data['guest_captcha'])) {
			$this->data['captcha'] = $this->load->controller($this->config->get('xtensions_controller_path').'x' . $this->config->get('config_captcha').'_captcha');
		}
		$field_data = $this->xtensions_checkout->getRefinedFields($xconfig, 'xtensions_checkout_checkout_register_account', 'checkout', 'account', $field_value, array());
		foreach ($custom_fields as $custom_field) {
			if ($custom_field['location'] == 'account') {
				$field_data[$custom_field['identifier']] = $custom_field;
				$field_data[$custom_field['identifier']]['custom_field'] = true;
				$field_data[$custom_field['identifier']]['show'] = true;
				$field_data[$custom_field['identifier']]['section'] = $custom_field['location'];
			}
		}
		$this->data['isMobile'] = $this->xtensions_checkout->isMobile();
		$this->data['show_country_code'] = false;
		if(isset($misc_options['show_country_code']) && $misc_options['show_country_code']){			
			$this->data['show_country_code'] = true;
			$this->data['only_top_countries'] = isset($misc_options['country_code_only_top']) && $misc_options['country_code_only_top'];
			$this->data['top_countries'] =  '';
			if($misc_options['country_code_top']){
				$top_countries = explode(',',$misc_options['country_code_top']);
				$this->data['top_countries'] = implode('","', $top_countries);
				$this->data['top_countries'] = str_replace(' ', '', '"'.$this->data['top_countries'].'"');
			}
			$this->data['all_countries'] =  '';
			if($misc_options['country_code_all']){
				$all_countries = explode(',',$misc_options['country_code_all']);
				$this->data['all_countries'] = implode('","', $all_countries);
				$this->data['all_countries'] = str_replace(' ', '', '"'.$this->data['all_countries'].'"');
			}			
		}
		$this->xtensions_checkout->generateForm($this,$field_data,$field_value,'login');
		$this->data['entry_newsletter'] = sprintf($this->language->get('entry_newsletter_checkout'), $this->config->get('config_name'));
		$this->data['display_newsletter'] = $field_data['newsletter']['show'];
		
		$this->data['forgotten'] = $this->url->link('account/forgotten', '', 'SSL');
		
		// @formatter:off			
		$children = array($this->config->get('xtensions_controller_path').'xcvc/xoptions');
		array_push($children, array('key'=>$this->config->get('xtensions_controller_path').'xcart','data'=>array('section'=>'login')));
		array_push($children,$this->config->get('xtensions_controller_path').'xcvc/xtotals');
		array_push($children,$this->config->get('xtensions_controller_path').'xsocial');
		// @formatter:on
		$this->data += $this->xtensions_checkout->getChildren($children);
		$this->data['xtensions_controller_path'] = $this->config->get('xtensions_controller_path');
		$this->xtensions_checkout->addEventActivity(array('entered_step_1'));
		if(isset($this->session->data['newsletter'])){
			$this->data['newsletter'] = $this->session->data['newsletter'];
		}else{
			$this->data['newsletter'] = ((isset($xconfig['options']['newsletter_default']) && $xconfig['options']['newsletter_default']=='checked')?1:0);
		}
		
		$this->data['smslogin'] = '';
		if($this->xtensions_checkout->configExists('xtensions/xsmspro')){
			$this->load->config('xtensions/xsmspro');
			if($this->config->get($this->config->get('xsmspro_config_pfx').'status') && $this->config->get($this->config->get('xsmspro_config_pfx').'login')){
				$this->data['smslogin'] = $this->load->controller($this->config->get('xsmspro_path').'/login',array('page'=>'checkout'));			
			}
		}
		$this->template = $this->config->get('xtensions_view_path').'xlogin';
		$this->response->setOutput($this->xtensions_checkout->renderView($this));
	}

	public function validate() {
		$this->language->load('checkout/checkout');
		$this->load->language($this->config->get('xtensions_language_path'));
		$json = array();
		if ($this->customer->isLogged()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		}
		
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');
		}
		
		if (!$json) {
			$this->load->model('account/customer');
			$this->session->data['account'] = $this->request->post['account'];
			if ($this->request->post['account'] == 'login') {
				// Check how many login attempts have been made.
				$login_info = $this->model_account_customer->getLoginAttempts($this->request->post['email']);
				
				if ($login_info && ($login_info['total'] >= $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
					$json['error']['warning'] = $this->language->get('error_attempts');
				}
				$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);
				
				if ($customer_info && !$customer_info['status']) {
					$json['error']['warning'] = $this->language->get('error_approved');
				}
				if (!isset($json['error'])) {
					if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {
						$json['error']['warning'] = $this->language->get('error_login');
						$this->model_account_customer->addLoginAttempt($this->request->post['email']);
					} else {
						$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
					}
				}
				if($this->xtensions_checkout->configExists('xtensions/xsmspro')){
					$this->load->config('xtensions/xsmspro');
					if($this->config->get($this->config->get('xsmspro_config_pfx').'status') && $this->config->get($this->config->get('xsmspro_config_pfx').'login_password')){
						$this->load->model($this->config->get('xsmspro_path'));					
						if($this->{$this->config->get('xsmspro_model')}->login($this->request->post['email'], $this->request->post['password'])){
							$json = array();
						}
					}
				}
				if (!$json) {
					// Trigger customer pre login event
					$this->event->trigger('pre.customer.login');
					unset($this->session->data['guest']);
					unset($this->session->data['payment_address']);
					unset($this->session->data['shipping_address']);
					unset($this->session->data['agree']);
					// Default Addresses
					$this->load->model('account/address');
					
					$address_info = $this->xtensions_checkout->getAddress($this, $this->customer->getAddressId());
					if ($address_info) {
						if ($this->config->get('config_tax_customer') == 'shipping') {
							$this->session->data['shipping_country_id'] = $address_info['country_id'];
							$this->session->data['shipping_zone_id'] = $address_info['zone_id'];
							$this->session->data['shipping_postcode'] = $address_info['postcode'];
							$this->session->data['shipping_address'] = $this->xtensions_checkout->getAddress($this, $this->customer->getAddressId());
						}
						
						if ($this->config->get('config_tax_customer') == 'payment') {
							$this->session->data['payment_country_id'] = $address_info['country_id'];
							$this->session->data['payment_zone_id'] = $address_info['zone_id'];
							$this->session->data['payment_address'] = $this->xtensions_checkout->getAddress($this, $this->customer->getAddressId());
						}
					} else {
						unset($this->session->data['shipping_country_id']);
						unset($this->session->data['shipping_zone_id']);
						unset($this->session->data['shipping_postcode']);
						unset($this->session->data['payment_country_id']);
						unset($this->session->data['payment_zone_id']);
						unset($this->session->data['payment_address']);
						unset($this->session->data['shipping_address']);
					}
					// Add to activity log
					if ($this->config->get('config_customer_activity')) {
						$this->load->model('account/activity');
						
						$activity_data = array(
							'customer_id' => $this->customer->getId(),
							'name' => $this->customer->getFirstName() . ' ' . $this->customer->getLastName() 
						);
						
						$this->model_account_activity->addActivity('login', $activity_data);
					}
					// Wishlist
					if (isset($this->session->data['wishlist']) && is_array($this->session->data['wishlist'])) {
						$this->load->model('account/wishlist');
						
						foreach ($this->session->data['wishlist'] as $key => $product_id) {
							$this->model_account_wishlist->addWishlist($product_id);
							
							unset($this->session->data['wishlist'][$key]);
						}
					}
					// Trigger customer post login event
					$this->event->trigger('post.customer.login');
					$this->xtensions_checkout->addEventActivity(array('login_attempt_successful'));
					$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
				}else{
					$this->xtensions_checkout->addEventActivity(array('login_attempt_unsuccessful'=>$json));
				}
			} else {
				// Customer Group
				$xconfig = $this->xtensions_checkout->getXtensionsData($this->config->get('config_store_id'), 'xtensions_best_checkout');
				$field_data = $this->xtensions_checkout->getRefinedFields($xconfig['xconfig'], 'xtensions_checkout_checkout_register_account', 'checkout', 'account');
				foreach ($field_data as $key => $field) {
					if ($field['show'] && $field['required']) {
						// @formatter:off							
						if (in_array($field['type'], array('text','date','textarea','datetime','time','email','tel','select','radio'))){
							$field_error = $this->xtensions_checkout->validateFields($key,$field,$this->request->post[$key]);
							if($field_error){
								$json['error'][$key] = $field_error;
							}
						} 
						
						if($this->request->post['account'] == 'register' && in_array($field['type'], array('password'))){
							$field_error = $this->xtensions_checkout->validateFields($key,$field,$this->request->post[$key]);
							if($field_error){
								$json['error'][$key] = $field_error;
							}
						}
						// @formatter:on
					}
				}				
				$this->load->model('account/customer_group');
				
				if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
					$customer_group_id = $this->request->post['customer_group_id'];
				} else {
					$customer_group_id = $this->config->get('config_customer_group_id');
				}
				// Custom field validation
				
				
				$custom_fields = $this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->getCustomFields($customer_group_id);				
				foreach ($custom_fields as $custom_field) {				
					if ($custom_field['location'] == 'account') {
						$custom_field_post = isset($this->request->post['custom_field'][$custom_field['custom_field_id']])?$this->request->post['custom_field'][$custom_field['custom_field_id']]:null;
						$custom_field_error = $this->xtensions_checkout->validateCustomField($custom_field,$custom_field_post);
						if($custom_field_error){
							$json['error']['custom_field' . $custom_field['custom_field_id']] = $custom_field_error;
						}
					}
				}			
				
				// Captcha
				if ($this->config->get('captcha_'.$this->config->get('config_captcha') . '_status')) {
					$register_captcha = in_array('register', (array)$this->config->get('config_captcha_page'))?true:false;
					$guest_captcha = in_array('guest', (array)$this->config->get('config_captcha_page'))?true:false;					
					
					if ($this->request->post['account'] == 'register' && $register_captcha) {
						$captcha = $this->load->controller($this->config->get('xtensions_controller_path').'x' . $this->config->get('config_captcha') . '_captcha/validate');
						if($captcha){
							$json['error']['captcha'] = $captcha;
						}
					}
					
					if ($this->request->post['account'] == 'guest' && $guest_captcha) {
						$captcha = $this->load->controller($this->config->get('xtensions_controller_path').'x' . $this->config->get('config_captcha') . '_captcha/validate');
						if($captcha){
							$json['error']['captcha'] = $captcha;
						}
					}
				}
				
				if(isset($xconfig['xconfig']['options']['show_country_code']) && $xconfig['xconfig']['options']['show_country_code'] && isset($this->request->post['country_code'])){
					$this->session->data['guest']['country_code'] = '+'.$this->request->post['country_code'];
					$this->session->data['guest']['full_telephone'] = $this->request->post['country_code'].$this->request->post['telephone'];
				}
				foreach ($this->config->get('xtensions_checkout_checkout_register_account') as $key) {
					$this->session->data['guest'][$key] = (isset($this->request->post[$key]) ? $this->request->post[$key] : '');	
				}
				$this->session->data['guest']['customer_group_id'] = $customer_group_id;
				if (isset($this->request->post['custom_field'])) {
					$this->session->data['guest']['custom_field'] = $this->request->post['custom_field'];
				} else {
					$this->session->data['guest']['custom_field'] = array();
				}
				
				if ($this->request->post['account'] == 'register') {
					if(isset($this->request->post['newsletter'])){
						$this->session->data['newsletter'] = true;
					}else{
						$this->session->data['newsletter'] = false;
					}
					if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
						$json['error']['email'] = $this->language->get('error_exists');
					}
					
					if ($this->config->get('config_account_id')) {
						$this->load->model('catalog/information');
						
						$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
						
						if ($information_info && !isset($this->request->post['agree'])) {
							$json['error']['warningagree'] = sprintf($this->language->get('error_agree'), $information_info['title']);
						}
					}
				}
				if (!$json) {
					if ($this->request->post['account'] == 'register') {
						if($this->xtensions_checkout->configExists('xtensions/xsmspro')){
							$this->load->config('xtensions/xsmspro');
							if($this->config->get($this->config->get('xsmspro_config_pfx').'status')){
								if($this->config->get($this->config->get('xsmspro_config_pfx').'restrict_duplicate')){
									$json = $this->load->controller($this->config->get('xsmspro_path').'/isDuplicate',array('telephone'=>$this->request->post['telephone'],'country_code'=>((isset($this->request->post['country_code']) && $this->request->post['country_code'])?'+'.$this->request->post['country_code']:'')));
									if($json){
										$this->response->addHeader('Content-Type: application/json');
										$this->response->setOutput(json_encode($json));
										return ;
									}
								}
								if($this->config->get($this->config->get('xsmspro_config_pfx').'email_register')){
									$json = $this->load->controller($this->config->get('xsmspro_path').'/registerEmail',array('page'=>'checkout'));
									if($json){
										if(isset($json['error'])){
											$this->response->addHeader('Content-Type: application/json');
											$this->response->setOutput(json_encode($json));
										}else{
											$this->session->data['xsmspro']['otpdata'] = $this->request->post;
										}
										return ;
									}
								}
								if($this->config->get($this->config->get('xsmspro_config_pfx').'register')){
									$json = $this->load->controller($this->config->get('xsmspro_path').'/register',array('page'=>'checkout'));
									if($json){
										if(isset($json['error'])){
											$this->response->addHeader('Content-Type: application/json');
											$this->response->setOutput(json_encode($json));
										}else{
											$this->session->data['xsmspro']['otpdata'] = $this->request->post;
										}
										return ;
									}
								}
							}
						}
						unset($this->session->data['guest']);
						unset($this->session->data['payment_address']);
						unset($this->session->data['shipping_address']);
						unset($this->session->data['agree']);
						$customer_id = $this->model_account_customer->addCustomer($this->xtensions_checkout->getRegisterPostData($this->request->post, 'c'));
						if(isset($xconfig['xconfig']['options']['show_country_code']) && $xconfig['xconfig']['options']['show_country_code'] && isset($this->request->post['country_code'])){
							$this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->editTelephone($customer_id,$this->request->post['telephone'],$this->request->post['country_code']);    
						}				
						unset($this->session->data['xsmspro']['otp_verfied']);
						if($this->xtensions_checkout->configExists('xtensions/xsmspro')){
							$this->load->config('xtensions/xsmspro');
							if($this->config->get($this->config->get('xsmspro_config_pfx').'status')){
								if($this->config->get($this->config->get('xsmspro_config_pfx').'register_confirmation')){
									$this->load->controller($this->config->get('xsmspro_path').'/confirm',array('type'=>'register_confirmation','message'=>'registration_confirmation'));
								}
								if($this->config->get($this->config->get('xsmspro_config_pfx').'admin_register')){
									$this->load->controller($this->config->get('xsmspro_path').'/confirm',array('type'=>'admin_register','message'=>'admin_registration'));
								}
							}
						}
						// Clear any previous login attempts for unregistered accounts.
						$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
						
						$customer_group = $this->model_account_customer_group->getCustomerGroup($customer_group_id);
						if ($customer_group && !$customer_group['approval']) {
							$this->customer->login($this->request->post['email'], $this->request->post['password']);
							$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
							$this->xtensions_checkout->addEventActivity(array('registration_successful'));
						} else {
							$json['redirect'] = $this->url->link('account/success');
							$this->xtensions_checkout->addEventActivity(array('registration_successful_pending_approval'));
						}
						// Add to activity log
						if ($this->config->get('config_customer_activity')) {
							$this->load->model('account/activity');
							
							if (isset($this->request->post['firstname']) || isset($this->request->post['lastname'])) {
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
					} else {
						if($this->xtensions_checkout->configExists('xtensions/xsmspro')){
							$this->load->config('xtensions/xsmspro');
							if($this->config->get($this->config->get('xsmspro_config_pfx').'status')){
								if($this->config->get($this->config->get('xsmspro_config_pfx').'email_guest')){
									$json = $this->load->controller($this->config->get('xsmspro_path').'/registerEmail',array('page'=>'guest_checkout'));
									if($json){
										if(isset($json['error'])){
											$this->response->addHeader('Content-Type: application/json');
											$this->response->setOutput(json_encode($json));
										}else{
											$this->session->data['xsmspro']['otpdata'] = $this->request->post;
										}
										return ;
									}
								}
								if($this->config->get($this->config->get('xsmspro_config_pfx').'guest')){
									$json = $this->load->controller($this->config->get('xsmspro_path').'/register',array('page'=>'guest_checkout'));
									if($json){
										if(isset($json['error'])){
											$this->response->addHeader('Content-Type: application/json');
											$this->response->setOutput(json_encode($json));
										}else{
											$this->session->data['xsmspro']['otpdata'] = $this->request->post;
										}
										return ;
									}
								}
							}
						}
						$this->xtensions_checkout->addEventActivity(array('guest_step1_successful'));
						$child = $this->xtensions_checkout->getChildren(array(array('key'=>$this->config->get('xtensions_controller_path').'xguest/matter','data'=>array('direct'=>1))));
						$json['next'] = $child['matter'];
						$json['guest'] = '1';
					}
				}else{
					if ($this->request->post['account'] == 'register') {
						$this->xtensions_checkout->addEventActivity(array('registration_unsuccessful'=>$json));
					}else{
						$this->xtensions_checkout->addEventActivity(array('guest_step1_unsuccessful'=>$json));
					}
				}
				$this->xtensions_checkout->addActivity(array('name','email','telephone'));
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function countryCode(){
		$this->load->language('checkout/checkout');
		$this->load->language($this->config->get('xtensions_language_path'));
		$this->data += $this->load->language($this->config->get('xtensions_language_path_countrycode'));
		$this->data += $this->xtensions_checkout->getXtensionsData($this->config->get('config_store_id'), 'xtensions_best_checkout');
		$xconfig = $this->data['xconfig'];		
		$field_data = $this->xtensions_checkout->getRefinedFields($xconfig, 'xtensions_checkout_checkout_register_account', 'checkout', 'account');
		$telephone_data = $field_data['telephone'];
		$this->data['tooltip'] = $telephone_data['tooltip'];
		$this->data['is_mask'] = $telephone_data['is_mask'];
		$this->data['mask'] = $telephone_data['mask'];
		$this->data['telephone_title'] = $telephone_data['title'];
		$this->data['type'] = $telephone_data['type'];
		$this->data['maxlength'] = $telephone_data['maximum'];
		$this->data['numeric'] = $telephone_data['numeric'];
		$this->data['telephone'] = $this->customer->getTelephone();
		$misc_options = $xconfig['options'];
		$this->data['display_old'] = (isset($misc_options['show_old_phone']) && $misc_options['show_old_phone']?true:false);
		$this->data['top_countries'] =  '';
		if($misc_options['country_code_top']){
			$this->data['only_top_countries'] = isset($misc_options['country_code_only_top']) && $misc_options['country_code_only_top'];
			$top_countries = explode(',',$misc_options['country_code_top']);
			$this->data['top_countries'] = implode('","', $top_countries);
			$this->data['top_countries'] = str_replace(' ', '', '"'.$this->data['top_countries'].'"');
		}
		$this->data['all_countries'] =  '';
		if($misc_options['country_code_all']){
			$all_countries = explode(',',$misc_options['country_code_all']);
			$this->data['all_countries'] = implode('","', $all_countries);
			$this->data['all_countries'] = str_replace(' ', '', '"'.$this->data['all_countries'].'"');
		}
		$this->data['isMobile'] = $this->xtensions_checkout->isMobile();
		$this->template = $this->config->get('xtensions_view_path').'xcountryform';
		$this->response->setOutput($this->xtensions_checkout->renderView($this));
	}
	
	public function validateTelephone(){
		$this->language->load('checkout/checkout');
		$this->load->language($this->config->get('xtensions_language_path'));
		$json = array();
		if (!$this->customer->isLogged()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		}
		
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');
		}
		
		if (!$json) {
			$xconfig = $this->xtensions_checkout->getXtensionsData($this->config->get('config_store_id'), 'xtensions_best_checkout');
			$field_data = $this->xtensions_checkout->getRefinedFields($xconfig['xconfig'], 'xtensions_checkout_checkout_register_account', 'checkout', 'account');
			$field = $field_data['telephone'];			
			if ($field['show'] && $field['required']) {
				$field_error = $this->xtensions_checkout->validateFields('telephone',$field,$this->request->post['telephone']);
				if($field_error){
					$json['error']['telephone'] = $field_error;
				}
			}			
		}
		if(!$json){
			$this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->editTelephone($this->customer->getId(),$this->request->post['telephone'],$this->request->post['country_code']);
			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
?>
