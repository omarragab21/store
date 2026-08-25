<?php
class ControllerExtensionNewsletterunsubscribe extends Controller {
	public function index() {
		$this->load->language('extension/module/tmdnewsletter');
		$this->load->model('extension/tmdnewsletter');
		$this->load->model('tool/image');
	$this->document->setTitle($this->language->get('heading_unsub_title'));

		$data['heading_title'] 		= $this->language->get('heading_unsub_title');
		$data['entry_name']  		= $this->language->get('entry_name');
		$data['entry_email']  		= $this->language->get('entry_email');
		$data['button_subscribe']  = $this->language->get('button_subscribe');
		$data['text_reason']  = $this->language->get('text_reason');
		
		$data['unsubscribe_titlestatus'] =$this->config->get('tmdnewsletter_unsbscribe_displaytitle');
		$data['unsubscribe_descstatus'] =$this->config->get('tmdnewsletter_unsbscribe_displaydiscription');
		$data['unsubscribe_logostatus'] =$this->config->get('tmdnewsletter_unsbscribe_displaylogo');
		
			if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $this->model_tool_image->resize($this->config->get('config_logo'), 100, 100);
			} else {
			$data['logo'] = '';
			}

		$newsletter_unsubscribetext  = $this->config->get('tmdnewsletter_unsbscribe');


		if (isset($this->request->get['code'])) {
			$data['code'] = $this->request->get['code'];
		} else {
			$data['code'] = 0;
		}
		 
		 $newsletter_unsubscribetitle_text   = $newsletter_unsubscribetext[$this->config->get('config_language_id')]['title'];
		 if(!empty($newsletter_unsubscribetitle_text)){
		 	$data['newsletter_unsubscribetitle_text']   = $newsletter_unsubscribetext[$this->config->get('config_language_id')]['title'];
		 }else{
		 	$data['newsletter_unsubscribetitle_text'] = $this->language->get('text_unsubscribe');
		 }

		 $newsletter_unsubscribedesc_text   = $newsletter_unsubscribetext[$this->config->get('config_language_id')]['description'];
		 if(!empty($newsletter_unsubscribedesc_text)){
		 	$data['newsletter_unsubscribedesc_text']   = html_entity_decode($newsletter_unsubscribetext[$this->config->get('config_language_id')]['description']);
		 }else{
		 	$data['newsletter_unsubscribedesc_text'] = $this->language->get('text_description');
		 }

		 $unsubscribe_successmsg_text   = $newsletter_unsubscribetext[$this->config->get('config_language_id')]['unsbscribe_successmessage'];
		 if(!empty($unsubscribe_successmsg_text)){
		 	$data['unsubscribe_successmsg_text']   = html_entity_decode($newsletter_unsubscribetext[$this->config->get('config_language_id')]['unsbscribe_successmessage']);
		 }else{
		 	$data['unsubscribe_successmsg_text'] = $this->language->get('text_success_unsub');
		 }
		 

		 $unsubscribe_invalidmessage_text   = $newsletter_unsubscribetext[$this->config->get('config_language_id')]['invalidmessage'];
		 if(!empty($unsubscribe_invalidmessage_text)){
		 	$data['unsubscribe_invalidmessage_text']   = $newsletter_unsubscribetext[$this->config->get('config_language_id')]['invalidmessage'];
		 }else{
		 	$data['unsubscribe_invalidmessage_text'] = $this->language->get('entry_name');
		 }
		
		$unsubscribe_confirmbtn_text   = $newsletter_unsubscribetext[$this->config->get('config_language_id')]['confirmbutton'];
		 if(!empty($unsubscribe_confirmbtn_text)){
		 	$data['unsubscribe_confirmbtn_text']   = $newsletter_unsubscribetext[$this->config->get('config_language_id')]['confirmbutton'];
		 }else{
		 	$data['unsubscribe_confirmbtn_text'] = $this->language->get('text_confirm_btn');
		 }

		 $unsubscribereason_infos =$this->config->get('tmdnewsletter_unsbscribe_reason');
			
		 	$data['unsubscribe_reasons'] = array();
		  if(!empty($unsubscribereason_infos)){
			 foreach ($unsubscribereason_infos as $unsubscribereason_info) {
				 $unsubscribereason_ttitle  = $unsubscribereason_info['description'][$this->config->get('config_language_id')];
				
			 	$data['unsubscribe_reasons'][] = array(
			 		'title' => $unsubscribereason_ttitle['title']
			 		////'reasonstatus' => $unsubscribereason_info['reasonstatus']
	
				);

				}
			}

			 if (isset($this->request->get['code'])) {
			$code = $this->request->get['code'];
			} else {
			$code = '';
			}

			$unsubscribe_subs=$this->model_extension_tmdnewsletter->getunsubscribeSubscriber($code);  
			
			///$data['return_reasons'] = $this->model_localisation_return_reason->getReturnReasons();


			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');


		if(!empty($unsubscribe_subs)){
			$this->response->setOutput($this->load->view('extension/newsletterconfirm', $data));
	   	}else{
			$this->response->setOutput($this->load->view('extension/newsletter_unsubscribe', $data));
	   	}					
	}

	function addunsubscribe(){
       $json = array();
        $this->load->language('extension/module/tmdnewsletter');
        $this->load->model('extension/tmdnewsletter');
      	if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
      		//print_r($this->request->post); die();
      	$codematch=$this->model_extension_tmdnewsletter->getSubscriberbycode($this->request->get['code']);  
	   
	   	if(!empty($codematch)){
	   	$this->model_extension_tmdnewsletter->editUnsubscriber($this->request->post, $this->request->get['code']);  
	   	$json['success'] = $this->language->get('text_success');
	    
	   	}else{
	   	$json['error'] = $this->language->get('text_codeerror');	
	   	}

		    
    	}         
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }

}