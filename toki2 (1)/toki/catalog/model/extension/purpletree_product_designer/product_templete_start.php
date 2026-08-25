<?php
class ModelExtensionPurpletreeProductDesignerProductTempleteStart extends Model{
	
	public function gettemplates($product_id){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_design_templates  WHERE  product_id = '" . (int)$product_id . "' AND status='1' ORDER BY sort_order ASC");
			if ($query->num_rows) {
				return $query->rows;
			}
	}
	public function getInsertincart($product_id){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_templates_cart  WHERE  session_id = '" . $this->db->escape($this->session->getId()) . "' AND product_id = '" . (int)$product_id . "' AND design='1'");
			if ($query->num_rows) {
				return $query->row['id'];
			}
	}
	public function insertincart($data){
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_templates_cart  WHERE customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "' AND product_id = '" . (int)$data['product_id'] . "'");
			if ($query->num_rows) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "purpletree_templates_cart WHERE customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "' AND product_id = '" . (int)$data['product_id'] . "'");
			}
			if(isset($data['qtyprice'])) {
				$data['qtyprice'] = $data['qtyprice'];
			} else {
				$data['qtyprice'] = 0;
			}
			$this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_templates_cart SET customer_id = '" . (int)$this->customer->getId() . "', session_id = '" . $this->db->escape($this->session->getId()) . "', product_id = '" . (int)$data['product_id'] . "', design = '" . (int)$data['design'] . "', qtyprice = '" . (int)$data['qtyprice'] . "', `option` = '" . $this->db->escape(json_encode($data['option'])) . "', date_added = NOW()");
			return $this->db->getLastId();
	}
}