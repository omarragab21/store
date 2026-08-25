<?php
class Modelextensiontmdsmsotpverify extends Model {
	public function accountverify($customer_id,$data){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tmdsmsverify WHERE customer_id='".$this->customer->getId()."'");

		if(isset($query->row['otp'])){
			$codematch= $query->row['otp'];
		}else{
			$codematch='';
		}
		
		
		if(isset($codematch) == $data['otpconfirm']){

		   $query=$this->db->query("UPDATE " . DB_PREFIX . "tmdsmsverify set verify_status ='1' where customer_id ='".$this->customer->getId() ."'");
		  
			$queryemail = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" .$this->customer->getId(). "'");
			if(isset($queryemail->row['email'])){
				$email = $queryemail->row['email'];
			}else{
				$email='';
				$this->response->redirect($this->url->link('account/account', '', true));
			}

  		   }

  		   
  		   return $customer_id;
	}

	public function getotp($customer_id){
	    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tmdsmsverify WHERE customer_id='".$this->customer->getId()."'");
		return $query->row;
	}

	public function sendotp($customer_id){
		$code = rand(100000,999999);
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tmdsmsverify WHERE customer_id = '" . $this->customer->getId() . "'");
        if(isset($query->row['customer_id']))
        {
		$this->db->query("UPDATE " . DB_PREFIX . "tmdsmsverify SET otp = '" . $code . "' WHERE customer_id = '" . $this->customer->getId() . "'");
        }
        else
        {
        	$this->db->query("insert " . DB_PREFIX . "tmdsmsverify SET otp = '" . $code . "' , customer_id = '" . $this->customer->getId() . "'");
      
        }
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id='".$this->customer->getId()."'");
		$customer=$query->row;
		
		if(isset($customer['telephone'])) {
			$telephone =$customer['telephone']; 
		} else {
			$telephone = '';
		}
		
		$tmdsms=$this->config->get('tmdsms_otpmsg');
		$tmdsmsurl = $this->config->get('tmdsms_url');
		$dltid = $this->config->get('tmdsms_otpmsg_dltid');


		$find = array(
			'{code}',										
			' ',										
		);
		
		$replace = array(
			'code'  =>$code,
		);

     	$mobilemessage = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $tmdsms))));
        $mobilemessage=str_replace(' ',' ',$mobilemessage);
     	if(!empty($mobilemessage)){
			$sms_data = array(
				'telephone' 	=> $customer['telephone'],
				'message' 		=> $mobilemessage,
				'dltid'         => $dltid,
 			);

			$this->load->model('extension/tmdsms/tmdsms');
			$this->model_extension_tmdsms_tmdsms->sendSms($sms_data);
		}

	}

	/*public function getnotverifyByEmail($email) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		$query1 = $this->db->query("SELECT * FROM " . DB_PREFIX . "tmdsmsverify WHERE customer_id='".$query->row['customer_id']."'");

		return $query->row;
	}*/
}
