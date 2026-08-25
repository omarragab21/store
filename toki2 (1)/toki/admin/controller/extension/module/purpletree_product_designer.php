<?php
class ControllerExtensionModulePurpletreeProductDesigner extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/purpletree_product_designer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')){
			if($this->validate()) {
						$this->load->model('extension/purpletree_product_designer/install');
						 $this->model_extension_purpletree_product_designer_install->CreateTables();
						 $this->model_extension_purpletree_product_designer_install->addColumns();
						 $this->model_extension_purpletree_product_designer_install->addFontAwsomeicons();
			
				 $this->load->model('extension/purpletree_product_designer/clipart');
				 $this->model_extension_purpletree_product_designer_clipart->updateDatabase();
				
				if(!empty($this->request->post['module_purpletree_product_designer_text'])){
					foreach($this->request->post['module_purpletree_product_designer_text'] as $xkey=>$xvalue){
					$this->request->post['module_purpletree_product_designer_text'][$xkey]=array(
					'pts_min'=>(int)$xvalue['pts_min'],
					'pts_max'=>(int)$xvalue['pts_max'],
					'pts_price'=>(float)$xvalue['pts_price'],
					);	
					}
				}
				if(isset($this->request->post['module_purpletree_product_designer_text'])) {
				$design_post_value=$this->request->post['module_purpletree_product_designer_text'];
				if(!empty($design_post_value)){
				foreach($design_post_value as $kkey=>$vvlue){
					if($vvlue['pts_min']==0 && $vvlue['pts_max']==0 && $vvlue['pts_price']==0){
						unset($this->request->post['module_purpletree_product_designer_text'][$kkey]);
					}
				}
				}
				}
			 if($this->request->post['module_purpletree_product_designer_v_t']==0 || !$this->config->get('module_purpletree_product_designer_status') || $this->request->post['module_purpletree_product_designer_p_d'] != $this->config->get('module_purpletree_product_designer_p_d')){
				$module	    	= 'oc_purpletree_customproductdesigner';

				if($_SERVER['HTTP_HOST'] == 'localhost') {
					$domain = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
				
				} else {
					$domain = 'http://'.$_SERVER['HTTP_HOST'];
				} 
				$valuee = $this->request->post['module_purpletree_product_designer_p_d'];
				 $ip_address = $this->get_client_ip();
				$url = "https://www.process.purpletreesoftware.com/occheckdata.php";
				$handle=curl_init($url);					
				curl_setopt($handle, CURLOPT_VERBOSE, true);
				curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($handle, CURLOPT_POSTFIELDS,
							"process_data=$valuee&domain_name=$domain&ip_address=$ip_address&module_name=$module");
				$result = curl_exec($handle);
				$result1 = json_decode($result);
				if(curl_error($handle))
				{
					echo 'error';
					die;
				}
				$ip_a = $_SERVER['HTTP_HOST'];
				if ($result1->status == 'success') {
					if (preg_match('(localhost|demo|test)',$domain)) {
						$str = 'qtriangle.in';
						$this->request->post['module_purpletree_product_designer_e_t'] = md5($str);
						$this->request->post['module_purpletree_product_designer_l_v_t']=0;
					} elseif(str_replace(array(':', '.'), '', $ip_a)) {
						if(is_numeric($ip_a)){
							$str = 'qtriangle.in';
							$this->request->post['module_purpletree_product_designer_e_t'] = md5($str);
							$this->request->post['module_purpletree_product_designer_l_v_t']=0;
						}
					}  else {
						$this->request->post['module_purpletree_product_designer_e_t'] = md5($domain);
						$this->request->post['module_purpletree_product_designer_l_v_t']=1;
					}
					$this->request->post['module_purpletree_product_designer_v_t']=1;
					if (isset($this->request->post['module_purpletree_product_designer_no_of_option'])) {
					$this->model_extension_purpletree_product_designer_install->gettotalPDFoption($this->request->post['module_purpletree_product_designer_no_of_option']);
				} 
					$this->model_setting_setting->editSetting('module_purpletree_product_designer', $this->request->post);

					$this->session->data['success'] = $this->language->get('text_product_designer_success');
				 } else {
					$this->session->data['warning'] = $this->language->get('text_product_designer_license_error');
				} 
			 } else {
				 if (isset($this->request->post['module_purpletree_product_designer_no_of_option'])) {
			$this->model_extension_purpletree_product_designer_install->gettotalPDFoption($this->request->post['module_purpletree_product_designer_no_of_option']);
		} 
				$this->model_setting_setting->editSetting('module_purpletree_product_designer', $this->request->post);

				$this->session->data['success'] = $this->language->get('text_product_designer_success');
			}  
			
			$this->response->redirect($this->url->link('extension/module/purpletree_product_designer', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		
			} else {
				$this->error['warning'] = $this->language->get('text_product_designer_license_error');//'Chek cthe form carefyully';
			}
	}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_status'] = $this->language->get('entry_status');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['text_license'] = $this->language->get('text_license');
		$data['text_setting'] = $this->language->get('text_setting');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} elseif(isset($this->session->data['warning'])){ 
			$data['error_warning'] = $this->session->data['warning'];
			unset($this->session->data['warning']);
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}
		
		if (isset($this->error['design_layer_error'])) {
			$data['error_warning'] = $this->error['design_layer_error'];
		} else {
			$data['error_warning'] = '';
		}
		if(isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/purpletree_product_designer', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/purpletree_product_designer', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}
		$data['enter_product_designer_key'] = $this->language->get('change_product_designer_license_key');
		if(null === $this->config->get('module_purpletree_product_designer_p_d') || $this->config->get('module_purpletree_product_designer_p_d') == '') {
			$data['enter_product_designer_key'] = $this->language->get('enter_product_designer_key');
		}
		$data['button_get_product_designer_license'] = $this->language->get('change_product_designer_license_key');
		if(null === $this->config->get('module_purpletree_product_designer_p_d') || $this->config->get('module_purpletree_product_designer_p_d') == '') {
			$data['button_get_product_designer_license'] = $this->language->get('button_get_product_designer_license');
		}
		$data['entry_product_designer_order_id'] = $this->language->get('entry_product_designer_order_id');
		$data['action'] = $this->url->link('extension/module/purpletree_product_designer', 'user_token=' . $this->session->data['user_token'], true);
		$data['entry_product_designer_email_id'] = $this->language->get('entry_product_designer_email_id');
		$data['button_product_designer_submit'] = $this->language->get('button_product_designer_submit');
		$data['please_product_designer_wait'] = $this->language->get('please_product_designer_wait');
		$data['dont_have_product_designer_lisence_key'] = $this->language->get('dont_have_product_designer_lisence_key');
		$data['ok_button'] = $this->language->get("Ok");
		$data['error_product_designer_order_id'] = $this->language->get('error_product_designer_order_id');
		$data['error_product_designer_email_id'] = $this->language->get('error_product_designer_email_id');
		
		$data['entry_watermark'] = $this->language->get('entry_watermark');
		$data['entry_watermark_text'] = $this->language->get('entry_watermark_text');
		$data['layer_options_text'] = $this->language->get('layer_options_text');
		$data['min_layer_text'] = $this->language->get('min_layer_text');
		$data['max_layer_text'] = $this->language->get('max_layer_text');
		$data['price_text'] = $this->language->get('price_text');
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_purpletree_product_designer_wm_status'])) {
			$data['module_purpletree_product_designer_wm_status'] = $this->request->post['module_purpletree_product_designer_wm_status'];
		} else {
			$data['module_purpletree_product_designer_wm_status'] = $this->config->get('module_purpletree_product_designer_wm_status');
		}
		
		if (isset($this->request->post['module_purpletree_product_designer_template'])) {
			$data['module_purpletree_product_designer_template'] = $this->request->post['module_purpletree_product_designer_template'];
		} else {
			$data['module_purpletree_product_designer_template'] = $this->config->get('module_purpletree_product_designer_template');
		}
		
		if (isset($this->request->post['module_purpletree_product_designer_buy_design'])) {
			$data['module_purpletree_product_designer_buy_design'] = $this->request->post['module_purpletree_product_designer_buy_design'];
		} else {
			$data['module_purpletree_product_designer_buy_design'] = $this->config->get('module_purpletree_product_designer_buy_design');
		}
		
		$this->load->model('tool/image');
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		if (isset($this->request->post['module_purpletree_product_designer_help_img'])) {
			$data['module_purpletree_product_designer_help_img'] = $this->request->post['module_purpletree_product_designer_help_img'];
			$help_image = $this->config->get('module_purpletree_product_designer_help_img');
			if($help_image){
			$data['thumb'] = $this->model_tool_image->resize($help_image, 100, 100);
			}
		} else {
			$data['module_purpletree_product_designer_help_img'] = $this->config->get('module_purpletree_product_designer_help_img');
			$help_image = $this->config->get('module_purpletree_product_designer_help_img');
			if($help_image){
			$data['thumb'] = $this->model_tool_image->resize($help_image, 100, 100);
			}
			
		}
		
		if (isset($this->request->post['module_purpletree_product_designer_no_of_option'])) {
			$data['module_purpletree_product_designer_no_of_option'] = $this->request->post['module_purpletree_product_designer_no_of_option'];
		} else {
			$data['module_purpletree_product_designer_no_of_option'] = $this->config->get('module_purpletree_product_designer_no_of_option');
		}
		if (isset($this->request->post['module_purpletree_product_designer_pdfproduct'])) {
			$data['module_purpletree_product_designer_pdfproduct'] = $this->request->post['module_purpletree_product_designer_pdfproduct'];
		} else {
			$data['module_purpletree_product_designer_pdfproduct'] = $this->config->get('module_purpletree_product_designer_pdfproduct');
		}

		if (isset($this->request->post['module_purpletree_product_designer_cmykcolorpick'])) {
			$data['module_purpletree_product_designer_cmykcolorpick'] = $this->request->post['module_purpletree_product_designer_cmykcolorpick'];
		} else {
			$data['module_purpletree_product_designer_cmykcolorpick'] = $this->config->get('module_purpletree_product_designer_cmykcolorpick');
		}
		
		if (isset($this->request->post['module_purpletree_product_designer_wm_text'])) {
			$data['module_purpletree_product_designer_wm_text'] = $this->request->post['module_purpletree_product_designer_wm_text'];
		} else if ($this->config->get('module_purpletree_product_designer_wm_text')){
			$data['module_purpletree_product_designer_wm_text'] = $this->config->get('module_purpletree_product_designer_wm_text');
		}else {
			$data['module_purpletree_product_designer_wm_text'] = 'Sample';
		}			
		if (isset($this->request->post['module_purpletree_product_designer_status'])) {
			$data['module_purpletree_product_designer_status'] = $this->request->post['module_purpletree_product_designer_status'];
		}else if ($this->config->get('module_purpletree_product_designer_status')){
			$data['module_purpletree_product_designer_status'] = $this->config->get('module_purpletree_product_designer_status');
		}else {
			$data['module_purpletree_product_designer_status'] = '';
		}
		if (isset($this->request->post['module_purpletree_product_designer_bootstrapjs'])) {
			$data['module_purpletree_product_designer_bootstrapjs'] = $this->request->post['module_purpletree_product_designer_bootstrapjs'];
		} else {
			$data['module_purpletree_product_designer_bootstrapjs'] = $this->config->get('module_purpletree_product_designer_bootstrapjs');
		}
		if (isset($this->request->post['module_purpletree_product_designer_jquery'])) {
			$data['module_purpletree_product_designer_jquery'] = $this->request->post['module_purpletree_product_designer_jquery'];
		} else {
			$data['module_purpletree_product_designer_jquery'] = $this->config->get('module_purpletree_product_designer_jquery');
		}
		if (isset($this->request->post['module_purpletree_product_designer_jquery_ui'])) {
			$data['module_purpletree_product_designer_jquery_ui'] = $this->request->post['module_purpletree_product_designer_jquery_ui'];
		} else {
			$data['module_purpletree_product_designer_jquery_ui'] = $this->config->get('module_purpletree_product_designer_jquery_ui');
		}
		if (isset($this->request->post['module_purpletree_product_designer_p_d'])) {
			$data['module_purpletree_product_designer_p_d'] = $this->request->post['module_purpletree_product_designer_p_d'];
		} else {
			$data['module_purpletree_product_designer_p_d'] = $this->config->get('module_purpletree_product_designer_p_d');
		}
		if (isset($this->request->post['module_purpletree_product_designer_v_t'])) {
			$data['module_purpletree_product_designer_v_t'] = 1;
		} else {
			$data['module_purpletree_product_designer_v_t'] = $this->config->get('module_purpletree_product_designer_v_t');
		}
		if (isset($this->request->post['module_purpletree_product_designer_l_v_t'])) {
			$data['module_purpletree_product_designer_l_v_t'] = 0;
		} else {
			$data['module_purpletree_product_designer_l_v_t'] = $this->config->get('module_purpletree_product_designer_l_v_t');
		}
		if (isset($this->request->post['module_purpletree_product_designer_e_t'])) {
			$str = 'qtriangle.in';
			$data['module_purpletree_product_designer_e_t'] = md5($str);
		} else {
			$data['module_purpletree_product_designer_e_t'] = $this->config->get('module_purpletree_product_designer_e_t');
		}
		
		if (isset($this->request->post['module_purpletree_product_designer_text'])) {
			$data['pts_design_options'] = $this->request->post['module_purpletree_product_designer_text'];
		} else {
			$data['pts_design_options'] = $this->config->get('module_purpletree_product_designer_text');
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/purpletree_product_designer', $data));
	}
	public function get_client_ip() {
		$ipaddress = '';
			if (getenv('HTTP_CLIENT_IP'))
				$ipaddress = getenv('HTTP_CLIENT_IP');
			else if(getenv('HTTP_X_FORWARDED_FOR'))
				$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
			else if(getenv('HTTP_X_FORWARDED'))
				$ipaddress = getenv('HTTP_X_FORWARDED');
			else if(getenv('HTTP_FORWARDED_FOR'))
				$ipaddress = getenv('HTTP_FORWARDED_FOR');
			else if(getenv('HTTP_FORWARDED'))
			   $ipaddress = getenv('HTTP_FORWARDED');
			else if(getenv('REMOTE_ADDR'))
				$ipaddress = getenv('REMOTE_ADDR');
			else
				$ipaddress = 'UNKNOWN';
			return $ipaddress;
	}
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/purpletree_product_designer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if(!isset($this->request->post['module_purpletree_product_designer_p_d']) || utf8_strlen($this->request->post['module_purpletree_product_designer_p_d']) < 1 ){
			$this->error['process_data'] = $this->language->get('text_product_designer_license_error');
		}
		
		if(!empty($this->request->post['module_purpletree_product_designer_text'])){
			foreach($this->request->post['module_purpletree_product_designer_text'] as $kkey=>$vvalue){

			if($vvalue['pts_min']=='' && $vvalue['pts_max']=='' && $vvalue['pts_price']==''){

			} else {
			if($vvalue['pts_min']=='' || $vvalue['pts_max']=='' || $vvalue['pts_price']==''){
				$this->error['design_layer_error'] = $this->language->get('design_error_text');;
			}
			}				
			}
		}
		return !$this->error;
	}
}
?>