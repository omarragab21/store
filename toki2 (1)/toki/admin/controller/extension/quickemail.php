<?php
class ControllerExtensionQuickemail extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/quickemail');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mail');

		$this->getForm();
	}

	public function sendmail() {
		$this->load->language('extension/quickemail');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/subscriber');
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			
			$semdmail= $this->model_extension_subscriber->sendEmail($this->request->post);
			if($semdmail){	
				$this->session->data['success'] = $this->language->get('text_success');
			}
			$url = '';

			$this->response->redirect($this->url->link('extension/quickemail', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('extension/quickemail');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mail');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			
			$this->model_extension_mail->editMail($this->request->get['mail_id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/mail', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	
	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_form'] 	= !isset($this->request->get['mail_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled'] 	= $this->language->get('text_enabled');
		$data['text_disabled'] 	= $this->language->get('text_disabled');
		$data['text_default'] 	= $this->language->get('text_default');
		$data['text_percent'] 	= $this->language->get('text_percent');
		$data['text_amount'] 	= $this->language->get('text_amount');
		$data['text_select'] 	= $this->language->get('text_select');
		$data['text_none'] 		= $this->language->get('text_none');
		$data['text_enable'] 	= $this->language->get('text_enable');
		$data['text_disable'] 	= $this->language->get('text_disable');
		$data['text_yes'] 	= $this->language->get('text_yes');
		$data['text_no'] 	= $this->language->get('text_no');
		$data['text_select'] 	= $this->language->get('text_select');
		
		$data['entry_mailtemp']  = $this->language->get('entry_mailtemp');
		$data['entry_subscriber'] 	= $this->language->get('entry_subscriber');
		$data['entry_type'] 	= $this->language->get('entry_type');
		$data['entry_store'] 	= $this->language->get('entry_store');
		

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

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
		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}
				
		$url = '';

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
			'href' => $this->url->link('extension/quickemail', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
		
		$data['emailtypes'] = array();
				
		$data['emailtypes'][] = array(
			'emailtype'    => $this->language->get('text_verifyemailaddress'),
			'value' 		=> 'verify email address'
		);
		
		$data['emailtypes'][] = array(
			'emailtype'    => $this->language->get('text_productspromotionmail'),
			'value' 		=> 'products promotion mail'
		);
		
		$data['emailtypes'][] = array(
			'emailtype'    => $this->language->get('text_confirmmail'),
			'value' 		=> 'confirm mail'
		);
		

		$data['accounts'] = array();

		$data['accounts'][] = array(
			'name' => $this->language->get('text_all'),
			'value' => 'all',
		);

		$data['accounts'][] = array(
			'name' => $this->language->get('text_guest'),
			'value' => '0'
		);

		$data['accounts'][] = array(
			'name' => $this->language->get('text_register'),
			'value' => '1'
		);


		$data['statuss'] = array();

		$data['statuss'][] = array(
			'name' => $this->language->get('text_all'),
			'value' => 'all',
		);

		$data['statuss'][] = array(
			'name' => $this->language->get('text_unverify'),
			'value' => '0',
		);

		$data['statuss'][] = array(
			'name' => $this->language->get('text_verified'),
			'value' => '1',
		);
		$data['statuss'][] = array(
			'name' => $this->language->get('text_subscribe'),
			'value' => 'subscribe',
		);
		$data['statuss'][] = array(
			'name' => $this->language->get('text_unsubscribe'),
			'value' => '2',
		);
		$data['statuss'][] = array(
			'name' => $this->language->get('text_decline'),
			'value' => '3',
		);
		
		
		$this->load->model('extension/mail');
		$data['mailtemplates'] = $this->model_extension_mail->getMails(0);	
		
		$this->load->model('marketing/coupon');
		$data['coupons'] = $this->model_marketing_coupon->getCoupons();	
		
		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();
		

		$data['action'] = $this->url->link('extension/quickemail/sendmail', 'user_token=' . $this->session->data['user_token'] . $url, true);
		

		$data['cancel'] = $this->url->link('extension/quickemail', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['mail_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$mail_info=$this->model_extension_mail->getMail($this->request->get['mail_id']);
			
		}
		$data['user_token'] = $this->session->data['user_token'];
		
		if (isset($this->request->post['emailtemplate'])) {
			$data['emailtemplate'] = $this->request->post['emailtemplate'];
		} else {
			$data['emailtemplate'] = '';
		}
		
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} else {
			$data['status'] = '';
		}

		if (isset($this->request->post['account'])) {
			$data['account'] = $this->request->post['account'];
		} else {
			$data['account'] = '';
		}
		


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/quickemail', $data));
	}

	
	
			
}