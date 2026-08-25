<?php
class ModelExtensionSubscriber extends Model {

	public function editSubscriber($subscriber_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "newsletter_subscribe SET status = '" . (int)$data['status'] . "', account = '" . (int)$data['account'] . "' WHERE subscriber_id = '" . (int)$subscriber_id . "'");
	}



	public function sendSubscribermail($subscriber_id, $data) {
		$subscriber_info = $this->db->query("SELECT * FROM " . DB_PREFIX . "newsletter_subscribe  WHERE subscriber_id = '" . (int)$subscriber_id . "'");

			if($subscriber_info->row['name']){
				$name= $subscriber_info->row['name'];
			}else{
				$name='';
			}
			if($subscriber_info->row['email']){
				$email= $subscriber_info->row['email'];
			}else{
				$email='';
			}

				if(!empty($data['message'])){
				$find = array(
				'{name}',
				'{email}',



				);
				$replace = array(
					'name' 		=> $name,
					'email' 		=> $email

				);


				$subject = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $data['subject']))));
				$message = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $data['message']))));

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

	public function addImport($data) {

		$subscriberquery = $this->db->query("SELECT * FROM " . DB_PREFIX . "newsletter_subscribe  WHERE email = '" . $this->db->escape($data['email']) . "'");
		$customerquery = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer  WHERE email = '" . $this->db->escape($data['email']) . "'");
			if(!empty($customerquery->row['customer_id'])){
				$customer_id=$customerquery->row['customer_id'];
			}else{
				$customer_id=0;
			}
		if(!empty($subscriberquery->row)){
		$this->db->query("UPDATE " . DB_PREFIX . "newsletter_subscribe SET name = '" . $this->db->escape($data['name']) . "', customer_id = '" . (int)$customer_id . "', email = '" . $this->db->escape($data['email']) . "', status = '" . (int)$data['status'] . "', account = '" . (int)$data['account'] . "', ip_address = '" . $this->db->escape($data['ip_address']) . "', date_added = NOW() WHERE email = '" . $this->db->escape($data['email']) . "'");
		}else{
		$this->db->query("INSERT INTO " . DB_PREFIX . "newsletter_subscribe SET name = '" . $this->db->escape($data['name']) . "', email = '" . $this->db->escape($data['email']) . "', status = '" . (int)$data['status'] . "', account = '" . (int)$data['account'] . "', ip_address = '" . $this->db->escape($data['ip_address']) . "', date_added = NOW()");
		}

	}

	public function deleteSubscriber($subscriber_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "newsletter_subscribe WHERE subscriber_id = '" . (int)$subscriber_id . "'");

	}

	public function deleteMailReport($mail_send_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "tmd_mail_send WHERE mail_send_id = '" . (int)$mail_send_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "newsletter_mail_report WHERE mail_send_id = '" . (int)$mail_send_id . "'");

	}

	public function getSubscriber($subscriber_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "newsletter_subscribe  WHERE subscriber_id = '" . (int)$subscriber_id . "'");

		return $query->row;
	}

	public function getSubscribers($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "newsletter_subscribe  WHERE subscriber_id<>0";

		if (!empty($data['filter_name'])) {
			$sql .= " AND name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$sql .= " AND email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (isset($data['filter_account']) && !is_null($data['filter_account'])) {
			$sql .= " AND account = '" . (int)$data['filter_account'] . "'";
		}
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND status = '" . (int)$data['filter_status'] . "'";
		}


		$sort_data = array(
			'name',
			'email',
			'ip_address',
			'status',
			'date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY subscriber_id";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalSubscribers($data = array()) {

		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "newsletter_subscribe  WHERE subscriber_id<>0";

		if (!empty($data['filter_name'])) {
			$sql .= " AND name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$sql .= " AND email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}
		if (isset($data['filter_account']) && !is_null($data['filter_account'])) {
			$sql .= " AND account = '" . (int)$data['filter_account'] . "'";
		}
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND status = '" . (int)$data['filter_status'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getMailTemplates($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "tmd_mail_send  WHERE mail_send_id<>0 ORDER by date_added DESC";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		$query = $this->db->query($sql);
		return $query->rows;

	}
	public function getMailReports($data, $mail_send_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "newsletter_mail_report  WHERE mail_send_id = '" . (int)$mail_send_id . "'";

		if (!empty($data['filter_email'])) {
			$sql .= " AND email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (!empty($data['filter_date'])) {
			$sql .= " AND date_added LIKE '" . $this->db->escape($data['filter_date']) . "'";
		}

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY mail_report_id";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " ASC";
		} else {
			$sql .= " DESC";
		}


		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}



	public function getTotalMailReports($data, $mail_send_id) {

		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "newsletter_mail_report  WHERE mail_send_id = '" . (int)$mail_send_id . "'";

		if (!empty($data['filter_email'])) {
			$sql .= " AND email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}
		if (!empty($data['filter_date'])) {
			$sql .= " AND date_added LIKE '" . $this->db->escape($data['filter_date']) . "'";
		}
		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalMailTemplates() {

		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "tmd_mail_send  WHERE mail_send_id<>0";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalSendMessages($mail_send_id) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "newsletter_mail_report  WHERE mail_send_id = '" . (int)$mail_send_id . "'";
		$query = $this->db->query($sql);
		return $query->row['total'];
	}

	public function getTotalSubscriberSwitch($mail_send_id) {

		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "newsletter_mail_report  WHERE mail_send_id = '" . (int)$mail_send_id . "' and account='1'";
		$query = $this->db->query($sql);
		return $query->row['total'];
	}
	public function getTotalclickedmail($mail_send_id) {

		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "newsletter_mail_report  WHERE mail_send_id = '" . (int)$mail_send_id . "' and open_day='1'";
		$query = $this->db->query($sql);
		return $query->row['total'];
	}

	public function getTotalSendMessagesall() {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "newsletter_mail_report  WHERE mail_report_id<>0";
		$query = $this->db->query($sql);
		return $query->row['total'];
	}

	public function getTotalSubscriberSwitchall() {

		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "newsletter_mail_report  WHERE account='1'";
		$query = $this->db->query($sql);
		return $query->row['total'];
	}
	public function getTotalclickedall() {

		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "newsletter_mail_report  WHERE open_day='1'";
		$query = $this->db->query($sql);
		return $query->row['total'];
	}


	public function sendEmail($data) {

	//mail template
		$this->load->model('extension/mail');
		$this->load->model('tool/image');
		$mail_id = $data['emailtemplate'];
		if(!empty($mail_id)){
		$mailinfo = $this->model_extension_mail->getMailbyId($mail_id);
		//assign products
		$product_info = $this->db->query("SELECT product_id FROM  " . DB_PREFIX . "newsletter_assign_product WHERE mail_id = '" . (int)$mail_id . "'");
		$products ='';
		foreach ($product_info->rows as $result) {

		  $query_product = $this->db->query("SELECT * FROM  " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$result['product_id'] . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

			//product image size
			if(!empty($mailinfo['image_width'])){
			$image_width=$mailinfo['image_width'];
			}else{
			$image_width=50;
			}
			if(!empty($mailinfo['image_height'])){
			$image_height=$mailinfo['image_height'];
			}else{
			$image_height=50;
			}

			if (is_file(DIR_IMAGE . $query_product->row['image'])) {
			$image = $this->model_tool_image->resize($query_product->row['image'], $image_width, $image_height);
			} else {
			$image = '';
			}

			if($query_product->row['name']){
				$productname= $query_product->row['name'];
			}else{
				$productname='';
			}



		if(empty($mailinfo['price_status'])){
			$price='';
			}else{
			if($query_product->row['price']){
				$price= $query_product->row['price'];
			}else{
				$price='';
			}

		}

		  	$products .='<div class="card" style="width:50%;float:left;"><a href="'.HTTP_CATALOG.'/index.php?route=product/product&product_id='.$query_product->row['product_id'].'"><img src="'.$image.'"><h3>'.$productname.'</h3><p></a>'.$price.'</p></div>';
		}
			$products .="<div style=clear:both>";

	//store logo
			$logo='<img src="'.$this->config->get('config_logo').'"/>';
		//assign coupon
			$coupon_info = $this->db->query("SELECT * FROM  " . DB_PREFIX . "coupon WHERE coupon_id = '" . (int)$mailinfo['coupon_id'] . "'");
			if($coupon_info->row){
				$coupon_code= $coupon_info->row['code'];
				$coupon_name= $coupon_info->row['name'];

			}else{
				$coupon_code='';
				$coupon_name='';
			}
	//quick mail
		if($data['status']=='all' &&  $data['account']=='all') {
		$querymails = $this->db->query("SELECT * FROM " . DB_PREFIX . "newsletter_subscribe");
		}
		elseif($data['status']=='all' && $data['account']!='all') {
		$querymails = $this->db->query("SELECT * FROM " . DB_PREFIX . "newsletter_subscribe  WHERE account = '" . (int)$data['account'] . "'");
		}
		elseif($data['status']!='all' && $data['account']=='all') {
		$querymails = $this->db->query("SELECT * FROM " . DB_PREFIX . "newsletter_subscribe  WHERE status = '" . (int)$data['status'] . "'");
		}
		elseif($data['status']=='subscribe' && $data['account']!=='all') {
		$querymails = $this->db->query("SELECT * FROM " . DB_PREFIX . "newsletter_subscribe  WHERE status != '2' and account = '" . (int)$data['account'] . "'");
		}
		else{
		$querymails = $this->db->query("SELECT * FROM " . DB_PREFIX . "newsletter_subscribe  WHERE status = '" . (int)$data['status'] . "' and account = '" . (int)$data['account'] . "'");
		}

		if(!empty($querymails->rows)) {
	//mail report
		$sql="INSERT INTO " . DB_PREFIX . "tmd_mail_send set
		mail_id='".$mail_id."',
		date_added=now()";
		$this->db->query($sql);

		$mail_send_id = $this->db->getLastId();

			foreach ($querymails->rows as $querymail) {
				$email=$querymail['email'];
					$name=$querymail['name'];

				if(!empty($mailinfo['status'])){
					if(!empty($mailinfo['message'])){
		//mail report
					$this->db->query("INSERT INTO " .DB_PREFIX . "newsletter_mail_report SET mail_send_id='".(int)$mail_send_id."', mail_id='".(int)$mail_id."' , email='".$querymail['email']."' , status='".$querymail['status']."' , account='".$querymail['account']."',  date_added=now()");
					$mail_report_id = $this->db->getLastId();

		//verification code
					$string1 = str_shuffle('abcdefghijklmnopqrstuvwxyz');
				    $random1 = substr($string1,0,3);
				    $string2 = str_shuffle('1234567890');
				    $random2 = substr($string2,0,3);
				    $randomcode = $random1.$random2;
		//count link click
			$store_info1 = HTTP_CATALOG.'index.php?route=extension/module/tmdnewsletter&mail_report_id='.$mail_report_id;
			$store_link1 = str_replace('&amp;','&',$store_info1);

			$store_info = HTTP_CATALOG.'index.php?route=common/home';
			$store_link = str_replace('&amp;','&',$store_info);


			$verifylink_info = HTTP_CATALOG.'index.php?route=extension/newsletterverify&code='.$randomcode;
			$verify_link = str_replace('&amp;','&',$verifylink_info);

			$unsubscribelink_info = HTTP_CATALOG.'index.php?route=extension/newsletter_unsubscribe&code='.$randomcode;
			$unsubscribe_link = str_replace('&amp;','&',$unsubscribelink_info);

			$this->db->query("UPDATE " . DB_PREFIX . "newsletter_subscribe SET code = '" . $randomcode . "' WHERE email = '" . $email . "'");



				$find = array(
				'{name}',
				'{email}',
				'{products}',
				'{coupon_name}',
				'{coupon_code}',
				'{store}',
				'{logo}',
				'{verify_link}',
				'{unsubscribe_link}',
				'{store_link}',

				);

				$replace = array(
					'name' 		=> $name,
					'email' 		=> $email,
					'products' 		=> $products,
					'coupon_name' 		=> $coupon_name,
					'coupon_code' 		=> $coupon_code,
					'store' 		=> html_entity_decode($this->config->get('config_name')),
					'logo' 		=> $logo,
					'verify_link'  => $verify_link,
					'unsubscribe_link'  => $unsubscribe_link,
					'store_link'  => $store_link

				);
				//print_r($replace); die();

				$subject = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $mailinfo['subject']))));
				$message = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $mailinfo['message']))));
				$message .= '<img src="'.$store_link1.'" >';

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

		}
			return true;
	}
		else{
			return false;
		}

}



}
