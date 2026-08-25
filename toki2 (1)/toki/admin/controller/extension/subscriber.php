<?php
require_once (DIR_SYSTEM . "library/tmdimportexport/PHPExcel.php");
class ControllerExtensionSubscriber extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/subscriber');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/subscriber');

		$this->getList();
	}


	public function delete() {
		$this->load->language('extension/subscriber');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/subscriber');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $subscriber_id) {
				$this->model_extension_subscriber->deleteSubscriber($subscriber_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}


			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/subscriber', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	

	protected function getList() {
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = null;
		}

		if (isset($this->request->get['filter_account'])) {
			$filter_account = $this->request->get['filter_account'];
		} else {
			$filter_account = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}


		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'subscriber_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_account'])) {
			$url .= '&filter_account=' . $this->request->get['filter_account'];
		}


		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}


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
			'href' => $this->url->link('extension/subscriber', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('extension/subscriber/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['copy'] = $this->url->link('extension/subscriber/copy', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/subscriber/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['import'] = $this->url->link('extension/subscriber/import', 'user_token=' . $this->session->data['user_token'] . $url,true );
		$data['export'] = $this->url->link('extension/subscriber/export', 'user_token=' . $this->session->data['user_token'] . $url,true );

		$data['subscribers'] = array();

		$filter_data = array(
			'filter_name'	  => $filter_name,
			'filter_email'	  => $filter_email,
			'filter_account'	  => $filter_account,
			'filter_status'   => $filter_status,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		);

		$this->load->model('tool/image');

		$subscriber_total = $this->model_extension_subscriber->getTotalSubscribers($filter_data);

		$results = $this->model_extension_subscriber->getSubscribers($filter_data);

		foreach ($results as $result) {
			 $status_clr =$result['status'];
			 $account_clr =$result['account'];

			if($result['status']==0){
				$status=$this->language->get('text_unverify');
			}
			if($result['status']==1){
				$status=$this->language->get('text_verified');
			}
			if($result['status']==2){
				$status=$this->language->get('text_unsubscribe');
			}
			if($result['status']==3){
				$status=$this->language->get('text_decline');
			}




			$data['subscribers'][] = array(
				'subscriber_id' => $result['subscriber_id'],
				'name'       => $result['name'],
				'email'      => $result['email'],
				'account'          => ($result['account'] ? $this->language->get('text_register') : $this->language->get('text_guest')),
				'ip_address'   => $result['ip_address'],
				'status'     => $status,
				'status_clr'     => $status_clr,
				'account_clr'     => $account_clr,
				'reason'     => $result['reason'],
				'date_added'     => $result['date_added'],
				'edit'       => $this->url->link('extension/subscriber/popupview', 'user_token=' . $this->session->data['user_token'] . '&subscriber_id=' . $result['subscriber_id'] . $url, true),
				'replymail'       => $this->url->link('extension/subscriber/popupreplymail', 'user_token=' . $this->session->data['user_token'] . '&subscriber_id=' . $result['subscriber_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_register'] = $this->language->get('text_register');
		$data['text_guest'] = $this->language->get('text_guest');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_editsubscriber'] = $this->language->get('text_editsubscriber');

		$data['column_image'] = $this->language->get('column_image');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_email'] = $this->language->get('column_email');
		$data['column_account'] = $this->language->get('column_account');
		$data['column_ip'] = $this->language->get('column_ip');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_date'] = $this->language->get('column_date');
		$data['column_store'] = $this->language->get('column_store');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_account'] = $this->language->get('entry_account');
		$data['entry_status'] = $this->language->get('entry_status');
		
		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');

		$data['user_token'] = $this->session->data['user_token'];

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

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_account'])) {
			$url .= '&filter_account=' . $this->request->get['filter_account'];
		}


		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}


		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('extension/subscriber', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_email'] = $this->url->link('extension/subscriber', 'user_token=' . $this->session->data['user_token'] . '&sort=email' . $url, true);
		$data['sort_account'] = $this->url->link('extension/subscriber', 'user_token=' . $this->session->data['user_token'] . '&sort=account' . $url, true);
		$data['sort_ip_address'] = $this->url->link('extension/subscriber', 'user_token=' . $this->session->data['user_token'] . '&sort=ip_address' . $url, true);
		$data['sort_status'] = $this->url->link('extension/subscriber', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		$data['sort_date_added'] = $this->url->link('extension/subscriber', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_account'])) {
			$url .= '&filter_account=' . $this->request->get['filter_account'];
		}


		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}


		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $subscriber_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/subscriber', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($subscriber_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($subscriber_total - $this->config->get('config_limit_admin'))) ? $subscriber_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $subscriber_total, ceil($subscriber_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_email'] = $filter_email;
		$data['filter_account'] = $filter_account;
		$data['filter_status'] = $filter_status;

		$data['sort'] = $sort;
		$data['order'] = $order;

		
		$data['statuss'] = array();

		$data['statuss'][] = array(
			'name' => $this->language->get('text_unverify'),
			'value' => '0',
		);

		$data['statuss'][] = array(
			'name' => $this->language->get('text_verified'),
			'value' => '1',
		);
		$data['statuss'][] = array(
			'name' => $this->language->get('text_unsubscribe'),
			'value' => '2',
		);
		$data['statuss'][] = array(
			'name' => $this->language->get('text_decline'),
			'value' => '3',
		);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/subscriber_list', $data));
	}

	public function popupview() {
			$this->load->model('extension/subscriber');
	
		$this->load->language('extension/subscriber');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['subscriber_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_plus'] = $this->language->get('text_plus');
		$data['text_minus'] = $this->language->get('text_minus');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_option'] = $this->language->get('text_option');
		$data['text_option_value'] = $this->language->get('text_option_value');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_percent'] = $this->language->get('text_percent');
		$data['text_amount'] = $this->language->get('text_amount');
		$data['text_editsubscriber'] = $this->language->get('text_editsubscriber');
		$data['text_name'] = $this->language->get('text_name');
		$data['text_email'] = $this->language->get('text_email');
		$data['text_acct'] = $this->language->get('text_acct');
		$data['text_status'] = $this->language->get('text_status');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['user_token'] = $this->session->data['user_token'];
		
	
	
		if (isset($this->request->get['subscriber_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$subscriber_info = $this->model_extension_subscriber->getSubscriber($this->request->get['subscriber_id']);
		}

		if (isset($this->request->get['subscriber_id'])) {
			$data['subscriberid'] = $this->request->get['subscriber_id'];
		} else {
			$data['subscriberid'] = '0';
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($subscriber_info)) {
			$data['name'] = $subscriber_info['name'];
		} else {
			$data['name'] = '';
		}
		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($subscriber_info)) {
			$data['email'] = $subscriber_info['email'];
		} else {
			$data['email'] = '';
		}
		if (isset($this->request->post['account'])) {
			$data['account'] = $this->request->post['account'];
		} elseif (!empty($subscriber_info)) {
			$data['account'] = $subscriber_info['account'];
		} else {
			$data['account'] = '';
		}
	
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($subscriber_info)) {
			$data['status'] = $subscriber_info['status'];
		} else {
			$data['status'] = '';
		}

		$data['accounts'] = array();

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
			'name' => $this->language->get('text_unverify'),
			'value' => '0',
		);

		$data['statuss'][] = array(
			'name' => $this->language->get('text_verified'),
			'value' => '1',
		);
		$data['statuss'][] = array(
			'name' => $this->language->get('text_unsubscribe'),
			'value' => '2',
		);
		$data['statuss'][] = array(
			'name' => $this->language->get('text_decline'),
			'value' => '3',
		);

		$this->response->setOutput($this->load->view('extension/subscriber_form', $data));
	}

	public function edit() {
		$json = array();
		$this->load->model('extension/subscriber');
		$this->load->language('extension/subscriber');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
		
			 $this->model_extension_subscriber->editSubscriber($this->request->get['subscriber_id'], $this->request->post);	
	 		 $json['success']= $this->language->get('text_successedit');	
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	  }
	}



public function popupreplymail() {
			$this->load->model('extension/subscriber');
	
		$this->load->language('extension/subscriber');

		$data['entry_subject'] = $this->language->get('entry_subject');
		$data['entry_message'] = $this->language->get('entry_message');
		$data['button_send'] = $this->language->get('button_send');

			
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['user_token'] = $this->session->data['user_token'];
		
	
	
		$subscriberr_info = $this->model_extension_subscriber->getSubscriber($this->request->get['subscriber_id']);
		

		if (isset($this->request->get['subscriber_id'])) {
			$data['subscriberid'] = $this->request->get['subscriber_id'];
		} else {
			$data['subscriberid'] = '0';
		}

		if (!empty($subscriberr_info['email'])) {
			$data['email'] = $subscriberr_info['email'];
		} else {
			$data['email'] = '';
		}
		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($subscriberr_info)) {
			$data['email'] = $subscriberr_info['email'];
		} else {
			$data['email'] = '';
		}

	
		$this->response->setOutput($this->load->view('extension/subscriber_replymail', $data));
	}


	public function sendreplymail() {
		$json = array();
		$this->load->model('extension/subscriber');
		$this->load->language('extension/subscriber');

		$data['entry_subject'] = $this->language->get('entry_subject');
		$data['entry_message'] = $this->language->get('entry_message');
		$data['button_send'] = $this->language->get('button_send');


		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
		

			if(empty($this->request->post['subject'])) {
			$json['error']= $this->language->get('error_subject');	
			}

			if(empty($json['error'])) {

			 $this->model_extension_subscriber->sendSubscribermail($this->request->get['subscriber_id'], $this->request->post);	
	 		 $json['success']= $this->language->get('text_successmail');	
			}		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	  }
	}

	public function export() {
		
		$this->load->language('extension/subscriber');
		$this->load->model('extension/subscriber');
		
		$data['subscribers']=array();
		$filter_data = array();
		
		$results = $this->model_extension_subscriber->getSubscribers($filter_data);

		$objPHPExcel = new PHPExcel();

		$objPHPExcel->getProperties()->setCreator("Subscribers");
		$objPHPExcel->getProperties()->setLastModifiedBy("Subscribers");
		$objPHPExcel->getProperties()->setTitle("Office Excel");
		$objPHPExcel->getProperties()->setSubject("Office Excel");
		$objPHPExcel->getProperties()->setDescription("Office Excel");
		$objPHPExcel->setActiveSheetIndex(0);
		
		$i=1;
		
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, 'subscriber_id');
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, 'Name');
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, 'Email');
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, 'Account');
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, 'Status');
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$i, 'Ip Address');
			

		foreach ($results as $result) {
		$i++;
			$account  = ($result['account'] ? $this->language->get('text_register') : $this->language->get('text_guest'));

			if($result['status']==0){
				$status=$this->language->get('text_unverify');
			}
			if($result['status']==1){
				$status=$this->language->get('text_verified');
			}
			if($result['status']==2){
				$status=$this->language->get('text_unsubscribe');
			}
			if($result['status']==3){
				$status=$this->language->get('text_decline');
			}

			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, $result['subscriber_id']);
			$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, $result['name']);
			$objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, $result['email']);
			$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, $account);
			$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, $status);
			$objPHPExcel->getActiveSheet()->SetCellValue('F'.$i, $result['ip_address']);
		}
		
		/* color setup */
		$al='F';
		
		for($col = 'A'; $col != $al; $col++) {
		   $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth(20);
		}
		$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(20);	
		$objPHPExcel->getActiveSheet()
		->getStyle('A1:'.$al.'1')
		->getFill()
		->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
		->getStartColor()
		->setARGB('02057D');
		
		$styleArray = array(
			'font'  => array(
			'bold'  => true,
			'color' => array('rgb' => 'FFFFFF'),
			'size'  => 9,
			'name'  => 'Verdana'
		));
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.$al.'1')->applyFromArray($styleArray);

		/* color setup */
		
		$excel='Excel5';	
		$filename = 'Subscribers.xls';
		$objPHPExcel->getActiveSheet()->setTitle('Subscribers Report');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $excel);
		$objWriter->save($filename );
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		$objWriter->save('php://output');
		unlink($filename);
	}

	public function import() {
	$this->load->language('extension/subscriber');
		$this->load->model('extension/subscriber');
		if(isset($this->session->data['token'])){
			$tokenchage = 'token=' . $this->session->data['token'];
		} else {
			$tokenchage = 'user_token=' . $this->session->data['user_token'];
		}

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->user->hasPermission('modify', 'extension/subscriber')) {
			
				
				if (is_uploaded_file($this->request->files['import']['tmp_name'])) {
					$content = file_get_contents($this->request->files['import']['tmp_name']);
				} else {
					$content = false;
				}

				
				if ($content) {
		////////////////////////// Started Import work  //////////////
				try {
					$objPHPExcel = PHPExcel_IOFactory::load($this->request->files['import']['tmp_name']);
				} catch(Exception $e) {
					die('Error loading file "'.pathinfo($this->path.$files,PATHINFO_BASENAME).'": '.$e->getMessage());
				}
		/*	@ get a file data into $sheetDatas variable */
				$sheetDatas = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
				

		/*	@ $i variable for getting data. in first iteration of loop we get size and color name of product */
				$i=0;
		/*
		@ arranging the data according to our need
		*/
				foreach($sheetDatas as $sheetData){
					
					if($i!=0) {

						$subscriber_id = $sheetData['A'];
						$name  		= $sheetData['B'];
						$email   	= $sheetData['C'];
						$account		= $sheetData['D'];
						$status		= $sheetData['E'];
						$ip_address		= $sheetData['F'];
		
					//account
					if($account=='Register'){
						$account=1;
					}
					elseif($account=='Guest'){
						$account=0;
					}
					//status
					if($status=='Un-verify'){
						$status=0;
					}
					elseif($status=='Verified'){
						$status=1;
					}
					elseif($status=='Unsubscribe'){
						$status=2;
					}
					elseif($status=='Decline'){
						$status=3;
					}	

						$data='';

						$data=array(
							'subscriber_id'=>$subscriber_id,
							'name'=>$name,
							'email'=>$email,
							'account'=>$account,
							'status'=>$status,
							'ip_address'=>$ip_address,
						);

						$this->model_extension_subscriber->addImport($data);
	
					}
					
					$i++;
				}
				
					
				$this->session->data['success'] = $this->language->get('text_importsuccess');

		////////////////////////// Started Import work  //////////////
				$this->response->redirect($this->url->link('extension/subscriber', $tokenchage , 'SSL'));
				} else {
					$this->error['warning'] = $this->language->get('error_emptyfile');
				}
			
				
			
		}
		$this->getList();

	}



	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/subscriber')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateCopy() {
		if (!$this->user->hasPermission('modify', 'extension/subscriber')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->load->model('extension/subscriber');
			$this->load->model('catalog/option');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_model'])) {
				$filter_model = $this->request->get['filter_model'];
			} else {
				$filter_model = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_model' => $filter_model,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_extension_subscriber->getSubscribers($filter_data);

			foreach ($results as $result) {
				$option_data = array();

				$product_options = $this->model_extension_subscriber->getProductOptions($result['subscriber_id']);

				foreach ($product_options as $product_option) {
					$option_info = $this->model_catalog_option->getOption($product_option['option_id']);

					if ($option_info) {
						$product_option_value_data = array();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);

							if ($option_value_info) {
								$product_option_value_data[] = array(
									'product_option_value_id' => $product_option_value['product_option_value_id'],
									'option_value_id'         => $product_option_value['option_value_id'],
									'name'                    => $option_value_info['name'],
									'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
									'price_prefix'            => $product_option_value['price_prefix']
								);
							}
						}

						$option_data[] = array(
							'product_option_id'    => $product_option['product_option_id'],
							'product_option_value' => $product_option_value_data,
							'option_id'            => $product_option['option_id'],
							'name'                 => $option_info['name'],
							'type'                 => $option_info['type'],
							'value'                => $product_option['value'],
							'required'             => $product_option['required']
						);
					}
				}

				$json[] = array(
					'subscriber_id' => $result['subscriber_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'model'      => $result['model'],
					'option'     => $option_data,
					'price'      => $result['price']
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
