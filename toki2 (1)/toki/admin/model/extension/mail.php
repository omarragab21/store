<?php
class ModelExtensionMail extends Model {
	public function addMail($data) {
		$sql="INSERT INTO " . DB_PREFIX . "newsletter_mail set
		emailtype='".$this->db->escape($data['emailtype'])."',
		coupon_id	='".(int)$data['coupon_id']."',
		image_height='".(int)$data['image_height']."',
		image_width='".(int)$data['image_width']."',
		price_status='".(int)$data['price_status']."',
		status='".(int)$data['status']."',
		date_added=now()";
		$this->db->query($sql);
		$mail_id = $this->db->getLastId();
		
		/* 04-03-2019 in update remove isset query */
			foreach ($data['subscriber_mail'] as $language_id => $value) {
				$this->db->query("INSERT INTO " .DB_PREFIX . "newsletter_mail_language SET 
				mail_id ='" . (int)$mail_id . "',
				language_id ='" . (int)$language_id . "',
				subject='".$this->db->escape($value['subject'])."',
				name='".$this->db->escape($value['name'])."',
				message='".$this->db->escape($value['message'])."'
				"); 
			}

		if (isset($data['product'])) {
			foreach ($data['product'] as $product_id) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "newsletter_assign_product SET product_id = '" . (int)$product_id . "', mail_id = '" . (int)$mail_id . "'");
			}
		}
		
		return $mail_id;
	}

	public function editMail($mail_id, $data) {
		$sql="update " . DB_PREFIX . "newsletter_mail set 
		emailtype='".$this->db->escape($data['emailtype'])."',
		coupon_id	='".(int)$data['coupon_id']."',
		image_height='".(int)$data['image_height']."',
		image_width='".(int)$data['image_width']."',
		price_status='".(int)$data['price_status']."',
		status='".(int)$data['status']."',
		date_modified=now()
		where mail_id='".$mail_id."'";
		$this->db->query($sql);
		
		$this->db->query("delete from " . DB_PREFIX . "newsletter_mail_language where  mail_id ='" . (int)$mail_id."'");
		
		/* 04-03-2019 in update remove isset query */
			foreach ($data['subscriber_mail'] as $language_id => $value) {
				$this->db->query("INSERT INTO " .DB_PREFIX . "newsletter_mail_language SET 
				mail_id ='" . (int)$mail_id . "',
				language_id ='" . (int)$language_id . "',
				subject='".$this->db->escape($value['subject'])."',
				name='".$this->db->escape($value['name'])."',
				message='".$this->db->escape($value['message'])."'
				"); 
			}

		$this->db->query("DELETE FROM " . DB_PREFIX . "newsletter_assign_product WHERE mail_id = '" . (int)$mail_id . "'");
	
		if (isset($data['product'])) {
			foreach ($data['product'] as $product_id) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "newsletter_assign_product SET product_id = '" . (int)$product_id . "', mail_id = '" . (int)$mail_id . "'");
			}
		}
		
	}
	
	public function deleteMail($mail_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "newsletter_mail WHERE mail_id = '" . (int)$mail_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "newsletter_mail_language WHERE mail_id = '" . (int)$mail_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "newsletter_assign_product WHERE mail_id = '" . (int)$mail_id . "'");
		$this->cache->delete('product');

		$query=$this->db->query($sql);
	}
	
	public function getMail($mail_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "newsletter_mail where mail_id='".$mail_id."'";
		$query = $this->db->query($sql);
		return $query->row;
	}
	
	public  function getMailInfo($emailtype){
		$query=$this->db->query("select * from " . DB_PREFIX . "newsletter_mail vm LEFT JOIN " . DB_PREFIX . "newsletter_mail_language vml on(vm.mail_id=vml.mail_id) where vm.emailtype='" .$emailtype."'and vml.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		return $query->row;
		
	}
	public  function getMailbyId($mail_id){
		$query=$this->db->query("select * from " . DB_PREFIX . "newsletter_mail vm LEFT JOIN " . DB_PREFIX . "newsletter_mail_language vml on(vm.mail_id=vml.mail_id) where vm.mail_id='" .$mail_id."'and vml.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		return $query->row;	
		
	}

	public function getMails($data) {
		$sql = "SELECT * FROM " . DB_PREFIX . "newsletter_mail vm left join " . DB_PREFIX . "newsletter_mail_language vml ON (vm.mail_id = vml.mail_id) where vml.language_id = '" . (int)$this->config->get('config_language_id') . "' and vm.mail_id<>0";
				
		$sort_data = array(
			'vml.name',
			'vm.mail_id'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY vm.mail_id";
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

	public function getTotalMail($data) {
		$sql ="SELECT COUNT(*) AS total FROM " . DB_PREFIX . "newsletter_mail where mail_id<>0";
				
		$query = $this->db->query($sql);
		return $query->row['total'];
	}
	
	public function getMailLanguage($mail_id) {
		$mail_data = array();
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX ."newsletter_mail_language WHERE  mail_id = '" . (int)$mail_id . "'");
		foreach ($query->rows as $result) {
			$mail_data[$result['language_id']] = array(
				'name'		    => $result['name'],
				'message'		=> $result['message'],
				'subject'		=> $result['subject']
			);	
	 	}
		return $mail_data;
	}
	
	public function getProductAssign($mail_id) {
		$product_assign_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "newsletter_assign_product WHERE mail_id = '" . (int)$mail_id . "'");

		foreach ($query->rows as $result) {
			$product_assign_data[] = $result['product_id'];
		}

		return $product_assign_data;
	}

	public function getProduct($product_id) {
		$query = $this->db->query("SELECT * FROM  " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}
}
