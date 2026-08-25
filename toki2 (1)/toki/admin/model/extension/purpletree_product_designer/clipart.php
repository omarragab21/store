<?php
class ModelExtensionPurpletreeProductDesignerClipart extends Model{

public function getClipartImages(){

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_clipart_image ORDER BY clipart_id ASC");
		return $query->rows;

		}
	public function addClipart($data){
		if (isset($data['product_image'])) {
			
			foreach ($data['product_image'] as $product_image) {
				 if(!empty($product_image['clipart_id'])){
					$this->db->query("UPDATE " . DB_PREFIX . "purpletree_clipart_image SET clipart_image = '" . $this->db->escape($product_image['image']) . "' WHERE clipart_id = '" . (int)$product_image['clipart_id'] . "'");
				}
				 
				 if(empty($product_image['clipart_id'])){			
				   if(!empty($product_image['image'])){
				   $this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_clipart_image SET clipart_image = '" . $this->db->escape($product_image['image']) . "'");			
				   }
				 }
			}
		}	
	}
	public function deleteClipart($clipart_id){				
				$this->db->query("DELETE FROM " . DB_PREFIX . "purpletree_clipart_image WHERE clipart_id = '" . (int)$clipart_id . "'");				
			}
	public function updateDatabase(){
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "purpletree_product_design` (
  							`id` int(11) NOT NULL AUTO_INCREMENT,
							`product_id` int(11) NOT NULL,
							`total_layers` int(11) NOT NULL,
							`total_text_layers` int(11) NOT NULL,
							`total_clipart_layers` int(11) NOT NULL,
							`total_image_layers` int(11) NOT NULL,
							`status` tinyint(1) NOT NULL,
  							PRIMARY KEY (`id`))
						");
						
			$field_query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_product_design` LIKE 'total_layers'");
			if (!$field_query->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_product_design`  ADD `total_layers` int(10) DEFAULT 999   AFTER `product_id`");
			}
			$field_query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_product_design` LIKE 'total_text_layers'");
			if (!$field_query->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_product_design`  ADD `total_text_layers` int(10) DEFAULT 99 AFTER `total_layers`");
			}
			
			$field_query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_product_design` LIKE 'total_clipart_layers'");
			if (!$field_query->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_product_design`  ADD `total_clipart_layers` int(10) DEFAULT 99 AFTER `total_text_layers`");
			}
			
			$field_query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_product_design` LIKE 'total_image_layers'");
			if (!$field_query->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_product_design`  ADD `total_image_layers` int(10) DEFAULT 99 AFTER `total_clipart_layers`");
			}
			$field_query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_canvas_order_image` LIKE 'product_design_option'");
			if (!$field_query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_canvas_order_image`  ADD `product_design_option` VARCHAR(255) NOT NULL  AFTER `image_label`");
			}		
	}
}
