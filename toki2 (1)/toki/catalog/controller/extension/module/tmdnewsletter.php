<?php
class ControllerExtensionModuleTmdnewsletter extends Controller {
	public function index() {
		$this->load->language('extension/module/tmdnewsletter');
		$this->load->model('extension/tmdnewsletter');

		$data['heading_title'] 		= $this->language->get('heading_title');
		$data['entry_name']  		= $this->language->get('entry_name');
		$data['entry_email']  		= $this->language->get('entry_email');
		$data['button_subscribe']  		= $this->language->get('button_subscribe');

		$data['newsletter_status'] =$this->config->get('tmdnewsletter_status');

		$data['newsletter_layoutstatus'] =$this->config->get('tmdnewsletter_layoutstatus');
		$data['newsletter_titlestatus'] =$this->config->get('tmdnewsletter_displaytitle');
		$data['newsletter_namestatus'] =$this->config->get('tmdnewsletter_displayname');

		$data['newsletter_descstatus'] =$this->config->get('tmdnewsletter_displaydesc');
		$data['newsletter_iconestatus'] =$this->config->get('tmdnewsletter_displayicon');
		$data['footer_bgcolor'] =$this->config->get('tmdnewsletter_footer_backgroundcolor');
		$data['footer_fontcolor'] =$this->config->get('tmdnewsletter_footer_fontcolor');

		$data['newsletter_iconclass'] =$this->config->get('tmdnewsletter_iconclass');
		$data['customcss'] =$this->config->get('tmdnewsletter_customcss');
		$data['agree_status']  = $this->config->get('tmdnewsletter_agree');
		$data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('extension/agree_privacy'));


		if(!empty($data['newsletter_iconclass'])){
		 	$data['newsletter_iconclass']   = $this->config->get('tmdnewsletter_iconclass');
		 }else{
		 	$data['newsletter_iconclass'] = 'fa fa-envelope';
		 }

		$newslettertext  = $this->config->get('tmdnewsletter_layout');

		 $newsletter_title_text   = $newslettertext[$this->config->get('config_language_id')]['title'];
		 if(!empty($newsletter_title_text)){
		 	$data['newsletter_title_text']   = $newslettertext[$this->config->get('config_language_id')]['title'];
		 }else{
		 	$data['newsletter_title_text'] = $this->language->get('heading_title');
		 }

		 $newsletter_desc_text   = $newslettertext[$this->config->get('config_language_id')]['description'];
		 if(!empty($newsletter_desc_text)){
		 	$data['newsletter_desc_text']   = html_entity_decode($newslettertext[$this->config->get('config_language_id')]['description']);
		 }else{
		 	$data['newsletter_desc_text'] = $this->language->get('text_description');
		 }
		 $newsletter_name_text   = $newslettertext[$this->config->get('config_language_id')]['name'];
		 if(!empty($newsletter_name_text)){
		 	$data['newsletter_name_text']   = $newslettertext[$this->config->get('config_language_id')]['name'];
		 }else{
		 	$data['newsletter_name_text'] = $this->language->get('entry_name');
		 }

		$newsletter_email_text   = $newslettertext[$this->config->get('config_language_id')]['email'];
		 if(!empty($newsletter_email_text)){
		 	$data['newsletter_email_text']   = $newslettertext[$this->config->get('config_language_id')]['email'];
		 }else{
		 	$data['newsletter_email_text'] = $this->language->get('entry_email');
		 }
		 $newsletter_btn_text   = $newslettertext[$this->config->get('config_language_id')]['button'];
		 if(!empty($newsletter_btn_text)){
		 	$data['newsletter_btn_text']   = $newslettertext[$this->config->get('config_language_id')]['button'];
		 }else{
		 	$data['newsletter_btn_text'] = $this->language->get('button_subscribe');
		 }

		 $newsletter_success_text   = $newslettertext[$this->config->get('config_language_id')]['successmessage'];
		 if(!empty($newsletter_success_text)){
		 	$data['newsletter_success_text']   = $newslettertext[$this->config->get('config_language_id')]['successmessage'];
		 }else{
		 	$data['newsletter_success_text'] = $this->language->get('text_success');
		 }
//new code start
		 $newsletter_privacy_text   = $newslettertext[$this->config->get('config_language_id')]['privacytext'];
		 if(!empty($newsletter_privacy_text)){
		 	$data['newsletter_privacy_text']   = html_entity_decode($newslettertext[$this->config->get('config_language_id')]['privacytext']);
		 }else{
		 	$data['newsletter_privacy_text'] = $this->language->get('text_privacy');
		 }
//new code end

		 //subscriber view link
		if (isset($this->request->get['mail_report_id'])) {
			$mail_report_id = $this->request->get['mail_report_id'];
		} else {
			$mail_report_id = 0;
		}
		if(!empty($mail_report_id)){
			$this->model_extension_tmdnewsletter->SubscriberViewlink($mail_report_id);
		}



		return $this->load->view('extension/module/tmdnewsletter', $data);

	}

	function addnewsletter(){
       $json = array();
        $this->load->language('extension/module/tmdnewsletter');
        $this->load->model('extension/tmdnewsletter');
      	if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

          	$newsletter_namestatus =$this->config->get('tmdnewsletter_displayname');
          	$agree_status  = $this->config->get('tmdnewsletter_agree');
          	//new code start

          	if(isset($this->request->post['subscribe_agree'])){
          		$agree=$this->request->post['subscribe_agree'];
          	}else{
				$agree='';
          	}
           //new code end

			if($newsletter_namestatus==1 || $newsletter_namestatus==2){
				if(empty($this->request->post['name'])) {

					if($newsletter_namestatus==1){

					$this->request->post['name']='';
					}

					if($newsletter_namestatus==2){
					$json['error']['name']= $this->language->get('error_name');
					}

				}
			}
			else{
				$this->request->post['name']='';
			}

			if(empty($this->request->post['email']) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
				$json['error']['email']= $this->language->get('error_email');
			}

			$subscriber_info = $this->model_extension_tmdnewsletter->getSubscriberByEmail($this->request->post['email']);

			if ($subscriber_info) {
				$json['error']['email']= $this->language->get('error_exists');
			}

		 //new code start
		if($agree_status==1){
			if (!$agree) {
				$json['error']['agree']= $this->language->get('error_exists');
			}
		}
        //new code end

			 if(empty($json['error'])) {


	           	$this->model_extension_tmdnewsletter->addSubscriber($this->request->post);

	           	$json['success'] = $this->language->get('text_success');
	        }
    	}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
}
