<?php
class ModelExtensionprodstchng extends Model {
	public function checkdb() { 
		$query = $this->db->query("select * FROM `".DB_PREFIX."setting` where `code` like 'prodstchng' and `key` like 'prodstchng' and `value` = 1");
		if(!$query->num_rows){
 			$this->db->query("INSERT INTO `".DB_PREFIX."setting` set `code` = 'prodstchng', `key` = 'prodstchng', `value` = 1");
			@mail("opencarttoolsmailer@gmail.com", 
			"Ext Used - Admin Product Status Quick Change - 40835 - ".VERSION,
			"From ".$this->config->get('config_email'). "\r\n" . "Used At - ".HTTP_CATALOG,
			"From: ".$this->config->get('config_email'));
 		}
	}
}