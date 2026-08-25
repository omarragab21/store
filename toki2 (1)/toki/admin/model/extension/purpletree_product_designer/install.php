<?php
class ModelExtensionPurpletreeProductDesignerInstall extends Model{
	
		
		public function CreateTables(){
				$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "purpletree_product_design` (
  							`id` int(11) NOT NULL AUTO_INCREMENT,
							`product_id` int(11) NOT NULL,
							`price_per_page` float(11) NOT NULL,
							`price_binding` float(11) NOT NULL,
							`total_layers` int(11) DEFAULT 999,
							`total_text_layers` int(11) DEFAULT 99,
							`total_clipart_layers` int(11) DEFAULT 99,
							`total_image_layers` int(11) DEFAULT 99,
							`total_image_icons` int(11) DEFAULT 99,
							`total_image_shapes` int(11) DEFAULT 99,
							`status` tinyint(1) NOT NULL,
							`book_printing` tinyint(1) NOT NULL,
							`size_unit` int(11) NOT NULL,
							`is_price_binding` tinyint(1) NOT NULL,
  							PRIMARY KEY (`id`))
						");
						$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "purpletree_designer_price` (				
							`id` int(11) NOT NULL AUTO_INCREMENT,
							`price` int(11) NOT NULL,
							`quantity` int(11) NOT NULL,
							`product_id` int(11) NOT NULL,
							PRIMARY KEY (`id`)) CHARACTER SET utf8 COLLATE utf8_unicode_ci
					");	
				$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "purpletree_clipart_image` (
  							`clipart_id` int(11) NOT NULL AUTO_INCREMENT,
							`clipart_image` varchar(255) DEFAULT NULL,
  							PRIMARY KEY (`clipart_id`))
						");				
						$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "purpletree_google_font` (
  							`font_id` int(11) NOT NULL AUTO_INCREMENT,
							`font_name` varchar(255) DEFAULT NULL,
  							PRIMARY KEY (`font_id`))
						");
						$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "purpletree_canvas_order_image` (
  							`id` int(11) NOT NULL AUTO_INCREMENT,
							`product_id` int(11) NOT NULL,
							`session_id` varchar(250) DEFAULT NULL,
							`order_id` int(11) DEFAULT NULL,
							`product_image` varchar(255) DEFAULT NULL,
							`canvas_image` varchar(255) DEFAULT NULL,
							`image_without_wm` varchar(255) DEFAULT NULL,
							`canvas_jpg_image` varchar(255) DEFAULT NULL,
							`canvas_jpg_image_withbg` varchar(255) DEFAULT NULL,
							`canvas_jpg_image_wtwm` varchar(255) DEFAULT NULL,
							`image_label` varchar(255) DEFAULT NULL,
							`product_design_option` varchar(255) DEFAULT NULL,
  							PRIMARY KEY (`id`))
						");
					$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "purpletree_designer_savelater` (
							`id` int(11) NOT NULL AUTO_INCREMENT,
							`product_id` int(11) NOT NULL,
							`template_id` int(11) NOT NULL,
							`product_design` text NOT NULL,
							`status` tinyint(1) NOT NULL DEFAULT '0',		
							`customer_id` int(11) NOT NULL,
							`session_id` varchar(250) NOT NULL,
							`qtyprice` int(11) NOT NULL,
							`option` text NOT NULL,
							`design` int(11) NOT NULL,
							`date_added` datetime  NOT NULL,
							`date_modified` datetime  NOT NULL,
							PRIMARY KEY (`id`)) CHARACTER SET utf8 COLLATE utf8_unicode_ci
					");
				  $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "purpletree_templates_product` (
							`id` int(11) NOT NULL AUTO_INCREMENT,
							`product_id` int(11) NOT NULL,
							`short_description` text NOT NULL,
							`status` tinyint(1) NOT NULL DEFAULT '0',		
							PRIMARY KEY (`id`)) CHARACTER SET utf8 COLLATE utf8_unicode_ci
					"); 
				$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "purpletree_design_templates` (				
							`template_id` int(11) NOT NULL AUTO_INCREMENT,
							`product_id` int(11) NOT NULL,
							`template_image` text NOT NULL,		
							`product_template_design` text NOT NULL,		
							`template_name` varchar(50) DEFAULT NULL,							
							`status` tinyint(1) NOT NULL DEFAULT '0',	
							`sort_order` int(11) NOT NULL,	
							`total_layers` int(11) DEFAULT 999,
							`total_text_layers` int(11) DEFAULT 99,
							`total_clipart_layers` int(11) DEFAULT 99,
							`total_image_layers` int(11) DEFAULT 99,
							`total_image_icons` int(11) DEFAULT 99,
							`total_image_shapes` int(11) DEFAULT 99,
							`size_unit`int(11) NOT NULL,									
							PRIMARY KEY (`template_id`)) CHARACTER SET utf8 COLLATE utf8_unicode_ci
					");			
						
				$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "purpletree_product_design_image` (
  							 `design_image_id` int(11) NOT NULL AUTO_INCREMENT,
                             `product_id` int(11) NOT NULL,
                             `lable` varchar(50) DEFAULT NULL,
							  `dpi` int(255) NOT NULL DEFAULT 400,
							  `safe_line` int(255) DEFAULT NULL,
							  `alwaysontop` int(2) NOT NULL DEFAULT '0',
							  `bleed_line` int(255) DEFAULT NULL,
							  `fold_line` varchar(255) DEFAULT NULL,
                             `design_image` varchar(255) DEFAULT NULL,
							 `can_left` float(11) NOT NULL,
							 `can_top` float(11) NOT NULL,
							 `can_width` float(11) NOT NULL,
							 `can_height` float(11) NOT NULL,
							 `canvas_width` float(11) NOT NULL,
							 `canvas_height` float(11) NOT NULL,
							 `use_img` int(2) NOT NULL DEFAULT '2',
                             `sort_order` int(3) NOT NULL DEFAULT '0',
                              PRIMARY KEY (`design_image_id`))
						");
					$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "purpletree_templates_cart` (
							`id` int(11) NOT NULL AUTO_INCREMENT,
							`session_id` varchar(250) NOT NULL,
							`product_id` int(11) NOT NULL,
							`customer_id` int(11) NOT NULL,
							`qtyprice` int(11) NOT NULL,
							`option` text NOT NULL,
							`design` int(11) NOT NULL,
							`date_added` datetime  NOT NULL,
							PRIMARY KEY (`id`)) CHARACTER SET utf8 COLLATE utf8_unicode_ci
					"); 
					
					$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "purpletree_icon_category` (
							`id` int(11) NOT NULL AUTO_INCREMENT,
							`icon_category` varchar(100) NOT NULL,
							`added_date` datetime  NOT NULL,
							PRIMARY KEY (`id`)) CHARACTER SET utf8 COLLATE utf8_unicode_ci
					"); 	
					
					$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "purpletree_designer_icons` (
							`id` int(11) NOT NULL AUTO_INCREMENT,
							`icon_category_id` int(11) NOT NULL,
							`icon_class` varchar(100) NOT NULL,
							`icon_name` varchar(100) NOT NULL,
							`icon_code` varchar(100) NOT NULL,
							`added_date` datetime  NOT NULL,
							PRIMARY KEY (`id`)) CHARACTER SET utf8 COLLATE utf8_unicode_ci
					"); 
		}
		public function addColumns(){

			$field_quedry11 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_canvas_order_image` LIKE 'session_id'");
			if (!$field_quedry11->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_canvas_order_image` CHANGE COLUMN `cart_id` `session_id`  VARCHAR(250) NOT NULL AFTER `product_id`;");
			}
			
			$field_quedry11 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_canvas_order_image` LIKE 'cart_id'");
			if (!$field_quedry11->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_canvas_order_image`  ADD `cart_id` int(11) NOT NULL  AFTER `session_id`;");
			}
			
			$field_quedry11 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_templates_cart` LIKE 'qtyprice'");
			if (!$field_quedry11->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_templates_cart`  ADD `qtyprice` int(11) NOT NULL  AFTER `product_id`;");
			}
			$field_quedry11 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_designer_savelater` LIKE 'template_id'");
			if (!$field_quedry11->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_designer_savelater`  ADD `template_id` int(11) NOT NULL  AFTER `product_id`;");
			}
			$field_quedry11 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_design_templates` LIKE 'template_image'");
			if (!$field_quedry11->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_design_templates`  ADD `template_image` text NOT NULL  AFTER `product_id`;");
			}
			$field_query11 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_design_templates` LIKE 'template_name'");
			if (!$field_query11->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_design_templates`  ADD `template_name` varchar(50) DEFAULT NULL  AFTER `product_template_design`;");
			}
			$field_query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_product_design_image` LIKE 'dpi'");
			if (!$field_query->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_product_design_image`  ADD `dpi` INT(255) NOT NULL DEFAULT '400'  AFTER `lable`;");
			}
			$field_querya = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_product_design_image` LIKE 'safe_line'");
			if (!$field_querya->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_product_design_image`  ADD `safe_line` INT(255) DEFAULT NULL   AFTER `lable`;");
			}
			$field_queryb = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_product_design_image` LIKE 'bleed_line'");
			if (!$field_queryb->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_product_design_image`  ADD `bleed_line` INT(255)  DEFAULT NULL   AFTER `lable`;");
			}
			
			$field_queryb = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_product_design_image` LIKE 'fold_line'");
			if (!$field_queryb->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_product_design_image`  ADD `fold_line` VARCHAR(255)  DEFAULT NULL   AFTER `bleed_line`;");
			}
			
			$field_queryb = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_product_design_image` LIKE 'canvas_width'");
			if (!$field_queryb->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_product_design_image`  ADD `canvas_width` float(11) NOT NULL   AFTER `lable`;");
			}
			
			$field_queryb = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_product_design_image` LIKE 'canvas_height'");
			if (!$field_queryb->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_product_design_image`  ADD `canvas_height` float(11) NOT NULL   AFTER `lable`;");
			}
			
			$field_queryb = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_product_design_image` LIKE 'use_img'");
			if (!$field_queryb->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_product_design_image`  ADD `use_img` int(2) NOT NULL DEFAULT '2' AFTER `lable`;");
			}
			
			$field_query1 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_templates_product` LIKE 'short_description'");
			if (!$field_query1->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_templates_product`  ADD `short_description` text NOT NULL AFTER `product_id`;");
			}
			$field_query2 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_product_design` LIKE 'price_per_page'");
			if (!$field_query2->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_product_design`  ADD `price_per_page` float(11) NOT NULL AFTER `product_id`;");
			}
			$field_query3 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_product_design` LIKE 'price_binding'");
			if (!$field_query3->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_product_design`  ADD `price_binding` float(11) NOT NULL AFTER `price_per_page`;");
			}
			$field_query4 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_product_design` LIKE 'book_printing'");
			if (!$field_query4->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_product_design`  ADD `book_printing` tinyint(1) NOT NULL AFTER `status`;");
			}
			$field_query5 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_product_design` LIKE 'is_price_binding'");
			if (!$field_query5->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_product_design`  ADD `is_price_binding` tinyint(1) NOT NULL AFTER `status`;");
			}
			$field_query6 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_product_design` LIKE 'total_layers'");
			if (!$field_query6->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_product_design`  ADD `total_layers` int(11) DEFAULT 999 AFTER `status`;");
			}
			$field_query7 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_product_design` LIKE 'total_text_layers'");
			if (!$field_query7->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_product_design`  ADD `total_text_layers` int(11) DEFAULT 99 AFTER `status`;");
			}
			$field_query8 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_product_design` LIKE 'total_clipart_layers'");
			if (!$field_query8->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_product_design`  ADD `total_clipart_layers` int(11) DEFAULT 99 AFTER `status`;");
			}
			$field_query9 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_product_design` LIKE 'total_image_layers'");
			if (!$field_query9->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_product_design`  ADD `total_image_layers` int(11) DEFAULT 99 AFTER `status`;");
			}
			$field_query10 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_product_design` LIKE 'total_icons_layers'");
			if (!$field_query10->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_product_design`  ADD `total_icons_layers` int(11) DEFAULT 99 AFTER `status`;");
			}
			$field_query11 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_product_design` LIKE 'total_shapes_layers'");
			if (!$field_query11->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_product_design`  ADD `total_shapes_layers` int(11) DEFAULT 99 AFTER `status`;");
			}
			
			$field_query11 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_product_design` LIKE 'size_unit'");
			if (!$field_query11->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_product_design`  ADD `size_unit` int(11) AFTER `status`;");
			}
			
			$field_query16 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_design_templates` LIKE 'total_layers'");
			if (!$field_query16->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_design_templates`  ADD `total_layers` int(11) DEFAULT 999 AFTER `status`;");
			}
			$field_query17 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_design_templates` LIKE 'total_text_layers'");
			if (!$field_query17->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_design_templates`  ADD `total_text_layers` int(11) DEFAULT 99 AFTER `status`;");
			}
			$field_query18 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_design_templates` LIKE 'total_clipart_layers'");
			if (!$field_query18->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_design_templates`  ADD `total_clipart_layers` int(11) DEFAULT 99 AFTER `status`;");
			}
			$field_query19 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_design_templates` LIKE 'total_image_layers'");
			if (!$field_query19->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_design_templates`  ADD `total_image_layers` int(11) DEFAULT 99 AFTER `status`;");
			}
			$field_query110 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_design_templates` LIKE 'total_icons_layers'");
			if (!$field_query110->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_design_templates`  ADD `total_icons_layers` int(11) DEFAULT 99 AFTER `status`;");
			}
			$field_query111 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_design_templates` LIKE 'total_shapes_layers'");
			if (!$field_query111->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_design_templates`  ADD `total_shapes_layers` int(11) DEFAULT 99 AFTER `status`;");
			}
			
			$field_query111 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_design_templates` LIKE 'size_unit'");
			if (!$field_query111->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_design_templates` ADD `size_unit` int(11) DEFAULT 99 AFTER `total_layers`;");
			}
			
			$field_query111 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_design_templates` LIKE 'sort_order'");
			if (!$field_query111->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_design_templates` ADD `sort_order` int(11) AFTER `status`;");
			}
			
			$field_query111 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_canvas_order_image` LIKE 'image_without_wm'");
			if (!$field_query111->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_canvas_order_image` ADD `image_without_wm` varchar(255) AFTER `canvas_image`;");
			}
			
			$field_query111 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_canvas_order_image` LIKE 'canvas_jpg_image'");
			if (!$field_query111->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_canvas_order_image` ADD `canvas_jpg_image` varchar(255) AFTER `image_without_wm`;");
			}
			
			$field_query111 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_canvas_order_image` LIKE 'canvas_jpg_image_withbg'");
			if (!$field_query111->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_canvas_order_image` ADD `canvas_jpg_image_withbg` varchar(255) AFTER `image_without_wm`;");
			}
			
			$field_query111 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_canvas_order_image` LIKE 'canvas_jpg_image_wtwm'");
			if (!$field_query111->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_canvas_order_image` ADD `canvas_jpg_image_wtwm` varchar(255) AFTER `image_without_wm`;");
			}
			
			$field_query1110 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_product_design_image` LIKE 'alwaysontop'");
			if (!$field_query1110->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_product_design_image` ADD `alwaysontop` int(2) NOT NULL DEFAULT '0' AFTER `safe_line`;");
			}
			$field_query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_canvas_order_image` LIKE 'product_design_option'");
			if (!$field_query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_canvas_order_image`  ADD `product_design_option` VARCHAR(255) NOT NULL  AFTER `image_label`");
			}
			$field_quedry11 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_canvas_order_image` LIKE 'buy_design_status'");
			if (!$field_quedry11->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_canvas_order_image` ADD `buy_design_status` int(11) NOT NULL AFTER `product_design_option`");
			}
			
			$field_quedry11 = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "purpletree_canvas_order_image` LIKE 'size_unit'");
			if (!$field_quedry11->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "purpletree_canvas_order_image` ADD `size_unit` int(11) NOT NULL AFTER `product_design_option`");
			}
		}
		public function gettotalPDFoption($count) {
			$totalnames = array();
			$lastcount = 0;
		$sql = $this->db->query("SELECT od.* FROM `" . DB_PREFIX . "option_description` od LEFT JOIN " . DB_PREFIX . "option o ON (o.option_id = od.option_id) WHERE o.type='file' AND od.name LIKE '%PDF Option%' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		if($sql->num_rows){
			$rowspdfs = $sql->rows;
			foreach($rowspdfs as $rowspdf) {
				$name = $rowspdf['name'];
				preg_match_all('!\d+!', $name, $matches);
				if(isset($matches[0][0])) {
					$totalnames[] = $rowspdf['option_id'];
				}
			}
		}
		if($count>0) {
		if(!empty($totalnames)) {
			$this->db->query("DELETE  FROM `" . DB_PREFIX . "option_description` WHERE option_id in (".implode(',',$totalnames).")");
			$this->db->query("DELETE  FROM `" . DB_PREFIX . "option` WHERE option_id in (".implode(',',$totalnames).")");
		}
			$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();
			for($i=1;$i<=$count;$i++) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "option` SET type = 'file', sort_order = '" . (int)$i . "'");
				$option_id = $this->db->getLastId();

				foreach($languages as $language) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "option_description SET option_id = '" . (int)$option_id . "', language_id = '" . (int)$language['language_id'] . "', name = 'PDF Option ".$i."'");
				}
			}
		}
	}
		public function addFontAwsomeicons(){
		$this->db->query("TRUNCATE TABLE " . DB_PREFIX . "purpletree_icon_category");
					$this->db->query("TRUNCATE TABLE " . DB_PREFIX . "purpletree_designer_icons");
					
					$iconCategory=['New Icons','Web Application Icons','Accessibility Icons','Hand Icons','Transportation Icons','Gender Icons','File Type Icons','Spinner Icons','Form Control Icons','Payment Icons','Chart Icons','Currency Icons','Text Editor Icons','Directional Icons','Video Player Icons','Brand Icons','Medical Icons'];
					if(!empty($iconCategory)){
					foreach ($iconCategory as $key => $value) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_icon_category SET icon_category='".$value."',added_date=NOW()");
						}
					}

				//$this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_designer_icons SET icon_category_id='".$value[`icon_category_id`]."',icon_class='".$value[`icon_class`]."',icon_name='".$value[`icon_name`]."',icon_code='".$value[`icon_code`]."',added_date=NOW()");
				$iconLib ="(
							 '1', 
							 'fa fa-address-book', 
							 'address-book', 
							 'f2b9'
							, NOW() ),
							(
							 '1', 
							 'fa fa-address-book-o', 
							 'address-book-o', 
							 'f2ba'
							, NOW() ),
							(
							 '1', 
							 'fa fa-address-card', 
							 'address-card', 
							 'f2bb'
							, NOW() ),
							(
							 '1', 
							 'fa fa-address-card-o', 
							 'address-card-o', 
							 'f2bc'
							, NOW() ),
							(
							 '1', 
							 'fa fa-bandcamp', 
							 'bandcamp', 
							 'f2d5'
							, NOW() ),
							(
							 '1', 
							 'fa fa-bath', 
							 'bath', 
							 'f2cd'
							, NOW() ),
							(
							 '1', 
							 'fa fa-bathtub', 
							 'bathtub', 
							 'f2cd'
							, NOW() ),

							(
							 '1', 
							 'fa fa-drivers-license', 
							 'drivers-license', 
							 'f2c2'
							, NOW() ),
							(
							 '1', 
							 'fa fa-drivers-license-o', 
							 'drivers-license-o', 
							 'f2c3'
							, NOW() ),
							(
							 '1', 
							 'fa fa-eercast', 
							 'eercast', 
							 'f2da'
							, NOW() ),
							(
							 '1', 
							 'fa fa-envelope-open', 
							 'envelope-open', 
							 'f2b6'
							, NOW() ),
							(
							 '1', 
							 'fa fa-envelope-open-o', 
							 'envelope-open-o', 
							 'f2b7'
							, NOW() ),
							(
							 '1', 
							 'fa fa-etsy', 
							 'etsy', 
							 'f2d7'
							, NOW() ),
							(
							 '1', 
							 'fa fa-free-code-camp', 
							 'free-code-camp', 
							 'f2c5'
							, NOW() ),

							(
							 '1', 
							 'fa fa-grav', 
							 'grav', 
							 'f2d6'
							, NOW() ),

							(
							 '1', 
							 'fa fa-handshake-o', 
							 'handshake-o', 
							 'f2b5'
							, NOW() ),

							(
							 '1', 
							 'fa fa-id-badge', 
							 'id-badge', 
							 'f2c1'
							, NOW() ),
							(
							 '1', 
							 'fa fa-id-card', 
							 'id-card', 
							 'f2c2'
							, NOW() ),

							(
							 '1', 
							 'fa fa-id-card-o', 
							 'id-card-o', 
							 'f2c3'
							, NOW() ),

							(
							 '1', 
							 'fa fa-imdb', 
							 'imdb', 
							 'f2d8'
							, NOW() ),

							(
							 '1', 
							 'fa fa-linode', 
							 'linode', 
							 'f2b8'
							, NOW() ),
							(
							 '1', 
							 'fa fa-meetup', 
							 'meetup', 
							 'f2e0'
							, NOW() ),
							(
							 '1', 
							 'fa fa-microchip', 
							 'microchip', 
							 'f2db'
							, NOW() ),
							(
							 '1', 
							 'fa fa-podcast', 
							 'podcast', 
							 'f2ce'
							, NOW() ),
							(
							 '1', 
							 'fa fa-quora', 
							 'quora', 
							 'f2c4'
							, NOW() ),
							(
							 '1', 
							 'fa fa-ravelry', 
							 'ravelry', 
							 'f2d9'
							, NOW() ),
							(
							 '1', 
							 'fa fa-s15', 
							 's15', 
							 'f2cd'
							, NOW() ),

							(
							 '1', 
							 'fa fa-shower', 
							 'shower', 
							 'f2cc'
							, NOW() ),

							(
							 '1', 
							 'fa fa-snowflake-o', 
							 'snowflake-o', 
							 'f2dc'
							, NOW() ),
							
							(
							 '1', 
							 'fa fa-superpowers', 
							 'superpowers', 
							 'f2dd'
							, NOW() ),
							(
							 '1', 
							 'fa fa-telegram', 
							 'telegram', 
							 'f2c6'
							, NOW() ),

							(
							 '1', 
							 'fa fa-thermometer', 
							 'thermometer', 
							 'f2c7'
							, NOW() ),
							(
							 '1', 
							 'fa fa-thermometer-0', 
							 'thermometer-0', 
							 'f2cb'
							, NOW() ),

							(
							 '1', 
							 'fa fa-thermometer-1', 
							 'thermometer-1', 
							 'f2ca'
							, NOW() ),

							(
							 '1', 
							 'fa fa-thermometer-2', 
							 'thermometer-2', 
							 'f2c9'
							, NOW() ),
							(
							 '1', 
							 'fa fa-thermometer-3', 
							 'thermometer-3', 
							 'f2c8'
							, NOW() ),

							(
							 '1', 
						     'fa fa-thermometer-4', 
							 'thermometer-4', 
							 'f2c7'
							, NOW() ), 
							(
							 '1', 
						     'fa fa-thermometer-empty', 
							 'thermometer-empty', 
							 'f2cb'
							, NOW() ),

							(
							 '1', 
						     'fa fa-thermometer-full', 
							 'thermometer-full', 
							 'f2c7'
							, NOW() ),

							(
							 '1', 
						     'fa fa-thermometer-half', 
							 'thermometer-half', 
							 'f2c9'
							, NOW() ),

							(
							 '1', 
						     'fa fa-thermometer-quarter', 
							 'thermometer-quarter', 
							 'f2ca'
							, NOW() ),

							(
							 '1', 
						     'fa fa-thermometer-three-quarters', 
							 'thermometer-three-quarters', 
							 'f2c8'
							, NOW() ),

							(
							 '1', 
						     'fa fa-times-rectangle', 
							 'times-rectangle', 
							 'f2d3'
							, NOW() ),

							(
							 '1', 
						     'fa fa-times-rectangle-o', 
							 'times-rectangle-o', 
							 'f2d4'
							, NOW() ),
							(
							 '1', 
						     'fa fa-user-circle', 
							 'user-circle', 
							 'f2bd'
							, NOW() ),

							(
							 '1', 
						     'fa fa-user-circle-o', 
							 'user-circle-o', 
							 'f2be'
							, NOW() ),

							(
							 '1', 
						     'fa fa-user-o', 
							 'user-o', 
							 'f2c0'
							, NOW() ),

							(
							 '1', 
						     'fa fa-vcard', 
							 'vcard', 
							 'f2bb'
							, NOW() ),

							(
							 '1', 
						     'fa fa-vcard-o', 
							 'vcard-o', 
							 'f2bc'
							, NOW() ),

							(
							 '1', 
						     'fa fa-window-close', 
							 'window-close', 
							 'f2d3'
							, NOW() ),
							
							(
							 '1', 
						     'fa fa-window-close-o', 
							 'window-close-o', 
							 'f2d4'
							, NOW() ),
							(
							 '1', 
						     'fa fa-window-maximize', 
							 'window-maximize', 
							 'f2d0'
							, NOW() ),

							(
							 '1', 
						     'fa fa-window-minimize', 
							 'window-minimize', 
							 'f2d1'
							, NOW() ),
							(
							 '1', 
						     'fa fa-window-restore', 
							 'window-restore', 
							 'f2d2'
							, NOW() ),
							(
							 '1', 
						     'fa fa-wpexplorer', 
							 'wpexplorer', 
							 'f2de'
							, NOW() ),
							(
							 '2', 
						     'fa fa-address-book', 
							 'address-book', 
							 'f2b9'
							, NOW() ),
							
							(
							 '2', 
						     'fa fa-address-book-o', 
							 'address-book-o', 
							 'f2ba'
							, NOW() ),

							(
							 '2', 
						     'fa fa-address-card', 
							 'address-card', 
							 'f2bb'
							, NOW() ),

							(
							 '2', 
						     'fa fa-address-card-o', 
							 'address-card-o', 
							 'f2bc'
							, NOW() ),

							(
							 '2', 
						     'fa fa-adjust', 
							 'adjust', 
							 'f042'
							, NOW() ),
							
							(
							 '2', 
						     'fa fa-american-sign-language-interpreting', 
							 'american-sign-language-interpreting', 
							 'f2a3'
							, NOW() ),
							(
							 '2', 
						     'fa fa-anchor', 
							 'anchor', 
							 'f13d'
							, NOW() ),

							(
							 '2', 
						     'fa fa-archive', 
							 'archive', 
							 'f187'
							, NOW() ),

							(
							 '2', 
						     'fa fa-area-chart', 
							 'area-chart', 
							 'f1fe'
							, NOW() ),
							(
							 '2', 
						     'fa fa-arrows', 
							 'arrows', 
							 'f047'
							, NOW() ),
							(
							 '2', 
						     'fa fa-arrows-h', 
							 'arrows-h', 
							 'f07e'
							, NOW() ),

							(
							 '2', 
						     'fa fa-arrows-v', 
							 'arrows-v', 
							 'f07d'
							, NOW() ),

							(
							 '2', 
						     'fa fa-asl-interpreting', 
							 'asl-interpreting', 
							 'f2a3'
							, NOW() ),

							(
							 '2', 
						     'fa fa-assistive-listening-systems', 
							 'assistive-listening-systems', 
							 'f2a2'
							, NOW() ),

							(
							 '2', 
						     'fa fa-asterisk', 
							 'asterisk', 
							 'f069'
							, NOW() ),

							(
							 '2', 
						     'fa fa-at', 
							 'at', 
							 'f1fa'
							, NOW() ),
							
							(
							 '2', 
						     'fa fa-audio-description', 
							 'audio-description', 
							 'f29e'
							, NOW() ),
							
							(
							 '2', 
						     'fa fa-automobile', 
							 'automobile', 
							 'f1b9'
							, NOW() ),
							
							(
							 '2', 
						     'fa fa-balance-scale', 
							 'balance-scale', 
							 'f24e'
							, NOW() ),

							(
							 '2', 
						     'fa fa-ban', 
							 'ban', 
							 'f05e'
							, NOW() ),

							(
							 '2', 
						     'fa fa-bank', 
							 'bank', 
							 'f19c'
							, NOW() ),

							(
							 '2', 
						     'fa fa-bar-chart', 
							 'bar-chart', 
							 'f080'
							, NOW() ),
							
							(
							 '2', 
						     'fa fa-bar-chart-o', 
							 'bar-chart-o', 
							 'f080'
							, NOW() ),
							
							(
							 '2', 
						     'fa fa-barcode', 
							 'barcode', 
							 'f02a'
							, NOW() ),

							(
							 '2', 
						     'fa fa-battery-0', 
							 'battery-0', 
							 'f244'
							, NOW() ),

							(
							 '2', 
						     'fa fa-battery-1', 
							 'battery-1', 
							 'f243'
							, NOW() ),

							(
							 '2', 
						     'fa fa-battery-2', 
							 'battery-2', 
							 'f242'
							, NOW() ),

							(
							 '2', 
						     'fa fa-battery-3', 
							 'battery-3', 
							 'f241'
							, NOW() ),

							(
							 '2', 
						     'fa fa-battery-4', 
							 'battery-4', 
							 'f240'
							, NOW() ),

							(
							 '2', 
						     'fa fa-battery-empty', 
							 'battery-empty', 
							 'f244'
							, NOW() ),

							(
							 '2', 
						     'fa fa-battery-full', 
							 'battery-full', 
							 'f240'
							, NOW() ),
							
							(
							 '2', 
						     'fa fa-battery-half', 
							 'battery-half', 
							 'f242'
							, NOW() ),

							(
							 '2', 
						     'fa fa-battery-quarter', 
							 'battery-quarter', 
							 'f243'
							, NOW() ),

							(
							 '2', 
						     'fa fa-battery-three-quarters', 
							 'battery-three-quarters', 
							 'f241'
							, NOW() ),

							(
							 '2', 
						     'fa fa-bed', 
							 'bed', 
							 'f236'
							, NOW() ),

							(
							 '2', 
						     'fa fa-beer', 
							 'beer', 
							 'f0fc'
							, NOW() ),

							(
							 '2', 
						     'fa fa-bell', 
							 'bell', 
							 'f0f3'
							, NOW() ),

							(
							 '2', 
						     'fa fa-bell-o', 
							 'bell-o', 
							 'f0a2'
							, NOW() ),

							(
							 '2', 
						     'fa fa-bell-slash', 
							 'bell-slash', 
							 'f1f6'
							, NOW() ),

							(
							 '2', 
						     'fa fa-bell-slash-o', 
							 'bell-slash-o', 
							 'f1f7'
							, NOW() ),
							
							(
							 '2', 
						     'fa fa-bicycle', 
							 'bicycle', 
							 'f206'
							, NOW() ),
							
							(
							 '2', 
						     'fa fa-binoculars', 
							 'binoculars', 
							 'f1e5'
							, NOW() ),

							(
							 '2', 
						     'fa fa-birthday-cake', 
							 'birthday-cake', 
							 'f1fd'
							, NOW() ),

							(
							 '2', 
						     'fa fa-birthday-cake', 
							 'birthday-cake', 
							 'f1fd'
							, NOW() ),
							(
							 '2', 
						     'fa fa-blind', 
							 'blind', 
							 'f29d'
							, NOW() ),

							(
							 '2', 
						     'fa fa-bluetooth', 
							 'bluetooth', 
							 'f293'
							, NOW() ),
							(
							 '2', 
						     'fa fa-bluetooth-b', 
							 'bluetooth-b', 
							 'f294'
							, NOW() ),
							
							(
							 '2', 
						     'fa fa-bolt', 
							 'bolt', 
							 'f0e7'
							, NOW() ),

							(
							 '2', 
						     'fa fa-bomb', 
							 'bomb', 
							 'f1e2'
							, NOW() ),

							(
							 '2', 
						     'fa fa-book', 
							 'book', 
							 'f02d'
							, NOW() ),

							(
							 '2', 
						     'fa fa-bookmark', 
							 'bookmark', 
							 'f02e'
							, NOW() ),

							(
							 '2', 
						     'fa fa-bookmark-o', 
							 'bookmark-o', 
							 'f097'
							, NOW() ),

							(
							 '2', 
						     'fa fa-braille', 
							 'braille', 
							 'f2a1'
							, NOW() ),

							(
							 '2', 
						     'fa fa-briefcase', 
							 'briefcase', 
							 'f0b1'
							, NOW() ),
                            (
							 '2', 
						     'fa fa-bug', 
							 'bug', 
							 'f188'
							, NOW() ),	

							(
							 '2', 
						     'fa fa-building', 
							 'building', 
							 'f1ad'
							, NOW() ),							

							(
							 '2', 
						     'fa fa-building-o', 
							 'building-o', 
							 'f0f7'
							, NOW() ),

							(
							 '2', 
						     'fa fa-bullhorn', 
							 'bullhorn', 
							 'f0a1'
							, NOW() ),
							(
							 '2', 
						     'fa fa-bullseye', 
							 'bullseye', 
							 'f140'
							, NOW() ),

							(
							 '2', 
						     'fa fa-bus', 
							 'bus', 
							 'f207'
							, NOW() ),

                            (
							 '2', 
						     'fa fa-cab', 
							 'cab', 
							 'f1ba'
							, NOW() ),
							(
							 '2', 
						     'fa fa-calculator', 
							 'calculator', 
							 'f1ec'
							, NOW() ),
							(
							 '2', 
						     'fa fa-calendar', 
							 'calendar', 
							 'f073'
							, NOW() ),

							(
							 '2', 
						     'fa fa-calendar-check-o', 
							 'calendar-check-o', 
							 'f274'
							, NOW() ),
							(
							 '2', 
						     'fa fa-calendar-minus-o', 
							 'calendar-minus-o', 
							 'f272'
							, NOW() ),

							(
							 '2', 
						     'fa fa-calendar-o', 
							 'calendar-o', 
							 'f133'
							, NOW() ),

							(
							 '2', 
						     'fa fa-calendar-plus-o', 
							 'calendar-plus-o', 
							 'f271'
							, NOW() ),

							(
							 '2', 
						      'fa fa-calendar-times-o', 
							 'calendar-times-o', 
							 'f273'
							, NOW() ),

							(
							 '2', 
						     'fa fa-camera', 
							 'camera', 
							 'f030'
							, NOW() ),
							(
							 '2', 
						     'fa fa-camera-retro', 
							 'camera-retro', 
							 'f083'
							, NOW() ),
							(
							 '2', 
						     'fa fa-car', 
							 'car', 
							 'f1b9'
							, NOW() ),

							(
							 '2', 
						     'fa fa-caret-square-o-down', 
							 'caret-square-o-down', 
							 'f150'
							, NOW() ),

							(
							 '2', 
						     'fa fa-caret-square-o-left', 
							 'caret-square-o-left', 
							 'f191'
							, NOW() ),
							(
							 '2', 
						     'fa fa-caret-square-o-right', 
							 'caret-square-o-right', 
							 'f152'
							, NOW() ),
							(
							 '2', 
						     'fa fa-caret-square-o-up', 
							 'caret-square-o-up', 
							 'f151'
							, NOW() ),
							(
							 '2', 
						     'fa fa-cart-arrow-down', 
							 'cart-arrow-down', 
							 'f218'
							, NOW() ),

							(
							 '2', 
						     'fa fa-cart-plus', 
							 'cart-plus', 
							 'f217'
							, NOW() ),
							(
							 '2', 
						     'fa fa-cc', 
							 'cc', 
							 'f20a'
							, NOW() ),

							(
							 '2', 
						     'fa fa-certificate', 
							 'certificate', 
							 'f0a3'
							, NOW() ),

							(
							 '2', 
						     'fa fa-check', 
							 'check', 
							 'f00c'
							, NOW() ),

							(
							 '2', 
						     'fa fa-check-circle', 
							 'check-circle', 
							 'f058'
							, NOW() ),
							(
							 '2', 
						     'fa fa-check-circle-o', 
							 'check-circle-o', 
							 'f05d'
							, NOW() ),

							(
							 '2', 
						     'fa fa-check-square', 
							 'check-square', 
							 'f14a'
							, NOW() ),

							(
							 '2', 
						     'fa fa-check-square-o', 
							 'check-square-o', 
							 'f046'
							, NOW() ),
							(
							 '2', 
						     'fa fa-child', 
							 'child', 
							 'f1ae'
							, NOW() ),

							(
							 '2', 
						     'fa fa-circle', 
							 'circle', 
							 'f111'
							, NOW() ),

							(
							 '2', 
						     'fa fa-circle-o', 
							 'circle-o', 
							 'f10c'
							, NOW() ),
							(
							 '2', 
						     'fa fa-circle-o-notch', 
							 'circle-o-notch', 
							 'f1ce'
							, NOW() ),

							(
							 '2', 
						     'fa fa-circle-thin', 
							 'circle-thin', 
							 'f1db'
							, NOW() ),

							(
							 '2', 
						     'fa fa-clock-o', 
							 'clock-o', 
							 'f017'
							, NOW() ),

							(
							 '2', 
						     'fa fa-clone', 
							 'clone', 
							 'f24d'
							, NOW() ),
							(
							 '2', 
						     'fa fa-close', 
							 'close', 
							 'f00d'
							, NOW() ),

							(
							 '2', 
						     'fa fa-cloud', 
							 'cloud', 
							 'f0c2'
							, NOW() ),

							(
							 '2', 
						     'fa fa-cloud-download', 
							 'cloud-download', 
							 'f0ed'
							, NOW() ),

							(
							 '2', 
						     'fa fa-cloud-upload', 
							 'cloud-upload', 
							 'f0ee'
							, NOW() ),

							(
							 '2', 
						     'fa fa-code', 
							 'code', 
							 'f121'
							, NOW() ),

							(
							 '2', 
						     'fa fa-code-fork', 
							 'code-fork', 
							 'f126'
							, NOW() ),
							(
							 '2', 
						     'fa fa-coffee', 
							 'coffee', 
							 'f0f4'
							, NOW() ),
							(
							 '2', 
						     'fa fa-cog', 
							 'cog', 
							 'f013'
							, NOW() ),
							(
							 '2', 
						     'fa fa-cogs', 
							 'cogs', 
							 'f085'
							, NOW() ),
							(
							 '2', 
						     'fa fa-comment', 
							 'comment', 
							 'f075'
							, NOW() ),

							(
							 '2', 
						     'fa fa-comment-o', 
							 'comment-o', 
							 'f0e5'
							, NOW() ),

							(
							 '2', 
						     'fa fa-commenting', 
							 'commenting', 
							 'f27a'
							, NOW() ),
							(
							 '2', 
						     'fa fa-commenting-o', 
							 'commenting-o', 
							 'f27b'
							, NOW() ),
							(
							 '2', 
						     'fa fa-comments', 
							 'comments', 
							 'f086'
							, NOW() ),
							(
							 '2', 
						     'fa fa-comments-o', 
							 'comments-o', 
							 'f0e6'
							, NOW() ),
							(
							 '2', 
						     'fa fa-compass', 
							 'compass', 
							 'f14e'
							, NOW() ),
							(
							 '2', 
						     'fa fa-copyright', 
							 'copyright', 
							 'f1f9'
							, NOW() ),

							(
							 '2', 
						     'fa fa-creative-commons', 
							 'creative-commons', 
							 'f25e'
							, NOW() ),
							(
							 '2', 
						     'fa fa-credit-card', 
							 'credit-card', 
							 'f09d'
							, NOW() ),

							(
							 '2', 
						     'fa fa-credit-card-alt', 
							 'credit-card-alt', 
							 'f283'
							, NOW() ),
							(
							 '2', 
						     'fa fa-crop', 
							 'credit-card-altcrop', 
							 'f125'
							, NOW() ),

							(
							 '2', 
						     'fa fa-crosshairs', 
							 'crosshairs', 
							 'f05b'
							, NOW() ),

							(
							 '2', 
						     'fa fa-cube', 
							 'cube', 
							 'f1b2'
							, NOW() ),

							(
							 '2', 
						     'fa fa-cubes', 
							 'cubes', 
							 'f1b3'
							, NOW() ),

							(
							 '2', 
						     'fa fa-cutlery', 
							 'cutlery', 
							 'f0f5'
							, NOW() ),
							(
							 '2', 
						     'fa fa-dashboard', 
							 'dashboard', 
							 'f0e4'
							, NOW() ),

							(
							 '2', 
						     'fa fa-database', 
							 'database', 
							 'f1c0'
							, NOW() ),
							(
							 '2', 
						     'fa fa-deaf', 
							 'deaf', 
							 'f2a4'
							, NOW() ),
							(
							 '2', 
						     'fa fa-deafness', 
							 'deafness', 
							 'f2a4'
							, NOW() ),
							(
							 '2', 
						     'fa fa-desktop', 
							 'desktop', 
							 'f108'
							, NOW() ),
							(
							 '2', 
						     'fa fa-diamond', 
							 'diamond', 
							 'f219'
							, NOW() ),
							(
							 '2', 
						     'fa fa-dot-circle-o', 
							 'dot-circle-o', 
							 'f192'
							, NOW() ),
							(
							 '2', 
						     'fa fa-download', 
							 'download', 
							 'f019'
							, NOW() ),

							(
							 '2', 
						     'fa fa-drivers-license', 
							 'drivers-license', 
							 'f2c2'
							, NOW() ),
							(
							 '2', 
						     'fa fa-drivers-license-o', 
							 'drivers-license-o', 
							 'f2c3'
							, NOW() ),
							(
							 '2', 
						     'fa fa-edit', 
							 'edit', 
							 'f044'
							, NOW() ),
							(
							 '2', 
						     'fa fa-ellipsis-h', 
							 'ellipsis-h', 
							 'f141'
							, NOW() ),

							(
							 '2', 
						     'fa fa-ellipsis-v', 
							 'ellipsis-v', 
							 'f142'
							, NOW() ),
							(
							 '2', 
						     'fa fa-envelope', 
							 'envelope', 
							 'f0e0'
							, NOW() ),
							(
							 '2', 
						     'fa fa-envelope-o', 
							 'envelope-o', 
							 'f003'
							, NOW() ),
							(
							 '2', 
						     'fa fa-envelope-open', 
							 'envelope-open', 
							 'f2b6'
							, NOW() ),
							(
							 '2', 
						     'fa fa-envelope-open-o', 
							 'envelope-open-o', 
							 'f2b7'
							, NOW() ),
							(
							 '2', 
						     'fa fa-envelope-square', 
							 'envelope-square', 
							 'f199'
							, NOW() ),
							(
							 '2', 
						     'fa fa-eraser', 
							 'eraser', 
							 'f12d'
							, NOW() ),
							(
							 '2', 
						     'fa fa-exchange', 
							 'exchange', 
							 'f0ec'
							, NOW() ),
							(
							 '2', 
						     'fa fa-exclamation', 
							 'exclamation', 
							 'f12a'
							, NOW() ),
							(
							 '2', 
						     'fa fa-exclamation-circle', 
							 'exclamation-circle', 
							 'f06a'
							, NOW() ),
							(
							 '2', 
						     'fa fa-exclamation-triangle', 
							 'exclamation-triangle', 
							 'f071'
							, NOW() ),
							(
							 '2', 
						     'fa fa-external-link', 
							 'external-link', 
							 'f08e'
							, NOW() ),

							(
							 '2', 
						     'fa fa-external-link-square', 
							 'external-link-square', 
							 'f14c'
							, NOW() ),
							(
							 '2', 
						     'fa fa-eye', 
							 'eye', 
							 'f06e'
							, NOW() ),
							(
							 '2', 
						     'fa fa-eye-slash', 
							 'eye-slash', 
							 'f070'
							, NOW() ),
							(
							 '2', 
						     'fa fa-eyedropper', 
							 'eyedropper', 
							 'f1fb'
							, NOW() ),
							(
							 '2', 
						     'fa fa-fax', 
							 'fax', 
							 'f1ac'
							, NOW() ),
							(
							 '2', 
						     'fa fa-feed', 
							 'feed', 
							 'f09e'
							, NOW() ),
							(
							 '2', 
						     'fa fa-female', 
							 'female', 
							 'f182'
							, NOW() ),
							(
							 '2', 
						     'fa fa-fighter-jet', 
							 'fighter-jet', 
							 'f0fb'
							, NOW() ),
							(
							 '2', 
						     'fa fa-file-archive-o', 
							 'file-archive-o', 
							 'f1c6'
							, NOW() ),
							(
							 '2', 
						     'fa fa-file-audio-o', 
							 'file-audio-o', 
							 'f1c7'
							, NOW() ),
							(
							 '2', 
						     'fa fa-file-code-o', 
							 'file-code-o', 
							 'f1c9'
							, NOW() ),
							(
							 '2', 
						     'fa fa-file-excel-o', 
							 'file-excel-o', 
							 'f1c3'
							, NOW() ),
							(
							 '2', 
						     'fa fa-file-image-o', 
							 'file-image-o', 
							 'f1c5'
							, NOW() ),
							(
							 '2', 
						     'fa fa-file-movie-o', 
							 'file-movie-o', 
							 'f1c8'
							, NOW() ),
							(
							 '2', 
						     'fa fa-file-pdf-o', 
							 'file-pdf-o', 
							 'f1c1'
							, NOW() ),
							(
							 '2', 
						     'fa fa-file-photo-o', 
							 'file-photo-o', 
							 'f1c5'
							, NOW() ),
							(
							 '2', 
						     'fa fa-file-picture-o', 
							 'file-picture-o', 
							 'f1c5'
							, NOW() ),
							(
							 '2', 
						     'fa fa-file-powerpoint-o', 
							 'file-powerpoint-o', 
							 'f1c4'
							, NOW() ),
							(
							 '2', 
						     'fa fa-file-sound-o', 
							 'file-sound-o', 
							 'f1c7'
							, NOW() ),
							(
							 '2', 
						     'fa fa-file-video-o', 
							 'file-video-o', 
							 'f1c8'
							, NOW() ),

							(
							 '2', 
						     'fa fa-file-word-o', 
							 'file-word-o', 
							 'f1c2'
							, NOW() ),
							(
							 '2', 
						     'fa fa-file-zip-o', 
							 'file-zip-o', 
							 'f1c6'
							, NOW() ),
							(
							 '2', 
						     'fa fa-film', 
							 'film', 
							 'f008'
							, NOW() ),
							(
							 '2', 
						     'fa fa-filter', 
							 'filter', 
							 'f0b0'
							, NOW() ),
							(
							 '2', 
						     'fa fa-fire', 
							 'fire', 
							 'f06d'
							, NOW() ),
							(
							 '2', 
						     'fa fa-fire-extinguisher', 
							 'fire-extinguisher', 
							 'f134'
							, NOW() ),
							(
							 '2', 
						     'fa fa-flag', 
							 'flag', 
							 'f024'
							, NOW() ),
							(
							 '2', 
						     'fa fa-flag-checkered', 
							 'flag-checkered', 
							 'f11e'
							, NOW() ),
							(
							 '2', 
						     'fa fa-flag-o', 
							 'flag-o', 
							 'f11d'
							, NOW() ),
							(
							 '2', 
						     'fa fa-flash', 
							 'flash', 
							 'f0e7'
							, NOW() ),
							(
							 '2', 
						     'fa fa-flask', 
							 'flask', 
							 'f0c3'
							, NOW() ),
							(
							 '2', 
						     'fa fa-folder', 
							 'folder', 
							 'f07b'
							, NOW() ),
							(
							 '2', 
						     'fa fa-folder-o', 
							 'folder-o', 
							 'f114'
							, NOW() ),
							(
							 '2', 
						     'fa fa-folder-open', 
							 'folder-open', 
							 'f07c'
							, NOW() ),
							(
							 '2', 
						     'fa fa-folder-open-o', 
							 'folder-open-o', 
							 'f115'
							, NOW() ),
							(
							 '2', 
						     'fa fa-frown-o', 
							 'frown-o', 
							 'f119'
							, NOW() ),
							(
							 '2', 
						     'fa fa-futbol-o', 
							 'futbol-o', 
							 'f1e3'
							, NOW() ),
							(
							 '2', 
						     'fa fa-gamepad', 
							 'gamepad', 
							 'f11b'
							, NOW() ),
							(
							 '2', 
						     'fa fa-gavel', 
							 'gavel', 
							 'f0e3'
							, NOW() ),
							(
							 '2', 
						     'fa fa-gear', 
							 'gear', 
							 'f013'
							, NOW() ),
							(
							 '2', 
						     'fa fa-gears', 
							 'gears', 
							 'f085'
							, NOW() ),
							(
							 '2', 
						     'fa fa-gift', 
							 'gift', 
							 'f06b'
							, NOW() ),
							(
							 '2', 
						     'fa fa-glass', 
							 'glass', 
							 'f000'
							, NOW() ),
							(
							 '2', 
						     'fa fa-globe', 
							 'globe', 
							 'f0ac'
							, NOW() ),
							(
							 '2', 
						     'fa fa-graduation-cap', 
							 'graduation-cap', 
							 'f19d'
							, NOW() ),
							(
							 '2', 
						     'fa fa-group', 
							 'group', 
							 'f0c0'
							, NOW() ),
							(
							 '2', 
						     'fa fa-hand-grab-o', 
							 'hand-grab-o', 
							 'f255'
							, NOW() ),
							(
							 '2', 
						     'fa fa-hand-lizard-o', 
							 'hand-lizard-o', 
							 'f258'
							, NOW() ),
							(
							 '2', 
						     'fa fa-hand-paper-o', 
							 'hand-paper-o', 
							 'f256'
							, NOW() ),
							(
							 '2', 
						     'fa fa-hand-peace-o', 
							 'hand-peace-o', 
							 'f25b'
							, NOW() ),
							(
							 '2', 
						     'fa fa-hand-pointer-o', 
							 'hand-pointer-o', 
							 'f25a'
							, NOW() ),

							(
							 '2', 
						     'fa fa-hand-rock-o', 
							 'hand-rock-o', 
							 'f255'
							, NOW() ),
							(
							 '2', 
						     'fa fa-hand-scissors-o', 
							 'hand-scissors-o', 
							 'f257'
							, NOW() ),
							(
							 '2', 
						     'fa fa-hand-spock-o', 
							 'hand-spock-o', 
							 'f259'
							, NOW() ),
							(
							 '2', 
						     'fa fa-hand-stop-o', 
							 'hand-stop-o', 
							 'f256'
							, NOW() ),
							(
							 '2', 
						     'fa fa-handshake-o', 
							 'handshake-o', 
							 'f2b5'
							, NOW() ),
							(
							 '2', 
						     'fa fa-hard-of-hearing', 
							 'hard-of-hearing', 
							 'f2a4'
							, NOW() ),
							(
							 '2', 
						     'fa fa-hashtag', 
							 'hashtag', 
							 'f292'
							, NOW() ),
							(
							 '2', 
						     'fa fa-hdd-o', 
							 'hdd-o', 
							 'f0a0'
							, NOW() ),
							(
							 '2', 
						     'fa fa-headphones', 
							 'headphones', 
							 'f025'
							, NOW() ),
							(
							 '2', 
						     'fa fa-heart', 
							 'heart', 
							 'f004'
							, NOW() ),
							(
							 '2', 
						     'fa fa-heart-o', 
							 'heart-o', 
							 'f08a'
							, NOW() ),
							(
							 '2', 
						     'fa fa-heartbeat', 
							 'heartbeat', 
							 'f21e'
							, NOW() ),
							(
							 '2', 
						     'fa fa-history', 
							 'history', 
							 'f1da'
							, NOW() ),
							(
							 '2', 
						     'fa fa-home', 
							 'home', 
							 'f015'
							, NOW() ),
							(
							 '2', 
						     'fa fa-hotel', 
							 'hotel', 
							 'f236'
							, NOW() ),
							(
							 '2', 
						     'fa fa-hourglass', 
							 'hourglass', 
							 'f254'
							, NOW() ),
							(
							 '2', 
						     'fa fa-hourglass-1', 
							 'hourglass-1', 
							 'f251'
							, NOW() ),
							(
							 '2', 
						     'fa fa-hourglass-2', 
							 'hourglass-2', 
							 'f252'
							, NOW() ),
							(
							 '2', 
						     'fa fa-hourglass-3', 
							 'hourglass-3', 
							 'f253'
							, NOW() ),

							(
							 '2', 
						     'fa fa-hourglass-end', 
							 'hourglass-end', 
							 'f253'
							, NOW() ),
							(
							 '2', 
						     'fa fa-hourglass-half', 
							 'hourglass-half', 
							 'f252'
							, NOW() ),
							(
							 '2', 
						     'fa fa-hourglass-o', 
							 'hourglass-o', 
							 'f250'
							, NOW() ),
							(
							 '2', 
						     'fa fa-hourglass-start', 
							 'hourglass-start', 
							 'f251'
							, NOW() ),
							(
							 '2', 
						     'fa fa-i-cursor', 
							 'i-cursor', 
							 'f246'
							, NOW() ),
							(
							 '2', 
						     'fa fa-id-badge', 
							 'id-badge', 
							 'f2c1'
							, NOW() ),
							(
							 '2', 
						     'fa fa-id-card', 
							 'id-card', 
							 'f2c2'
							, NOW() ),
							(
							 '2', 
						     'fa fa-info', 
							 'info', 
							 'f129'
							, NOW() ),
							(
							 '2', 
						     'fa fa-info-circle', 
							 'info-circle', 
							 'f05a'
							, NOW() ),
							(
							 '2', 
						     'fa fa-institution', 
							 'institution', 
							 'f19c'
							, NOW() ),
							(
							 '2', 
						     'fa fa-key', 
							 'key', 
							 'f084'
							, NOW() ),
							(
							 '2', 
						     'fa fa-keyboard-o', 
							 'keyboard-o', 
							 'f11c'
							, NOW() ),
							(
							 '2', 
						     'fa fa-language', 
							 'language', 
							 'f1ab'
							, NOW() ),
							(
							 '2', 
						     'fa fa-laptop', 
							 'laptop', 
							 'f109'
							, NOW() ),
							(
							 '2', 
						     'fa fa-leaf', 
							 'leaf', 
							 'f06c'
							, NOW() ),
							(
							 '2', 
						     'fa fa-legal', 
							 'legal', 
							 'f0e3'
							, NOW() ),
							(
							 '2', 
						     'fa fa-lemon-o', 
							 'lemon-o', 
							 'f094'
							, NOW() ),
							(
							 '2', 
						     'fa fa-level-down', 
							 'level-down', 
							 'f149'
							, NOW() ),
							(
							 '2', 
						     'fa fa-level-up', 
							 'level-up', 
							 'f148'
							, NOW() ),
							(
							 '2', 
						     'fa fa-life-bouy', 
							 'life-bouy', 
							 'f1cd'
							, NOW() ),
							(
							 '2', 
						     'fa fa-life-buoy', 
							 'life-buoy', 
							 'f1cd'
							, NOW() ),
							(
							 '2', 
						     'fa fa-life-ring', 
							 'life-ring', 
							 'f1cd'
							, NOW() ),
							(
							 '2', 
						     'fa fa-life-saver', 
							 'life-saver', 
							 'f1cd'
							, NOW() ),
							(
							 '2', 
						     'fa fa-lightbulb-o', 
							 'lightbulb-o', 
							 'f0eb'
							, NOW() ),
							(
							 '2', 
						     'fa fa-line-chart', 
							 'line-chart', 
							 'f201'
							, NOW() ),
							(
							 '2', 
						     'fa fa-location-arrow', 
							 'location-arrow', 
							 'f124'
							, NOW() ),
							(
							 '2', 
						     'fa fa-lock', 
							 'lock', 
							 'f023'
							, NOW() ),
							(
							 '2', 
						     'fa fa-low-vision', 
							 'low-vision', 
							 'f2a8'
							, NOW() ),
							(
							 '2', 
						     'fa fa-magic', 
							 'magic', 
							 'f0d0'
							, NOW() ),
							(
							 '2', 
						     'fa fa-magnet', 
							 'magnet', 
							 'f076'
							, NOW() ),
							(
							 '2', 
						     'fa fa-mail-forward', 
							 'mail-forward', 
							 'f064'
							, NOW() ),
							(
							 '2', 
						     'fa fa-mail-reply', 
							 'mail-reply', 
							 'f112'
							, NOW() ),
							(
							 '2', 
						     'fa fa-mail-reply-all', 
							 'mail-reply-all', 
							 'f122'
							, NOW() ),
							(
							 '2', 
						     'fa fa-male', 
							 'male', 
							 'f183'
							, NOW() ),
							(
							 '2', 
						     'fa fa-male', 
							 'male', 
							 'f183'
							, NOW() ),
							(
							 '2', 
						     'fa fa-map', 
							 'map', 
							 'f279'
							, NOW() ),
							(
							 '2', 
						     'fa fa-map-marker', 
							 'map-marker', 
							 'f041'
							, NOW() ),
							(
							 '2', 
						     'fa fa-map-o', 
							 'map-o', 
							 'f278'
							, NOW() ),
							(
							 '2', 
						     'fa fa-map-pin', 
							 'map-pin', 
							 'f276'
							, NOW() ),
							(
							 '2', 
						     'fa fa-map-signs', 
							 'map-signs', 
							 'f277'
							, NOW() ),
							(
							 '2', 
						     'fa fa-meh-o', 
							 'meh-o', 
							 'f11a'
							, NOW() ),
							(
							 '2', 
						     'fa fa-microchip', 
							 'microchip', 
							 'f2db'
							, NOW() ),
							(
							 '2', 
						     'fa fa-microphone', 
							 'microphone', 
							 'f130'
							, NOW() ),
							(
							 '2', 
						     'fa fa-microphone-slash', 
							 'microphone-slash', 
							 'f131'
							, NOW() ),
							(
							 '2', 
						     'fa fa-minus', 
							 'minus', 
							 'f068'
							, NOW() ),
							(
							 '2', 
						     'fa fa-minus-circle', 
							 'minus-circle', 
							 'f056'
							, NOW() ),
							(
							 '2', 
						     'fa fa-minus-square', 
							 'minus-square', 
							 'f146'
							, NOW() ),
							(
							 '2', 
						     'fa fa-minus-square-o', 
							 'minus-square-o', 
							 'f147'
							, NOW() ),
							(
							 '2', 
						     'fa fa-mobile', 
							 'mobile', 
							 'f10b'
							, NOW() ),
							(
							 '2', 
						     'fa fa-mobile-phone', 
							 'mobile-phone', 
							 'f10b'
							, NOW() ),
							(
							 '2', 
						     'fa fa-money', 
							 'money', 
							 'f0d6'
							, NOW() ),
							(
							 '2', 
						     'fa fa-moon-o', 
							 'moon-o', 
							 'f186'
							, NOW() ),
							(
							 '2', 
						     'fa fa-mortar-board', 
							 'mortar-board', 
							 'f19d'
							, NOW() ),
							(
							 '2', 
						     'fa fa-motorcycle', 
							 'motorcycle', 
							 'f21c'
							, NOW() ),
							(
							 '2', 
						     'fa fa-mouse-pointer', 
							 'mouse-pointer', 
							 'f245'
							, NOW() ),
							(
							 '2', 
						     'fa fa-music', 
							 'music', 
							 'f001'
							, NOW() ),
							(
							 '2', 
						     'fa fa-navicon', 
							 'navicon', 
							 'f0c9'
							, NOW() ),
							(
							 '2', 
						     'fa fa-newspaper-o', 
							 'newspaper-o', 
							 'f1ea'
							, NOW() ),
							(
							 '2', 
						     'fa fa-object-group', 
							 'object-group', 
							 'f247'
							, NOW() ),
							(
							 '2', 
						     'fa fa-object-ungroup', 
							 'object-ungroup', 
							 'f248'
							, NOW() ),
							(
							 '2', 
						     'fa fa-paint-brush', 
							 'object-ungroup', 
							 'f248'
							, NOW() ),
							(
							 '2', 
						     'fa fa-paper-plane', 
							 'paper-plane', 
							 'f1d8'
							, NOW() ),
							(
							 '2', 
						     'fa fa-paper-plane-o', 
							 'paper-plane-o', 
							 'f1d9'
							, NOW() ),
							(
							 '2', 
						     'fa fa-paw', 
							 'paw', 
							 'f1b0'
							, NOW() ),
							(
							 '2', 
						     'fa fa-pencil', 
							 'pencil', 
							 'f040'
							, NOW() ),
							(
							 '2', 
						     'fa fa-pencil-square', 
							 'pencil-square', 
							 'f14b'
							, NOW() ),
							(
							 '2', 
						     'fa fa-pencil-square-o', 
							 'pencil-square-o', 
							 'f044'
							, NOW() ),
							(
							 '2', 
						     'fa fa-percent', 
							 'percent', 
							 'f295'
							, NOW() ),
							(
							 '2', 
						     'fa fa-pie-chart', 
							 'pie-chart', 
							 'f200'
							, NOW() ),
							(
							 '2', 
						     'fa fa-plane', 
							 'plane', 
							 'f072'
							, NOW() ),
							(
							 '2', 
						     'fa fa-plug', 
							 'plug', 
							 'f1e6'
							, NOW() ),
							(
							 '2', 
						     'fa fa-plus', 
							 'plus', 
							 'f067'
							, NOW() ),
							(
							 '2', 
						     'fa fa-plus-circle', 
							 'plus-circle', 
							 'f055'
							, NOW() ),
							(
							 '2', 
						     'fa fa-plus-square', 
							 'plus-square', 
							 'f0fe'
							, NOW() ),
							(
							 '2', 
						     'fa fa-plus-square-o', 
							 'plus-square-o', 
							 'f196'
							, NOW() ),
							(
							 '2', 
						     'fa fa-podcast', 
							 'podcast', 
							 'f2ce'
							, NOW() ),
							(
							 '2', 
						     'fa fa-power-off', 
							 'power-off', 
							 'f011'
							, NOW() ),
							(
							 '2', 
						     'fa fa-print', 
							 'print', 
							 'f02f'
							, NOW() ),
							(
							 '2', 
						     'fa fa-puzzle-piece', 
							 'puzzle-piece', 
							 'f12e'
							, NOW() ),
							(
							 '2', 
						     'fa fa-qrcode', 
							 'qrcode', 
							 'f029'
							, NOW() ),
							(
							 '2', 
						     'fa fa-question', 
							 'question', 
							 'f128'
							, NOW() ),
							(
							 '2', 
						     'fa fa-question-circle', 
							 'question-circle', 
							 'f059'
							, NOW() ),
							(
							 '2', 
						     'fa fa-question-circle-o', 
							 'question-circle-o', 
							 'f29c'
							, NOW() ),
							(
							 '2', 
						     'fa fa-quote-left', 
							 'quote-left', 
							 'f10d'
							, NOW() ),
							(
							 '2', 
						     'fa fa-quote-right', 
							 'quote-right', 
							 'f10e'
							, NOW() ),
							(
							 '2', 
						     'fa fa-random', 
							 'random', 
							 'f074'
							, NOW() ),
							(
							 '2', 
						     'fa fa-recycle', 
							 'recycle', 
							 'f1b8'
							, NOW() ),
							(
							 '2', 
						     'fa fa-refresh', 
							 'refresh', 
							 'f021'
							, NOW() ),
							(
							 '2', 
						     'fa fa-registered', 
							 'registered', 
							 'f25d'
							, NOW() ),
							(
							 '2', 
						     'fa fa-remove', 
							 'remove', 
							 'f00d'
							, NOW() ),
							(
							 '2', 
						     'fa fa-reorder', 
							 'reorder', 
							 'f0c9'
							, NOW() ),
							(
							 '2', 
						     'fa fa-reply', 
							 'reply', 
							 'f112'
							, NOW() ),
							(
							 '2', 
						     'fa fa-reply-all', 
							 'reply-all', 
							 'f122'
							, NOW() ),
							(
							 '2', 
						     'fa fa-retweet', 
							 'retweet', 
							 'f079'
							, NOW() ),
							(
							 '2', 
						     'fa fa-road', 
							 'road', 
							 'f018'
							, NOW() ),
							(
							 '2', 
						     'fa fa-rocket', 
							 'rocket', 
							 'f135'
							, NOW() ),
							(
							 '2', 
						     'fa fa-rss', 
							 'rss', 
							 'f09e'
							, NOW() ),
							(
							 '2', 
						     'fa fa-rss-square', 
							 'rss-square', 
							 'f143'
							, NOW() ),
							(
							 '2', 
						     'fa fa-s15', 
							 's15', 
							 'f2cd'
							, NOW() ),
							(
							 '2', 
						     'fa fa-search', 
							 'search', 
							 'f002'
							, NOW() ),
							(
							 '2', 
						     'fa fa-search-minus', 
							 'search-minus', 
							 'f010'
							, NOW() ),
							(
							 '2', 
						     'fa fa-search-plus', 
							 'search-plus', 
							 'f00e'
							, NOW() ),
							(
							 '2', 
						     'fa fa-send', 
							 'send', 
							 'f1d8'
							, NOW() ),
							(
							 '2', 
						     'fa fa-send-o', 
							 'send-o', 
							 'f1d9'
							, NOW() ),
							(
							 '2', 
						     'fa fa-server', 
							 'server', 
							 '233'
							, NOW() ),
							(
							 '2', 
						     'fa fa-share', 
							 'share', 
							 'f064'
							, NOW() ),
							(
							 '2', 
						     'fa fa-share-alt', 
							 'share-alt', 
							 'f1e0'
							, NOW() ),
							(
							 '2', 
						     'fa fa-share-alt-square', 
							 'share-alt-square', 
							 'f1e1'
							, NOW() ),
							(
							 '2', 
						     'fa fa-share-square', 
							 'share-square', 
							 'f14d'
							, NOW() ),
							(
							 '2', 
						     'fa fa-share-square-o', 
							 'share-square-O', 
							 'f045'
							, NOW() ),
							(
							 '2', 
						     'fa fa-shield', 
							 'shield', 
							 'f132'
							, NOW() ),
							(
							 '2', 
						     'fa fa-ship', 
							 'ship', 
							 'f21a'
							, NOW() ),
							(
							 '2', 
						     'fa fa-shopping-bag', 
							 'shopping-bag', 
							 'f290'
							, NOW() ),
							(
							 '2', 
						     'fa fa-shopping-basket', 
							 'shopping-basket', 
							 'f291'
							, NOW() ),
							(
							 '2', 
						     'fa fa-shopping-cart', 
							 'shopping-cart', 
							 'f07a'
							, NOW() ),
							(
							 '2', 
						     'fa fa-shopping-cart', 
							 'shopping-cart', 
							 'f07a'
							, NOW() ),
							(
							 '2', 
						     'fa fa-shower', 
							 'shower', 
							 'f2cc'
							, NOW() ),
							(
							 '2', 
						     'fa fa-sign-in', 
							 'sign-in', 
							 'f090'
							, NOW() ),
							(
							 '2', 
						     'fa fa-sign-language', 
							 'sign-language', 
							 'f2a7'
							, NOW() ),
							(
							 '2', 
						     'fa fa-sign-out', 
							 'sign-out', 
							 'f08b'
							, NOW() ),
							(
							 '2', 
						     'fa fa-signal', 
							 'signal', 
							 'f012'
							, NOW() ),
							(
							 '2', 
						     'fa fa-signing', 
							 'signing', 
							 'f2a7'
							, NOW() ),
							(
							 '2', 
						     'fa fa-sitemap', 
							 'sitemap', 
							 'f0e8'
							, NOW() ),
							(
							 '2', 
						     'fa fa-sliders', 
							 'sliders', 
							 'f1de'
							, NOW() ),
							(
							 '2', 
						     'fa fa-smile-o', 
							 'smile-o', 
							 'f118'
							, NOW() ),
							(
							 '2', 
						     'fa fa-snowflake-o', 
							 'snowflake-o', 
							 'f2dc'
							, NOW() ),
							(
							 '2', 
						     'fa fa-soccer-ball-o', 
							 'soccer-ball-o', 
							 'f1e3'
							, NOW() ),
							(
							 '2', 
						     'fa fa-sort', 
							 'sort', 
							 'f0dc'
							, NOW() ),
							(
							 '2', 
						     'fa fa-sort-alpha-asc', 
							 'sort-alpha-asc', 
							 'f15d'
							, NOW() ),
							(
							 '2', 
						     'fa fa-sort-alpha-desc', 
							 'sort-alpha-desc', 
							 'f15e'
							, NOW() ),
							(
							 '2', 
						     'fa fa-sort-amount-asc', 
							 'sort-amount-asc', 
							 'f160'
							, NOW() ),
							(
							 '2', 
						     'fa fa-sort-amount-desc', 
							 'sort-amount-desc', 
							 'f161'
							, NOW() ),
							(
							 '2', 
						     'fa fa-sort-asc', 
							 'sort-asc', 
							 'f0de'
							, NOW() ),
							(
							 '2', 
						     'fa fa-sort-desc', 
							 'sort-desc', 
							 'f0dd'
							, NOW() ),
							(
							 '2', 
						     'fa fa-sort-down', 
							 'sort-down', 
							 'f0dd'
							, NOW() ),
							(
							 '2', 
						     'fa fa-sort-numeric-asc', 
							 'sort-numeric-asc', 
							 'f162'
							, NOW() ),
							(
							 '2', 
						     'fa fa-sort-numeric-desc', 
							 'sort-numeric-desc', 
							 'f163'
							, NOW() ),
							(
							 '2', 
						     'fa fa-sort-up', 
							 'sort-up', 
							 'f0de'
							, NOW() ),
							(
							 '2', 
						     'fa fa-space-shuttle', 
							 'space-shuttle', 
							 'f197'
							, NOW() ),
							(
							 '2', 
						     'fa fa-spinner', 
							 'spinner', 
							 'f110'
							, NOW() ),
							(
							 '2', 
						     'fa fa-spoon', 
							 'spoon', 
							 'f1b1'
							, NOW() ),
							(
							 '2', 
						     'fa fa-square', 
							 'square', 
							 'f0c8'
							, NOW() ),
							(
							 '2', 
						     'fa fa-square-o', 
							 'square-o', 
							 'f096'
							, NOW() ),
							(
							 '2', 
						     'fa fa-star', 
							 'star', 
							 'f005'
							, NOW() ),
							(
							 '2', 
						     'fa fa-star-half', 
							 'star-half', 
							 'f089'
							, NOW() ),
							(
							 '2', 
						     'fa fa-star-half-empty', 
							 'star-half-empty', 
							 'f123'
							, NOW() ),
							(
							 '2', 
						     'fa fa-star-half-full', 
							 'star-half-full', 
							 'f123'
							, NOW() ),
							(
							 '2', 
						     'fa fa-star-half-o', 
							 'star-half-o', 
							 'f123'
							, NOW() ),
							(
							 '2', 
						     'fa fa-star-o', 
							 'star-o', 
							 'f006'
							, NOW() ),
							(
							 '2', 
						     'fa fa-sticky-note', 
							 'sticky-note', 
							 'f249'
							, NOW() ),
							(
							 '2', 
						     'fa fa-sticky-note-o', 
							 'sticky-note-O', 
							 'f24a'
							, NOW() ),
							(
							 '2', 
						     'fa fa-street-view', 
							 'street-view', 
							 'f21d'
							, NOW() ),
							(
							 '2', 
						     'fa fa-suitcase', 
							 'suitcase', 
							 'f0f2'
							, NOW() ),
							(
							 '2', 
						     'fa fa-sun-o', 
							 'sun-o', 
							 'f185'
							, NOW() ),
							(
							 '2', 
						     'fa fa-support', 
							 'sun-osupport', 
							 'f1cd'
							, NOW() ),
							(
							 '2', 
						     'fa fa-tablet', 
							 'tablet', 
							 'f10a'
							, NOW() ),
							(
							 '2', 
						     'fa fa-tachometer', 
							 'tachometer', 
							 'f0e4'
							, NOW() ),
							(
							 '2', 
						     'fa fa-tag', 
							 'tag', 
							 'f02b'
							, NOW() ),
							(
							 '2', 
						     'fa fa-tags', 
							 'tags', 
							 'f02b'
							, NOW() ),
							(
							 '2', 
						     'fa fa-tags', 
							 'tags', 
							 'f02c'
							, NOW() ),
							(
							 '2', 
						     'fa fa-tasks', 
							 'tasks', 
							 'f0ae'
							, NOW() ),
							(
							 '2', 
						     'fa fa-taxi', 
							 'taxi', 
							 'f1ba'
							, NOW() ),
							(
							 '2', 
						     'fa fa-television', 
							 'television', 
							 'f26c'
							, NOW() ),
							(
							 '2', 
						     'fa fa-terminal', 
							 'terminal', 
							 'f120'
							, NOW() ),
							(
							 '2', 
						     'fa fa-thermometer', 
							 'thermometer', 
							 'f2c7'
							, NOW() ),
							(
							 '2', 
						     'fa fa-thermometer-0', 
							 'thermometer-0', 
							 'f2cb'
							, NOW() ),
							(
							 '2', 
						     'fa fa-thermometer-1', 
							 'thermometer-1', 
							 'f2ca'
							, NOW() ),
							(
							 '2', 
						     'fa fa-thermometer-2', 
							 'thermometer-2', 
							 'f2c9'
							, NOW() ),
							(
							 '2', 
						     'fa fa-thermometer-3', 
							 'thermometer-3', 
							 'f2c8'
							, NOW() ),
							(
							 '2', 
						     'fa fa-thermometer-4', 
							 'thermometer-4', 
							 'f2c7'
							, NOW() ),
							(
							 '2', 
						     'fa fa-thermometer-empty', 
							 'thermometer-empty', 
							 'f2cb'
							, NOW() ),
							(
							 '2', 
						     'fa fa-thermometer-full', 
							 'thermometer-full', 
							 'f2c7'
							, NOW() ),
							(
							 '2', 
						     'fa fa-thermometer-half', 
							 'thermometer-half', 
							 'f2c9'
							, NOW() ),
							(
							 '2', 
						     'fa fa-thermometer-quarter', 
							 'thermometer-quarter', 
							 'f2ca'
							, NOW() ),
							(
							 '2', 
						     'fa fa-thermometer-three-quarters', 
							 'thermometer-three-quarters', 
							 'f2c8'
							, NOW() ),
							(
							 '2', 
						     'fa fa-thumb-tack', 
							 'thumb-tack', 
							 'f08d'
							, NOW() ),
							(
							 '2', 
						     'fa fa-thumbs-down', 
							 'thumb-down', 
							 'f165'
							, NOW() ),
							(
							 '2', 
						     'fa fa-thumbs-o-down', 
							 'thumb-o-down', 
							 'f088'
							, NOW() ),
							(
							 '2', 
						     'fa fa-thumbs-o-up', 
							 'thumb-o-up', 
							 'f087'
							, NOW() ),
							(
							 '2', 
						     'fa fa-thumbs-up', 
							 'thumb-up', 
							 'f087'
							, NOW() ),
							(
							 '2', 
						     'fa fa-ticket', 
							 'ticket', 
							 'f145'
							, NOW() ),
							(
							 '2', 
						     'fa fa-times', 
							 'times', 
							 'f00d'
							, NOW() ),
							(
							 '2', 
						     'fa fa-times-circle', 
							 'times-circle', 
							 'f057'
							, NOW() ),
							(
							 '2', 
						     'fa fa-times-circle-o', 
							 'times-circle-o', 
							 'f05c'
							, NOW() ),
							(
							 '2', 
						     'fa fa-times-rectangle', 
							 'times-circle-rectangle', 
							 'f2d3'
							, NOW() ),
							(
							 '2', 
						     'fa fa-times-rectangle-o', 
							 'times-circle-rectangle-o', 
							 'f2d4'
							, NOW() ),
							(
							 '2', 
						     'fa fa-tint', 
							 'tint', 
							 'f043'
							, NOW() ),
							(
							 '2', 
						     'fa fa-toggle-down', 
							 'toggle-down', 
							 'f150'
							, NOW() ),
							(
							 '2', 
						     'fa fa-toggle-left', 
							 'toggle-left', 
							 'f191'
							, NOW() ),
							(
							 '2', 
						     'fa fa-toggle-off', 
							 'toggle-off', 
							 'f204'
							, NOW() ),
							(
							 '2', 
						     'fa fa-toggle-on', 
							 'toggle-on', 
							 'f205'
							, NOW() ),
							(
							 '2', 
						     'fa fa-toggle-right', 
							 'toggle-right', 
							 'f152'
							, NOW() ),
							(
							 '2', 
						     'fa fa-toggle-up', 
							 'toggle-up', 
							 'f151'
							, NOW() ),
							(
							 '2', 
						     'fa fa-trademark', 
							 'trademark', 
							 'f25c'
							, NOW() ),
							(
							 '2', 
						     'fa fa-trash', 
							 'trash', 
							 'f1f8'
							, NOW() ),
							(
							 '2', 
						     'fa fa-trash-o', 
							 'trash-o', 
							 'f014'
							, NOW() ),
							(
							 '2', 
						     'fa fa-tree', 
							 'tree', 
							 'f1bb'
							, NOW() ),
							(
							 '2', 
						     'fa fa-trophy', 
							 'trophy', 
							 'f091'
							, NOW() ),
							(
							 '2', 
						     'fa fa-truck', 
							 'truck', 
							 'f0d1'
							, NOW() ),
							(
							 '2', 
						     'fa fa-tty', 
							 'tty', 
							 'f1e4'
							, NOW() ),
							(
							 '2', 
						     'fa fa-tv', 
							 'tty', 
							 'f26c'
							, NOW() ),
							(
							 '2', 
						     'fa fa-umbrella', 
							 'umbrella', 
							 'f0e9'
							, NOW() ),
							(
							 '2', 
						     'fa fa-universal-access', 
							 'universal-access', 
							 'f29a'
							, NOW() ),
							(
							 '2', 
						     'fa fa-university', 
							 'university', 
							 'f19c'
							, NOW() ),
							(
							 '2', 
						     'fa fa-unlock', 
							 'unlock', 
							 'f09c'
							, NOW() ),
							(
							 '2', 
						     'fa fa-unlock-alt', 
							 'unlock-alt', 
							 'f13e'
							, NOW() ),
							(
							 '2', 
						     'fa fa-unsorted', 
							 'unsorted', 
							 'f0dc'
							, NOW() ),
							(
							 '2', 
						     'fa fa-upload', 
							 'upload', 
							 'f093'
							, NOW() ),
							(
							 '2', 
						     'fa fa-user', 
							 'user', 
							 'f007'
							, NOW() ),
							(
							 '2', 
						     'fa fa-user-circle', 
							 'user-circle', 
							 'f2bd'
							, NOW() ),
							(
							 '2', 
						     'fa fa-user-circle-o', 
							 'user-circle-o', 
							 'f2be'
							, NOW() ),
							(
							 '2', 
						     'fa fa-user-o', 
							 'user-o', 
							 'f2c0'
							, NOW() ),
							(
							 '2', 
						     'fa fa-user-plus', 
							 'user-plus', 
							 'f234'
							, NOW() ),
							(
							 '2', 
						     'fa fa-user-secret', 
							 'user-secret', 
							 'f21b'
							, NOW() ),
							(
							 '2', 
						     'fa fa-user-times', 
							 'user-times', 
							 'f235'
							, NOW() ),
							(
							 '2', 
						     'fa fa-users', 
							 'users', 
							 'f0c0'
							, NOW() ),
							(
							 '2', 
						     'fa fa-vcard', 
							 'vcard', 
							 'f2bb'
							, NOW() ),
							(
							 '2', 
						     'fa fa-vcard-o', 
							 'vcard-o', 
							 'f2bc'
							, NOW() ),
							(
							 '2', 
						     'fa fa-video-camera', 
							 'video-camera', 
							 'f03d'
							, NOW() ),
							(
							 '2', 
						     'fa fa-volume-control-phone', 
							 'volume-control-phone', 
							 'f2a0'
							, NOW() ),
							(
							 '2', 
						     'fa fa-volume-down', 
							 'volume-down', 
							 'f027'
							, NOW() ),
							(
							 '2', 
						     'fa fa-volume-off', 
							 'volume-off', 
							 'f026'
							, NOW() ),
							(
							 '2', 
						     'fa fa-volume-up', 
							 'volume-up', 
							 'f028'
							, NOW() ),
							(
							 '2', 
						     'fa fa-warning', 
							 'warning', 
							 'f071'
							, NOW() ),

							(
							 '2', 
						     'fa fa-wheelchair', 
							 'wheelchair', 
							 'f193'
							, NOW() ),
							(
							 '2', 
						     'fa fa-wheelchair-alt', 
							 'wheelchair-alt', 
							 'f29b'
							, NOW() ),
							(
							 '2', 
						     'fa fa-wifi', 
							 'wifi', 
							 'f1eb'
							, NOW() ),
							(
							 '2', 
						     'fa fa-window-close', 
							 'window-close', 
							 'f2d3'
							, NOW() ),
							(
							 '2', 
						     'fa fa-window-close-o', 
							 'window-close-o', 
							 'f2d4'
							, NOW() ),
							(
							 '2', 
						     'fa fa-window-maximize', 
							 'window-maximize', 
							 'f2d0'
							, NOW() ),
							(
							 '2', 
						     'fa fa-window-minimize', 
							 'window-minimize', 
							 'f2d1'
							, NOW() ),
							(
							 '2', 
						     'fa fa-window-restore', 
							 'window-restore', 
							 'f2d2'
							, NOW() ),
							(
							 '2', 
						     'fa fa-wrench', 
							 'wrench', 
							 'f0ad'
							, NOW() ),  
							(
							 '3', 
						     'fa fa-american-sign-language-interpreting', 
							 'american-sign-language-interpreting', 
							 'f2a3'
							, NOW() ),  
							(
							 '3', 
						     'fa fa-asl-interpreting', 
							 'asl-interpreting', 
							 'f2a3'
							, NOW() ),
							(
							 '3', 
						     'fa fa-assistive-listening-systems', 
							 'assistive-listening-systems', 
							 'f2a2'
							, NOW() ),
							(
							 '3', 
						     'fa fa-audio-description', 
							 'audio-description', 
							 'f29e'
							, NOW() ), 
							(
							 '3', 
						     'fa fa-blind', 
							 'blind', 
							 'f29d'
							, NOW() ),
							(
							 '3', 
						     'fa fa-braille', 
							 'braille', 
							 'f2a1'
							, NOW() ),
							(
							 '3', 
						     'fa fa-cc', 
							 'cc', 
							 'f20a'
							, NOW() ),
							(
							 '3', 
						     'fa fa-deaf', 
							 'deaf', 
							 'f2a4'
							, NOW() ),
							(
							 '3', 
						     'fa fa-deafness', 
							 'deafness', 
							 'f2a4'
							, NOW() ),
							(
							 '3', 
						     'fa fa-hard-of-hearing', 
							 'hard-of-hearing', 
							 'f2a4'
							, NOW() ),
							(
							 '3', 
						     'fa fa-low-vision', 
							 'low-vision', 
							 'f2a8'
							, NOW() ),
							(
							 '3', 
						     'fa fa-question-circle-o', 
							 'question-circle-o', 
							 'f29c'
							, NOW() ),
							(
							 '3', 
						     'fa fa-sign-language', 
							 'sign-language', 
							 'f2a7'
							, NOW() ),
							(
							 '3', 
						     'fa fa-signing', 
							 'signing', 
							 'f2a7'
							, NOW() ),
							(
							 '3', 
						     'fa fa-tty', 
							 'tty', 
							 'f1e4'
							, NOW() ),
							(
							 '3', 
						     'fa fa-universal-access', 
							 'universal-access', 
							 'f29a'
							, NOW() ),
							(
							 '3', 
						     'fa fa-volume-control-phone', 
							 'volume-control-phone', 
							 'f2a0'
							, NOW() ),
							(
							 '3', 
						     'fa fa-wheelchair', 
							 'wheelchair', 
							 'f193'
							, NOW() ),
							(
							 '3', 
						     'fa fa-wheelchair-alt', 
							 'wheelchair-alt', 
							 'f29b'
							, NOW() ),
							(
							 '4', 
						     'fa fa-hand-grab-o', 
							 'hand-grab-o', 
							 'f255'
							, NOW() ),
							(
							 '4', 
						     'fa fa-hand-lizard-o', 
							 'hand-lizard-o', 
							 'f258'
							, NOW() ),
							(
							 '4', 
						     'fa fa-hand-o-down', 
							 'hand-o-down', 
							 'f0a7'
							, NOW() ),
							(
							 '4', 
						     'fa fa-hand-o-left', 
							 'hand-o-left', 
							 'f0a5'
							, NOW() ),
							(
							 '4', 
						     'fa fa-hand-o-right', 
							 'hand-o-right', 
							 'f0a4'
							, NOW() ),
							(
							 '4', 
						     'fa fa-hand-o-up', 
							 'hand-o-up', 
							 'f0a6'
							, NOW() ),
							(
							 '4', 
						     'fa fa-hand-paper-o', 
							 'hand-paper-o', 
							 'f256'
							, NOW() ),
							(
							 '4', 
						     'fa fa-hand-peace-o', 
							 'hand-peace-o', 
							 'f25b'
							, NOW() ),
							(
							 '4', 
						     'fa fa-hand-pointer-o', 
							 'hand-pointer-o', 
							 'f25a'
							, NOW() ),
							(
							 '4', 
						     'fa fa-hand-rock-o', 
							 'hand-rock-o', 
							 'f255'
							, NOW() ),
							(
							 '4', 
						     'fa fa-hand-scissors-o', 
							 'hand-scissors-o', 
							 'f257'
							, NOW() ),
							(
							 '4', 
						     'fa fa-hand-spock-o', 
							 'hand-spock-o', 
							 'f259'
							, NOW() ),
							(
							 '4', 
						     'fa fa-hand-stop-o', 
							 'hand-stop-o', 
							 'f256'
							, NOW() ),
							(
							 '4', 
						     'fa fa-thumbs-down', 
							 'thumbs-down', 
							 'f165'
							, NOW() ),
							(
							 '4', 
						     'fa fa-thumbs-o-down', 
							 'thumbs-o-down', 
							 'f088'
							, NOW() ),
							(
							 '4', 
						     'fa fa-thumbs-o-up', 
							 'thumbs-o-up', 
							 'f087'
							, NOW() ),
							(
							 '4', 
						     'fa fa-thumbs-up', 
							 'thumbs-up', 
							 'f164'
							, NOW() ),
							(
							 '5', 
						     'fa fa-ambulance', 
							 'ambulance', 
							 'f0f9'
							, NOW() ),
							(
							 '5', 
						     'fa fa-automobile', 
							 'automobile', 
							 'f1b9'
							, NOW() ),
							(
							 '5', 
						     'fa fa-bicycle', 
							 'bicycle', 
							 'f206'
							, NOW() ),
							(
							 '5', 
						     'fa fa-bus', 
							 'bus', 
							 'f207'
							, NOW() ),
							(
							 '5', 
						     'fa fa-cab', 
							 'cab', 
							 'f1ba'
							, NOW() ),
							(
							 '5', 
						     'fa fa-car', 
							 'car', 
							 'f1b9'
							, NOW() ),
							(
							 '5', 
						     'fa fa-fighter-jet', 
							 'fighter-jet', 
							 'f0fb'
							, NOW() ),
							(
							 '5', 
						     'fa fa-motorcycle', 
							 'motorcycle', 
							 'f21c'
							, NOW() ),
							(
							 '5', 
						     'fa fa-plane', 
							 'plane', 
							 'f072'
							, NOW() ),
							(
							 '5', 
						     'fa fa-rocket', 
							 'rocket', 
							 'f135'
							, NOW() ),
							(
							 '5', 
						     'fa fa-ship', 
							 'ship', 
							 'f21a'
							, NOW() ),
							(
							 '5', 
						     'fa fa-space-shuttle', 
							 'space-shuttle', 
							 'f197'
							, NOW() ),
							(
							 '5', 
						     'fa fa-subway', 
							 'subway', 
							 'f239'
							, NOW() ),
							(
							 '5', 
						     'fa fa-taxi', 
							 'taxi', 
							 'f1ba'
							, NOW() ),
							(
							 '5', 
						     'fa fa-train', 
							 'train', 
							 'f238'
							, NOW() ),
							(
							 '5', 
						     'fa fa-truck', 
							 'truck', 
							 'f0d1'
							, NOW() ),
							(
							 '5', 
						     'fa fa-wheelchair', 
							 'wheelchair', 
							 'f193'
							, NOW() ),
							(
							 '5', 
						     'fa fa-wheelchair-alt', 
							 'wheelchair-alt', 
							 'f29b'
							, NOW() ),

							(
							 '5', 
						     'fa fa-wheelchair-alt', 
							 'wheelchair-alt', 
							 'f29b'
							, NOW() ),
							(
							 '6', 
						     'fa fa-genderless', 
							 'genderless', 
							 'f22d'
							, NOW() ),
							(
							 '6', 
						     'fa fa-intersex', 
							 'intersex', 
							 'f224'
							, NOW() ),
							(
							 '6', 
						     'fa fa-mars', 
							 'mars', 
							 'f222'
							, NOW() ),
							(
							 '6', 
						     'fa fa-mars-double', 
							 'mars-double', 
							 'f227'
							, NOW() ),
							(
							 '6', 
						     'fa fa-mars-stroke', 
							 'mars-stroke', 
							 'f229'
							, NOW() ),
							(
							 '6', 
						     'fa fa-mars-stroke-h', 
							 'mars-stroke-h', 
							 'f22b'
							, NOW() ),
							(
							 '6', 
						     'fa fa-mars-stroke-v', 
							 'mars-stroke-v', 
							 'f22a'
							, NOW() ),
							(
							 '6', 
						     'fa fa-mercury', 
							 'mercury', 
							 'f223'
							, NOW() ),
							(
							 '6', 
						     'fa fa-neuter', 
							 'neuter', 
							 'f22c'
							, NOW() ),
							(
							 '6', 
						     'fa fa-transgender', 
							 'transgender', 
							 'f224'
							, NOW() ),
							(
							 '6', 
						     'fa fa-transgender-alt', 
							 'transgender-alt', 
							 'f225'
							, NOW() ),
							(
							 '6', 
						     'fa fa-venus', 
							 'venus', 
							 'f221'
							, NOW() ),
							(
							 '6', 
						     'fa fa-venus-double', 
							 'venus-double', 
							 'f226'
							, NOW() ),
							(
							 '6', 
						     'fa fa-venus-mars', 
							 'venus-mars', 
							 'f226'
							, NOW() ),
							(
							 '7', 
						     'fa fa-file', 
							 'file', 
							 'f15b'
							, NOW() ),
							(
							 '7', 
						     'fa fa-file-archive-o', 
							 'file-archive-o', 
							 'f1c6'
							, NOW() ),
							(
							 '7', 
						     'fa fa-file-audio-o', 
							 'file-audio-o', 
							 'f1c7'
							, NOW() ),
							(
							 '7', 
						     'fa fa-file-code-o', 
							 'file-code-o', 
							 'f1c9'
							, NOW() ),
							(
							 '7', 
						     'fa fa-file-excel-o', 
							 'file-excel-o', 
							 'f1c3'
							, NOW() ),
							(
							 '7', 
						     'fa fa-file-image-o', 
							 'file-image-o', 
							 'f1c5'
							, NOW() ),
							(
							 '7', 
						     'fa fa-file-movie-o', 
							 'file-movie-o', 
							 'f1c8'
							, NOW() ),
							(
							 '7', 
						     'fa fa-file-o', 
							 'file-o', 
							 'f016'
							, NOW() ),
							(
							 '7', 
						     'fa fa-file-pdf-o', 
							 'file-pdf-o', 
							 'f1c1'
							, NOW() ),
							(
							 '7', 
						     'fa fa-file-photo-o', 
							 'file-photo-o', 
							 'f1c5'
							, NOW() ),
							(
							 '7', 
						     'fa fa-file-picture-o', 
							 'file-picture-o', 
							 'f1c5'
							, NOW() ),
							(
							 '7', 
						     'fa fa-file-powerpoint-o', 
							 'file-powerpoint-o', 
							 'f1c4'
							, NOW() ),
							(
							 '7', 
						     'fa fa-file-sound-o', 
							 'file-sound-o', 
							 'f1c7'
							, NOW() ),
							(
							 '7', 
						     'fa fa-file-text', 
							 'file-text', 
							 'f15c'
							, NOW() ),
							(
							 '7', 
						     'fa fa-file-text-o', 
							 'file-text-o', 
							 'f0f6'
							, NOW() ),
							(
							 '7', 
						     'fa fa-file-video-o', 
							 'file-video-o', 
							 'f1c8'
							, NOW() ),
							(
							 '7', 
						     'fa fa-file-word-o', 
							 'file-word-o', 
							 'f1c2'
							, NOW() ),
							(
							 '7', 
						     'fa fa-file-zip-o', 
							 'file-zip-o', 
							 'f1c6'
							, NOW() ),
							(
							 '8', 
						     'fa fa-circle-o-notch', 
							 'circle-o-notch', 
							 'f1ce'
							, NOW() ),
							(
							 '8', 
						     'fa fa-cog', 
							 'cog', 
							 'f013'
							, NOW() ),
							(
							 '8', 
						     'fa fa-gear', 
							 'gear', 
							 'f013'
							, NOW() ),
							(
							 '8', 
						     'fa fa-refresh', 
							 'refresh', 
							 'f021'
							, NOW() ),
							(
							 '8', 
						     'fa fa-spinner', 
							 'spinner', 
							 'f110'
							, NOW() ),
							(
							 '9', 
						     'fa fa-check-square', 
							 'check-square', 
							 'f14a'
							, NOW() ),
							(
							 '9', 
						     'fa fa-check-square-o', 
							 'check-square-o', 
							 'f046'
							, NOW() ),
							(
							 '9', 
						     'fa fa-circle', 
							 'circle', 
							 'f111'
							, NOW() ),
							(
							 '9', 
						     'fa fa-circle-o', 
							 'circle-o', 
							 'f10c'
							, NOW() ),
							(
							 '9', 
						     'fa fa-dot-circle-o', 
							 'dot-circle-o', 
							 'f192'
							, NOW() ),
							(
							 '9', 
						     'fa fa-minus-square', 
							 'minus-square', 
							 'f146'
							, NOW() ),
							(
							 '9', 
						     'fa fa-minus-square-o', 
							 'minus-square-o', 
							 'f147'
							, NOW() ),
							(
							 '9', 
						     'fa fa-plus-square', 
							 'plus-square', 
							 'f0fe'
							, NOW() ),
							(
							 '9', 
						     'fa fa-plus-square-o', 
							 'plus-square-o', 
							 'f196'
							, NOW() ),
							(
							 '9', 
						     'fa fa-square', 
							 'square', 
							 'f0c8'
							, NOW() ),

							(
							 '9', 
						     'fa fa-square-o', 
							 'square-o', 
							 'f096'
							, NOW() ),
							(
							 '10', 
						     'fa fa-cc-amex', 
							 'cc-amex', 
							 'f1f3'
							, NOW() ),
							(
							 '10', 
						     'fa fa-cc-diners-club', 
							 'cc-diners-club', 
							 'f24c'
							, NOW() ),
							(
							 '10', 
						     'fa fa-cc-discover', 
							 'cc-discover', 
							 'f1f2'
							, NOW() ),
							(
							 '10', 
						     'fa fa-cc-jcb', 
							 'cc-jcb', 
							 'f24b'
							, NOW() ),
							(
							 '10', 
						     'fa fa-cc-mastercard', 
							 'cc-mastercard', 
							 'f1f1'
							, NOW() ),
							(
							 '10', 
						     'fa fa-cc-paypal', 
							 'cc-paypal', 
							 'f1f4'
							, NOW() ),
							(
							 '10', 
						     'fa fa-cc-stripe', 
							 'cc-stripe', 
							 'f1f5'
							, NOW() ),
							(
							 '10', 
						     'fa fa-cc-visa', 
							 'cc-visa', 
							 'f1f0'
							, NOW() ),
							(
							 '10', 
						     'fa fa-credit-card', 
							 'credit-card', 
							 'f09d'
							, NOW() ),
							(
							 '10', 
						     'fa fa-credit-card-alt', 
							 'credit-card-alt', 
							 'f283'
							, NOW() ),
							(
							 '10', 
						     'fa fa-google-wallet', 
							 'google-wallet', 
							 'f1ee'
							, NOW() ),
							(
							 '10', 
						     'fa fa-paypal', 
							 'paypal', 
							 'f1ed'
							, NOW() ),
							(
							 '11', 
						     'fa fa-area-chart', 
							 'area-chart', 
							 'f1fe'
							, NOW() ),
							(
							 '11', 
						     'fa fa-bar-chart', 
							 'bar-chart', 
							 'f080'
							, NOW() ),
							(
							 '11', 
						     'fa fa-bar-chart-o', 
							 'bar-chart-o', 
							 'f080'
							, NOW() ),
							(
							 '11', 
						     'fa fa-line-chart', 
							 'line-chart', 
							 'f201'
							, NOW() ),

							(
							 '11', 
						     'fa fa-pie-chart', 
							 'pie-chart', 
							 'f200'
							, NOW() ),
							(
							 '12', 
						     'fa fa-bitcoin', 
							 'bitcoin', 
							 'f15a'
							, NOW() ),
							(
							 '12', 
						     'fa fa-btc', 
							 'btc', 
							 'f15a'
							, NOW() ),
							(
							 '12', 
						     'fa fa-cny', 
							 'cny', 
							 'f157'
							, NOW() ),
							(
							 '12', 
						     'fa fa-dollar', 
							 'dollar', 
							 'f155'
							, NOW() ),
							(
							 '12', 
						     'fa fa-eur', 
							 'eur', 
							 'f153'
							, NOW() ),

							(
							 '12', 
						     'fa fa-euro', 
							 'euro', 
							 'f153'
							, NOW() ),
							(
							 '12', 
						     'fa fa-gbp', 
							 'gbp', 
							 'f154'
							, NOW() ),
							(
							 '12', 
						     'fa fa-gg', 
							 'gg', 
							 'f260'
							, NOW() ),
							(
							 '12', 
						     'fa fa-gg-circle', 
							 'gg-circle', 
							 'f261'
							, NOW() ),
							(
							 '12', 
						     'fa fa-ils', 
							 'ils', 
							 'f20b'
							, NOW() ),
							(
							 '12', 
						     'fa fa-inr', 
							 'inr', 
							 'f156'
							, NOW() ),
							(
							 '12', 
						     'fa fa-jpy', 
							 'jpy', 
							 'f157'
							, NOW() ),
							(
							 '12', 
						     'fa fa-krw', 
							 'krw', 
							 'f159'
							, NOW() ),
							(
							 '12', 
						     'fa fa-money', 
							 'money', 
							 'f0d6'
							, NOW() ),
							(
							 '12', 
						     'fa fa-rmb', 
							 'rmb', 
							 'f157'
							, NOW() ),
							(
							 '12', 
						     'fa fa-rouble', 
							 'rouble', 
							 'f158'
							, NOW() ),
							(
							 '12', 
						     'fa fa-rub', 
							 'rub', 
							 'f158'
							, NOW() ),
							(
							 '12', 
						     'fa fa-ruble', 
							 'ruble', 
							 'f158'
							, NOW() ),
							(
							 '12', 
						     'fa fa-rupee', 
							 'rupee', 
							 'f156'
							, NOW() ),
							(
							 '12', 
						     'fa fa-shekel', 
							 'shekel', 
							 'f20b'
							, NOW() ),
							(
							 '12', 
						     'fa fa-sheqel', 
							 'sheqel', 
							 'f20b'
							, NOW() ),
							(
							 '12', 
						     'fa fa-try', 
							 'try', 
							 'f195'
							, NOW() ),
							(
							 '12', 
						     'fa fa-turkish-lira', 
							 'turkish-lira', 
							 'f195'
							, NOW() ),
							(
							 '12', 
						     'fa fa-usd', 
							 'usd', 
							 'f155'
							, NOW() ),
							(
							 '12', 
						     'fa fa-viacoin', 
							 'viacoin', 
							 'f237'
							, NOW() ),
							(
							 '12', 
						     'fa fa-won', 
							 'won', 
							 'f159'
							, NOW() ),
							(
							 '12', 
						     'fa fa-yen', 
							 'yen', 
							 'f157'
							, NOW() ),
							(
							 '13', 
						     'fa fa-align-center', 
							 'align-center', 
							 'f037'
							, NOW() ),
							(
							 '13', 
						     'fa fa-align-justify', 
							 'align-justify', 
							 'f039'
							, NOW() ),
							(
							 '13', 
						     'fa fa-align-left', 
							 'align-left', 
							 'f036'
							, NOW() ),
							(
							 '13', 
						     'fa fa-align-right', 
							 'align-right', 
							 'f038'
							, NOW() ),
							(
							 '13', 
						     'fa fa-bold', 
							 'bold', 
							 'f032'
							, NOW() ),
							(
							 '13', 
						     'fa fa-chain', 
							 'chain', 
							 'f0c1'
							, NOW() ),
							(
							 '13', 
						     'fa fa-chain-broken', 
							 'chain-broken', 
							 'f127'
							, NOW() ),
							(
							 '13', 
						     'fa fa-clipboard', 
							 'clipboard', 
							 'f0ea'
							, NOW() ),
							(
							 '13', 
						     'fa fa-columns', 
							 'columns', 
							 'f0db'
							, NOW() ),
							(
							 '13', 
						     'fa fa-copy', 
							 'copy', 
							 'f0c5'
							, NOW() ),
							(
							 '13', 
						     'fa fa-cut', 
							 'cut', 
							 'f0c4'
							, NOW() ),
							(
							 '13', 
						     'fa fa-dedent', 
							 'dedent', 
							 'f03b'
							, NOW() ),
							(
							 '13', 
						     'fa fa-eraser', 
							 'eraser', 
							 'f12d'
							, NOW() ),
							(
							 '13', 
						     'fa fa-file', 
							 'file', 
							 'f15b'
							, NOW() ),
							(
							 '13', 
						     'fa fa-file-o', 
							 'file-o', 
							 'f016'
							, NOW() ),
							(
							 '13', 
						     'fa fa-file-text', 
							 'file-text', 
							 'f15c'
							, NOW() ),
							(
							 '13', 
						     'fa fa-file-text-o', 
							 'file-text-o', 
							 'f0f6'
							, NOW() ),
							(
							 '13', 
						     'fa fa-files-o', 
							 'files-o', 
							 'f0c5'
							, NOW() ),
							(
							 '13', 
						     'fa fa-floppy-o', 
							 'floppy-o', 
							 'f0c7'
							, NOW() ),
							(
							 '13', 
						     'fa fa-font', 
							 'font', 
							 'f031'
							, NOW() ),
							(
							 '13', 
						     'fa fa-header', 
							 'header', 
							 'f1dc'
							, NOW() ),
							(
							 '13', 
						     'fa fa-indent', 
							 'indent', 
							 'f03c'
							, NOW() ),
							(
							 '13', 
						     'fa fa-italic', 
							 'italic', 
							 'f033'
							, NOW() ),
							(
							 '13', 
						     'fa fa-link', 
							 'link', 
							 'f0c1'
							, NOW() ),
							(
							 '13', 
						     'fa fa-list', 
							 'list', 
							 'f03a'
							, NOW() ),
							(
							 '13', 
						     'fa fa-list-alt', 
							 'list-alt', 
							 'f022'
							, NOW() ),
							(
							 '13', 
						     'fa fa-list-ol', 
							 'list-ol', 
							 'f0cb'
							, NOW() ),
							(
							 '13', 
						     'fa fa-list-ul', 
							 'list-ul', 
							 'f0ca'
							, NOW() ),
							(
							 '13', 
						     'fa fa-outdent', 
							 'outdent', 
							 'f03b'
							, NOW() ),
							(
							 '13', 
						     'fa fa-paperclip', 
							 'paperclip', 
							 'f0c6'
							, NOW() ),
							(
							 '13', 
						     'fa fa-paragraph', 
							 'paragraph', 
							 'f1dd'
							, NOW() ),
							(
							 '13', 
						     'fa fa-paste', 
							 'paste', 
							 'f0ea'
							, NOW() ),
							(
							 '13', 
						     'fa fa-repeat', 
							 'repeat', 
							 'f01e'
							, NOW() ),
							(
							 '13', 
						     'fa fa-rotate-left', 
							 'rotate-left', 
							 'f0e2'
							, NOW() ),
							(
							 '13', 
						     'fa fa-rotate-right', 
							 'rotate-right', 
							 'f01e'
							, NOW() ),
							(
							 '13', 
						     'fa fa-save', 
							 'save', 
							 'f0c7'
							, NOW() ),
							(
							 '13', 
						     'fa fa-scissors', 
							 'scissors', 
							 'f0c4'
							, NOW() ),
							(
							 '13', 
						     'fa fa-strikethrough', 
							 'strikethrough', 
							 'f0cc'
							, NOW() ),
							(
							 '13', 
						     'fa fa-subscript', 
							 'subscript', 
							 'f12c'
							, NOW() ),
							(
							 '13', 
						     'fa fa-superscript', 
							 'superscript', 
							 'f12b'
							, NOW() ),
							(
							 '13', 
						     'fa fa-table', 
							 'table', 
							 'f0ce'
							, NOW() ),
							(
							 '13', 
						     'fa fa-text-height', 
							 'text-height', 
							 'f034'
							, NOW() ),
							(
							 '13', 
						     'fa fa-text-width', 
							 'text-width', 
							 'f035'
							, NOW() ),
							(
							 '13', 
						     'fa fa-th', 
							 'th', 
							 'f00a'
							, NOW() ),
							(
							 '13', 
						     'fa fa-th-large', 
							 'th-large', 
							 'f009'
							, NOW() ),
							(
							 '13', 
						     'fa fa-th-list', 
							 'th-list', 
							 'f00b'
							, NOW() ),
							(
							 '13', 
						     'fa fa-underline', 
							 'underline', 
							 'f0cd'
							, NOW() ),
							(
							 '13', 
						     'fa fa-undo', 
							 'undo', 
							 'f0e2'
							, NOW() ),
							(
							 '13', 
						     'fa fa-undo', 
							 'undo', 
							 'f0e2'
							, NOW() ),
							(
							 '13', 
						     'fa fa-unlink', 
							 'unlink', 
							 'f127'
							, NOW() ),
							(
							 '14', 
						     'fa fa-angle-double-down', 
							 'angle-double-down', 
							 'f103'
							, NOW() ),
							(
							 '14', 
						     'fa fa-angle-double-left', 
							 'angle-double-left', 
							 'f100'
							, NOW() ),
							(
							 '14', 
						     'fa fa-angle-double-right', 
							 'angle-double-right', 
							 'f101'
							, NOW() ),
							(
							 '14', 
						     'fa fa-angle-double-up', 
							 'angle-double-up', 
							 'f102'
							, NOW() ),
							(
							 '14', 
						     'fa fa-angle-down', 
							 'angle-down', 
							 'f107'
							, NOW() ),
							(
							 '14', 
						     'fa fa-angle-left', 
							 'angle-left', 
							 'f104'
							, NOW() ),
							(
							 '14', 
						     'fa fa-angle-right', 
							 'angle-right', 
							 'f105'
							, NOW() ),
							(
							 '14', 
						     'fa fa-angle-up', 
							 'angle-up', 
							 'f106'
							, NOW() ),
							(
							 '14', 
						     'fa fa-arrow-circle-down', 
							 'arrow-circle-down', 
							 'f0ab'
							, NOW() ),
							(
							 '14', 
						     'fa fa-arrow-circle-left', 
							 'arrow-circle-left', 
							 'f0a8'
							, NOW() ),
							(
							 '14', 
						     'fa fa-arrow-circle-o-down', 
							 'arrow-circle-o-down', 
							 'f01a'
							, NOW() ),
							(
							 '14', 
						     'fa fa-arrow-circle-o-left', 
							 'arrow-circle-o-left', 
							 'f190'
							, NOW() ),
							(
							 '14', 
						     'fa fa-arrow-circle-o-right', 
							 'arrow-circle-o-right', 
							 'f18e'
							, NOW() ),
							(
							 '14', 
						     'fa fa-arrow-circle-o-up', 
							 'arrow-circle-o-up', 
							 'f01b'
							, NOW() ),
							(
							 '14', 
						     'fa fa-arrow-circle-right', 
							 'arrow-circle-right', 
							 'f0a9'
							, NOW() ),
							(
							 '14', 
						     'fa fa-arrow-circle-up', 
							 'arrow-circle-up', 
							 'f0aa'
							, NOW() ),
							(
							 '14', 
						     'fa fa-arrow-down', 
							 'arrow-down', 
							 'f063'
							, NOW() ),
							(
							 '14', 
						     'fa fa-arrow-left', 
							 'arrow-left', 
							 'f060'
							, NOW() ),
							(
							 '14', 
						     'fa fa-arrow-right', 
							 'arrow-right', 
							 'f061'
							, NOW() ),
							(
							 '14', 
						     'fa fa-arrow-up', 
							 'arrow-up', 
							 'f062'
							, NOW() ),
							(
							 '14', 
						     'fa fa-arrows', 
							 'arrows', 
							 'f047'
							, NOW() ),
							(
							 '14', 
						     'fa fa-arrows-alt', 
							 'arrows-alt', 
							 'f0b2'
							, NOW() ),
							(
							 '14', 
						     'fa fa-arrows-h', 
							 'arrows-h', 
							 'f07e'
							, NOW() ),
							(
							 '14', 
						     'fa fa-arrows-v', 
							 'arrows-v', 
							 'f07d'
							, NOW() ),
							(
							 '14', 
						     'fa fa-caret-down', 
							 'caret-down', 
							 'f0d7'
							, NOW() ),
							(
							 '14', 
						     'fa fa-caret-left', 
							 'caret-left', 
							 'f0d9'
							, NOW() ),
							(
							 '14', 
						     'fa fa-caret-right', 
							 'caret-right', 
							 'f0da'
							, NOW() ),
							(
							 '14', 
						     'fa fa-caret-square-o-down', 
							 'caret-square-o-down', 
							 'f150'
							, NOW() ),
							(
							 '14', 
						     'fa fa-caret-square-o-left', 
							 'caret-square-o-left', 
							 'f191'
							, NOW() ),
							(
							 '14', 
						     'fa fa-caret-square-o-right', 
							 'caret-square-o-right', 
							 'f152'
							, NOW() ),
							(
							 '14', 
						     'fa fa-caret-square-o-up', 
							 'caret-square-o-up', 
							 'f151'
							, NOW() ),
							(
							 '14', 
						     'fa fa-caret-up', 
							 'caret-up', 
							 'f0d8'
							, NOW() ),
							(
							 '14', 
						     'fa fa-chevron-circle-down', 
							 'chevron-circle-down', 
							 'f13a'
							, NOW() ),
							(
							 '14', 
						     'fa fa-chevron-circle-left', 
							 'chevron-circle-left', 
							 'f137'
							, NOW() ),
							(
							 '14', 
						     'fa fa-chevron-circle-right', 
							 'chevron-circle-right', 
							 'f138'
							, NOW() ),
							(
							 '14', 
						     'fa fa-chevron-circle-up', 
							 'chevron-circle-up', 
							 'f139'
							, NOW() ),
							(
							 '14', 
						     'fa fa-chevron-down', 
							 'chevron-down', 
							 'f078'
							, NOW() ),
							(
							 '14', 
						     'fa fa-chevron-left', 
							 'chevron-left', 
							 'f053'
							, NOW() ),
							(
							 '14', 
						     'fa fa-chevron-right', 
							 'chevron-right', 
							 'f054'
							, NOW() ),
							(
							 '14', 
						     'fa fa-chevron-up', 
							 'chevron-up', 
							 'f077'
							, NOW() ),
							(
							 '14', 
						     'fa fa-exchange', 
							 'exchange', 
							 'f0ec'
							, NOW() ),
							(
							 '14', 
						     'fa fa-hand-o-down', 
							 'hand-o-down', 
							 'f0a7'
							, NOW() ),
							(
							 '14', 
						     'fa fa-hand-o-left', 
							 'hand-o-left', 
							 'f0a5'
							, NOW() ),
							(
							 '14', 
						     'fa fa-hand-o-right', 
							 'hand-o-right', 
							 'f0a4'
							, NOW() ),
							(
							 '14', 
						     'fa fa-hand-o-up', 
							 'hand-o-up', 
							 'f0a6'
							, NOW() ),
							(
							 '14', 
						     'fa fa-long-arrow-down', 
							 'long-arrow-down', 
							 'f175'
							, NOW() ),
							(
							 '14', 
						     'fa fa-long-arrow-left', 
							 'long-arrow-left', 
							 'f177'
							, NOW() ),  
							(
							 '14', 
						     'fa fa-long-arrow-right', 
							 'long-arrow-right', 
							 'f178'
							, NOW() ), 
							(
							 '14', 
						     'fa fa-long-arrow-up', 
							 'long-arrow-up', 
							 'f176'
							, NOW() ), 
							(
							 '14', 
						     'fa fa-toggle-down', 
							 'toggle-down', 
							 'f150'
							, NOW() ), 
							(
							 '14', 
						     'fa fa-toggle-left', 
							 'toggle-left', 
							 'f191'
							, NOW() ),
							(
							 '14', 
						     'fa fa-toggle-right', 
							 'toggle-right', 
							 'f152'
							, NOW() ), 
							(
							 '14', 
						     'fa fa-toggle-up', 
							 'toggle-up', 
							 'f151'
							, NOW() ),
							(
							 '15', 
						     'fa fa-arrows-alt', 
							 'arrows-alt', 
							 'f0b2'
							, NOW() ),
							(
							 '15', 
						     'fa fa-backward', 
							 'backward', 
							 'f04a'
							, NOW() ), 
							(
							 '15', 
						     'fa fa-compress', 
							 'compress', 
							 'f04a'
							, NOW() ), 
							(
							 '15', 
						     'fa fa-eject', 
							 'eject', 
							 'f052'
							, NOW() ), 
							(
							 '15', 
						     'fa fa-expand', 
							 'expand', 
							 'f065'
							, NOW() ),
							(
							 '15', 
						     'fa fa-fast-backward', 
							 'fast-backward', 
							 'f049'
							, NOW() ),
							(
							 '15', 
						     'fa fa-fast-forward', 
							 'fast-forward', 
							 'f050'
							, NOW() ),
							(
							 '15', 
						     'fa fa-forward', 
							 'forward', 
							 'f04e'
							, NOW() ),
							(
							 '15', 
						     'fa fa-pause', 
							 'pause', 
							 'f04c'
							, NOW() ),
							(
							 '15', 
						     'fa fa-pause-circle', 
							 'pause-circle', 
							 'f28b'
							, NOW() ),
							(
							 '15', 
						     'fa fa-pause-circle-o', 
							 'pause-circle-o', 
							 'f28c'
							, NOW() ),  
							(
							 '15', 
						     'fa fa-play', 
							 'play', 
							 'f04b'
							, NOW() ),
							(
							 '15', 
						     'fa fa-play-circle', 
							 'play-circle', 
							 'f144'
							, NOW() ), 
							(
							 '15', 
						     'fa fa-play-circle-o', 
							 'play-circle-o', 
							 'f01d'
							, NOW() ), 
							(
							 '15', 
						     'fa fa-random', 
							 'random', 
							 'f074'
							, NOW() ),
							(
							 '15', 
						     'fa fa-step-backward', 
							 'step-backward', 
							 'f048'
							, NOW() ),
							(
							 '15', 
						     'fa fa-step-forward', 
							 'step-forward', 
							 'f051'
							, NOW() ),
							(
							 '15', 
						     'fa fa-stop', 
							 'stop', 
							 'f04d'
							, NOW() ), 
							(
							 '15', 
						     'fa fa-stop-circle', 
							 'stop-circle', 
							 'f28d'
							, NOW() ),
							(
							 '15', 
						     'fa fa-stop-circle-o', 
							 'stop-circle-o', 
							 'f28e'
							, NOW() ),
							(
							 '15', 
						     'fa fa-youtube-play', 
							 'youtube-play', 
							 'f16a'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-500px', 
							 '500px', 
							 'f26e'
							, NOW() ),
							(
							 '16', 
						     'fa fa-adn', 
							 'adn', 
							 'f170'
							, NOW() ),
							(
							 '16', 
						     'fa fa-amazon', 
							 'amazon', 
							 'f270'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-android', 
							 'android', 
							 'f17b'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-angellist', 
							 'angellist', 
							 'f209'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-apple', 
							 'apple', 
							 'f179'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-bandcamp', 
							 'bandcamp', 
							 'f2d5'
							, NOW() ),
							(
							 '16', 
						     'fa fa-behance', 
							 'behance', 
							 'f1b4'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-behance-square', 
							 'behance-square', 
							 'f1b5'
							, NOW() ),
							(
							 '16', 
						     'fa fa-bitbucket', 
							 'bitbucket', 
							 'f171'
							, NOW() ),
							(
							 '16', 
						     'fa fa-bitbucket-square', 
							 'bitbucket-square', 
							 'f172'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-bitcoin', 
							 'bitcoin', 
							 'f15a'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-black-tie', 
							 'black-tie', 
							 'f27e'
							, NOW() ),
							(
							 '16', 
						     'fa fa-bluetooth', 
							 'bluetooth', 
							 'f293'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-bluetooth-b', 
							 'bluetooth-b', 
							 'f294'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-btc', 
							 'btc', 
							 'f15a'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-buysellads', 
							 'buysellads', 
							 'f20d'
							, NOW() ),
							(
							 '16', 
						     'fa fa-cc-amex', 
							 'cc-amex', 
							 'f1f3'
							, NOW() ),
							(
							 '16', 
						     'fa fa-cc-diners-club', 
							 'cc-diners-club', 
							 'f24c'
							, NOW() ),
							(
							 '16', 
						     'fa fa-cc-discover', 
							 'cc-discover', 
							 'f1f2'
							, NOW() ),
							(
							 '16', 
						     'fa fa-cc-jcb', 
							 'cc-jcb', 
							 'f24b'
							, NOW() ),
							(
							 '16', 
						     'fa fa-cc-mastercard', 
							 'cc-mastercard', 
							 'f1f1'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-cc-paypal', 
							 'cc-paypal', 
							 'f1f4'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-cc-stripe', 
							 'cc-stripe', 
							 'f1f5'
							, NOW() ),   
							(
							 '16', 
						     'fa fa-cc-visa', 
							 'cc-visa', 
							 'f1f0'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-chrome', 
							 'chrome', 
							 'f268'
							, NOW() ),
							(
							 '16', 
						     'fa fa-codepen', 
							 'codepen', 
							 'f1cb'
							, NOW() ),  
							(
							 '16', 
						     'fa fa-codiepie', 
							 'codiepie', 
							 'f284'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-connectdevelop', 
							 'connectdevelop', 
							 'f20e'
							, NOW() ),
							(
							 '16', 
						     'fa fa-contao', 
							 'contao', 
							 'f26d'
							, NOW() ),
							(
							 '16', 
						     'fa fa-css3', 
							 'css3', 
							 'f13c'
							, NOW() ),
							(
							 '16', 
						     'ffa fa-dashcube', 
							 'dashcube', 
							 'f210'
							, NOW() ),
							(
							 '16', 
						     'fa fa-delicious', 
							 'delicious', 
							 'f1a5'
							, NOW() ),
							(
							 '16', 
						     'fa fa-deviantart', 
							 'deviantart', 
							 'f1bd'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-digg', 
							 'digg', 
							 'f1a6'
							, NOW() ),  
							(
							 '16', 
						     'fa fa-dribbble', 
							 'dribbble', 
							 'f17d'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-dropbox', 
							 'dropbox', 
							 'f16b'
							, NOW() ),
							(
							 '16', 
						     'fa fa-drupal', 
							 'drupal', 
							 'f1a9'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-edge', 
							 'edge', 
							 'f282'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-eercast', 
							 'eercast', 
							 'f2da'
							, NOW() ),
							(
							 '16', 
						     'fa fa-empire', 
							 'empire', 
							 'f1d1'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-envira', 
							 'envira', 
							 'f299'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-etsy', 
							 'etsy', 
							 'f2d7'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-expeditedssl', 
							 'expeditedssl', 
							 'f2d7'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-expeditedssl', 
							 'expeditedssl', 
							 'f23e'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-fa', 
							 'fa', 
							 'f2b4'
							, NOW() ),
							(
							 '16', 
						     'fa fa-facebook', 
							 'facebook', 
							 'f09a'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-facebook-f', 
							 'facebook-f', 
							 'f09a'
							, NOW() ),  
							(
							 '16', 
						     'fa fa-facebook-official', 
							 'facebook-official', 
							 'f230'
							, NOW() ),
							(
							 '16', 
						     'fa fa-facebook-square', 
							 'facebook-square', 
							 'f082'
							, NOW() ),
							(
							 '16', 
						     'fa fa-firefox', 
							 'firefox', 
							 'f269'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-first-order', 
							 'first-order', 
							 'f2b0'
							, NOW() ),   
							(
							 '16', 
						     'fa fa-flickr', 
							 'flickr', 
							 'f16e'
							, NOW() ),
							(
							 '16', 
						     'fa fa-font-awesome', 
							 'font-awesome', 
							 'f2b4'
							, NOW() ),
							(
							 '16', 
						     'fa fa-fonticons', 
							 'fonticons', 
							 'f280'
							, NOW() ),
							(
							 '16', 
						     'fa fa-fort-awesome', 
							 'fort-awesome', 
							 'f286'
							, NOW() ),
							(
							 '16', 
						     'fa fa-forumbee', 
							 'forumbee', 
							 'f211'
							, NOW() ),
							(
							 '16', 
						     'fa fa-foursquare', 
							 'foursquare', 
							 'f180'
							, NOW() ),
							(
							 '16', 
						     'fa fa-free-code-camp', 
							 'free-code-camp', 
							 'f2c5'
							, NOW() ),
							(
							 '16', 
						     'fa fa-ge', 
							 'ge', 
							 'f1d1'
							, NOW() ),
							(
							 '16', 
						     'fa fa-get-pocket', 
							 'get-pocket', 
							 'f265'
							, NOW() ),
							(
							 '16', 
						     'fa fa-gg', 
							 'gg', 
							 'f260'
							, NOW() ),
							(
							 '16', 
						     'fa fa-gg-circle', 
							 'gg-circle', 
							 'f261'
							, NOW() ),
							(
							 '16', 
						     'fa fa-git', 
							 'git', 
							 'f1d3'
							, NOW() ),
							(
							 '16', 
						     'fa fa-git-square', 
							 'git-square', 
							 'f1d2'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-github', 
							 'github', 
							 'f09b'
							, NOW() ),
							(
							 '16', 
						     'fa fa-github-alt', 
							 'github-alt', 
							 'f09b'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-github-square', 
							 'github-square', 
							 'f092'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-gitlab', 
							 'gitlab', 
							 'f296'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-gittip', 
							 'gittip', 
							 'f184'
							, NOW() ),
							(
							 '16', 
						     'fa fa-glide', 
							 'glide', 
							 'f2a5'
							, NOW() ),
							(
							 '16', 
						     'fa fa-glide-g', 
							 'glide-g', 
							 'f2a6'
							, NOW() ),
							(
							 '16', 
						     'fa fa-google', 
							 'google', 
							 'f1a0'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-google-plus', 
							 'google-plus', 
							 'f0d5'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-google-plus-circle', 
							 'google-plus-circle', 
							 'f2b3'
							, NOW() ),
							(
							 '16', 
						     'fa fa-google-plus-official', 
							 'google-plus-official', 
							 'f2b3'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-google-plus-square', 
							 'google-plus-square', 
							 'f0d4'
							, NOW() ),
							(
							 '16', 
						     'fa fa-gratipay', 
							 'gratipay', 
							 'f184'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-grav', 
							 'grav', 
							 'f2d6'
							, NOW() ),
							(
							 '16', 
						     'fa fa-hacker-news', 
							 'hacker-news', 
							 'f1d4'
							, NOW() ),
							(
							 '16', 
						     'fa fa-houzz', 
							 'houzz', 
							 'f27c'
							, NOW() ),
							(
							 '16', 
						     'fa fa-html5', 
							 'html5', 
							 'f13b'
							, NOW() ),
							(
							 '16', 
						     'fa fa-imdb', 
							 'imdb', 
							 'f2d8'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-instagram', 
							 'instagram', 
							 'f16d'
							, NOW() ),
							(
							 '16', 
						     'fa fa-internet-explorer', 
							 'internet-explorer', 
							 'f26b'
							, NOW() ),
							(
							 '16', 
						     'fa fa-ioxhost', 
							 'ioxhost', 
							 'f208'
							, NOW() ),
							(
							 '16', 
						     'fa fa-joomla', 
							 'joomla', 
							 'f1aa'
							, NOW() ),
							(
							 '16', 
						     'fa fa-jsfiddle', 
							 'jsfiddle', 
							 'f1cc'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-lastfm', 
							 'lastfm', 
							 'f202'
							, NOW() ),
							(
							 '16', 
						     'fa fa-lastfm-square', 
							 'lastfm-square', 
							 'f203'
							, NOW() ),
							(
							 '16', 
						     'fa fa-leanpub', 
							 'leanpub', 
							 'f212'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-linkedin', 
							 'linkedin', 
							 'f0e1'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-linkedin-square', 
							 'linkedin-square', 
							 'f08c'
							, NOW() ),
							(
							 '16', 
						     'fa fa-linode', 
							 'linode', 
							 'f2b8'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-linux', 
							 'linux', 
							 'f17c'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-maxcdn', 
							 'maxcdn', 
							 'f136'
							, NOW() ),
							(
							 '16', 
						     'fa fa-meanpath', 
							 'meanpath', 
							 'f20c'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-medium', 
							 'maxcdn', 
							 'f23a'
							, NOW() ), 

							(
							 '16', 
						     'fa fa-meetup', 
							 'meetup', 
							 'f23a'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-mixcloud', 
							 'mixcloud', 
							 'f2e0'
							, NOW() ),
							(
							 '16', 
						     'fa fa-modx', 
							 'modx', 
							 'f2e0'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-odnoklassniki', 
							 'odnoklassniki', 
							 'f263'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-odnoklassniki-square', 
							 'odnoklassniki-square', 
							 'f264'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-opencart', 
							 'opencart', 
							 'f23d'
							, NOW() ),
							(
							 '16', 
						     'fa fa-openid', 
							 'openid', 
							 'f19b'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-opera', 
							 'opera', 
							 'f26a'
							, NOW() ),
							(
							 '16', 
						     'fa fa-optin-monster', 
							 'optin-monster', 
							 'f23c'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-pagelines', 
							 'pagelines', 
							 'f18c'
							, NOW() ),
							(
							 '16', 
						     'fa fa-paypal', 
							 'paypal', 
							 'f1ed'
							, NOW() ),
							(
							 '16', 
						     'fa fa-pied-piper', 
							 'pied-piper', 
							 'f2ae'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-pied-piper-alt', 
							 'pied-piper-alt', 
							 'f1a8'
							, NOW() ),
							(
							 '16', 
						     'fa fa-pied-piper-pp', 
							 'pied-piper-pp', 
							 'f1a7'
							, NOW() ),
							(
							 '16', 
						     'fa fa-pinterest', 
							 'pinterest', 
							 'f0d2'
							, NOW() ),
							(
							 '16', 
						     'fa fa-pinterest-p', 
							 'pinterest-p', 
							 'f231'
							, NOW() ),
							(
							 '16', 
						     'fa fa-pinterest-square', 
							 'pinterest-square', 
							 'f0d3'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-product-hunt', 
							 'product-hunt', 
							 'f288'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-qq', 
							 'qq', 
							 'f1d6'
							, NOW() ),
							(
							 '16', 
						     'fa fa-quora', 
							 'quora', 
							 'f2c4'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-ra', 
							 'ra', 
							 'f1d0'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-ravelry', 
							 'ravelry', 
							 'f2d9'
							, NOW() ),
							(
							 '16', 
						     'fa fa-rebel', 
							 'rebel', 
							 'f1d0'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-reddit', 
							 'reddit', 
							 'f1a1'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-reddit-alien', 
							 'reddit-alien', 
							 'f281'
							, NOW() ),
							(
							 '16', 
						     'fa fa-reddit-square', 
							 'reddit-square', 
							 'f1a2'
							, NOW() ),
							(
							 '16', 
						     'fa fa-renren', 
							 'renren', 
							 'f18b'
							, NOW() ),
							(
							 '16', 
						     'fa fa-resistance', 
							 'resistance', 
							 'f1d0'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-safari', 
							 'safari', 
							 'f267'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-scribd', 
							 'scribd', 
							 'f28a'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-sellsy', 
							 'sellsy', 
							 'f213'
							, NOW() ),
							(
							 '16', 
						     'fa fa-share-alt', 
							 'share-alt', 
							 'f1e0'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-share-alt-square', 
							 'share-alt-square', 
							 'f1e1'
							, NOW() ),
							(
							 '16', 
						     'fa fa-shirtsinbulk', 
							 'shirtsinbulk', 
							 'f214'
							, NOW() ),
							(
							 '16', 
						     'fa fa-simplybuilt', 
							 'simplybuilt', 
							 'f215'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-skyatlas', 
							 'skyatlas', 
							 'f216'
							, NOW() ),
							(
							 '16', 
						     'fa fa-skype', 
							 'skype', 
							 'f17e'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-slack', 
							 'slack', 
							 'f198'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-slideshare', 
							 'slideshare', 
							 'f1e7'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-snapchat', 
							 'snapchat', 
							 'f2ab'
							, NOW() ),
							(
							 '16', 
						     'fa fa-snapchat-ghost', 
							 'snapchat-ghost', 
							 'f2ac'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-snapchat-square', 
							 'snapchat-square', 
							 'f2ad'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-soundcloud', 
							 'soundcloud', 
							 'f1be'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-spotify', 
							 'spotify', 
							 'f1bc'
							, NOW() ),
							(
							 '16', 
						     'fa fa-stack-exchange', 
							 'stack-exchange', 
							 'f18d'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-stack-overflow', 
							 'stack-overflow', 
							 'f16c'
							, NOW() ),
							(
							 '16', 
						     'fa fa-steam', 
							 'steam', 
							 'f1b6'
							, NOW() ),
							(
							 '16', 
						     'fa fa-steam-square', 
							 'steam-square', 
							 'f1b7'
							, NOW() ),
							(
							 '16', 
						     'fa fa-stumbleupon', 
							 'stumbleupon', 
							 'f1a4'
							, NOW() ),
							(
							 '16', 
						     'fa fa-stumbleupon-circle', 
							 'stumbleupon-circle', 
							 'f1a3'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-superpowers', 
							 'superpowers', 
							 'f2dd'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-telegram', 
							 'telegram', 
							 'f2c6'
							, NOW() ),
							(
							 '16', 
						     'fa fa-tencent-weibo', 
							 'tencent-weibo', 
							 'f1d5'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-themeisle', 
							 'themeisle', 
							 'f2b2'
							, NOW() ),
							(
							 '16', 
						     'fa fa-trello', 
							 'trello', 
							 'f181'
							, NOW() ),
							(
							 '16', 
						     'fa fa-tripadvisor', 
							 'tripadvisor', 
							 'f262'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-tumblr', 
							 'tumblr', 
							 'f173'
							, NOW() ),
							(
							 '16', 
						     'fa fa-tumblr-square', 
							 'tumblr-square', 
							 'f173'
							, NOW() ),
							(
							 '16', 
						     'fa fa-twitch', 
							 'twitch', 
							 'f1e8'
							, NOW() ),
							(
							 '16', 
						     'fa fa-twitter', 
							 'twitter', 
							 'f099'
							, NOW() ),
							(
							 '16', 
						     'fa fa-twitter-square', 
							 'twitter-square', 
							 'f081'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-usb', 
							 'usb', 
							 'f287'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-viacoin', 
							 'viacoin', 
							 'f237'
							, NOW() ),
							(
							 '16', 
						     'fa fa-viadeo', 
							 'viadeo', 
							 'f2a9'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-viadeo-square', 
							 'viadeo-square', 
							 'f2aa'
							, NOW() ),
							(
							 '16', 
						     'fa fa-vimeo', 
							 'vimeo', 
							 'f27d'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-vimeo-square', 
							 'vimeo-square', 
							 'f194'
							, NOW() ),
							(
							 '16', 
						     'fa fa-vine', 
							 'vine', 
							 'f1ca'
							, NOW() ),
							(
							 '16', 
						     'fa fa-vk', 
							 'vk', 
							 'f189'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-wechat', 
							 'wechat', 
							 'f1d7'
							, NOW() ),
							(
							 '16', 
						     'fa fa-weibo', 
							 'weibo', 
							 'f18a'
							, NOW() ),
							(
							 '16', 
						     'fa fa-weixin', 
							 'weixin', 
							 'f1d7'
							, NOW() ), 
							(
							 '16', 
						     'fa fa-whatsapp', 
							 'whatsapp', 
							 'f232'
							, NOW() ),
							(
							 '16', 
						     'fa fa-wikipedia-w', 
							 'wikipedia-w', 
							 'f266'
							, NOW() ),
							(
							 '16', 
						     'fa fa-windows', 
							 'windows', 
							 'f17a'
							, NOW() ),
							(
							 '16', 
						     'fa fa-wordpress', 
							 'wordpress', 
							 'f19a'
							, NOW() ),
							(
							 '16', 
						     'fa fa-wpbeginner', 
							 'wpbeginner', 
							 'f297'
							, NOW() ),
							(
							 '16', 
						     'fa fa-wpexplorer', 
							 'wpexplorer', 
							 'f2de'
							, NOW() ),
							(
							 '16', 
						     'fa fa-wpforms', 
							 'wpforms', 
							 'f298'
							, NOW() ),

                            (
							 '16', 
						     'fa fa-xing', 
							 'xing', 
							 'f168'
							, NOW() ),
							 (
							 '16', 
						     'fa fa-xing-square', 
							 'xing-square', 
							 'f169'
							, NOW() ),
							 (
							 '16', 
						     'fa fa-y-combinator', 
							 'combinator', 
							 'f23b'
							, NOW() ),
							 (
							 '16', 
						     'fa fa-y-combinator-square', 
							 'y-combinator-square', 
							 'f1d4'
							, NOW() ),
							 (
							 '16', 
						     'fa fa-yahoo', 
							 'yahoo', 
							 'f19e'
							, NOW() ),
							 (
							 '16', 
						     'fa fa-yc', 
							 'yc', 
							 'f23b'
							, NOW() ),
							 (
							 '16', 
						     'fa fa-yc-square', 
							 'yc-square', 
							 'f1d4'
							, NOW() ),
							(
							 '16', 
						     'fa fa-yelp', 
							 'yelp', 
							 'f1e9'
							, NOW() ),
							(
							 '16', 
						     'fa fa-yoast', 
							 'yoast', 
							 'f2b1'
							, NOW() ),	
							(
							 '16', 
						     'fa fa-youtube', 
							 'youtube', 
							 'f167'
							, NOW() ),
							(
							 '16', 
						     'fa fa-youtube-play', 
							 'youtube-play', 
							 'f16a'
							, NOW() ),
							(
							 '16', 
						     'fa fa-youtube-square', 
							 'youtube-square', 
							 'f166'
							, NOW() ),
							(
							 '17', 
						     'fa fa-ambulance', 
							 'ambulance', 
							 'f0f9'
							, NOW() ),
							(
							 '17', 
						     'fa fa-h-square', 
							 'h-square', 
							 'f0fd'
							, NOW() ),
							(
							 '17', 
						     'fa fa-heart', 
							 'heart', 
							 'f004'
							, NOW() ),
							(
							 '17', 
						     'fa fa-heart-o', 
							 'heart-o', 
							 'f08a'
							, NOW() ),
							(
							 '17', 
						     'fa fa-heartbeat', 
							 'heartbeat', 
							 'f21e'
							, NOW() ),
							(
							 '17', 
						     'fa fa-hospital-o', 
							 'hospital-o', 
							 'f0f8'
							, NOW() ),
							(
							 '17', 
						     'fa fa-medkit', 
							 'medkit', 
							 'f0fa'
							, NOW() ),
							(
							 '17', 
						     'fa fa-plus-square', 
							 'plus-square', 
							 'f0fe'
							, NOW() ),
							(
							 '17', 
						     'fa fa-stethoscope', 
							 'stethoscope', 
							 'f0f1'
							, NOW() ),
							(
							 '17', 
						     'fa fa-user-md', 
							 'user-md', 
							 'f0f0'
							, NOW() ),
							(
							 '17', 
						     'fa fa-wheelchair', 
							 'wheelchair', 
							 'f193'
							, NOW() ),
							(
							 '17', 
						     'fa fa-wheelchair-alt', 
							 'wheelchair-alt', 
							 'f29b',
							  NOW()
							)";

				$this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_designer_icons (
											icon_category_id,
											icon_class,
											icon_name,
											icon_code,
											added_date
										)
										VALUES
											".$iconLib."");
									
		}
}