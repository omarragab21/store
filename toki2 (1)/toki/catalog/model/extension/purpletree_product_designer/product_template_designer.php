<?php
class ModelExtensionPurpletreeProductDesignerProductTemplateDesigner extends Model{
	public function getCanvasTemplateSizeUnit($product_id,$template_id) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_design_templates  WHERE product_id = '" . (int)$product_id . "' AND template_id = '" . (int)$template_id . "'");
				if($query->num_rows>0){
                 return $query->row['size_unit'];
		         } else {
			       return null;
		         }
			}
	public function getCanvasSizeUnit($product_id) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_product_design  WHERE product_id = '" . (int)$product_id . "'");
				if($query->num_rows>0){
                 return $query->row['size_unit'];
		         } else {
			       return null;
		         }
			}
			
	public function getIcons() {
		$query = $this->db->query("SELECT pdi.id,pdi.icon_class,pdi.icon_name,pdi.icon_code,pic.icon_category  FROM " . DB_PREFIX . "purpletree_designer_icons pdi LEFT JOIN " . DB_PREFIX . "purpletree_icon_category pic ON(pdi.icon_category_id=pic.id)");
			if($query->num_rows){
				return $query->rows;
			}
		}
		
	public function saveCanvasDatasession($data) {
		 $this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_designer_savelater SET product_id = '" . (int)$data['product_id'] . "',template_id = '" . (int)$data['template_id'] . "',product_design='".$this->db->escape($data['jsonData'])."', session_id = '" . $this->db->escape($this->session->getId()) . "', date_added=NOW(),date_modified=NOW()"); 
		 return $this->db->getLastId();
	
	}
	public function saveCanvasDatacustomer($id) {
		if($this->customer->getId()){
			$customer_id = $this->customer->getId();
		 $this->db->query("UPDATE " . DB_PREFIX . "purpletree_designer_savelater SET customer_id='".(int)$customer_id."',date_modified=NOW()  WHERE id = '" . (int)$id . "' "); 
		}
	}
	public function saveCanvasData($data=array()) {
		$customer_id=0;
			$sqlqy = " AND session_id = '" . $this->db->escape($this->session->getId()) . "'";
		if($this->customer->getId()){
			$customer_id = $this->customer->getId();
			$sqlqy = " AND customer_id='".(int)$customer_id."' ";
		}
		if(!isset($data['template_id'])) {
			$data['template_id'] = 0;
		}
			$query = $this->db->query("SELECT `id` FROM " . DB_PREFIX . "purpletree_designer_savelater WHERE product_id = '" . (int)$data['product_id'] . "' ". $sqlqy ." AND template_id = '" . (int)$data['template_id'] . "'");
			if($query->num_rows){
				 $this->db->query("UPDATE " . DB_PREFIX . "purpletree_designer_savelater SET product_design='".$this->db->escape($data['jsonData'])."',customer_id='".(int)$customer_id."',date_modified=NOW()  WHERE product_id = '" . (int)$data['product_id'] . "' ". $sqlqy ." AND template_id = '" . (int)$data['template_id'] . "'"); 
				 return $query->row['id'];
			} else {
		 $this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_designer_savelater SET product_id = '" . (int)$data['product_id'] . "',template_id = '" . (int)$data['template_id'] . "',product_design='".$this->db->escape($data['jsonData'])."',customer_id='".(int)$customer_id."', session_id = '" . $this->db->escape($this->session->getId()) . "', date_added=NOW(),date_modified=NOW()"); 
		 return $this->db->getLastId();
			}
	}
	
	public function getCanvasData($saved_id) {
			$customer_id=0;
			if($this->customer->getId()){
			$customer_id=$this->customer->getId();
			}
		$query= $this->db->query("SELECT product_design FROM " . DB_PREFIX . "purpletree_designer_savelater WHERE id = '" . (int)$saved_id . "'");  
					$data = NULL;
			if($query->num_rows){	
					$data = $query->row['product_design'];
				} 
			return $data;
	}
	
	public function getcustomqtyPrice($id,$product_id){
		$query = $this->db->query("SELECT  `quantity`,`price` FROM " . DB_PREFIX . "purpletree_designer_price WHERE product_id = '" . (int)$product_id . "' AND id = '" . (int)$id. "'");
					$data  = array();
				if($query->num_rows){	
					$data  = $query->row;
				} 
			return $data;
	}
	
	public function getCartDatacustom($product_id){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_templates_cart  WHERE  session_id = '" . $this->db->escape($this->session->getId()) . "' AND product_id = '" . (int)$product_id . "'");
			$data  = array();
				if($query->num_rows){	
					$data = $query->row;
				} 
			return $data;
	}
	public function getDatafromTemplateCart($product_id, $design){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_templates_cart  WHERE  session_id = '" . $this->db->escape($this->session->getId()) . "' AND product_id = '" . (int)$product_id . "' AND design='".(int)$design."'");
			$data  = array();
				if($query->num_rows){	
					$data = $query->row;
				} 
			return $data;
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
	public function getIconByAjax($search_icon){
			$sql = "SELECT pdi.id,pdi.icon_class,pdi.icon_name,pdi.icon_code,pic.icon_category  FROM " . DB_PREFIX . "purpletree_designer_icons pdi LEFT JOIN " . DB_PREFIX . "purpletree_icon_category pic ON(pdi.icon_category_id=pic.id)";
		
			if($search_icon){
			$sql.=" WHERE icon_name LIKE '%" . $this->db->escape($search_icon) . "%'";
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
		public function getProductDesignrcust($product_id) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_product_design_image ppdi LEFT JOIN " . DB_PREFIX . "purpletree_product_design ppd ON( ppdi.product_id=ppd.product_id) WHERE ppdi.product_id = '" . (int)$product_id . "' AND ppd.product_id = '" . (int)$product_id . "' ORDER BY ppdi.sort_order ASC");
				if($query->num_rows){
					return $query->rows;
		         }else{
			       return null;
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
			
			public function getCanvasOldData($product_id) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_product_design  WHERE product_id = '" . (int)$product_id . "'");
				if($query->num_rows>0){
                 return $query->rows;
		         } else {
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
			
			public function getTemplateProduct($product_id,$template_id) {
				$query = $this->db->query("SELECT pdt.*, ptp.* FROM " . DB_PREFIX . "purpletree_templates_product ptp LEFT JOIN " . DB_PREFIX . "purpletree_design_templates pdt ON( ptp.product_id=pdt.product_id) WHERE pdt.product_id = '" . (int)$product_id . "' AND pdt.template_id = '" . (int)$template_id . "'");
				if($query->num_rows>0){
                 return $query->row;
		         }else{
			       return null;
		         }
			}
}