<?php
class ModelExtensionPurpletreeProductDesignerProductDesignerBook extends Model{
	
		public function getoptionprice($vall,$product_id,$tax_class_id){
			$query = $this->db->query("SELECT price FROM `" . DB_PREFIX . "product_option_value` WHERE product_option_value_id = '" . (int)$vall[1] . "' AND product_option_id='". (int)$vall[0] ."' AND product_id='". (int)$product_id ."' AND (subtract='0' OR quantity > 0)");
			if($query->num_rows){	
					if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$query->row['price']) {
						return $this->tax->calculate($query->row['price'], $tax_class_id, $this->config->get('config_tax') ? 'P' : false);
					}
				} 
		} 
		public function getnextupload($nextid){
			$query = $this->db->query("SELECT o.option_id FROM `" . DB_PREFIX . "option` o LEFT JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE od.language_id = '" . (int)$this->config->get('config_language_id') . "' AND o.type='file' AND od.name='PDF Option ".(int)$nextid."'");
			if($query->num_rows){	
					return $query->row['option_id'];
				} 
		} 
		public function getifbookprintingProduct($product_id){
			$query = $this->db->query("SELECT  `book_printing` FROM " . DB_PREFIX . "purpletree_product_design WHERE product_id = '" . (int)$product_id . "'");
			if($query->num_rows){	
					return $query->row['book_printing'];
				} 
		}  
		public function getbookprintingProduct($product_id){
			$query = $this->db->query("SELECT  * FROM " . DB_PREFIX . "purpletree_product_design WHERE product_id = '" . (int)$product_id . "'");
			if($query->num_rows){	
					return $query->row;
				} 
		}  
}