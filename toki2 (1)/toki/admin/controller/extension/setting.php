<?php
class Controllerextensionsetting extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/setting');

		$this->document->setTitle($this->language->get('heading_title1'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			
			$this->model_setting_setting->editSetting('tmdsms', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/setting', 'user_token=' . $this->session->data['user_token'] , true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
	
		$data['tab_setting'] = $this->language->get('tab_setting');
		$data['tab_registrtion'] = $this->language->get('tab_registrtion');
		$data['tab_status'] = $this->language->get('tab_status');
		$data['tab_affiliate'] = $this->language->get('tab_affiliate');
		$data['tab_admin'] = $this->language->get('tab_admin');
		$data['tab_otpverification'] = $this->language->get('tab_otpverification');
		$data['tab_loginwithotp'] = $this->language->get('tab_loginwithotp');

		$data['entry_dltid'] = $this->language->get('entry_dltid');
		$data['entry_url'] = $this->language->get('entry_url');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_registrtion'] = $this->language->get('entry_registrtion');
		$data['entry_registrtiondis'] = $this->language->get('entry_registrtiondis');
		$data['entry_approve'] = $this->language->get('entry_approve');
		$data['entry_affiliate_approval'] = $this->language->get('entry_affiliate_approval');
		$data['entry_affiliate_disapproval'] = $this->language->get('entry_affiliate_disapproval');
		$data['entry_forgot_password'] = $this->language->get('entry_forgot_password');
		$data['entry_ssl'] = $this->language->get('entry_ssl');
		$data['entry_customer'] = $this->language->get('entry_customer');
		$data['entry_order'] = $this->language->get('entry_order');
		$data['entry_customerregistrtion'] = $this->language->get('entry_customerregistrtion');
		$data['entry_adminaffiliate'] = $this->language->get('entry_adminaffiliate');
		$data['entry_otpverification'] = $this->language->get('entry_otpverification');
		$data['entry_otpmsg'] = $this->language->get('entry_otpmsg');
		$data['entry_verifypage'] = $this->language->get('entry_verifypage');
		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_descriptiontop'] = $this->language->get('entry_descriptiontop');
		$data['entry_descriptionbottom'] = $this->language->get('entry_descriptionbottom');
		$data['entry_labelotp'] = $this->language->get('entry_labelotp');
		$data['entry_btnverify'] = $this->language->get('entry_btnverify');
		$data['entry_resend'] = $this->language->get('entry_resend');
		$data['entry_invalidotp'] = $this->language->get('entry_invalidotp');

		$data['entry_loginbtnstatus'] = $this->language->get('entry_loginbtnstatus');
		$data['entry_btntext'] = $this->language->get('entry_btntext');
		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_label'] = $this->language->get('entry_label');
		$data['entry_label2'] = $this->language->get('entry_label2');
		$data['entry_errormsg'] = $this->language->get('entry_errormsg');
		$data['entry_errornotmatchmsg'] = $this->language->get('entry_errornotmatchmsg');
		$data['entry_submitbtn'] = $this->language->get('entry_submitbtn');
		$data['entry_debug'] = $this->language->get('entry_debug');


		$data['button_save'] = $this->language->get('button_save');
		$data['button_stay'] = $this->language->get('button_stay');
		$data['button_cancel'] = $this->language->get('button_cancel');


		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] , true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/setting', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/setting', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] , true);

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		
		//$data['affiliates'] = $this->model_mail_affiliate->getAffiliates();

		
		
		if (isset($this->request->post['tmdsms_method'])) {
			$data['tmdsms_method'] = $this->request->post['tmdsms_method'];
		} else {
			$data['tmdsms_method'] = $this->config->get('tmdsms_method');
		}
		
		if (isset($this->request->post['tmdsms_debug'])) {
			$data['tmdsms_debug'] = $this->request->post['tmdsms_debug'];
		} else {
			$data['tmdsms_debug'] = $this->config->get('tmdsms_debug');
		}
		
		if (isset($this->request->post['tmdsms_key'])) {
			$data['tmdsms_key'] = $this->request->post['tmdsms_key'];
		} else {
			$data['tmdsms_key'] = $this->config->get('tmdsms_key');
		}
		
		if (isset($this->request->post['tmdsms_masterkey'])) {
			$data['tmdsms_masterkey'] = $this->request->post['tmdsms_masterkey'];
		} else {
			$data['tmdsms_masterkey'] = $this->config->get('tmdsms_masterkey');
		}
		
		if (isset($this->request->post['tmdsms_secretkey'])) {
			$data['tmdsms_secretkey'] = $this->request->post['tmdsms_secretkey'];
		} else {
			$data['tmdsms_secretkey'] = $this->config->get('tmdsms_secretkey');
		}
		
		if (isset($this->request->post['tmdsms_telephone'])) {
			$data['tmdsms_telephone'] = $this->request->post['tmdsms_telephone'];
		} else {
			$data['tmdsms_telephone'] = $this->config->get('tmdsms_telephone');
		}
		
		if (isset($this->request->post['tmdsms_url'])) {
			$data['tmdsms_url'] = $this->request->post['tmdsms_url'];
		} else {
			$data['tmdsms_url'] = $this->config->get('tmdsms_url');
		}
		
		if (isset($this->request->post['tmdsms_customer_registrtion'])) {
			$data['tmdsms_customer_registrtion'] = $this->request->post['tmdsms_customer_registrtion'];
		} else {
			$data['tmdsms_customer_registrtion'] = $this->config->get('tmdsms_customer_registrtion');
		}

		if (isset($this->request->post['tmdsms_registrtion_approval'])) {
			$data['tmdsms_registrtion_approval'] = $this->request->post['tmdsms_registrtion_approval'];
		} else {
			$data['tmdsms_registrtion_approval'] = $this->config->get('tmdsms_registrtion_approval');
		}

		if (isset($this->request->post['tmdsms_registrtion_disapproval'])) {
			$data['tmdsms_registrtion_disapproval'] = $this->request->post['tmdsms_registrtion_disapproval'];
		} else {
			$data['tmdsms_registrtion_disapproval'] = $this->config->get('tmdsms_registrtion_disapproval');
		}

		if (isset($this->request->post['tmdsms_forgotpassword_sms'])) {
			$data['tmdsms_forgotpassword_sms'] = $this->request->post['tmdsms_forgotpassword_sms'];
		} else {
			$data['tmdsms_forgotpassword_sms'] = $this->config->get('tmdsms_forgotpassword_sms');
		}
		
		if (isset($this->request->post['tmdsms_orderstatus'])) {
			$data['tmdsms_orderstatus'] = $this->request->post['tmdsms_orderstatus'];
		} elseif($this->config->get('tmdsms_orderstatus')) {
			$data['tmdsms_orderstatus'] = $this->config->get('tmdsms_orderstatus');
		} else {
			$data['tmdsms_orderstatus'] = array();
		}

		if (isset($this->request->post['tmdsms_status'])) {
			$data['tmdsms_status'] = $this->request->post['tmdsms_status'];
		} else {
			$data['tmdsms_status'] = $this->config->get('tmdsms_status');
		}

		if (isset($this->request->post['tmdsms_ssl'])) {
			$data['tmdsms_ssl'] = $this->request->post['tmdsms_ssl'];
		} else {
			$data['tmdsms_ssl'] = $this->config->get('tmdsms_ssl');
		}
		
		if (isset($this->request->post['tmdsms_affiliate_approval'])) {
			$data['tmdsms_affiliate_approval'] = $this->request->post['tmdsms_affiliate_approval'];
		} else {
			$data['tmdsms_affiliate_approval'] = $this->config->get('tmdsms_affiliate_approval');
		}
		if (isset($this->request->post['tmdsms_affiliate_disapproval'])) {
			$data['tmdsms_affiliate_disapproval'] = $this->request->post['tmdsms_affiliate_disapproval'];
		} else {
			$data['tmdsms_affiliate_disapproval'] = $this->config->get('tmdsms_affiliate_disapproval');
		}
		if (isset($this->request->post['tmdsms_forgotpassword_affiliate'])) {
			$data['tmdsms_forgotpassword_affiliate'] = $this->request->post['tmdsms_forgotpassword_affiliate'];
		} else {
			$data['tmdsms_forgotpassword_affiliate'] = $this->config->get('tmdsms_forgotpassword_affiliate');
		}
		
		if (isset($this->request->post['tmdsms_admin_customer'])) {
			$data['tmdsms_admin_customer'] = $this->request->post['tmdsms_admin_customer'];
		} else {
			$data['tmdsms_admin_customer'] = $this->config->get('tmdsms_admin_customer');
		}

		if (isset($this->request->post['tmdsms_admin_order'])) {
			$data['tmdsms_admin_order'] = $this->request->post['tmdsms_admin_order'];
		} else {
			$data['tmdsms_admin_order'] = $this->config->get('tmdsms_admin_order');
		}

		if (isset($this->request->post['tmdsms_admin_affiliate'])) {
			$data['tmdsms_admin_affiliate'] = $this->request->post['tmdsms_admin_affiliate'];
		} else {
			$data['tmdsms_admin_affiliate'] = $this->config->get('tmdsms_admin_affiliate');
		}

		if (isset($this->request->post['tmdsms_admin_affiliate'])) {
			$data['tmdsms_admin_affiliate'] = $this->request->post['tmdsms_admin_affiliate'];
		} else {
			$data['tmdsms_admin_affiliate'] = $this->config->get('tmdsms_admin_affiliate');
		}

		if (isset($this->request->post['tmdsms_otpverifystatus'])) {
			$data['tmdsms_otpverifystatus'] = $this->request->post['tmdsms_otpverifystatus'];
		} else {
			$data['tmdsms_otpverifystatus'] = $this->config->get('tmdsms_otpverifystatus');
		}

		if (isset($this->request->post['tmdsms_otpmsg'])) {
			$data['tmdsms_otpmsg'] = $this->request->post['tmdsms_otpmsg'];
		} else {
			$data['tmdsms_otpmsg'] = $this->config->get('tmdsms_otpmsg');
		}

		if (isset($this->request->post['tmdsms_otpverify'])) {
			$data['tmdsms_otpverify'] = $this->request->post['tmdsms_otpverify'];
		} else {
			$data['tmdsms_otpverify'] = $this->config->get('tmdsms_otpverify');
		}

		if (isset($this->request->post['tmdsms_loginbtnstatus'])) {
			$data['tmdsms_loginbtnstatus'] = $this->request->post['tmdsms_loginbtnstatus'];
		} else {
			$data['tmdsms_loginbtnstatus'] = $this->config->get('tmdsms_loginbtnstatus');
		}

		if (isset($this->request->post['tmdsms_loginotp'])) {
			$data['tmdsms_loginotp'] = $this->request->post['tmdsms_loginotp'];
		} else {
			$data['tmdsms_loginotp'] = $this->config->get('tmdsms_loginotp');
		}

		if (isset($this->request->post['tmdsms_loginotpmsg'])) {
			$data['tmdsms_loginotpmsg'] = $this->request->post['tmdsms_loginotpmsg'];
		} else {
			$data['tmdsms_loginotpmsg'] = $this->config->get('tmdsms_loginotpmsg');
		}


		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		/* DLT Template Ids*/	
		if (isset($this->request->post['tmdsms_registrtion_approval_dltid'])) {
			$data['tmdsms_registrtion_approval_dltid'] = $this->request->post['tmdsms_registrtion_approval_dltid'];
		} else {
			$data['tmdsms_registrtion_approval_dltid'] = $this->config->get('tmdsms_registrtion_approval_dltid');
		}
		
		if (isset($this->request->post['tmdsms_registrtion_disapproval_dltid'])) {
			$data['tmdsms_registrtion_disapproval_dltid'] = $this->request->post['tmdsms_registrtion_disapproval_dltid'];
		} else {
			$data['tmdsms_registrtion_disapproval_dltid'] = $this->config->get('tmdsms_registrtion_disapproval_dltid');
		}
		
		if (isset($this->request->post['tmdsms_forgotpassword_dltid'])) {
			$data['tmdsms_forgotpassword_dltid'] = $this->request->post['tmdsms_forgotpassword_dltid'];
		} else {
			$data['tmdsms_forgotpassword_dltid'] = $this->config->get('tmdsms_forgotpassword_dltid');
		}
		
		if (isset($this->request->post['tmdsms_affiliate_approval_dltid'])) {
			$data['tmdsms_affiliate_approval_dltid'] = $this->request->post['tmdsms_affiliate_approval_dltid'];
		} else {
			$data['tmdsms_affiliate_approval_dltid'] = $this->config->get('tmdsms_affiliate_approval_dltid');
		}
		
		if (isset($this->request->post['tmdsms_affiliate_disapproval_dltid'])) {
			$data['tmdsms_affiliate_disapproval_dltid'] = $this->request->post['tmdsms_affiliate_disapproval_dltid'];
		} else {
			$data['tmdsms_affiliate_disapproval_dltid'] = $this->config->get('tmdsms_affiliate_disapproval_dltid');
		}
		
		if (isset($this->request->post['tmdsms_affiliate_disapproval_dltid'])) {
			$data['tmdsms_affiliate_disapproval_dltid'] = $this->request->post['tmdsms_affiliate_disapproval_dltid'];
		} else {
			$data['tmdsms_affiliate_disapproval_dltid'] = $this->config->get('tmdsms_affiliate_disapproval_dltid');
		}
		
		if (isset($this->request->post['tmdsms_forgotpassword_affiliate_dltid'])) {
			$data['tmdsms_forgotpassword_affiliate_dltid'] = $this->request->post['tmdsms_forgotpassword_affiliate_dltid'];
		} else {
			$data['tmdsms_forgotpassword_affiliate_dltid'] = $this->config->get('tmdsms_forgotpassword_affiliate_dltid');
		}
		
		if (isset($this->request->post['tmdsms_admin_order_dltid'])) {
			$data['tmdsms_admin_order_dltid'] = $this->request->post['tmdsms_admin_order_dltid'];
		} else {
			$data['tmdsms_admin_order_dltid'] = $this->config->get('tmdsms_admin_order_dltid');
		}
		
		if (isset($this->request->post['tmdsms_admin_order_dltid'])) {
			$data['tmdsms_admin_order_dltid'] = $this->request->post['tmdsms_admin_order_dltid'];
		} else {
			$data['tmdsms_admin_order_dltid'] = $this->config->get('tmdsms_admin_order_dltid');
		}
		
		if (isset($this->request->post['tmdsms_admin_customer_dltid'])) {
			$data['tmdsms_admin_customer_dltid'] = $this->request->post['tmdsms_admin_customer_dltid'];
		} else {
			$data['tmdsms_admin_customer_dltid'] = $this->config->get('tmdsms_admin_customer_dltid');
		}
		
		if (isset($this->request->post['tmdsms_admin_affiliate_dltid'])) {
			$data['tmdsms_admin_affiliate_dltid'] = $this->request->post['tmdsms_admin_affiliate_dltid'];
		} else {
			$data['tmdsms_admin_affiliate_dltid'] = $this->config->get('tmdsms_admin_affiliate_dltid');
		}
		
		if (isset($this->request->post['tmdsms_otpmsg_dltid'])) {
			$data['tmdsms_otpmsg_dltid'] = $this->request->post['tmdsms_otpmsg_dltid'];
		} else {
			$data['tmdsms_otpmsg_dltid'] = $this->config->get('tmdsms_otpmsg_dltid');
		}
		
		if (isset($this->request->post['tmdsms_loginotpmsg_dltid'])) {
			$data['tmdsms_loginotpmsg_dltid'] = $this->request->post['tmdsms_loginotpmsg_dltid'];
		} else {
			$data['tmdsms_loginotpmsg_dltid'] = $this->config->get('tmdsms_loginotpmsg_dltid');
		}
		/* DLT Template Ids*/	
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/setting', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/setting')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}