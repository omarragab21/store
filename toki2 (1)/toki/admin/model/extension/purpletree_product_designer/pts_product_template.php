<?php
class ModelExtensionPurpletreeProductDesignerPtsProductTemplate extends Model{
	
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
		
	public function getIcons() {
		$query = $this->db->query("SELECT pdi.id,pdi.icon_class,pdi.icon_name,pdi.icon_code,pic.icon_category  FROM " . DB_PREFIX . "purpletree_designer_icons pdi LEFT JOIN " . DB_PREFIX . "purpletree_icon_category pic ON(pdi.icon_category_id=pic.id)");
			if($query->num_rows){
				return $query->rows;
			}
	}
	public function getProductprice_quantitys($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_designer_price WHERE product_id = '" . (int)$product_id . "' ORDER BY id");

		return $query->rows;
	} 
	
	public function getshortdesc($product_id) {
		$query = $this->db->query("SELECT `short_description` FROM " . DB_PREFIX . "purpletree_templates_product  WHERE product_id = '" . (int)$product_id . "'");
			if($query->num_rows){
				return $query->row['short_description'];
			}
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
		
	public function editDetail($product_id,$data) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_design_templates SET product_id = '" . (int)$data['product_id'] . "',template_name='".$this->db->escape($data['template_name'])."',template_image='".$this->db->escape($data['template_image'])."', status = '" . (int)$data['status'] . "'");
		}
		
		public function geAdmintempProduct($data = array()) {
			$sql = "SELECT pvt.product_id,pvt.status, pd.*,p.* FROM " . DB_PREFIX . "purpletree_templates_product pvt LEFT JOIN " . DB_PREFIX . "product p ON (pvt.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "purpletree_product_design ppd ON (p.product_id = ppd.product_id)";
			
			//purpletree_design_templates
			$sql.=" WHERE pd.language_id = '" . (int)$this->config->get('config_language_id')  . "' ";
			
			if (!empty($data['filter_name'])) {
				$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
			}
			
			if (!empty($data['filter_model'])) {
				$sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
			}
			
			if (isset($data['filter_status']) && $data['filter_status'] !== '') {
				$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
			}
				$sql .= " AND ppd.book_printing = '0'";
			
			$sql .= " GROUP BY p.product_id";
			
			$sort_data = array(
			'pd.name',
			'p.model',
			'p.status',
			'p.sort_order'
			);
			
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
				} else {
				$sql .= " ORDER BY pd.name";
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
		
		public function getTotalAdmintempProduct($data = array()) {
					$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "purpletree_design_templates` (				
							`template_id` int(11) NOT NULL AUTO_INCREMENT,
							`product_id` int(11) NOT NULL,
							`product_template_design` text NOT NULL,
							`status` tinyint(1) NOT NULL DEFAULT '0',				
							PRIMARY KEY (`template_id`)) CHARACTER SET utf8 COLLATE utf8_unicode_ci
					");	
			$sql = "SELECT COUNT(*) AS total,pvtp.status AS template_status ,pvt.product_id,pvt.status, pd.*,p.* FROM " . DB_PREFIX . "purpletree_templates_product pvt LEFT JOIN " . DB_PREFIX . "product p ON (pvt.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "purpletree_design_templates pvtp ON (pvt.id = pvtp.product_id)";
			
			
			$sql.=" WHERE pd.language_id = '" . (int)$this->config->get('config_language_id')  . "' ";
			
			if (!empty($data['filter_name'])) {
				$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
			}
			
			if (!empty($data['filter_model'])) {
				$sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
			}
			
			if (isset($data['filter_status']) && $data['filter_status'] !== '') {
				$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
			}
			
			$sql .= " GROUP BY p.product_id";
			
			$sort_data = array(
			'pd.name',
			'p.model',
			'p.status',
			'p.sort_order'
			);
			
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
				} else {
				$sql .= " ORDER BY pd.name";
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
			
			if($query->num_rows) {
				return $query->row['total'];
				} else {
				return 0;
			}
		}
		public function geSellerproducttemptotal($data = array()) {
			
			$sql = "SELECT p.model,pvtps.template_name,pvtps.template_id,pvtps.template_image,pvtps.status,pvtps.sort_order,pd.name as product_name,pvtps.template_id,p.product_id,p.image,pvtps.status AS template_status,pvtps.product_id FROM " . DB_PREFIX . "purpletree_design_templates pvtps LEFT JOIN " . DB_PREFIX . "purpletree_templates_product pvt ON (pvt.product_id = pvtps.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pvt.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) ";
			
			
			$sql.=" WHERE pd.language_id = '" . (int)$this->config->get('config_language_id')  . "'";
		
			
			if (!empty($data['filter_name'])) {
				$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
			}
			if (!empty($data['template_name'])) {
				$sql .= " AND pvtps.template_name LIKE '" . $this->db->escape($data['template_name']) . "%'";
			}
			
			if (isset($data['filter_status']) && $data['filter_status'] !== '') {
				$sql .= " AND pvtps.status = '" . (int)$data['filter_status'] . "'";
			}
			if (isset($data['grouppro']) && $data['grouppro'] == 1) {
				$sql .= " GROUP BY p.product_id";
			}
			else {
				$sql .= " GROUP BY pvtps.template_id";
			}
			$sort_data = array(
			'pvs.store_name',
			'pd.name',
			'p.model',
			'pvtps.status',
			'p.sort_order'
			);
			
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
				} else {
				$sql .= " ORDER BY pd.name";
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
			
			return $query->num_rows;
		}
				public function geSellerproducttemp($data = array()) {
			$sql = "SELECT p.model,pvtps.template_name,pvtps.template_id,pvtps.template_image,pvtps.status,pvtps.sort_order,pd.name as product_name,pvtps.template_id,p.product_id,p.image,pvtps.status AS template_status,pvtps.product_id FROM " . DB_PREFIX . "purpletree_design_templates pvtps LEFT JOIN " . DB_PREFIX . "purpletree_templates_product pvt ON (pvt.product_id = pvtps.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pvt.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) ";
			
			
			$sql.=" WHERE pd.language_id = '" . (int)$this->config->get('config_language_id')  . "'";
		
			
			if (!empty($data['filter_name'])) {
				$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
			}
			if (!empty($data['template_name'])) {
				$sql .= " AND pvtps.template_name LIKE '" . $this->db->escape($data['template_name']) . "%'";
			}
			
			if (isset($data['filter_status']) && $data['filter_status'] !== '') {
				$sql .= " AND pvtps.status = '" . (int)$data['filter_status'] . "'";
			}
			if (isset($data['grouppro']) && $data['grouppro'] == 1) {
				$sql .= " GROUP BY p.product_id";
			} else {
				$sql .= " GROUP BY pvtps.template_id";
			}
			$sort_data = array(
			'pvs.store_name',
			'pd.name',
			'p.model',
			'pvtps.status',
			'p.sort_order'
			);
			
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
				} else {
				$sql .= " ORDER BY pd.name";
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
		public function addTemplateData($data=array()) {
				
				 $sqlmod='';
				if($data['product_designer_total_layers']!=''){
					$sqlmod.=",total_layers = '" . (int)$data['product_designer_total_layers'] . "'";
				}
				if($data['product_designer_total_text_layers']!=''){
					$sqlmod.=",total_text_layers = '" . (int)$data['product_designer_total_text_layers'] . "'";
				}
				if($data['product_designer_total_clipart_layers']!=''){
					$sqlmod.=",total_clipart_layers = '" . (int)$data['product_designer_total_clipart_layers'] . "'";
				}
				if($data['product_designer_total_image_layers']!=''){
					$sqlmod.=",total_image_layers = '" . (int)$data['product_designer_total_image_layers'] . "'";
				}
				if($data['product_designer_total_shapes_layers']!=''){
					$sqlmod.=",total_shapes_layers = '" . (int)$data['product_designer_total_shapes_layers'] . "'";
				}
				if($data['product_designer_total_icons_layers']!=''){
					$sqlmod.=",total_icons_layers = '" . (int)$data['product_designer_total_icons_layers'] . "'";
				}
				
				if($data['product_designer_canvas_size']!=''){
					$sqlmod.=",size_unit = '" . (int)$data['product_designer_canvas_size'] . "'";
				}
				if($data['product_designer_sort_order']!=''){
					$sqlmod.=",sort_order = '" . (int)$data['product_designer_sort_order'] . "'";
				} else {
					$sqlmod.=",sort_order = '0'";
				}
				$this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_design_templates SET status='".(int)$data['status']."',product_template_design='".$this->db->escape($data['product_template_design'])."',template_name='".$this->db->escape($data['template_name'])."',template_image='".$this->db->escape($data['template_image'])."',product_id='".(int)$data['product_id']."' ".$sqlmod ." ");
		}
		
		public function editsellertempDetail($template_id,$data=array()) {
			$query = $this->db->query("SELECT template_id FROM " . DB_PREFIX . "purpletree_design_templates WHERE template_id = '".(int)$template_id."'");
		
			if($query->num_rows > 0){
					 $sqlmod='';
				if($data['product_designer_total_layers']!=''){
					$sqlmod.=",total_layers = '" . (int)$data['product_designer_total_layers'] . "'";
				}
				if($data['product_designer_total_text_layers']!=''){
					$sqlmod.=",total_text_layers = '" . (int)$data['product_designer_total_text_layers'] . "'";
				}
				if($data['product_designer_total_clipart_layers']!=''){
					$sqlmod.=",total_clipart_layers = '" . (int)$data['product_designer_total_clipart_layers'] . "'";
				}
				if($data['product_designer_total_image_layers']!=''){
					$sqlmod.=",total_image_layers = '" . (int)$data['product_designer_total_image_layers'] . "'";
				}
				if($data['product_designer_total_shapes_layers']!=''){
					$sqlmod.=",total_shapes_layers = '" . (int)$data['product_designer_total_shapes_layers'] . "'";
				}
				if($data['product_designer_total_icons_layers']!=''){
					$sqlmod.=",total_icons_layers = '" . (int)$data['product_designer_total_icons_layers'] . "'";
				}
				if($data['product_designer_canvas_size']!=''){
					$sqlmod.=",size_unit = '" . (int)$data['product_designer_canvas_size'] . "'";
				}
				if($data['product_designer_sort_order']!=''){
					$sqlmod.=",sort_order = '" . (int)$data['product_designer_sort_order'] . "'";
				} else {
					$sqlmod.=",sort_order = '0'";
				}
				$this->db->query("UPDATE " . DB_PREFIX . "purpletree_design_templates SET template_id ='".$data['template_id']."', status='".(int)$data['status']."',product_template_design='".$this->db->escape($data['product_template_design'])."',template_name='".$this->db->escape($data['template_name'])."',template_image='".$this->db->escape($data['template_image'])."' ". $sqlmod ." WHERE template_id='".(int)$template_id."'");
				
			}  
		}
		
		public function editCanvasLabel($template_id,$data=array()) {
			$query = $this->db->query("SELECT template_id FROM " . DB_PREFIX . "purpletree_design_templates WHERE template_id = '".(int)$template_id."'");
		
			if($query->num_rows > 0){
				$this->db->query("UPDATE " . DB_PREFIX . "purpletree_design_templates SET product_template_design='".$this->db->escape($data)."' WHERE template_id='".(int)$template_id."'");
			}  
		}
		public function getTemplateDetail($template_id) { 
			
			$query = $this->db->query("SELECT pvtp.* FROM " . DB_PREFIX . "purpletree_design_templates pvtp  WHERE pvtp.template_id ='".(int)$template_id."'");
			
			
			return $query->row;
		}
		
	public function getTemplateId1($template_id) {
			$query = $this->db->query("SELECT template_id FROM " . DB_PREFIX . "purpletree_design_templates WHERE template_id ='".(int)$template_id."'");
				if($query->num_rows){
			return $query->row['template_id'];
				}
		}
		
	public function deletesellertempProduct($id) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "purpletree_design_templates WHERE template_id = '" . (int)$id . "'");
		}	
	public function deleteAdmintempProduct($product_id) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "purpletree_templates_product WHERE product_id = '" . (int)$product_id . "'");
		}	
		
	public function getTemplateId($template_id) {
			$query = $this->db->query("SELECT id FROM " . DB_PREFIX . "purpletree_templates_product WHERE id ='".(int)$template_id."'");
			
			if( $query->num_rows>0){
			return $query->row['id'];
			} else {
				return NULL;
			}
		} 
	public function editAdmintemp($product_id,$data) {
			$this->db->query("UPDATE " . DB_PREFIX . "purpletree_templates_product SET status = '" . (int)$data['status'] . "' WHERE product_id = '" . (int)$product_id . "'");
		}
	public function getDesignerTemplateProduct() {
		$query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "purpletree_templates_product");
			if($query->num_rows>0){
		return $query->rows;
			}
	}		
		
}
