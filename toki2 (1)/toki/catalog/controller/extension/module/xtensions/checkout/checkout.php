<?php
class ControllerExtensionModuleXtensionsCheckoutCheckout extends Controller {
	public function country() {
		$json = array();

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']		
			);
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	public function customfield() {
		$json = array();

		// Customer Group
		if (isset($this->request->get['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->get['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->get['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$custom_fields = $this->xtensions_checkout->getXtensionsModel($this->config->get('xtensions_custom_fields_model_path'))->getCustomFields($customer_group_id);

		foreach ($custom_fields as $custom_field) {
			$json[] = array(
				'custom_field_id' => $custom_field['custom_field_id'],
				'required'        => $custom_field['required']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function step(){
		if(isset($this->request->get['step'])){
			$this->session->data['xtensions_checkout_step'] = $this->request->get['step'];
		}
		$this->xtensions_checkout->addEventActivity(array('entered_step_'.$this->session->data['xtensions_checkout_step']));
		$this->xtensions_checkout->addActivity(array('step'));
	}
	
	public function event(){
		if(isset($this->request->get['code'])){
			switch ($this->request->get['code']){
				case 'address_same':
					if(isset($this->request->post['xshipping_address_check'])){
						$this->xtensions_checkout->addEventActivity(array('address_same'));
					}else{
						$this->xtensions_checkout->addEventActivity(array('address_different'));
					}
				break;
				case 'address_changed':
					$this->xtensions_checkout->addEventActivity(array('address_changed'=>array('address_id'=>$this->request->post['address_id'])));
					break;
				case 'saddress_changed':
					$this->xtensions_checkout->addEventActivity(array('saddress_changed'=>array('saddress_id'=>$this->request->post['saddress_id'])));	
				break;
			}
		}
		
		
	}
}
?>