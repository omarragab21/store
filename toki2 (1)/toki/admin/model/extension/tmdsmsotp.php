<?php
class ModelExtensionTmdsmsotp extends Model {
	public function install() {
$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."tmdsms` (
  `tmdsms_id` int(11) NOT NULL AUTO_INCREMENT,
  `sms_from` int(11) NOT NULL,
  `sms_to` varchar(255) NOT NULL,
  `customer_id` text NOT NULL,
  `customer_group_id` int(11) NOT NULL,
  `sms` text NOT NULL,
  `telephone` text NOT NULL,
  PRIMARY KEY (`tmdsms_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;");

$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."tmdsmsverify` (
  `verify_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `otp` varchar(200) NOT NULL,
  `verify_status` int(11) NOT NULL,
  PRIMARY KEY (`verify_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;");
	}
	public function uninstall() {
	$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."tmdsms`");
	$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."tmdsmsverify`");
	}
}
