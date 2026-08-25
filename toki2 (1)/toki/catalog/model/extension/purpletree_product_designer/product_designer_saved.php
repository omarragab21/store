<?php
class ModelExtensionPurpletreeProductDesignerProductDesignerSaved extends Model{
	
	public function deleteRecord($id){
		$this->db->query("DELETE FROM " . DB_PREFIX . "purpletree_designer_savelater WHERE id=".(int)$id);
	}
	public function getTotalRecords(){
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "purpletree_designer_savelater pds LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = pds.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND pds.customer_id = '" . (int)$this->customer->getId() . "' AND pds.customer_id != '0' ORDER BY pds.id DESC");
		
		return $query->row['total'];
	}
	public function getRecords($start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 1;
		}
		$query = $this->db->query("SELECT pd.name,pds.*,pds.status AS pdsstatus,pds.id AS pdsid FROM " . DB_PREFIX . "purpletree_designer_savelater pds LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = pds.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND pds.customer_id = '" . (int)$this->customer->getId() . "' AND pds.customer_id != '0' ORDER BY pds.id DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	} 
}