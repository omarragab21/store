<?php
class ControllerExtensionNewsletterverify extends Controller {
	public function index() {
		$this->load->language('extension/module/tmdnewsletter');
		$this->load->model('extension/tmdnewsletter');
		$this->load->model('tool/image');
	$this->document->setTitle($this->language->get('heading_verify_title'));

		$data['heading_title'] 		= $this->language->get('heading_verify_title');
		$data['entry_name']  		= $this->language->get('entry_name');
		$data['entry_email']  		= $this->language->get('entry_email');
		$data['button_subscribe']  = $this->language->get('button_subscribe');
		
		$data['verify_titlestatus'] =$this->config->get('tmdnewsletter_verify_displaytitle');
		$data['verify_descstatus'] =$this->config->get('tmdnewsletter_verify_displaydiscription');
		$data['verify_logostatus'] =$this->config->get('tmdnewsletter_verify_displaylogo');
		$data['verify_declinebtn'] =$this->config->get('tmdnewsletter_verify_displaydecline');
		
		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $this->model_tool_image->resize($this->config->get('config_logo'), 100, 100);
			} else {
			$data['logo'] = '';
			}

		$newsletter_verifytext  = $this->config->get('tmdnewsletter_verify');


		if (isset($this->request->get['code'])) {
			$data['code'] = $this->request->get['code'];
		} else {
			$data['code'] = '';
		}
		 
		 $newsletter_verifytitle_text   = $newsletter_verifytext[$this->config->get('config_language_id')]['title'];
		 if(!empty($newsletter_verifytitle_text)){
		 	$data['newsletter_verifytitle_text']   = $newsletter_verifytext[$this->config->get('config_language_id')]['title'];
		 }else{
		 	$data['newsletter_verifytitle_text'] = $this->language->get('text_verifytitle');
		 }

		 $newsletter_verifydesc_text   = $newsletter_verifytext[$this->config->get('config_language_id')]['description'];
		 if(!empty($newsletter_verifydesc_text)){
		 	$data['newsletter_verifydesc_text']   = html_entity_decode($newsletter_verifytext[$this->config->get('config_language_id')]['description']);
		 }else{
		 	$data['newsletter_verifydesc_text'] = $this->language->get('text_description');
		 }

		 $newsletter_verifysuccessmsg_text   = $newsletter_verifytext[$this->config->get('config_language_id')]['verifysuccessmessage'];
		 if(!empty($newsletter_verifysuccessmsg_text)){
		 	$data['newsletter_verifysuccessmsg_text']   = html_entity_decode($newsletter_verifytext[$this->config->get('config_language_id')]['verifysuccessmessage']);
		 }else{
		 	$data['newsletter_verifysuccessmsg_text'] = $this->language->get('text_success_verify');
		 }
		 $newsletter_declinesuccessmsg_text   = $newsletter_verifytext[$this->config->get('config_language_id')]['declinesuccessmessage'];
		 if(!empty($newsletter_declinesuccessmsg_text)){
		 	$data['newsletter_declinesuccessmsg_text']   = html_entity_decode($newsletter_verifytext[$this->config->get('config_language_id')]['declinesuccessmessage']);
		 }else{
		 	$data['newsletter_declinesuccessmsg_text'] = $this->language->get('text_success_decline');
		 }

		 $newsletter_invalidmessage_text   = $newsletter_verifytext[$this->config->get('config_language_id')]['invalidmessage'];
		 if(!empty($newsletter_invalidmessage_text)){
		 	$data['newsletter_invalidmessage_text']   = $newsletter_verifytext[$this->config->get('config_language_id')]['invalidmessage'];
		 }else{
		 	$data['newsletter_invalidmessage_text'] = $this->language->get('entry_name');
		 }
		
		$newsletter_confirmbtn_text   = $newsletter_verifytext[$this->config->get('config_language_id')]['confirmbutton'];
		 if(!empty($newsletter_confirmbtn_text)){
		 	$data['newsletter_confirmbtn_text']   = $newsletter_verifytext[$this->config->get('config_language_id')]['confirmbutton'];
		 }else{
		 	$data['newsletter_confirmbtn_text'] = $this->language->get('text_confirm_btn');
		 }
		 $newsletter_declinebtn_text   = $newsletter_verifytext[$this->config->get('config_language_id')]['declinebutton'];
		 if(!empty($newsletter_declinebtn_text)){
		 	$data['newsletter_declinebtn_text']   = $newsletter_verifytext[$this->config->get('config_language_id')]['declinebutton'];
		 }else{
		 	$data['newsletter_declinebtn_text'] = $this->language->get('text_decline_btn');
		 }

		 if (isset($this->request->get['code'])) {
			$code = $this->request->get['code'];
		} else {
			$code = '';
		}

		$verified_subs=$this->model_extension_tmdnewsletter->getSubscriberverified($code);  
		$decline_subs=$this->model_extension_tmdnewsletter->getSubscriberdecline($code);  
			
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

		
	   	if(!empty($verified_subs)){
			$this->response->setOutput($this->load->view('extension/newsletterconfirm', $data));
	   	}elseif(!empty($decline_subs)){
			$this->response->setOutput($this->load->view('extension/newsletterdecline', $data));
	   	}else{
			$this->response->setOutput($this->load->view('extension/newsletterverify', $data));
	   	}

						
	}

	function addverifynewsletter(){
       $json = array();
        $this->load->language('extension/module/tmdnewsletter');
        $this->load->model('extension/tmdnewsletter');
      	if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
      	$codematch=$this->model_extension_tmdnewsletter->getSubscriberbycode($this->request->get['code']);  
	   
	   	if(!empty($codematch)){
	   	$this->model_extension_tmdnewsletter->editSubscriber($this->request->get['code']);  
	   	$json['success'] = $this->language->get('text_success');
	    
	   	}else{
	   	$json['error'] = $this->language->get('text_codeerror');	
	   	}

		    
    	}         
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }

    function adddeclinenewsletter(){
       $json = array();
        $this->load->language('extension/module/tmdnewsletter');
        $this->load->model('extension/tmdnewsletter');
      	if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
        $codematch=$this->model_extension_tmdnewsletter->getSubscriberbycode($this->request->get['code']);  
	   	
	   	if(!empty($codematch)){
	   	 $this->model_extension_tmdnewsletter->editDeclineSubscriber($this->request->get['code']);  
	   		$json['success'] = $this->language->get('text_success');
	   	}else{
	 		$json['error'] = $this->language->get('text_codeerror');
	   	}	
            
    	}         
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
}