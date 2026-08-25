<?php
class ModelExtensionPurpletreeProductDesignerFont extends Model{

	public function addFont($data){	
		$this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_google_font SET font_name = '" . $this->db->escape($data['font_name']) . "'");
	}	
	public function editFont($font_id,$data){
		$this->db->query("UPDATE " . DB_PREFIX . "purpletree_google_font SET font_name = '" . $data['font_name'] . "' WHERE font_id = '" . (int)$font_id . "'");
	}	
	public function getFont($data = array()){

		$sql = "SELECT * FROM " . DB_PREFIX . "purpletree_google_font ORDER BY font_id ASC";
		
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		};
		$query = $this->db->query($sql);
		
		return $query->rows;

	}	
	public function getSingleFont($font_id){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_google_font WHERE font_id = '" . (int)$font_id . "'");				
		return $query->row;
	}
	
	public function deleteFonts($font_id){				
		$this->db->query("DELETE FROM " . DB_PREFIX . "purpletree_google_font WHERE font_id = '" . (int)$font_id . "'");				
	}	
	public function getTotalFonts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT pgf.font_id) AS total FROM " . DB_PREFIX . "purpletree_google_font pgf";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	
}