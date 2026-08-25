<?php
class ControllerExtensionModuleJform extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/jform');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$data_module['name'] = $this->request->post['name'];
			$data_module['status'] = $this->request->post['status'];
			$data_module['module_id'] = 0;
			if (!isset($this->request->get['module_id'])) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "module` SET `name` = '" . $this->db->escape($data_module['name']) . "', `code` = '" . $this->db->escape('jform') . "', `setting` = '" . $this->db->escape(json_encode($data_module)) . "'");
				$module_id = $this->db->getLastId();
				$data_module['module_id'] = $module_id;
				$this->model_setting_module->editModule($module_id, $data_module);
				//$module_id = $this->model_setting_module->addModule('jform', $data_module);
				$this->addJform($this->request->post,$module_id);
			} else {
				$data_module['module_id'] = $this->request->get['module_id'];
				$this->model_setting_module->editModule($this->request->get['module_id'], $data_module);
				$this->updateJform($this->request->post, $this->request->get['module_id']);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_name'] = $this->language->get('entry_name');

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
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/jform', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/jform', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/jform', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/jform', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}
		
		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}
		
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '1';
		}
		
		//cuogn tu day
		//check table to create
		$jform = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "jform` (`jform_id` int(11) NOT NULL AUTO_INCREMENT,`module_id` int(11) NOT NULL,`information_id` int(11) NOT NULL,`email` text COLLATE utf8_bin NOT NULL,`respone` int(1) NOT NULL DEFAULT '0',`max_file_upload` int(11) NOT NULL,`file_allow` varchar(500) COLLATE utf8_bin NOT NULL,`form_width` int(2) NOT NULL DEFAULT '12',`captcha` int(1) NOT NULL DEFAULT '1', `link_redirect` text COLLATE utf8_bin NOT NULL, PRIMARY KEY (`jform_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;";
		$this->db->query($jform);
		
		$jform_description = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "jform_description` (`jform_id` int(11) NOT NULL,`language_id` int(11) NOT NULL,`heading_title` varchar(500) COLLATE utf8_bin NOT NULL,`admin_subject` varchar(500) COLLATE utf8_bin NOT NULL,`admin_body` text COLLATE utf8_bin NOT NULL,`message` text COLLATE utf8_bin NOT NULL,`send_button` varchar(500) COLLATE utf8_bin NOT NULL, `customer_subject` varchar(500) COLLATE utf8_bin NOT NULL,`customer_body` text COLLATE utf8_bin NOT NULL,PRIMARY KEY (`jform_id`,`language_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
		$this->db->query($jform_description);
		$jform_field = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "jform_field` (`jform_field_id` int(11) NOT NULL AUTO_INCREMENT,`jform_id` int(11) NOT NULL,`field_type` varchar(50) COLLATE utf8_bin NOT NULL,`field_value` text COLLATE utf8_bin NOT NULL,`sort_order` int(11) NOT NULL,`required` int(1) NOT NULL DEFAULT '1',`width` int(11) NOT NULL DEFAULT '12', PRIMARY KEY (`jform_field_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;";
		$this->db->query($jform_field);
		$jform_field_description = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "jform_field_description` (`jform_field_id` int(11) NOT NULL,`language_id` int(11) NOT NULL,`title` varchar(500) COLLATE utf8_bin NOT NULL,`place_holder` varchar(500) COLLATE utf8_bin NOT NULL,`text_error` text COLLATE utf8_bin NOT NULL,`text_help` text COLLATE utf8_bin NOT NULL,PRIMARY KEY (`jform_field_id`,`language_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
		$this->db->query($jform_field_description);
		//
		
		//$data['text_form'] = !isset($this->request->get['custom_field_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_choose'] = $this->language->get('text_choose');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_multiselect'] = $this->language->get('text_multiselect');
		$data['text_radio'] = $this->language->get('text_radio');
		$data['text_checkbox'] = $this->language->get('text_checkbox');
		$data['text_input'] = $this->language->get('text_input');
		$data['text_text'] = $this->language->get('text_text');
		$data['text_textarea'] = $this->language->get('text_textarea');
		$data['text_file'] = $this->language->get('text_file');
		$data['text_date'] = $this->language->get('text_date');
		$data['text_datetime'] = $this->language->get('text_datetime');
		$data['text_time'] = $this->language->get('text_time');
		
		if(isset($this->request->get['module_id'])){
			$module_id = $this->request->get['module_id'];
		}else{
			$module_id = 0;
		}
		
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		$data['cols'] = array(
						'1' => 'col-sm-1 - 1/12',
						'2' => 'col-sm-2 - 1/6',
						'3' => 'col-sm-3 - 1/4',
						'4' => 'col-sm-4 - 1/3',
						'5' => 'col-sm-5 - 5/12',
						'6' => 'col-sm-6 - 1/2',
						'7' => 'col-sm-7 - 7/12',
						'8' => 'col-sm-8 - 2/3',
						'9' => 'col-sm-9 - 9/12',
						'10' => 'col-sm-10 - 5/6',
						'11' => 'col-sm-11 - 11/12',
						'12' => 'col-sm-12 - Full width',
					);
		$this->load->model('catalog/information');
		$data['informations'] = $this->model_catalog_information->getInformations();
	
		$form_setting = $this->getFormSetting($module_id);
		if($form_setting){
			$data['jform_id'] = $form_setting['jform_id'];
			$data['email'] = $form_setting['email'];
			$data['max_file_upload'] = $form_setting['max_file_upload'];
			$data['file_allow'] = $form_setting['file_allow'];
			$data['form_width'] = $form_setting['form_width'];
			$data['captcha'] = $form_setting['captcha'];
			$data['respone'] = $form_setting['respone'];
			$data['jform_description'] = $this->getFormDescription($form_setting['jform_id']);
			$data['fields'] = $this->getFormField($form_setting['jform_id']);
			$data['information_id'] = $form_setting['information_id'];
		}else{
			$data['jform_id'] = '0';
			$data['email'] = '';
			$data['max_file_upload'] = '2000';
			$data['file_allow'] = 'pdf,png,gif,zip,jpg';
			$data['form_width'] = '12';
			$data['captcha'] = '1';
			$data['respone'] = '0';
			$data['jform_description'] =  array();
			$data['fields'] = array();
			$data['information_id'] = 0;
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/jform', $data));
	}

	protected function getFormSetting($module_id){
		$sql = "SELECT * FROM " . DB_PREFIX . "jform WHERE module_id='" . $module_id . "'";
		$query = $this->db->query($sql);
		return $query->row;
	}
	
	protected function getFormDescription($form_id){
		$sql = "SELECT * FROM " . DB_PREFIX . "jform_description WHERE jform_id='" . $form_id . "'";
		$query = $this->db->query($sql);
		$field_description_data = array();
		foreach ($query->rows as $result) {
			$field_description_data[$result['language_id']] = array(
				'heading_title'             => $result['heading_title'],
				'admin_subject'     => $result['admin_subject'],
				'admin_body'     => $result['admin_body'],
				'message'     => $result['message'],
				'send_button'     => $result['send_button'],
				'customer_subject' => $result['customer_subject'],
				'customer_body'      => $result['customer_body']
			);
		}
		
		return $field_description_data;
	}
	
	protected function getFormField($form_id){
		$sql = "SELECT * FROM " . DB_PREFIX . "jform_field WHERE jform_id='" . $form_id . "' ORDER BY sort_order";
		$query = $this->db->query($sql);
		$data_field = array();
		foreach($query->rows as $field){
			$field_description_data = $this->getFieldDescriptions($field['jform_field_id']);
			$data_field[] = array(
				'jform_field_id' => $field['jform_field_id'],
				'value' => $field['field_value'],
				'type' => $field['field_type'],
				'require' => $field['required'],
				'sort_order' => $field['sort_order'],
				'width' => $field['width'],
				'field_description' => $field_description_data
			);
		}
		return $data_field;
	}
	
	protected function getFieldDescriptions($jform_field_id) {
		$field_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "jform_field_description WHERE jform_field_id = '" . (int)$jform_field_id . "'");

		foreach ($query->rows as $result) {
			$field_description_data[$result['language_id']] = array(
				'title' => $result['title'],
				'text_error' => $result['text_error'],
				'text_help' => $result['text_help'],
				'place_holder' => $result['place_holder']
			);
		}

		return $field_description_data;
	}
	
	protected function addJform($data,$module_id){
		$this->db->query("INSERT INTO " . DB_PREFIX . "jform SET module_id ='" . (int)$module_id . "', information_id ='" . (int)$data['information_id'] . "', email ='". $data['email'] . "', max_file_upload ='". (int)$data['max_file_upload']. "', file_allow = '". $data['file_allow']. "', respone = '". (int)$data['respone']. "', captcha = '". (int)$data['captcha']. "', form_width = '". (int)$data['form_width']. "'");
		$jform_id = $this->db->getLastId();
		if($data['jform_description']){
			foreach($data['jform_description'] as $language_id => $value){
				$this->db->query("INSERT INTO " . DB_PREFIX . "jform_description SET jform_id ='" . (int)$jform_id . "', language_id ='". $language_id . "', heading_title ='". $value['title']. "', admin_subject = '". $value['admin_subject']. "', admin_body = '". $this->db->escape($value['admin_body']). "', message = '". $value['message']. "', send_button = '". $value['send_button']. "', customer_subject = '". $value['customer_subject']. "', customer_body = '". $this->db->escape($value['customer_body']). "'");
			}
		}
		
		if($data['field']){	
			//print_r($data['field']);
			foreach($data['field'] as $field){
				$this->db->query("INSERT INTO " . DB_PREFIX . "jform_field SET jform_id ='" . (int)$jform_id . "', required ='". (isset($field['require']) ? (int)$field['require'] : 0) . "', field_type ='". $field['type'] . "', field_value = '". $field['value']. "', sort_order = '". $field['sort_order']. "', width = '". $field['width']. "'");
				$jform_field_id = $this->db->getLastId();
				if($field['field_description']){
					foreach($field['field_description'] as $language_id => $val)
						$this->db->query("INSERT INTO " . DB_PREFIX . "jform_field_description SET jform_field_id ='" . (int)$jform_field_id . "', language_id ='". $language_id . "', text_error ='". $val['text_error']. "', title = '". $val['title']. "'");
				}
			}
		}
	}
	
	protected function updateJform($data,$module_id){
		$this->db->query("UPDATE " . DB_PREFIX . "jform SET module_id ='" . (int)$module_id . "', information_id ='" . (int)$data['information_id'] . "', email ='". $data['email'] . "', max_file_upload ='". (int)$data['max_file_upload']. "', file_allow = '". $data['file_allow']. "', respone = '". (int)$data['respone']. "', captcha = '". (int)$data['captcha']. "', form_width = '". (int)$data['form_width']. "' WHERE jform_id='". (int)$data['jform_id'] . "'");
		if($data['jform_description']){
			$this->db->query("DELETE FROM " . DB_PREFIX . "jform_description WHERE jform_id='" . $data['jform_id'] . "'");
			foreach($data['jform_description'] as $language_id => $value){
				$this->db->query("INSERT INTO " . DB_PREFIX . "jform_description SET jform_id ='" . (int)$data['jform_id'] . "', language_id ='". $language_id . "', heading_title ='". $value['title']. "', admin_subject = '". $value['admin_subject']. "', admin_body = '". $this->db->escape($value['admin_body']). "', message = '". $value['message']. "', send_button = '". $value['send_button']. "', customer_subject = '". $value['customer_subject']. "', customer_body = '". $this->db->escape($value['customer_body']). "'");
			}
		}
		$this->db->query("DELETE FROM " . DB_PREFIX . "jform_field WHERE jform_id='" . $data['jform_id'] . "'");
		if($data['field']){
			foreach($data['field'] as $field){
				$this->db->query("INSERT INTO " . DB_PREFIX . "jform_field SET jform_id ='" . (int)$data['jform_id'] . "', required ='". (isset($field['require']) ? (int)$field['require'] : 0) . "', field_type ='". $field['type'] . "', field_value = '". $field['value']. "', sort_order = '". $field['sort_order']. "', width = '". $field['width']. "'");
				$jform_field_id = $this->db->getLastId();
				if($field['field_description']){
					$this->db->query("DELETE FROM " . DB_PREFIX . "jform_field_description WHERE jform_field_id='" . $field['jform_field_id'] . "'");
					foreach($field['field_description'] as $language_id => $val)
						$this->db->query("INSERT INTO " . DB_PREFIX . "jform_field_description SET jform_field_id ='" . (int)$jform_field_id . "', language_id ='". $language_id . "', text_error ='". $val['text_error']. "', title = '". $val['title']. "'");
				}
			}
		}
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/jform')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if (!$this->request->post['name']) {
			$this->error['name'] = 'Please fill in module name';
		}
		return !$this->error;
	}
}