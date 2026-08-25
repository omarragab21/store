<?php
class ModelExtensionModuleXtensionsXCustomField extends Model {	
		
	public function getCustomField($custom_field_id) {
		$query = $this->db->query("SELECT *, cf.custom_field_id, cfd.language_id FROM `" . DB_PREFIX . "custom_field` cf LEFT JOIN `" . DB_PREFIX . "xcustom_field` xcf ON (cf.custom_field_id = xcf.custom_field_id) LEFT JOIN `" . DB_PREFIX . "custom_field_description` cfd ON (cf.custom_field_id = cfd.custom_field_id) LEFT JOIN `" . DB_PREFIX . "xcustom_field_description` xcfd ON (cfd.custom_field_id = xcfd.custom_field_id AND cfd.language_id = xcfd.language_id) WHERE cf.status = '1' AND cf.custom_field_id = '" . (int)$custom_field_id . "' AND cfd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getCustomFields($customer_group_id = 0) {
		$custom_field_data = array();

		if (!$customer_group_id) {
			$custom_field_query = $this->db->query("SELECT *, cf.custom_field_id, cfd.language_id FROM `" . DB_PREFIX . "custom_field` cf LEFT JOIN `" . DB_PREFIX . "xcustom_field` xcf ON (cf.custom_field_id = xcf.custom_field_id) LEFT JOIN `" . DB_PREFIX . "custom_field_description` cfd ON (cf.custom_field_id = cfd.custom_field_id) LEFT JOIN `" . DB_PREFIX . "xcustom_field_description` xcfd ON (cfd.custom_field_id = xcfd.custom_field_id AND cfd.language_id = xcfd.language_id) WHERE cf.status = '1' AND cfd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cf.status = '1' ORDER BY cf.sort_order ASC");
		} else {
			$custom_field_query = $this->db->query("SELECT *, cf.custom_field_id, cfd.language_id FROM `" . DB_PREFIX . "custom_field_customer_group` cfcg LEFT JOIN `" . DB_PREFIX . "custom_field` cf ON (cfcg.custom_field_id = cf.custom_field_id) LEFT JOIN `" . DB_PREFIX . "xcustom_field` xcf ON (cf.custom_field_id = xcf.custom_field_id) LEFT JOIN `" . DB_PREFIX . "custom_field_description` cfd ON (cf.custom_field_id = cfd.custom_field_id) LEFT JOIN `" . DB_PREFIX . "xcustom_field_description` xcfd ON (cfd.custom_field_id = xcfd.custom_field_id AND cfd.language_id = xcfd.language_id) WHERE cf.status = '1' AND cfd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cfcg.customer_group_id = '" . (int)$customer_group_id . "' ORDER BY cf.sort_order ASC");
		}

		foreach ($custom_field_query->rows as $custom_field) {
			$custom_field_value_data = array();

			if ($custom_field['type'] == 'select' || $custom_field['type'] == 'radio' || $custom_field['type'] == 'checkbox') {
				$custom_field_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "custom_field_value cfv LEFT JOIN " . DB_PREFIX . "custom_field_value_description cfvd ON (cfv.custom_field_value_id = cfvd.custom_field_value_id) WHERE cfv.custom_field_id = '" . (int)$custom_field['custom_field_id'] . "' AND cfvd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY cfv.sort_order ASC");

				foreach ($custom_field_value_query->rows as $custom_field_value) {
					$custom_field_value_data[] = array(
						'custom_field_value_id' => $custom_field_value['custom_field_value_id'],
						'name'                  => $custom_field_value['name']
					);
				}
			}			

			$custom_field_data[] = array(
				'custom_field_id'    => $custom_field['custom_field_id'],
				'custom_field_value' => $custom_field_value_data,
				'name'               => $custom_field['name'],
				'type'               => $custom_field['type'],
				'value'              => $custom_field['value'],
				'location'           => $custom_field['location'],
				'required'           => empty($custom_field['required']) || $custom_field['required'] == 0 ? false : true,
				'sort_order'         => $custom_field['xsort_order'],
				'error'				 => $custom_field['error'],
				'tips'				 => $custom_field['tips'],
				'status'			 => $custom_field['status'],
				'is_mask'		     => ($custom_field['type']=='text' && $custom_field['mask'])?true:false,
				'mask'				 => $custom_field['mask'],
				'minimum'			 => $custom_field['minimum'],
				'maximum'			 => $custom_field['maximum'],
				'identifier'		 => $custom_field['identifier'],
				'isnumeric'			 => $custom_field['isnumeric'],
				'email_display'		 => $custom_field['email_display'],
				'order_display'		 => $custom_field['order_display'],
				'list_display'		 => $custom_field['list_display'],
				'invoice'			 => $custom_field['invoice'],
				'validation'         => $custom_field['validation'],
				'regional_validation'=> $custom_field['regional_validation'],
			);
		}

		return $custom_field_data;
	}

	public function getCustomFieldByIdentifier($fieldData,$location){
		$result = array();
		$fields = $this->getCustomFields(0);
		foreach ($fields as $field){
			if($field['location']==$location){
				if(isset($fieldData[$field['custom_field_id']])){
					if(($field['type']!='select') && ($field['type']!='radio') && ($field['type']!='checkbox')){
						$result[$field['identifier']] = $fieldData[$field['custom_field_id']];
					}elseif(($field['type']=='select') || ($field['type']=='radio')){
						$result[$field['identifier']] = $this->getCustomFieldValueById($field['custom_field_id'],$fieldData[$field['custom_field_id']],$field['type']);
					}elseif($field['type']=='checkbox' ){
						$string = '';
						foreach ($field['custom_field_value'] as $custom_field_value) {
							if(in_array($custom_field_value['custom_field_value_id'], $fieldData[$field['custom_field_id']])) {
								$string .= $this->getCustomFieldValueById($field['custom_field_id'],$custom_field_value['custom_field_value_id'],$field['type'])." | ";
							}
						}
						$result[$field['identifier']] = rtrim($string," | ");
					}
					else{
						$result[$field['identifier']] = '';
					}
				}else{
					$result[$field['identifier']] = '';
				}
			}else{
				$result[$field['identifier']] = '';
			}
		}
		return $result;
	}

	public function getCustomFieldValueById($custom_field_id,$custom_field_value_id,$type){
		$query = $this->db->query("SELECT name FROM `" . DB_PREFIX . "custom_field_value_description` cf WHERE cf.custom_field_id = '" . (int)$custom_field_id . "' AND cf.custom_field_value_id = '" . (int)$custom_field_value_id . "' AND cf.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		if($query->num_rows){
			return $query->row['name'];
		}else {
			return "";
		}
	}

	public function getFormattedAddressForOrder($order){

		$payment['firstname'] = $order['payment_firstname'];
		$payment['lastname'] = $order['payment_lastname'];
		$payment['company'] = $order['payment_company'];
		$payment['address_1'] = $order['payment_address_1'];
		$payment['address_2'] = $order['payment_address_2'];
		$payment['address_format'] = $order['payment_address_format'];
		$payment['city'] = $order['payment_city'];
		$payment['postcode'] = $order['payment_postcode'];
		$payment['zone'] = $order['payment_zone'];
		$payment['zone_code'] = $order['payment_zone_code'];
		$payment['country'] = $order['payment_country'];
		$shipping['firstname'] = $order['shipping_firstname'];
		$shipping['lastname'] = $order['shipping_lastname'];
		$shipping['company'] = $order['shipping_company'];
		$shipping['address_1'] = $order['shipping_address_1'];
		$shipping['address_2'] = $order['shipping_address_2'];
		$shipping['address_format'] = $order['shipping_address_format'];
		$shipping['city'] = $order['shipping_city'];
		$shipping['postcode'] = $order['shipping_postcode'];
		$shipping['zone'] = $order['shipping_zone'];
		$shipping['zone_code'] = $order['shipping_zone_code'];
		$shipping['country'] = $order['shipping_country'];
		$order_query = $this->getOrderCustomFields($order['order_id']);
		$payment['identified_data'] = $this->getCustomFieldByIdentifier($this->xtensions_checkout->unserialize($order_query['payment_custom_field']),'address');
		$shipping['identified_data'] = $this->getCustomFieldByIdentifier($this->xtensions_checkout->unserialize($order_query['shipping_custom_field']),'address');
		return array('payment_address_formatted' => $this->getFormattedAddress($payment),
					'shipping_address_formatted' => $this->getFormattedAddress($shipping),
		);
	}

	public function getOrderCustomFields($order_id){
		$order_query = $this->db->query("SELECT custom_field, payment_custom_field, shipping_custom_field FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");

		return $order_query->row;
	}

	public function getFormattedAddress($result,$getCustomFieldByIdentifier=false){
		if ($result['address_format']) {
			$format = $result['address_format'];
		} else {
			$stringP = '';
			foreach($this->getCustomFields() as $field){
				$stringP .= '{'.$field['identifier'].'}'."\n";
			}
			$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" .$stringP. '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
		}

		$find = array(
	  			'{firstname}',
	  			'{lastname}',
	  			'{company}',				
      			'{address_1}',
      			'{address_2}',
     			'{city}',
      			'{postcode}',
      			'{zone}',
				'{zone_code}',
      			'{country}'
      			);
      			foreach($this->getCustomFields() as $field){
      				array_push($find, '{'.$field['identifier'].'}');
      			}
      			$replace = array(
	  			'firstname' => $result['firstname'],
	  			'lastname'  => $result['lastname'],
	  			'company'   => $result['company'],      			
      			'address_1' => $result['address_1'],
      			'address_2' => $result['address_2'],
      			'city'      => $result['city'],
      			'postcode'  => $result['postcode'],
      			'zone'      => $result['zone'],
				'zone_code' => $result['zone_code'],
      			'country'   => $result['country']  
      			);

      			$replace = array_merge($replace,$result['identified_data']);
				$trim_replace = array();
      			foreach ($replace as $key=>$value){
      				$trim_replace[$key] = is_array($value)?$value:trim($value);
      			}
      			return str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $trim_replace, $format))));
	}

	public function getLinearAddress($result,$getCustomFieldByIdentifier=false){
		$xtensions_data = $this->xtensions_checkout->getXtensionsData($this->config->get('config_store_id'), 'xtensions_best_checkout');
		$xconfig = $xtensions_data['xconfig'];	
		
		if(isset($xconfig['options']['linear_address_format']) && $xconfig['options']['linear_address_format']){
			$format = $xconfig['options']['linear_address_format'];
		} else {
			$format = '{firstname} {lastname}, {address_1} {address_2}, {city}, {zone}, {country}';
		}

		$find = array(
	  			'{firstname}',
	  			'{lastname}',
	  			'{company}',				
      			'{address_1}',
      			'{address_2}',
     			'{city}',
      			'{postcode}',
      			'{zone}',
				'{zone_code}',
      			'{country}'
      			);
      			foreach($this->getCustomFields() as $field){
      				array_push($find, '{'.$field['identifier'].'}');
      			}
      			$replace = array(
	  			'firstname' => $result['firstname'],
	  			'lastname'  => $result['lastname'],
	  			'company'   => $result['company'],      			
      			'address_1' => $result['address_1'],
      			'address_2' => $result['address_2'],
      			'city'      => $result['city'],
      			'postcode'  => $result['postcode'],
      			'zone'      => $result['zone'],
				'zone_code' => $result['zone_code'],
      			'country'   => $result['country']  
      			);

      			$replace = array_merge($replace,$result['identified_data']);
      			$trim_replace = array();
      			foreach ($replace as $key=>$value){
      				$trim_replace[$key] = is_array($value)?$value:trim($value);
      			}

      			return trim(str_replace($find, $trim_replace, $format));
	}

	public function getAccountCustomFields($customer_id){
		$query = $this->db->query("SELECT custom_field FROM `" . DB_PREFIX . "customer` WHERE customer_id = '" . (int)$customer_id . "'");
		if($query->num_rows){
			return $this->xtensions_checkout->unserialize($query->row['custom_field']);
		}else{
			return array();
		}
	}
	
	public function editOrderXtensions($order_id,$data){
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET  marketing_id = '" . (int)$data['marketing_id'] . "', tracking = '" . $this->db->escape($data['tracking']) . "', language_id = '" . (int)$data['language_id'] . "', currency_id = '" . (int)$data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float)$data['currency_value'] . "', ip = '" . $this->db->escape($data['ip']) . "', forwarded_ip = '" .  $this->db->escape($data['forwarded_ip']) . "', user_agent = '" . $this->db->escape($data['user_agent']) . "', accept_language = '" . $this->db->escape($data['accept_language']) . "', date_added = NOW() WHERE order_id = '" . (int)$order_id . "'");
	}
	
	//Validate EU VAT Number Country
	public function getCountryByIsoCode2($iso_code_2) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE iso_code_2 = '" . $this->db->escape($iso_code_2) . "' AND status = '1'");
		if($query->num_rows){
			return $query->row;
		}
		return array();
	}
	
	public function editTelephone($customer_id,$telephone,$telephone_country_code){
		$best_config_options = $this->xtensions_checkout->getXtensionsData($this->config->get('config_store_id'), 'xtensions_best_checkout')['xconfig']['options'];	
		if(isset($best_config_options['country_code_location']) && $best_config_options['country_code_location'] == 'customer'){
			$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET telephone = '" . $this->db->escape('+'.$telephone_country_code.' '.$telephone) . "' WHERE customer_id = '" . (int)$customer_id . "'");			
		}else{
			$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET telephone = '" . $this->db->escape($telephone) . "' WHERE customer_id = '" . (int)$customer_id . "'");			
		}
		$this->addCustomerCountryCode($customer_id, '+'.$telephone_country_code, '+'.$telephone_country_code. ' ' .$telephone);
	}
	
	public function addCustomerCountryCode($customer_id,$telephone_country_code,$full_telephone){
		$this->db->query("DELETE FROM `" . DB_PREFIX . "xcustomer_data` WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "xcustomer_data` SET customer_id = '" . (int)$customer_id . "', telephone_country_code = '" . $this->db->escape($telephone_country_code) . "', full_telephone = '" . $this->db->escape($full_telephone) . "'");
	}
	
	/* public function addOrderCountryCode($order_id,$telephone_country_code,$telephone){
		$this->db->query("DELETE FROM `" . DB_PREFIX . "xorder_data` WHERE order_id = '" . (int)$order_id . "'");
		$best_config_options = $this->xtensions_checkout->getXtensionsData($this->config->get('config_store_id'), 'xtensions_best_checkout')['xconfig']['options'];	
		if(isset($best_config_options['country_code_location']) && $best_config_options['country_code_location'] == 'customer'){
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET telephone = '" . $this->db->escape($telephone) . "' WHERE order_id = '" . (int)$order_id . "'");
			$this->db->query("INSERT INTO `" . DB_PREFIX . "xorder_data` SET order_id = '" . (int)$order_id . "', telephone_country_code = '" . $this->db->escape($telephone_country_code) . "', full_telephone = '" . $this->db->escape($telephone) . "', country_code_location = 'customer'");
		}else{
			$this->db->query("INSERT INTO `" . DB_PREFIX . "xorder_data` SET order_id = '" . (int)$order_id . "', telephone_country_code = '" . $this->db->escape($telephone_country_code) . "', full_telephone = '" . $this->db->escape($telephone_country_code.' '.$telephone) . "'");	
		}		
	} */
	
	public function setOrderCountryCode($order_id){
		$this->db->query("DELETE FROM `" . DB_PREFIX . "xorder_data` WHERE order_id = '" . (int)$order_id . "'");
		$best_config_options = $this->xtensions_checkout->getXtensionsData($this->config->get('config_store_id'), 'xtensions_best_checkout')['xconfig']['options'];	
		if($this->customer->isLogged()){
			if(isset($best_config_options['country_code_location']) && $best_config_options['country_code_location'] == 'customer'){
				$customer_data = $this->separateCountryCode();				
				$this->db->query("INSERT INTO `" . DB_PREFIX . "xorder_data` SET order_id = '" . (int)$order_id . "', telephone_country_code = '" . $this->db->escape($customer_data['country_code']) . "', full_telephone = '" . $this->db->escape($customer_data['country_code'].' '.$customer_data['telephone']) . "', country_code_location = 'customer'");
			}else{
				$telephone_country_code = $this->getCountryCode($this->customer->getId());
				$this->db->query("INSERT INTO `" . DB_PREFIX . "xorder_data` SET order_id = '" . (int)$order_id . "', telephone_country_code = '" . $this->db->escape($telephone_country_code) . "', full_telephone = '" . $this->db->escape($telephone_country_code.' '.$this->customer->getTelephone()) . "'");
			}			
		}else{
			$telephone_country_code = (isset($this->session->data['guest']['country_code'])?$this->session->data['guest']['country_code']:'');
			$telephone = (isset($this->session->data['guest']['telephone'])?$this->session->data['guest']['telephone']:'');
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET telephone = '" . $this->db->escape($telephone_country_code.' '.$telephone) . "' WHERE order_id = '" . (int)$order_id . "'");
			if(isset($best_config_options['country_code_location']) && $best_config_options['country_code_location'] == 'customer'){				
				$this->db->query("INSERT INTO `" . DB_PREFIX . "xorder_data` SET order_id = '" . (int)$order_id . "', telephone_country_code = '" . $this->db->escape($telephone_country_code) . "', full_telephone = '" . $this->db->escape($telephone_country_code.' '.$telephone) . "', country_code_location = 'customer'");
			}else{
				$this->db->query("INSERT INTO `" . DB_PREFIX . "xorder_data` SET order_id = '" . (int)$order_id . "', telephone_country_code = '" . $this->db->escape($telephone_country_code) . "', full_telephone = '" . $this->db->escape($telephone_country_code.' '.$telephone) . "'");
			}
			//$this->addOrderCountryCode($order_id, $telephone_country_code, (isset($this->session->data['guest']['telephone'])?$this->session->data['guest']['telephone']:''));
		}
	}
	
	public function getCountryCode($customer_id) {
		$query = $this->db->query("SELECT telephone_country_code FROM " . DB_PREFIX . "xcustomer_data WHERE customer_id = '" . (int)$customer_id . "'");
		if($query->num_rows)
			return $query->row['telephone_country_code'];
		else 
			return '';
	}
	
	public function separateCountryCode(){
		$country_code = '';
		$telephone = '';
		$telephone_number = explode(" ",$this->customer->getTelephone());
		if(count($telephone_number) >= 2){
			if(isset($telephone_number[0]) && isset($telephone_number[1])){
				$country_code = $customer_info['country_code'] = $telephone_number[0];
				array_shift($telephone_number);
				$telephone_number_string = implode('', $telephone_number);
				$telephone = preg_replace('/[^0-9]/', '', $telephone_number_string);				
			}
		}
		return array('telephone'=>$telephone,'country_code'=>$country_code);
	}
}
