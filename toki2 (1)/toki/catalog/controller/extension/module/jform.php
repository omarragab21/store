<?php
class ControllerExtensionModuleJform extends Controller {
	private $error = array();
	public function index($setting) {
		//$this->load->language('module/jform');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

		$data['heading_title'] = $setting['name'];

		$data['jform'] = $this->getFormSetting($setting['module_id']);
		//print_r();
		$jform_id = $data['jform']['jform_id'];
		//print_r($jform_id);
		$data['jform_description'] = $this->getFormDescription($jform_id);
		//print_r($data['jform_description']);
		$data['jform_field'] = $this->getFormField($jform_id);
		//$data['jform_field_description'] = $this->getFieldDescriptions($data['jform_field']['jform_field_id']);
		if($data['jform']['captcha']){
			$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'), $this->error);
		}else{
			$data['captcha'] = '';
		}
		return $this->load->view('extension/module/jform', $data);

	}
	
	protected function getFormSetting($module_id){
		$sql = "SELECT * FROM " . DB_PREFIX . "jform WHERE module_id='" . $module_id . "'";
		$query = $this->db->query($sql);
		return $query->row;
	}
	
	protected function getFormSettingByID($id){
		$sql = "SELECT * FROM " . DB_PREFIX . "jform WHERE jform_id='" . $id . "'";
		$query = $this->db->query($sql);
		return $query->row;
	}
	
	protected function getFormDescription($form_id){
		$sql = "SELECT * FROM " . DB_PREFIX . "jform_description WHERE jform_id='" . $form_id . "' AND language_id='" . (int)$this->config->get('config_language_id') . "'";
		$query = $this->db->query($sql);
		$field_description_data = array();
		foreach ($query->rows as $result) {
			$field_description_data = array(
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
				'required' => $field['required'],
				'width' => $field['width'],
				'field_description' => $field_description_data
			);
		}
		return $data_field;
	}
	
	protected function getFieldDescriptions($jform_field_id) {
		$field_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "jform_field_description WHERE jform_field_id = '" . (int)$jform_field_id . "'  AND language_id='" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $result) {
			$field_description_data = array(
				'title' => $result['title'],
				'text_error' => $result['text_error'],
				'text_help' => $result['text_help'],
				'place_holder' => $result['place_holder']
			);
		}

		return $field_description_data;
	}
	
	public function formSubmit(){
		$json = array();
		if(isset($this->request->post['jform_id'])){
			$jform_id = $this->request->post['jform_id'];
			$jform_description = $this->getFormDescription($jform_id);
			$jform = $this->getFormSettingByID($jform_id);
			
			$fields = $this->getFormField($jform_id);
			$content = '';
			$customer_email = $this->config->get('config_email');
			$attachment = array();
			foreach($fields as $field){
				$field_id = $field['jform_field_id'];
				if(isset($this->request->post['field' . $field_id]) || isset($this->request->files['field' . $field_id])){

					if($field['type'] == 'email' && $this->request->post['field' . $field_id]){
						$customer_email = $this->request->post['field' . $field_id];
						$content .= $field['field_description']['title'] . ': ' . $this->request->post['field' . $field_id] . '<br />';
					}elseif((($field['type'] == 'checkbox') || ($field['type'] == 'multiselect')) && is_array($this->request->post['field' . $field_id])){
						$content .= $field['field_description']['title'] . ': ';
						foreach($this->request->post['field' . $field_id] as $val){
							$content .= $val . '<br />';
						}
					}elseif($field['type'] == 'file'){
						$content .= $field['field_description']['title'] . ': ' . $this->request->files['field' . $field_id]['name'] . '<br />';
						$filename = $this->request->files['field' . $field_id]['name'];
						$file = token(32) . '.' . $filename;
						move_uploaded_file($this->request->files['field' . $field_id]['tmp_name'], DIR_UPLOAD . $file);
						$attachment[] = DIR_UPLOAD . $file;
						// Hide the uploaded file name so people can not link to it directly.
						$this->load->model('tool/upload');

						$this->model_tool_upload->addUpload($filename, $file);
					}else{
						$content .= $field['field_description']['title'] . ': ' . $this->request->post['field' . $field_id] . '<br />';
					}
				}
			}
			
			$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
			
			if(isset($jform['email'])){
				$mail->setTo($jform['email']);
			}else{
				$mail->setTo($this->config->get('config_email'));
			}
			$mail->setFrom($customer_email);
			$mail->setSender(html_entity_decode($customer_email, ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode($jform_description['admin_subject'], ENT_QUOTES, 'UTF-8'));
			if($attachment){
				foreach($attachment as $at){
					$mail->addAttachment($at);
				}
			}
			$mail->setHtml($content);
			$mail->send();
			
			if(isset($jform['respone']) && $jform['respone']){
				$mail->setTo($customer_email);
				$mail->setFrom($jform['email']);
				$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
				$mail->setSubject(html_entity_decode($jform_description['customer_subject'], ENT_QUOTES, 'UTF-8'));
				$mail->setHtml($jform_description['customer_body']);
				$mail->send();
			}
			$json['message'] = $jform_description['message'];
		}else{
			$json['message'] = 'error: Can not send!';
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function validate() {
		$json = array();
		if(isset($this->request->post['jform_id'])){
			$jform_id = $this->request->post['jform_id'];
			$jform_description = $this->getFormDescription($jform_id);
			$jform = $this->getFormSettingByID($jform_id);
			
			$fields = $this->getFormField($jform_id);
			//$content = '';
			foreach($fields as $field){
				$field_id = $field['jform_field_id'];
				if($field['type'] == 'email'){
					if (($field['required'] && isset($this->request->post['field' . $field_id])) && !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['field' . $field_id])) {
						$json['error'] = $field['field_description']['text_error'];
						$json['field_id'] = $field_id;
						break;
					} 
				}elseif(($field['type'] == 'text') || ($field['type'] == 'textarea') || ($field['type'] == 'date') || ($field['type'] == 'date-time')){
					if($field['required'] && utf8_strlen($this->request->post['field' . $field_id]) < 2){
						$json['error'] = $field['field_description']['text_error'];
						$json['field_id'] = $field_id;
						break;
					}
				}elseif($field['type'] == 'select' || $field['type'] == 'radio' || $field['type'] == 'multiselect' || $field['type'] == 'checkbox'){
					if($field['required'] && !isset($this->request->post['field' . $field_id])){
						$json['error'] = $field['field_description']['text_error'];
						$json['field_id'] = $field_id;
						break;
					}
				}elseif($field['type'] == 'file'){
					if($field['required'] && (empty($_FILES['field' . $field_id]['name']) && !is_file($_FILES['field' . $field_id]['tmp_name']))){
						$json['error'] = 'Please select file upload';
						$json['field_id'] = $field_id;
					}elseif(!empty($_FILES['field' . $field_id]['name']) && is_file($_FILES['field' . $field_id]['tmp_name'])){
						$check = $this->checkupload($_FILES['field' . $field_id],$jform['max_file_upload'],$jform['file_allow']);
						if(isset($check['error'])){
							$json['error'] = $check['error'];
							$json['field_id'] = $field_id;
						}
					}
				}
			}
			
		}
		
		// Captcha
		if ($this->config->get($this->config->get('config_captcha') . '_status') && $jform['captcha']) {
			$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

			if ($captcha) {
				$json['error_captcha'] = $captcha;
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	
	}
	
	public function checkupload($file, $max_file_size, $file_allow) {
		$this->load->language('tool/upload');

		$json = array();
		
		if (!empty($file['name']) && is_file($file['tmp_name'])) {
			// Sanitize the filename
			$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($file['name'], ENT_QUOTES, 'UTF-8')));

			// Validate the filename length
			if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 64)) {
				$json['error'] = $this->language->get('error_filename');
			}
			
			//check size of file upload
			//$max_file_size = 2000;
			if($file_allow){
				$filetypes = explode(",", $file_allow);
			}else{
				$extension_allowed = preg_replace('~\r?<br />~', "<br />", $this->config->get('config_file_ext_allowed'));

				$filetypes = explode("<br />", $extension_allowed);
			}
			
			$size_of_uploaded_file = $file["size"]/1024;
			if($size_of_uploaded_file > $max_file_size){
				$json['error'] = 'Size of file upload allow is ' . $max_file_size . 'M';
			}

			// Allowed file extension types
			$allowed = array();

			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}

			if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Allowed file mime types
			/*
			$allowed = array();

			$mime_allowed = preg_replace('~\r?<br />~', "<br />", $this->config->get('config_file_mime_allowed'));

			$filetypes = explode("<br />", $mime_allowed);

			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}

			if (!in_array($file['type'], $allowed)) {
				$json['error'] = $this->language->get('error_filetype');
			}
			*/

			// Check to see if any PHP files are trying to be uploaded
			$content = file_get_contents($file['tmp_name']);

			if (preg_match('/\<\?php/i', $content)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Return any upload error
			if ($file['error'] != UPLOAD_ERR_OK) {
				$json['error'] = $this->language->get('error_upload_' . $file['error']);
			}
		} else {
			$json['error'] = $this->language->get('error_upload');
		}
		return $json;
		/*
		if (!$json) {
			$file = token(32) . '.' . $filename;

			move_uploaded_file($file['tmp_name'], DIR_UPLOAD . $file);

			// Hide the uploaded file name so people can not link to it directly.
			$this->load->model('tool/upload');

			$json['code'] = $this->model_tool_upload->addUpload($filename, $file);

			$json['success'] = $this->language->get('text_upload');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		*/
	}
}