<?php
class ModelExtensionPaymentTabbyBase extends Model {
    const SUPPORTED_COUNTRIES = ['SA', 'AE', 'KW', 'BH'];
    var $code = 'base';

	public function getMethod($address, $total) {
		$this->load->language('extension/payment/' . $this->code);

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_' . $this->code . '_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if ($this->config->get('payment_'.$this->code.'_total') > 0 && $this->config->get('payment_'.$this->code.'_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('payment_'.$this->code.'_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}
        // country processing
        $countries = $this->config->get('module_tabby_countries');
        if (empty($countries)) $countries = self::SUPPORTED_COUNTRIES;
        if (!in_array($address['iso_code_2'], $countries)) $status = false;

		$method_data = array();

		if ($status) {
            // get title and see if there is translation for it
            $title = $this->config->get('payment_'.$this->code.'_title');
            if (empty($title)) $title = 'text_' . $this->code;

            // fix for versions pre 2.1.0.0_rc1
            if (method_exists($this->language, 'all')) {
                $translations = $this->language->all();
                if (array_key_exists($title, $translations)) $title = $translations[$title];
            }
            
			$method_data = array(
				'code'       => $this->code,
				'title'      => $title,
				'terms'      => $this->config->get('payment_'.$this->code.'_terms'),
				'sort_order' => $this->config->get('payment_'.$this->code.'_sort_order')
			);
		}
		return $method_data;
	}

}
