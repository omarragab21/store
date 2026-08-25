<?php
class ModelExtensionShippingSMSA extends Model {

	public function addConsignment($data) {

		$this->db->query("INSERT INTO `" . DB_PREFIX . "smsa` SET order_id = '" . (int)$data['order_id'] . "', awb_number = '" . $this->db->escape($data['awb_number']) . "', reference_number = '" . $this->db->escape($data['reference_number']) . "', pickup_date = '" . $this->db->escape($data['pickup_date']) . "', shipment_label = '" . $this->db->escape($data['shipment_label']) . "', status = '" . $this->db->escape($data['status']) . "', date_added = NOW()");
	}

	public function editConsignment($consignment_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "smsa` SET order_id = '" . (int)$data['order_id'] . "', awb_number = '" . $this->db->escape($data['awb_number']) . "', reference_number = '" . $this->db->escape($data['reference_number']) . "', pickup_date = '" . $this->db->escape($data['pickup_date']) . "', shipment_label = '" . $this->db->escape($data['shipment_label']) . "', status = '" . $this->db->escape($data['status']) . "', date_added = NOW() WHERE consignment_id = '" . (int)$consignment_id . "'");
	}

	public function deleteConsignment($consignment_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "return` WHERE return_id = '" . (int)$return_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "return_history WHERE return_id = '" . (int)$return_id . "'");
	}

	public function getConsignment($order_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "smsa` WHERE order_id = '" . (int)$order_id . "' ORDER BY date_added DESC ");

		return $query->rows;
	}
}