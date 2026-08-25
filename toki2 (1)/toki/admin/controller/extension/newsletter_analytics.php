<?php
class ControllerExtensionNewsletteranalytics extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/newsletter_analytics');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/subscriber');
		$this->load->model('extension/mail');

		$this->getList();
	}


	public function delete() {
		$this->load->language('extension/newsletter_analytics');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/subscriber');

			if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $mail_send_id) {
				$this->model_extension_subscriber->deleteMailReport($mail_send_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

		$this->response->redirect($this->url->link('extension/newsletter_analytics', 'user_token=' . $this->session->data['user_token'] , true));
		}


		$this->getList();
	}

	

	protected function getList() {
		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = null;
		}
		if (isset($this->request->get['filter_date'])) {
			$filter_date = $this->request->get['filter_date'];
		} else {
			$filter_date = null;
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_date'])) {
			$url .= '&filter_date=' . urlencode(html_entity_decode($this->request->get['filter_date'], ENT_QUOTES, 'UTF-8'));
		}
		$url='';

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/newsletter_analytics', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$filter_data = array(
			'filter_email'	  => $filter_email,
			'filter_date'	  => $filter_date,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		);

		$data['mailreports'] = array();		
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['delete'] = $this->url->link('extension/newsletter_analytics/delete','&user_token=' . $this->session->data['user_token'], true);

				$this->load->model('extension/mail');

		$subscriber_switch_all = $this->model_extension_subscriber->getTotalSubscriberSwitchall(0);
		$sendmail_all = $this->model_extension_subscriber->getTotalSendMessagesall(0);
		$clickedmail_all = $this->model_extension_subscriber->getTotalclickedall(0);


		$mailtemplate_total = $this->model_extension_subscriber->getTotalMailTemplates($filter_data);

		$results = $this->model_extension_subscriber->getMailTemplates($filter_data);

		foreach ($results as $result) {
			$mail_template = $this->model_extension_mail->getMailbyId($result['mail_id']);

			if(!empty($mail_template['name'])){
			$template_name=	$mail_template['name'];
			}else{
				$template_name='';
			}

			$subscriber_switch_total = $this->model_extension_subscriber->getTotalSubscriberSwitch($result['mail_send_id']);
						
			$sendmail_total = $this->model_extension_subscriber->getTotalSendMessages($result['mail_send_id']);

			$clickedmail_total = $this->model_extension_subscriber->getTotalclickedmail($result['mail_send_id']);

			$data['mailreports'][] = array(
				'mail_send_id' => $result['mail_send_id'],
				'template_name' => $template_name,
				'subscriber_switch_total' => $subscriber_switch_total,
				'sendmail_total' => $sendmail_total,
				'clickedmail_total' => $clickedmail_total,
				'date_added'     => $result['date_added'],
				'view'       => $this->url->link('extension/newsletter_analytics/view', 'user_token=' . $this->session->data['user_token'] . '&mail_send_id=' . $result['mail_send_id'] , true)
		
			);
		}


		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_yes_required'] = $this->language->get('text_yes_required');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_guest'] = $this->language->get('text_guest');
		$data['text_register'] = $this->language->get('text_register');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_sendto'] = $this->language->get('column_sendto');
		$data['column_email'] = $this->language->get('column_email');
		$data['column_account'] = $this->language->get('column_account');
		$data['column_ip'] = $this->language->get('column_ip');
		$data['column_date'] = $this->language->get('column_date');
		$data['column_switched'] = $this->language->get('column_switched');
		$data['column_clicked'] = $this->language->get('column_clicked');
		$data['column_sendmessages'] = $this->language->get('column_sendmessages');
		$data['column_template'] = $this->language->get('column_template');
		$data['column_senddate'] = $this->language->get('column_senddate');
		$data['column_date'] = $this->language->get('column_date');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_savestay'] = $this->language->get('button_savestay');
		$data['button_view'] = $this->language->get('button_view');
		$data['button_filter'] = $this->language->get('button_filter');
		$data['button_remove'] = $this->language->get('button_remove');
		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}


		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}
	
		if (isset($this->request->get['filter_date'])) {
			$url .= '&filter_date=' . urlencode(html_entity_decode($this->request->get['filter_date'], ENT_QUOTES, 'UTF-8'));
		}


		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		$url = '';

		$pagination = new Pagination();
		$pagination->total = $mailtemplate_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/newsletter_analytics', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($mailtemplate_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($mailtemplate_total - $this->config->get('config_limit_admin'))) ? $mailtemplate_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $mailtemplate_total, ceil($mailtemplate_total / $this->config->get('config_limit_admin')));

		$data['filter_email'] = $filter_email;
		$data['filter_date'] = $filter_date;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/newsletter_analytics', $data));
	}


	public function view() {
		$this->load->language('extension/newsletter_analytics');

		$this->document->setTitle($this->language->get('heading_title2'));

		$this->load->model('extension/subscriber');

		$data['heading_title2'] = $this->language->get('heading_title2');
		$data['text_maillist'] = $this->language->get('text_maillist');
	
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_yes_required'] = $this->language->get('text_yes_required');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_guest'] = $this->language->get('text_guest');
		$data['text_register'] = $this->language->get('text_register');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['entry_sendto'] = $this->language->get('entry_sendto');
		$data['entry_date'] = $this->language->get('entry_date');
		$data['help_review_guest'] = $this->language->get('help_review_guest');
		
		$data['column_name'] = $this->language->get('column_name');
		$data['column_sendto'] = $this->language->get('column_sendto');
		$data['column_email'] = $this->language->get('column_email');
		$data['column_account'] = $this->language->get('column_account');
		$data['column_ip'] = $this->language->get('column_ip');
		$data['column_date'] = $this->language->get('column_date');
		$data['column_switched'] = $this->language->get('column_switched');
		$data['column_clicked'] = $this->language->get('column_clicked');
		$data['column_sendmessages'] = $this->language->get('column_sendmessages');
		$data['column_template'] = $this->language->get('column_template');
		$data['column_senddate'] = $this->language->get('column_senddate');
		$data['column_date'] = $this->language->get('column_date');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_action'] = $this->language->get('column_action');

		
		$data['button_filter'] = $this->language->get('button_filter');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['user_token'] = $this->session->data['user_token'];

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);


		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/newsletter_analytics', 'user_token=' . $this->session->data['user_token'], true)
		);


		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		if (isset($this->request->get['mail_send_id'])) {
			$data['mail_send_id'] = $this->request->get['mail_send_id'];
		} else {
			$data['mail_send_id'] = '';
		}

		if (isset($this->request->get['mail_send_id'])) {
			$mail_send_id = $this->request->get['mail_send_id'];
		} else {
			$mail_send_id = '';
		}

		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = null;
		}
		if (isset($this->request->get['filter_date'])) {
			$filter_date = $this->request->get['filter_date'];
		} else {
			$filter_date = null;
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		$url='';
		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_date'])) {
			$url .= '&filter_date=' . urlencode(html_entity_decode($this->request->get['filter_date'], ENT_QUOTES, 'UTF-8'));
		}

		$filter_data = array(
			'filter_email'	  => $filter_email,
			'filter_date'	  => $filter_date,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		);

		$data['mailreports'] = array();

		$mailreport_total = $this->model_extension_subscriber->getTotalMailReports($filter_data, $mail_send_id);

		$results = $this->model_extension_subscriber->getMailReports($filter_data, $mail_send_id);

		foreach ($results as $result) {


			 $status_clr =$result['status'];
			 $account_clr =$result['account'];

			if($result['status']==0){
				$status='Un-verify';
			}
			if($result['status']==1){
				$status='Verified';
			}
			if($result['status']==2){
				$status='Unsubscribe';
			}
			if($result['status']==3){
				$status='Decline';
			}
			if($result['account']==1 || $result['account']==2){
				$account=$this->language->get('text_register');
			}
			if($result['account']==0){
				$account=$this->language->get('text_guest');
			}




			$data['mailreports'][] = array(
				'mail_report_id' => $result['mail_report_id'],
				'email'      => $result['email'],
				'account'          => $account,
				'status'     => $status,
				'status_clr'     => $status_clr,
				'account_clr'     => $account_clr,
				'date_added'     => $result['date_added'],
				'view'       => $this->url->link('extension/newsletter_analytics/view', 'user_token=' . $this->session->data['user_token'] . '&mail_report_id=' . $result['mail_report_id'] . $url, true)
		
			);
		}



		///$data['delete'] = $this->url->link('extension/module/tmdnewsletter/delete','&status=1&user_token=' . $this->session->data['user_token'] . $url, true);

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



		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}
	
		if (isset($this->request->get['filter_date'])) {
			$url .= '&filter_date=' . urlencode(html_entity_decode($this->request->get['filter_date'], ENT_QUOTES, 'UTF-8'));
		}


		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$url = '';

		$pagination = new Pagination();
		$pagination->total = $mailreport_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/newsletter_analytics/view', 'user_token=' . $this->session->data['user_token'] . $url. '&mail_send_id=' . $mail_send_id . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($mailreport_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($mailreport_total - $this->config->get('config_limit_admin'))) ? $mailreport_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $mailreport_total, ceil($mailreport_total / $this->config->get('config_limit_admin')));

		$data['filter_email'] = $filter_email;
		$data['filter_date'] = $filter_date;

		$data['cancel'] = $this->url->link('extension/newsletter_analytics', 'user_token=' . $this->session->data['user_token'] , true);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/send_mail', $data));
	
	}

}
