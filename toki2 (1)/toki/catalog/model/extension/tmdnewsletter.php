<?php
class ModelExtensionTmdnewsletter extends Model {
	public function addSubscriber($data) {
		
		$customer_id = $this->customer->getId();
		if(!empty($customer_id)){
			$account=1;
		}else{
			$account=0;
		}

			$string1 = str_shuffle('abcdefghijklmnopqrstuvwxyz');
            $random1 = substr($string1,0,3);
            $string2 = str_shuffle('1234567890');
            $random2 = substr($string2,0,3);
            $randomcode = $random1.$random2;
          
           $verification_mailstatus =$this->config->get('tmdnewsletter_verification');
           if(!empty($verification_mailstatus)){
           	$status='0';
           }else{
           	$status='1';
           }

		$this->db->query("INSERT INTO " . DB_PREFIX . "newsletter_subscribe SET  name = '" . $this->db->escape($data['name']) . "', email = '" . $this->db->escape($data['email']) . "', customer_id = '" . $this->customer->getId() . "',  ip_address = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', account = '" . $account . "',  code = '" . $randomcode . "', status = '" . $status . "',  date_added = NOW()");

			$subscriber_id = $this->db->getLastId();

		if(!empty($verification_mailstatus)){

			$verifyemail = $this->config->get('tmdnewsletter_verificationmail');
			$verifyemailsubject = $verifyemail[$this->config->get('config_language_id')]['subject'];
			$verifyemailmessage = $verifyemail[$this->config->get('config_language_id')]['message'];
			$url='';

			$verifylink_info = $this->url->link('extension/newsletterverify', $url . '&code=' . $randomcode);	
			$verify_link = str_replace('&amp;','&',$verifylink_info);
			
			$unsubscribelink_info = $this->url->link('extension/newsletter_unsubscribe', $url . '&code=' . $randomcode);	
			$unsubscribe_link = str_replace('&amp;','&',$unsubscribelink_info);
			
			$logo='<img src="'.$this->config->get('config_logo').'"/>';	

			
			if(isset($verifyemailmessage)){
			$find = array(
			'{name}',										
			'{email}',										
			'{verify_link}',										
			'{unsubscribe_link}',
			'{store}',															
			'{logo}',										

			);

			$replace = array(
			'name'  => $data['name'],
			'email'  => $data['email'],
			'verify_link'  => $verify_link,
			'unsubscribe_link'  => $unsubscribe_link,
			'store' 		=> html_entity_decode($this->config->get('config_name')),
			'logo'  => $logo
	
			);

			$subject = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace,$verifyemailsubject))));
			$message = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace,html_entity_decode($verifyemailmessage)))));

			$mail = new Mail($this->config->get('config_mail_engine'));			
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($data['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject($subject);
			$mail->setHtml(html_entity_decode($message));
			$mail->send();
		}
	}

/// New code
		
		 $confirmation_mailstatus =$this->config->get('tmdnewsletter_confirmation');


		if(!empty($confirmation_mailstatus)){

			
			$email= $data['email'];	
			$name= $data['name'];	
			
			$confirmemail = $this->config->get('tmdnewsletter_confirmationmail');
			$confirmemailsubject = $confirmemail[$this->config->get('config_language_id')]['subject'];
			$confirmemailmessage = $confirmemail[$this->config->get('config_language_id')]['message'];
			
			$url='';

			
			$unsubscribelink_info = $this->url->link('tmdnewsletter/newsletter_unsubscribe', $url . '&code=' . $randomcode);	
			$unsubscribe_link = str_replace('&amp;','&',$unsubscribelink_info);
				
			$logo='<img src="'.$this->config->get('config_logo').'"/>';	

			if(isset($confirmemailmessage)){
			$find = array(
			'{name}',
			'{email}',
			'{store}',															
			'{logo}',										
			'{unsubscribe_link}',
											

			);
			$replace = array(
			'name'  => $name,
			'email'  => $email,
			'store' => html_entity_decode($this->config->get('config_name')),
			'logo'  => $logo,
			'unsubscribe_link'  => $unsubscribe_link,
			
			);	

			$subject = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace,$confirmemailsubject))));
			$message = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace,html_entity_decode($confirmemailmessage)))));

			$mail = new Mail();			
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($data['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject($subject);
			$mail->setHtml(html_entity_decode($message));
			$mail->send();
			}

		}
		/// New code

	}


	public function editSubscriber($code) {
		$this->db->query("UPDATE " . DB_PREFIX . "newsletter_subscribe SET status = '1' WHERE code = '" . $code . "'");		
		  $confirmation_mailstatus =$this->config->get('tmdnewsletter_confirmation');


		if(!empty($confirmation_mailstatus)){

			$query_mail = $this->db->query("SELECT * FROM " . DB_PREFIX . "newsletter_subscribe  WHERE code = '" . $code . "'");
			$email= $query_mail->row['email'];	
			$name= $query_mail->row['name'];	
			
			$confirmemail = $this->config->get('tmdnewsletter_confirmationmail');
			$confirmemailsubject = $confirmemail[$this->config->get('config_language_id')]['subject'];
			$confirmemailmessage = $confirmemail[$this->config->get('config_language_id')]['message'];
			
			$url='';

			
			$unsubscribelink_info = $this->url->link('extension/newsletter_unsubscribe', $url . '&code=' . $code);	
			$unsubscribe_link = str_replace('&amp;','&',$unsubscribelink_info);
				
			$logo='<img src="'.$this->config->get('config_logo').'"/>';	

			if(isset($confirmemailmessage)){
			$find = array(
			'{name}',
			'{email}',
			'{store}',															
			'{logo}',										
			'{unsubscribe_link}',
											

			);
			$replace = array(
			'name'  => $name,
			'email'  => $email,
			'store' => html_entity_decode($this->config->get('config_name')),
			'logo'  => $logo,
			'unsubscribe_link'  => $unsubscribe_link,
			
			);	

			$subject = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace,$confirmemailsubject))));
			$message = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace,html_entity_decode($confirmemailmessage)))));

			$mail = new Mail($this->config->get('config_mail_engine'));			
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($email);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject($subject);
			$mail->setHtml(html_entity_decode($message));
			$mail->send();
			}

		}
	}
	
	public function editDeclineSubscriber($code) {
		$this->db->query("UPDATE " . DB_PREFIX . "newsletter_subscribe SET status = '3' WHERE code = '" . $code . "'");		
	}

	public function editUnsubscriber($data, $code) {
		if(empty($data['reason'])){
			$data['reason']='';
		}
		$this->db->query("UPDATE " . DB_PREFIX . "newsletter_subscribe SET reason = '" . $this->db->escape($data['reason']) . "', status = '2' WHERE code = '" . $code . "'");		
	}

	public function SubscriberSwitch() {
	$email_report = $this->db->query("SELECT * FROM " . DB_PREFIX . "newsletter_mail_report  WHERE mail_report_id<>0");
		if(!empty($email_report->rows)){
		foreach ($email_report->rows as $result) {

		$query_customer = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer  WHERE email = '" . $result['email'] . "'");	
			if(!empty($query_customer->row['email'])){
				$email=$query_customer->row['email'];
			}else{
				$email='';
			}

				if(!empty($email)){
				$this->db->query("UPDATE " . DB_PREFIX . "newsletter_mail_report SET  account = '1' WHERE email = '" . $email . "' and account!='1'");		
				$this->db->query("UPDATE " . DB_PREFIX . "newsletter_subscribe SET  account = '1' WHERE email = '" . $email . "'");		
				}
			}
	}
			

	}

	public function getSubscriberbycode($code) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "newsletter_subscribe  WHERE code = '" . $code . "'");
		return $query->row;
	}
	public function getSubscriberverified($code) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "newsletter_subscribe  WHERE code = '" . $code . "' and status='1'");
		return $query->row;
	}
	public function getSubscriberdecline($code) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "newsletter_subscribe  WHERE code = '" . $code . "' and status='3'");
		return $query->row;
	}
	public function getunsubscribeSubscriber($code) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "newsletter_subscribe  WHERE code = '" . $code . "' and status='2'");
		return $query->row;
	}

	public function SubscriberViewlink($mail_report_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "newsletter_mail_report  WHERE mail_report_id = '" . $mail_report_id . "'");
		
		if(!empty($query->row)){
			$this->db->query("UPDATE " . DB_PREFIX . "newsletter_mail_report SET  open_day = '1' WHERE mail_report_id = '" . $mail_report_id . "'");					
		}
	}

	public function getLayout($route) {
		$sql = "SELECT * FROM " . DB_PREFIX . "layout_route WHERE '" . $this->db->escape($route) . "' LIKE route AND store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY route DESC LIMIT 1";
		$query = $this->db->query($sql);
		return $query->row;
	}
	public function getSubscriberByEmail($email) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "newsletter_subscribe WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}

	
}
