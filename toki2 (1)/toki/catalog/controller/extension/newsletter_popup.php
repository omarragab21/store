<?php
class ControllerExtensionNewsletterpopup extends Controller {
	public function index() {
		//newsletter 7-5-2019 code start
		  $this->load->language('extension/newsletter_popup');
		 $this->load->model('extension/tmdnewsletter');
		 $this->load->model('tool/image');
		
		$data['heading_title'] 		= $this->language->get('heading_title');
		$data['entry_name']  		= $this->language->get('entry_name');
		$data['entry_email']  		= $this->language->get('entry_email');
		$data['button_subscribe']  		= $this->language->get('button_subscribe');
		
		$data['newsletter_status'] =$this->config->get('tmdnewsletter_status');
	
		$data['footerstatus'] =$this->config->get('tmdnewsletter_footerstatus');
		
		$data['footer_titlestatus'] =$this->config->get('tmdnewsletter_footer_displaytitle');
		$data['footer_namestatus'] =$this->config->get('tmdnewsletter_footer_displayname');
		$data['footer_bgcolor'] =$this->config->get('tmdnewsletter_footer_backgroundcolor');
		$data['footer_fontcolor'] =$this->config->get('tmdnewsletter_footer_fontcolor');
		$data['customcss'] =$this->config->get('tmdnewsletter_customcss');
		$newsletter_footertext  = $this->config->get('tmdnewsletter_footer');
		
		//new code start
		$data['agree_status']  = $this->config->get('tmdnewsletter_agree');
		$newslettertext  = $this->config->get('tmdnewsletter_layout');
		 $newsletter_privacy_text   = $newslettertext[$this->config->get('config_language_id')]['privacytext'];
		 if(!empty($newsletter_privacy_text)){
		 	$data['newsletter_privacy_text']   = html_entity_decode($newslettertext[$this->config->get('config_language_id')]['privacytext']);
		 }else{
		 	$data['newsletter_privacy_text'] = $this->language->get('text_privacy');
		 }
		//new code end

		 $newsletter_footertitle_text   = $newsletter_footertext[$this->config->get('config_language_id')]['title'];
		 if(!empty($newsletter_footertitle_text)){
		 	$data['newsletter_footertitle_text']   = $newsletter_footertext[$this->config->get('config_language_id')]['title'];
		 }else{
		 	$data['newsletter_footertitle_text'] = $this->language->get('heading_title');
		 }

		 $newsletter_footerdesc_text   = $newsletter_footertext[$this->config->get('config_language_id')]['description'];
		 if(!empty($newsletter_footerdesc_text)){
		 	$data['newsletter_footerdesc_text']   = html_entity_decode($newsletter_footertext[$this->config->get('config_language_id')]['description']);
		 }else{
		 	$data['newsletter_footerdesc_text'] = $this->language->get('text_description');
		 }
		 $newsletter_footername_text   = $newsletter_footertext[$this->config->get('config_language_id')]['name'];
		 if(!empty($newsletter_footername_text)){
		 	$data['newsletter_footername_text']   = $newsletter_footertext[$this->config->get('config_language_id')]['name'];
		 }else{
		 	$data['newsletter_footername_text'] = $this->language->get('entry_name');
		 }
		
		$newsletter_footeremail_text   = $newsletter_footertext[$this->config->get('config_language_id')]['email'];
		 if(!empty($newsletter_footeremail_text)){
		 	$data['newsletter_footeremail_text']   = $newsletter_footertext[$this->config->get('config_language_id')]['email'];
		 }else{
		 	$data['newsletter_footeremail_text'] = $this->language->get('entry_email');
		 }
		 $newsletter_footerbtn_text   = $newsletter_footertext[$this->config->get('config_language_id')]['button'];
		 if(!empty($newsletter_footerbtn_text)){
		 	$data['newsletter_footerbtn_text']   = $newsletter_footertext[$this->config->get('config_language_id')]['button'];
		 }else{
		 	$data['newsletter_footerbtn_text'] = $this->language->get('button_subscribe');
		 }

		 $newsletter_footersuccess_text   = $newsletter_footertext[$this->config->get('config_language_id')]['successmessage'];
		 if(!empty($newsletter_footersuccess_text)){
		 	$data['newsletter_footersuccess_text']   = $newsletter_footertext[$this->config->get('config_language_id')]['successmessage'];
		 }else{
		 	$data['newsletter_footersuccess_text'] = $this->language->get('text_success');
		 }
		 //laytout
		if (isset($this->request->get['route'])) {
			$route = (string)$this->request->get['route'];
		} else {
			$route = 'common/home';
		}
		

		 $footer_applyonlayout  = $this->config->get('tmdnewsletter_footer_applyonlayout');

		
		$layout = $this->model_extension_tmdnewsletter->getLayout($route);
		if(!empty($footer_applyonlayout) && !empty($layout)){
		
			if (in_array($layout['layout_id'], $footer_applyonlayout)) {
				$data['showonlayout']=true; 
			}else{
				$data['showonlayout']=false; 
			}
		}

		//subscriber switched
		$subscriber_register = $this->model_extension_tmdnewsletter->SubscriberSwitch(0);
						
		
		//newsletter popup start

		$data['popupstatus'] =$this->config->get('tmdnewsletter_popupstatus');
	
		$data['popup_titlestatus'] =$this->config->get('tmdnewsletter_popup_displaytitle');
		$data['popup_namestatus'] =$this->config->get('tmdnewsletter_popup_displayname');
		$data['popup_bgimage'] =$this->config->get('tmdnewsletter_popupbgimage');
		
		if ($data['popup_bgimage']) {
			$data['popup_bgimage'] = $this->model_tool_image->resize($data['popup_bgimage'], 800,800);
		}	
		 	$newsletter_popuptext  = $this->config->get('tmdnewsletter_popup');
		
		 $newsletter_popuptitle_text   = $newsletter_popuptext[$this->config->get('config_language_id')]['title'];
		
		 if(!empty($newsletter_popuptitle_text)){
		 	$data['newsletter_popuptitle_text']   = $newsletter_popuptext[$this->config->get('config_language_id')]['title'];
		 }else{
		 	$data['newsletter_popuptitle_text'] = $this->language->get('heading_title1');
		 }

		 $newsletter_popupdesc_text   = $newsletter_popuptext[$this->config->get('config_language_id')]['description'];
		 if(!empty($newsletter_popupdesc_text)){
		 	$data['newsletter_popupdesc_text']   = html_entity_decode($newsletter_popuptext[$this->config->get('config_language_id')]['description']);
		 }else{
		 	$data['newsletter_popupdesc_text'] = $this->language->get('text_description');
		 }

		 $newsletter_popupname_text   = $newsletter_popuptext[$this->config->get('config_language_id')]['name'];
		 if(!empty($newsletter_popupname_text)){
		 	$data['newsletter_popupname_text']   = $newsletter_popuptext[$this->config->get('config_language_id')]['name'];
		 }else{
		 	$data['newsletter_popupname_text'] = $this->language->get('entry_name');
		 }

		 $newsletter_popupemail_text   = $newsletter_popuptext[$this->config->get('config_language_id')]['email'];
		 if(!empty($newsletter_popupemail_text)){
		 	$data['newsletter_popupemail_text']   = $newsletter_popuptext[$this->config->get('config_language_id')]['email'];
		 }else{
		 	$data['newsletter_popupemail_text'] = $this->language->get('entry_email');
		 }


		 $newsletter_popupbtn_text   = $newsletter_popuptext[$this->config->get('config_language_id')]['button'];
		 if(!empty($newsletter_popupbtn_text)){
		 	$data['newsletter_popupbtn_text']   = $newsletter_popuptext[$this->config->get('config_language_id')]['button'];
		 }else{
		 	$data['newsletter_popupbtn_text'] = $this->language->get('button_subscribe');
		 }

		 $newsletter_popupdontbtn_text   = $newsletter_popuptext[$this->config->get('config_language_id')]['button_dontshow'];
		 if(!empty($newsletter_popupdontbtn_text)){
		 	$data['newsletter_popupdontbtn_text']   = $newsletter_popuptext[$this->config->get('config_language_id')]['button_dontshow'];
		 }else{
		 	$data['newsletter_popupdontbtn_text'] = $this->language->get('button_dontshow');
		 }

		 $newsletter_popupsuccess_text   = $newsletter_popuptext[$this->config->get('config_language_id')]['successmessage'];
		 if(!empty($newsletter_popupsuccess_text)){
		 	$data['newsletter_popupsuccess_text']   = $newsletter_popuptext[$this->config->get('config_language_id')]['successmessage'];
		 }else{
		 	$data['newsletter_popupsuccess_text'] = $this->language->get('text_success');
		 }


		$popup_applyonlayout  = $this->config->get('tmdnewsletter_popup_applyonlayout');

		
		$layout_popup = $this->model_extension_tmdnewsletter->getLayout($route);
		if(!empty($popup_applyonlayout) && !empty($layout)){
		
			if (in_array($layout_popup['layout_id'], $popup_applyonlayout)) {
				$data['showpopuplayout']=true; 
			}else{
				$data['showpopuplayout']=false; 
			}
		}


		return $this->load->view('extension/newsletter_popup', $data);

		//newsletter popup end
		//newsletter 7-5-2019 code end
	
	}	

	function addnewsletterfooter(){
       $json = array();

        $this->load->language('extension/module/tmdnewsletter');
        $this->load->model('extension/tmdnewsletter');
      	if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
          
          	$tmdnewsletter_footerstatus =$this->config->get('tmdnewsletter_footer_displayname');
          	$tmdnewsletter_popupstatus =$this->config->get('tmdnewsletter_popup_displayname');
			$agree_status  = $this->config->get('tmdnewsletter_agree');
  
          	if(isset($this->request->post['subscribe_agree'])){
          		$agree=$this->request->post['subscribe_agree'];
          	}else{
				$agree='';
          	}


			if($tmdnewsletter_footerstatus==1 || $tmdnewsletter_footerstatus==2){
				if(empty($this->request->post['name'])) {

					if($tmdnewsletter_footerstatus==1){
			
					$this->request->post['name']='';
					}

					if($tmdnewsletter_footerstatus==2){
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
				$json['error']['agree']= $this->language->get('error_agree');
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

    function addnewsletterpopup(){
       $json = array();
        $this->load->language('extension/module/tmdnewsletter');
        $this->load->model('extension/tmdnewsletter');
      	if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
          
          	$tmdnewsletter_footerstatus =$this->config->get('tmdnewsletter_footer_displayname');
          	$tmdnewsletter_popupstatus =$this->config->get('tmdnewsletter_popup_displayname');
          	$agree_status  = $this->config->get('tmdnewsletter_agree');

			  if(isset($this->request->post['subscribe_agree'])){
          		$agree=$this->request->post['subscribe_agree'];
          	}else{
				$agree='';
          	}
			if($tmdnewsletter_popupstatus==1 || $tmdnewsletter_popupstatus==2){
				if(empty($this->request->post['name'])) {

					if($tmdnewsletter_popupstatus==1){
			
					$this->request->post['name']='';
					}

					if($tmdnewsletter_popupstatus==2){
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