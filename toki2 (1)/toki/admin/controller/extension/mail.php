<?php
class ControllerExtensionMail extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/mail');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mail');

		$this->getList();
	}

	public function add() {
		$this->load->language('extension/mail');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mail');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			
			$this->model_extension_mail->addMail($this->request->post);
						
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

	public function edit() {
		$this->load->language('extension/mail');

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

	public function delete() {
		$this->load->language('extension/mail');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/mail');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $mail_id) {
				$this->model_extension_mail->deleteMail($mail_id);
			}

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

		$this->getList();
	}
		
	protected function getList() {
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'mail_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
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
			'href' => $this->url->link('extension/mail', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('extension/mail/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/mail/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['mails'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
				
		$mail_total = $this->model_extension_mail->getTotalMail($filter_data);
		$results = $this->model_extension_mail->getMails($filter_data);

		foreach ($results as $result) {
			$data['mails'][] = array(
				'mail_id'   => $result['mail_id'],
				'name'      => $result['name'],
				'date_added'=> $result['date_added'],
				'edit'      => $this->url->link('extension/mail/edit', 'user_token=' . $this->session->data['user_token'] . '&mail_id=' . $result['mail_id'] . $url, true)
			);
		}

		$data['heading_title'] 	= $this->language->get('heading_title');
		$data['user_token'] 			= $this->session->data['user_token'];
		$data['text_list'] 		= $this->language->get('text_list');
		$data['text_no_results']= $this->language->get('text_no_results');
		$data['text_confirm'] 	= $this->language->get('text_confirm');
		$data['text_enable'] 	= $this->language->get('text_enable');
		$data['text_disable'] 	= $this->language->get('text_disable');
		$data['text_select'] 	= $this->language->get('text_select');
		$data['text_none'] 		= $this->language->get('text_none');

		$data['column_name']    = $this->language->get('column_name');
		$data['column_date'] 	= $this->language->get('column_date');
		$data['column_action'] 	= $this->language->get('column_action');	
		
		$data['button_add'] 	= $this->language->get('button_add');
		$data['button_edit'] 	= $this->language->get('button_edit');
		$data['button_delete'] 	= $this->language->get('button_delete');
		$data['button_filter'] 	= $this->language->get('button_filter');

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

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name']     = $this->url->link('extension/mail', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_date']     = $this->url->link('extension/mail', 'user_token=' . $this->session->data['user_token'] . '&sort=date' . $url, true);

		$url = '';
		
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $mail_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/mail', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($mail_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($mail_total - $this->config->get('config_limit_admin'))) ? $mail_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $mail_total, ceil($mail_total / $this->config->get('config_limit_admin')));
				
		$data['sort'] 			  = $sort;
		$data['order'] 			  = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		
		$this->response->setOutput($this->load->view('extension/mail_list', $data));
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
		
		$data['entry_message']  = $this->language->get('entry_message');
		$data['entry_subject'] 	= $this->language->get('entry_subject');
		$data['entry_title'] 	= $this->language->get('entry_title');
		$data['entry_product'] 	= $this->language->get('entry_product');
		$data['entry_name'] 	= $this->language->get('entry_name');
		$data['entry_type'] 	= $this->language->get('entry_type');
		$data['entry_status'] 	= $this->language->get('entry_status');
		$data['entry_imgsize'] 	= $this->language->get('entry_imgsize');
		$data['entry_price'] 	= $this->language->get('entry_price');
		$data['entry_coupon'] 	= $this->language->get('entry_coupon');
		
		$data['tab_mail'] 		= $this->language->get('tab_mail');
		$data['tab_product'] 		= $this->language->get('tab_product');
		$data['tab_coupon'] 		= $this->language->get('tab_coupon');
		$data['tab_info'] 		= $this->language->get('tab_info');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
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
			'href' => $this->url->link('extension/mail', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
		
		$data['emailtypes'] = array();
				
		$data['emailtypes'][] = array(
			'emailtype'    => $this->language->get('text_verifyemailaddress'),
			'value' 		=> $this->language->get('text_verifyemail')
		);
		
		$data['emailtypes'][] = array(
			'emailtype'    => $this->language->get('text_productspromotionmail'),
			'value' 		=> $this->language->get('text_productspromotion')
		);
		
		$data['emailtypes'][] = array(
			'emailtype'    => $this->language->get('text_confirmmail'),
			'value' 		=> $this->language->get('text_confirmmail_val')
		);
		
		
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();	
		
		$this->load->model('marketing/coupon');
		$data['coupons'] = $this->model_marketing_coupon->getCoupons();	
		

		if (!isset($this->request->get['mail_id'])) {
			$data['action'] = $this->url->link('extension/mail/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/mail/edit', 'user_token=' . $this->session->data['user_token'] . '&mail_id=' . $this->request->get['mail_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('extension/mail', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['mail_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$mail_info=$this->model_extension_mail->getMail($this->request->get['mail_id']);
			
		}
		$data['user_token'] = $this->session->data['user_token'];
		
		if (isset($this->request->post['subscriber_mail'])) {
			$data['subscriber_mail'] = $this->request->post['subscriber_mail'];
		} elseif (isset($this->request->get['mail_id'])) {
			$data['subscriber_mail'] = $this->model_extension_mail->getMailLanguage($this->request->get['mail_id']);
		} else {
			$data['subscriber_mail'] = array();
		}
		
		if (isset($this->request->post['emailtype'])) {
			$data['emailtype'] = $this->request->post['emailtype'];
		} elseif (!empty($mail_info['emailtype'])) {
			$data['emailtype'] = $mail_info['emailtype'];
		} else {
			$data['emailtype'] = '';
		}
		
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($mail_info['status'])) {
			$data['status'] = $mail_info['status'];
		} else {
			$data['status'] = '';
		}

		if (isset($this->request->post['price_status'])) {
			$data['price_status'] = $this->request->post['price_status'];
		} elseif (!empty($mail_info['price_status'])) {
			$data['price_status'] = $mail_info['price_status'];
		} else {
			$data['price_status'] = '';
		}

		if (isset($this->request->post['coupon_id'])) {
			$data['couponid'] = $this->request->post['coupon_id'];
		} elseif (!empty($mail_info['coupon_id'])) {
			$data['couponid'] = $mail_info['coupon_id'];
		} else {
			$data['couponid'] = '';
		}

		if (isset($this->request->post['image_width'])) {
			$data['image_width'] = $this->request->post['image_width'];
		} elseif (!empty($mail_info['image_width'])) {
			$data['image_width'] = $mail_info['image_width'];
		} else {
			$data['image_width'] = '';
		}
		if (isset($this->request->post['image_height'])) {
			$data['image_height'] = $this->request->post['image_height'];
		} elseif (!empty($mail_info['image_height'])) {
			$data['image_height'] = $mail_info['image_height'];
		} else {
			$data['image_height'] = '';
		}
		

		if (isset($this->request->post['product'])) {
			$products = $this->request->post['product'];
		} elseif (isset($this->request->get['mail_id'])) {
			$products = $this->model_extension_mail->getProductAssign($this->request->get['mail_id']);
		} else {
			$products = array();
		}	

		
		$data['product_assigns'] = array();

		foreach ($products as $product_id) {
			$product_info = $this->model_extension_mail->getProduct($product_id);

			if ($product_info) {
				$data['product_assigns'][] = array(
					'product_id' => $product_info['product_id'],
					'name'       => $product_info['name']
				);
			}
		}


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/mail_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/mail')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		foreach ($this->request->post['subscriber_mail'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}
		
		return !$this->error;
	}
	
	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/mail')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		return !$this->error;
	}
			
}