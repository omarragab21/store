<?php
class ModelExtensionModuleXtensionsCustomField extends Model {	
	
	public function addCustomField($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "custom_field` SET type = '" . $this->db->escape($data['type']) . "', value = '" . $this->db->escape($data['value']) . "', validation = '" . $this->db->escape($data['validation']) . "', location = '" . $this->db->escape($data['location']) . "', status = '" . (isset($data['status'])?1:0) . "'");

		$custom_field_id = $this->db->getLastId();
		
		//$this->db->query("DELETE FROM `" . DB_PREFIX . "xcustom_field` WHERE custom_field_id = '" . (int)$custom_field_id ."'");
		
		$this->db->query("INSERT INTO `" . DB_PREFIX . "xcustom_field` SET custom_field_id = '" . (int)$custom_field_id ."', xsort_order = '". $this->db->escape($data['xsort_order']) ."', regional_validation = '". $this->db->escape($data['regional_validation']) ."', list_display = '" . (isset($data['list_display'])?1:0) . "', order_display = '" . (isset($data['order_display'])?1:0) . "', email_display = '" . (isset($data['email_display'])?1:0) . "', invoice = '" . (isset($data['invoice'])?1:0) . "', isnumeric = '" . (isset($data['isnumeric'])?1:0) . "', identifier = '" .  $this->db->escape($data['identifier']) . "', mask = '" .  $this->db->escape($data['mask']) . "', minimum = '" . (int)$data['minimum'] . "', maximum = '" .  (int)$data['maximum'] . "'");
		
		foreach ($data['custom_field_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "custom_field_description  SET custom_field_id = '" . (int)$custom_field_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "xcustom_field_description SET custom_field_id = '" . (int)$custom_field_id . "', language_id = '" . (int)$language_id . "', error = '" . $this->db->escape($value['error']) . "', tips = '" . $this->db->escape($value['tips']) . "'");
		}

		if (isset($data['custom_field_customer_group'])) {
			foreach ($data['custom_field_customer_group'] as $custom_field_customer_group) {
				if (isset($custom_field_customer_group['customer_group_id'])) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "custom_field_customer_group SET custom_field_id = '" . (int)$custom_field_id . "', customer_group_id = '" . (int)$custom_field_customer_group['customer_group_id'] . "', required = '" . (int)(isset($custom_field_customer_group['required']) ? 1 : 0) . "'");
				}
			}
		}

		if (isset($data['custom_field_value'])) {
			foreach ($data['custom_field_value'] as $custom_field_value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "custom_field_value SET custom_field_id = '" . (int)$custom_field_id . "', sort_order = '" . (int)$custom_field_value['sort_order'] . "'");

				$custom_field_value_id = $this->db->getLastId();

				foreach ($custom_field_value['custom_field_value_description'] as $language_id => $custom_field_value_description) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "custom_field_value_description SET custom_field_value_id = '" . (int)$custom_field_value_id . "', language_id = '" . (int)$language_id . "', custom_field_id = '" . (int)$custom_field_id . "', name = '" . $this->db->escape($custom_field_value_description['name']) . "'");
				}
			}
		}
	}

	public function editCustomField($custom_field_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "custom_field` SET type = '" . $this->db->escape($data['type']) . "', value = '" . $this->db->escape($data['value']) . "', validation = '" . $this->db->escape($data['validation']) . "', location = '" . $this->db->escape($data['location']) . "', status = '" . (isset($data['status'])?1:0) . "' WHERE custom_field_id = '" . (int)$custom_field_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "xcustom_field` WHERE custom_field_id = '" . (int)$custom_field_id ."'");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "xcustom_field` SET custom_field_id = '" . (int)$custom_field_id ."', xsort_order = '". $this->db->escape($data['xsort_order']) ."', regional_validation = '". $this->db->escape($data['regional_validation']) ."', list_display = '" . (isset($data['list_display'])?1:0) . "', order_display = '" . (isset($data['order_display'])?1:0) . "', email_display = '" . (isset($data['email_display'])?1:0) . "', invoice = '" . (isset($data['invoice'])?1:0) . "', isnumeric = '" . (isset($data['isnumeric'])?1:0) . "', identifier = '" .  $this->db->escape($data['identifier']) . "', mask = '" .  $this->db->escape($data['mask']) . "', minimum = '" . (int)$data['minimum'] . "', maximum = '" .  (int)$data['maximum'] . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "custom_field_description WHERE custom_field_id = '" . (int)$custom_field_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "xcustom_field_description WHERE custom_field_id = '" . (int)$custom_field_id . "'");

		foreach ($data['custom_field_description'] as $language_id => $value) {			
			$this->db->query("INSERT INTO " . DB_PREFIX . "custom_field_description  SET custom_field_id = '" . (int)$custom_field_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "xcustom_field_description SET custom_field_id = '" . (int)$custom_field_id . "', language_id = '" . (int)$language_id . "', error = '" . $this->db->escape($value['error']) . "', tips = '" . $this->db->escape($value['tips']) . "'");		
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "custom_field_customer_group WHERE custom_field_id = '" . (int)$custom_field_id . "'");

		if (isset($data['custom_field_customer_group'])) {
			foreach ($data['custom_field_customer_group'] as $custom_field_customer_group) {
				if (isset($custom_field_customer_group['customer_group_id'])) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "custom_field_customer_group SET custom_field_id = '" . (int)$custom_field_id . "', customer_group_id = '" . (int)$custom_field_customer_group['customer_group_id'] . "', required = '" . (int)(isset($custom_field_customer_group['required']) ? 1 : 0) . "'");
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "custom_field_value WHERE custom_field_id = '" . (int)$custom_field_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "custom_field_value_description WHERE custom_field_id = '" . (int)$custom_field_id . "'");

		if (isset($data['custom_field_value'])) {
			foreach ($data['custom_field_value'] as $custom_field_value) {
				if ($custom_field_value['custom_field_value_id']) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "custom_field_value SET custom_field_value_id = '" . (int)$custom_field_value['custom_field_value_id'] . "', custom_field_id = '" . (int)$custom_field_id . "', sort_order = '" . (int)$custom_field_value['sort_order'] . "'");
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "custom_field_value SET custom_field_id = '" . (int)$custom_field_id . "', sort_order = '" . (int)$custom_field_value['sort_order'] . "'");
				}

				$custom_field_value_id = $this->db->getLastId();

				foreach ($custom_field_value['custom_field_value_description'] as $language_id => $custom_field_value_description) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "custom_field_value_description SET custom_field_value_id = '" . (int)$custom_field_value_id . "', language_id = '" . (int)$language_id . "', custom_field_id = '" . (int)$custom_field_id . "', name = '" . $this->db->escape($custom_field_value_description['name']) . "'");
				}
			}
		}
	}

	public function deleteCustomField($custom_field_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "custom_field` WHERE custom_field_id = '" . (int)$custom_field_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "xcustom_field` WHERE custom_field_id = '" . (int)$custom_field_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "custom_field_description` WHERE custom_field_id = '" . (int)$custom_field_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "xcustom_field_description` WHERE custom_field_id = '" . (int)$custom_field_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "custom_field_customer_group` WHERE custom_field_id = '" . (int)$custom_field_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "custom_field_value` WHERE custom_field_id = '" . (int)$custom_field_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "custom_field_value_description` WHERE custom_field_id = '" . (int)$custom_field_id . "'");
	}

	public function getCustomField($custom_field_id) {
		//$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "custom_field` cf LEFT JOIN " . DB_PREFIX . "custom_field_description cfd ON (cf.custom_field_id = cfd.custom_field_id) WHERE cf.custom_field_id = '" . (int)$custom_field_id . "' AND cfd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		$query = $this->db->query("SELECT *, cf.custom_field_id, cfd.language_id  FROM `" . DB_PREFIX . "custom_field` cf LEFT JOIN `" . DB_PREFIX . "xcustom_field` xcf ON (cf.custom_field_id = xcf.custom_field_id) LEFT JOIN `" . DB_PREFIX . "custom_field_description` cfd ON (cf.custom_field_id = cfd.custom_field_id) LEFT JOIN `" . DB_PREFIX . "xcustom_field_description` xcfd ON (cfd.custom_field_id = xcfd.custom_field_id AND cfd.language_id = xcfd.language_id) WHERE cf.custom_field_id = '" . (int)$custom_field_id . "' AND cfd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getCustomFields($data = array()) {
		if (empty($data['filter_customer_group_id'])) {
			$sql = "SELECT *, cf.custom_field_id, cfd.language_id  FROM `" . DB_PREFIX . "custom_field` cf LEFT JOIN `" . DB_PREFIX . "xcustom_field` xcf ON (cf.custom_field_id = xcf.custom_field_id) LEFT JOIN " . DB_PREFIX . "custom_field_description cfd ON (cf.custom_field_id = cfd.custom_field_id) LEFT JOIN `" . DB_PREFIX . "xcustom_field_description` xcfd ON (cfd.custom_field_id = xcfd.custom_field_id AND cfd.language_id = xcfd.language_id) WHERE cfd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		} else {
			$sql = "SELECT *, cf.custom_field_id, cfd.language_id  FROM " . DB_PREFIX . "custom_field_customer_group cfcg LEFT JOIN `" . DB_PREFIX . "custom_field` cf ON (cfcg.custom_field_id = cf.custom_field_id) LEFT JOIN `" . DB_PREFIX . "xcustom_field` xcf ON (cf.custom_field_id = xcf.custom_field_id) LEFT JOIN " . DB_PREFIX . "custom_field_description cfd ON (cf.custom_field_id = cfd.custom_field_id) LEFT JOIN `" . DB_PREFIX . "xcustom_field_description` xcfd ON (cfd.custom_field_id = xcfd.custom_field_id AND cfd.language_id = xcfd.language_id) WHERE cfd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		}

		if (!empty($data['filter_name'])) {
			$sql .= " AND cfd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_customer_group_id'])) {
			$sql .= " AND cfcg.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
		}

		$sort_data = array(
			'cfd.name',
			'cf.type',
			'cf.location',
			'cf.status',
			'xcf.xsort_order'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY cfd.name";
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

	public function getCustomFieldDescriptions($custom_field_id) {
		$custom_field_data = array();

		$query = $this->db->query("SELECT *, cfd.custom_field_id, cfd.language_id  FROM " . DB_PREFIX . "custom_field_description cfd LEFT JOIN " . DB_PREFIX . "xcustom_field_description xcfd ON (cfd.custom_field_id = xcfd.custom_field_id AND cfd.language_id = xcfd.language_id) WHERE cfd.custom_field_id = '" . (int)$custom_field_id . "'");

		foreach ($query->rows as $result) {
			$custom_field_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $custom_field_data;
	}
	public function getCustomFieldLanguage($custom_field_id) {
		$custom_field_data = array();

		$query = $this->db->query( "SELECT *, cfd.custom_field_id, cfd.language_id  FROM " . DB_PREFIX . "custom_field_description cfd LEFT JOIN " . DB_PREFIX . "xcustom_field_description xcfd ON (cfd.custom_field_id = xcfd.custom_field_id AND cfd.language_id = xcfd.language_id) WHERE cfd.custom_field_id = '" . (int)$custom_field_id . "'");

		foreach ($query->rows as $result) {
			$custom_field_data[$result['language_id']] = $result;
		}

		return $custom_field_data;
	}

	public function getCustomFieldValue($custom_field_value_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "custom_field_value cfv LEFT JOIN " . DB_PREFIX . "custom_field_value_description cfvd ON (cfv.custom_field_value_id = cfvd.custom_field_value_id) WHERE cfv.custom_field_value_id = '" . (int)$custom_field_value_id . "' AND cfvd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getCustomFieldValues($custom_field_id) {
		$custom_field_value_data = array();

		$custom_field_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "custom_field_value cfv LEFT JOIN " . DB_PREFIX . "custom_field_value_description cfvd ON (cfv.custom_field_value_id = cfvd.custom_field_value_id) WHERE cfv.custom_field_id = '" . (int)$custom_field_id . "' AND cfvd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY cfv.sort_order ASC");

		foreach ($custom_field_value_query->rows as $custom_field_value) {
			$custom_field_value_data[$custom_field_value['custom_field_value_id']] = array(
				'custom_field_value_id' => $custom_field_value['custom_field_value_id'],
				'name'                  => $custom_field_value['name']
			);
		}

		return $custom_field_value_data;
	}

	public function getCustomFieldCustomerGroups($custom_field_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "custom_field_customer_group` WHERE custom_field_id = '" . (int)$custom_field_id . "'");

		return $query->rows;
	}

	public function getCustomFieldValueDescriptions($custom_field_id) {
		$custom_field_value_data = array();

		$custom_field_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "custom_field_value WHERE custom_field_id = '" . (int)$custom_field_id . "'");

		foreach ($custom_field_value_query->rows as $custom_field_value) {
			$custom_field_value_description_data = array();

			$custom_field_value_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "custom_field_value_description WHERE custom_field_value_id = '" . (int)$custom_field_value['custom_field_value_id'] . "'");

			foreach ($custom_field_value_description_query->rows as $custom_field_value_description) {
				$custom_field_value_description_data[$custom_field_value_description['language_id']] = array('name' => $custom_field_value_description['name']);
			}

			$custom_field_value_data[] = array(
				'custom_field_value_id'          => $custom_field_value['custom_field_value_id'],
				'custom_field_value_description' => $custom_field_value_description_data,
				'sort_order'                     => $custom_field_value['sort_order']
			);
		}

		return $custom_field_value_data;
	}

	public function getTotalCustomFields() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "custom_field`");

		return $query->row['total'];
	}

	public function getCustomFieldsAll($customer_group_id = 0) {
		$custom_field_data = array();

		if (!$customer_group_id) {
			$custom_field_query = $this->db->query("SELECT *, cf.custom_field_id, cfd.language_id  FROM `" . DB_PREFIX . "custom_field` cf LEFT JOIN `" . DB_PREFIX . "xcustom_field` xcf ON (cf.custom_field_id = xcf.custom_field_id) LEFT JOIN `" . DB_PREFIX . "custom_field_description` cfd ON (cf.custom_field_id = cfd.custom_field_id) LEFT JOIN `" . DB_PREFIX . "xcustom_field_description` xcfd ON (cfd.custom_field_id = xcfd.custom_field_id AND cfd.language_id = xcfd.language_id) WHERE cf.status = '1' AND cfd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cf.status = '1' ORDER BY cf.sort_order ASC");
		} else {
			$custom_field_query = $this->db->query("SELECT *, cf.custom_field_id, cfd.language_id  FROM `" . DB_PREFIX . "custom_field_customer_group` cfcg LEFT JOIN `" . DB_PREFIX . "custom_field` cf ON (cfcg.custom_field_id = cf.custom_field_id) LEFT JOIN `" . DB_PREFIX . "xcustom_field` xcf ON (cf.custom_field_id = xcf.custom_field_id) LEFT JOIN `" . DB_PREFIX . "custom_field_description` cfd ON (cf.custom_field_id = cfd.custom_field_id) LEFT JOIN `" . DB_PREFIX . "xcustom_field_description` xcfd ON (cfd.custom_field_id = xcfd.custom_field_id AND cfd.language_id = xcfd.language_id) WHERE cf.status = '1' AND cfd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cfcg.customer_group_id = '" . (int)$customer_group_id . "' ORDER BY cf.sort_order ASC");
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
				'mask'				 => $custom_field['mask'],
				'minimum'			 => $custom_field['minimum'],
				'maximum'			 => $custom_field['maximum'],
				'identifier'		 => $custom_field['identifier'],
				'isnumeric'			 => $custom_field['isnumeric'],
				'email_display'		 => $custom_field['email_display'],
				'order_display'		 => $custom_field['order_display'],
				'list_display'		 => $custom_field['list_display'],
				'invoice'			 => $custom_field['invoice'],
				'validation'		 => $custom_field['validation'],
			);
		}

		return $custom_field_data;
	}

	public function getCustomFieldByIdentifier($fieldData,$location){
		$result = array();
		$fields = $this->getCustomFieldsAll(0);
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
					}else{
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
		$payment['company_id'] = '';
		$payment['tax_id'] = '';
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
		$shipping['company_id'] = '';
		$shipping['tax_id'] = '';
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
			foreach($this->getCustomFieldsAll() as $field){
				$stringP .= '{'.$field['identifier'].'}'."\n";
			}
			$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{company_id}' . "\n" . '{tax_id}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" .$stringP. '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
		}

		$find = array(
	  			'{firstname}',
	  			'{lastname}',
	  			'{company}',
				'{company_id}',
				'{tax_id}',
      			'{address_1}',
      			'{address_2}',
     			'{city}',
      			'{postcode}',
      			'{zone}',
				'{zone_code}',
      			'{country}'
      			);
      			foreach($this->getCustomFieldsAll() as $field){
      				array_push($find, '{'.$field['identifier'].'}');
      			}
      			$replace = array(
	  			'firstname' => $result['firstname'],
	  			'lastname'  => $result['lastname'],
	  			'company'   => $result['company'],
      			'company_id'=> $result['company_id'],
      			'tax_id'    => $result['tax_id'],
      			'address_1' => $result['address_1'],
      			'address_2' => $result['address_2'],
      			'city'      => $result['city'],
      			'postcode'  => $result['postcode'],
      			'zone'      => $result['zone'],
				'zone_code' => $result['zone_code'],
      			'country'   => $result['country']  
      			);
      			 
      			$replace = array_merge($replace,$result['identified_data']);

      			return str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
	}
}
