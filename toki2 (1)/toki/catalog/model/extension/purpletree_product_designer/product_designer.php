<?php
class ModelExtensionPurpletreeProductDesignerProductDesigner extends Model{
	
		public function getshortdescription($product_id){
			$query = $this->db->query("SELECT  `short_description` FROM " . DB_PREFIX . "purpletree_templates_product WHERE product_id = '" . (int)$product_id . "'");
			if($query->num_rows){	
					return $query->row['short_description'];
				} 
		
		}  
		
		public function getBuyDesignStatus($order_id){
			$query = $this->db->query("SELECT  `buy_design_status` FROM " . DB_PREFIX . "purpletree_canvas_order_image WHERE order_id = '" . (int)$order_id . "'");
			if($query->num_rows){	
					return $query->row['buy_design_status'];
				} 
			return NULL;
		} 
			public function getdiscount($product_id){
		$query = $this->db->query("SELECT  id as product_discount_id,`quantity`,`price` FROM " . DB_PREFIX . "purpletree_designer_price WHERE product_id = '" . (int)$product_id . "'");
		if($query->num_rows){	
				return $query->rows;
			} 
	
	}  
		public function istemplateproduct($product_id){
			$sql   = "SELECT * FROM " . DB_PREFIX . "purpletree_product_design ppd WHERE product_id = '" . $product_id . "' AND status = 1";
			$query = $this->db->query($sql);
				if($query->num_rows){	
					return true;
				} 
			return false;
	}
	public function getCanvasFromOrder($order_id,$product_id){

			$sql   = "SELECT * FROM " . DB_PREFIX . "purpletree_canvas_order_image pcoi WHERE order_id = '" . $order_id . "' AND product_id = '" . $product_id . "'";
			$query = $this->db->query($sql);
			$data  = array();
				if($query->num_rows>0){	
					$data = $query->rows;
				} 
			return $data;
	}
	public function getClipartImages(){

			$sql = "SELECT * FROM " . DB_PREFIX . "purpletree_clipart_image pci";
			$query=$this->db->query($sql);
			
			if($query->num_rows>0){	
						return $query->rows;
				} else {
						return NULL;
					}
			}		
	public function getFonts(){

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_google_font ORDER BY font_id ASC");
		return $query->rows;
		
	}	
	public function getClipartAjaxImages($search_image){
		$sql = "SELECT * FROM " . DB_PREFIX . "purpletree_clipart_image pci ";
			if($search_image){
			$sql.=" WHERE clipart_image LIKE '%" . $this->db->escape($search_image) . "%'";
			}
			$query=$this->db->query($sql);
			
			if($query->num_rows>0){	
						return $query->rows;
				} else {
						return NULL;
					}
			
			}			
	public function getTotalImages($data=array()){
			$sql = "SELECT * FROM " . DB_PREFIX . "purpletree_clipart_image ";

			$query=$this->db->query($sql);
			
			if($query->num_rows>0){	
						return $query->num_rows;
				} else {
						return NULL;
					}
			}
		public function getProductDesignImages($product_id) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_product_design_image  WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC");
				if($query->num_rows>0){
                 return $query->rows;
		         }else{
			       return null;
		         }
			}	
			
		public function getProductDesignLimit($product_id) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_product_design  WHERE product_id = '" . (int)$product_id . "'");
			if($query->num_rows>0){
			 return $query->row;
			 }else{
			   return null;
			 }
		}
}
