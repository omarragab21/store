<?php
class ControllerExtensiontmdsmsotpverify extends Controller {
	public function index() {
		$this->load->language('extension/tmdsms/otpverify');
		$this->load->model('extension/tmdsms/otpverify');
			$this->model_extension_tmdsms_otpverify->sendotp($this->customer->isLogged());
		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);
        $data['accountlink']= $this->url->link('account/account', '', true);
		$headingtitle = $this->config->get('tmdsms_title');
		
		$labelotp = $this->config->get('tmdsms_labelotp');
		$data['otpstatus'] = $this->config->get('tmdsms_otpverifystatus');

		$tmdsmsotp=$this->config->get('tmdsms_otpverify');
		$data['descriptiontop']=strip_tags(html_entity_decode($tmdsmsotp[$this->config->get('config_language_id')]['descriptiontop']));

		$data['descriptionbottom'] = strip_tags(html_entity_decode($tmdsmsotp[$this->config->get('config_language_id')]['descriptionbottom']));
		
		if($tmdsmsotp){
			$data['heading_title'] = $tmdsmsotp[$this->config->get('config_language_id')]['title'];
		} else {
			$data['heading_title'] = $this->language->get('heading_title');
		}

		if($tmdsmsotp){
			$data['button_continue'] = $tmdsmsotp[$this->config->get('config_language_id')]['btnverify'];
		} else {
			$data['button_continue'] = $this->language->get('button_continue');
		}

		if($tmdsmsotp){
			$data['button_resend'] = $tmdsmsotp[$this->config->get('config_language_id')]['resendbtn'];
		} else {
			$data['button_resend'] = $this->language->get('button_resend');
		}

		if($tmdsmsotp){
			$data['text_otp'] = $tmdsmsotp[$this->config->get('config_language_id')]['labelotp'];
		} else {
			$data['text_otp'] = $this->language->get('text_otp');
		}

         $data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		
		$data['customer_id'] = $this->request->get['customer_id'];
		$this->response->setOutput($this->load->view('extension/tmdsms/otpverify', $data));
	}

	public function passunlock() {
		$this->load->language('extension/tmdsms/otpverify');
		$this->load->model('extension/tmdsms/otpverify');
		
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$json = array();
			$customer_id = $this->customer->isLogged();

			$result = $this->model_extension_tmdsms_otpverify->getotp($customer_id);
			
			$tmdsmsotplogin = $this->config->get('tmdsms_loginotp');
			if($tmdsmsotplogin){
				$errormsg = $tmdsmsotplogin[$this->config->get('config_language_id')]['errormsg'];
			} else {
				$errormsg = $this->language->get('error_otp');
			}
			
			if($tmdsmsotplogin){
				$successmsg = $tmdsmsotplogin[$this->config->get('config_language_id')]['successmsg'];
			} else {
				$successmsg = $this->language->get('text_success');
			}
			
			if($this->request->post['otpconfirm'] != $result['otp']) {
				$json['error'] = $errormsg;
			}
			if(empty($json['error'])) {
				
				$this->model_extension_tmdsms_otpverify->accountverify($customer_id,$this->request->post);
				
				$json['success'] = $successmsg;
				$json['redirect']= $this->url->link('account/success');
				
			}	
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function sendotp(){
		$this->load->language('extension/tmdsms/otpverify');
		$this->load->model('extension/tmdsms/otpverify');
		
		$customer_id = $this->request->get['customer_id'];

		$json = array();
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			if(empty($json['error'])) {

				$this->model_extension_tmdsms_otpverify->sendotp($this->customer->isLogged());
				
				$tmdsmsotplogin = $this->config->get('tmdsms_loginotp');
				if($tmdsmsotplogin){
					$successmsg = $tmdsmsotplogin[$this->config->get('config_language_id')]['successmsg'];
				} else {
					$successmsg = $this->language->get('text_otpsuccess');
				}
				
				$json['success'] = $successmsg;
			}	
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}