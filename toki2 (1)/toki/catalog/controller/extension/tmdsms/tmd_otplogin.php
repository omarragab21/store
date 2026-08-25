<?php
class ControllerExtensiontmdsmstmdotplogin extends Controller {
	private $error = array();

	public function index() {
		$this->load->model('account/customer');

		$this->load->language('extension/tmdsms/tmd_otplogin');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_login'),
			'href' => $this->url->link('account/login', '', true)
		);

		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} elseif (isset($this->error['warning'])) {
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

		/*30 05 2019 */

		$data['mainstatus'] = $this->config->get('tmdsms_status');
		$data['loginotpstatus'] = $this->config->get('tmdsms_loginbtnstatus');

		$tmdsmsotplogin = $this->config->get('tmdsms_loginotp');
		
		if($tmdsmsotplogin){
			$data['entry_loginotp'] = $tmdsmsotplogin[$this->config->get('config_language_id')]['loginbtntext'];
		} else {
			$data['entry_loginotp'] = $this->language->get('entry_loginotp');
		}

		if($tmdsmsotplogin){
			$data['entry_logintitle'] = $tmdsmsotplogin[$this->config->get('config_language_id')]['logintitle'];
		} else {
			$data['entry_logintitle'] = $this->language->get('entry_logintitle');
		}

		if($tmdsmsotplogin){
			$data['entry_loginlabel'] = $tmdsmsotplogin[$this->config->get('config_language_id')]['label'];
		} else {
			$data['entry_loginlabel'] = $this->language->get('entry_loginlabel');
		}

		if($tmdsmsotplogin){
			$data['entry_label2'] = $tmdsmsotplogin[$this->config->get('config_language_id')]['label2'];
		} else {
			$data['entry_label2'] = $this->language->get('entry_label2');
		}

		if($tmdsmsotplogin){
			$data['entry_errormsg'] = $tmdsmsotplogin[$this->config->get('config_language_id')]['errormsg'];
		} else {
			$data['entry_errormsg'] = $this->language->get('entry_errormsg');
		}

		if($tmdsmsotplogin){
			$data['entry_submitbtn'] = $tmdsmsotplogin[$this->config->get('config_language_id')]['submitbtntext'];
		} else {
			$data['entry_submitbtn'] = $this->language->get('entry_submitbtn');
		}
		
		if($tmdsmsotplogin){
			$data['entry_resendotp'] = $tmdsmsotplogin[$this->config->get('config_language_id')]['resendotp'];
		} else {
			$data['entry_resendotp'] = $this->language->get('entry_resendotp');
		}

		return $this->load->view('extension/tmdsms/tmd_otplogin', $data);
	}

	public function chkphonenumber() {
		$json=array();
			$this->load->model('account/customer');

			$this->load->language('extension/tmdsms/tmd_otplogin');

			$tmdsmsotplogin = $this->config->get('tmdsms_loginotp');
			
			if($tmdsmsotplogin){
				$errormsg = $tmdsmsotplogin[$this->config->get('config_language_id')]['errormsg'];
			} else {
				$errormsg = $this->language->get('entry_errormsg');
			}
			
			if($tmdsmsotplogin){
				$successmsg = $tmdsmsotplogin[$this->config->get('config_language_id')]['successmsg'];
			} else {
				$successmsg = 'OTP Matched....!';
			}

			$errornotmatch = $tmdsmsotplogin[$this->config->get('config_language_id')]['errornotmatch'];

			if(empty($this->request->post['telephone'])){
				
				$json['error'] = $errormsg;
			} else {
				$customer_info = $this->model_account_customer->getCustomerByPhone($this->request->post['telephone']);
					
					if(!$customer_info) {
						$json['error']= $errornotmatch;						
					} else {
						$this->session->data['tmdcustomerid']=$customer_info['customer_id'];
						
						$json['success']= $successmsg;
							
							$otp = rand(100000,999999);
							$this->session->data['otpcode']=$otp;

							$this->sendsms();
							
					}
			}
			
		$this->response->setOutput(json_encode($json));
	}

	public function addotp() {
		$json=array();
			
			$this->load->model('account/customer');

			$this->load->language('extension/tmdsms/tmd_otplogin');

			$tmdsmsotplogin = $this->config->get('tmdsms_loginotp');
			if($tmdsmsotplogin){
				$errormsg = $tmdsmsotplogin[$this->config->get('config_language_id')]['errornotmatch'];
			} else {
				$errormsg = $this->language->get('entry_errormsg');
			}

			if($tmdsmsotplogin){
				$invalidmsg = $tmdsmsotplogin[$this->config->get('config_language_id')]['invalidotperrormsg'];
			} else {
				$invalidmsg = $this->language->get('entry_invaliderrormsg');
			}

			if(empty($this->request->post['otp'])){
				
				$json['error'] = $errormsg;
			} else {		
				
				$otpcode = $this->session->data['otpcode'];
				
				if($otpcode!=$this->request->post['otp']) {
					$json['error'] = $invalidmsg;
					
				} else {	
					$this->session->data['optvarifystatus']=true;
					
					$tmdsmsotplogin = $this->config->get('tmdsms_loginotp');
					if($tmdsmsotplogin){
						$successmsg = $tmdsmsotplogin[$this->config->get('config_language_id')]['successmsg'];
					} else {
						$successmsg = 'OTP Matched....!';
					}
					
					$json['success']=$successmsg;
					
					$customer_info = $this->model_account_customer->getCustomer($this->session->data['tmdcustomerid']);
				
					$this->customer->login($customer_info['email'],'',1);
					$this->model_account_customer->addLoginAttempt($customer_info['email']);
					
					$data['accounturl'] = $this->url->link('account/account', '', true);
						if(isset($this->request->get['route'])){
						 $this->session->data['pageroute']=$this->request->get['route'];
						} else{
						 $this->session->data['pageroute']='';
						}
						
					if (isset($this->request->post['redirect']) && $this->request->post['redirect'] != $this->url->link('account/logout', '', true) && (strpos($this->request->post['redirect'], $this->config->get('config_url')) !== false || strpos($this->request->post['redirect'], $this->config->get('config_ssl')) !== false)) {
						$json['redirect']=str_replace('&amp;', '&', $this->request->post['redirect']);
					} else {
						if($this->session->data['pageroute']=='checkout/checkout'){
						$json['redirect']=$this->url->link('checkout/checkout', '', true);
						} else{
						$json['redirect']=$this->url->link('account/account', '', true);
						}
						
					}
		
					
				}
			}
		$this->response->setOutput(json_encode($json));
	}
	
	public function resendotp() {
		$json=array();
		$otp = rand(100000,999999);
		$this->session->data['otpcode'] = $otp;
		
		$this->sendsms();
		$tmdsmsotplogin = $this->config->get('tmdsms_loginotp');
		if($tmdsmsotplogin){
			$successmsg = $tmdsmsotplogin[$this->config->get('config_language_id')]['successmsg'];
		} else {
			$successmsg = '';
		}
		$json['success']= $successmsg;
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function sendsms() {
		
		if($this->config->get('tmdsms_loginbtnstatus')){
			
			if(isset($this->request->post['telephone'])) {
				$otpcode = $this->session->data['otpcode'];
				$find = array(
					'{code}'
				);
				
				$this->load->model('tool/image');
				$replace = array(
					'code' => $otpcode
				);
						
				$mobilemessage=str_replace(' ',' ',trim(str_replace($find, $replace, $this->config->get('tmdsms_otpmsg'))));
				
				if(!empty($mobilemessage)){
					$sms_data = array(
						'telephone' 	=> $this->request->post['telephone'],
						'message' 		=> $mobilemessage,
						'dltid' 		=> $this->config->get('tmdsms_loginotpmsg_dltid'),
					);

					$this->load->model('extension/tmdsms/tmdsms');
					$this->model_extension_tmdsms_tmdsms->sendSms($sms_data);
				}				
			
			}	
		}
	}

}
