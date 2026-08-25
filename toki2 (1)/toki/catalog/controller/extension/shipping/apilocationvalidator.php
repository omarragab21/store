<?php
class ControllerExtensionShippingApilocationvalidator extends Controller {
	public function index() {
            $data = array();
            if(!isset($this->session->data['user_token'])){
                $this->session->data['user_token'] = null;
            }

            $data['aramex_loader'] = "catalog/view/javascript/aramex/jqueryui/aramex_loader.gif";
            $data['url_autosities'] = $this->url->link('extension/shipping/searchautocities','user_token=' . $this->session->data['user_token'], true);
            $data['url_applyvalidation'] = $this->url->link('extension/shipping/applyvalidation','user_token=' . $this->session->data['user_token'], true);
            $data['type'] = "#collapse-payment-address";
            $data['button'] = "#button-register";
            $data['aramex_allow'] = $this->config->get('shipping_aramex_api_location_validator');
			return($this->load->view('extension/shipping/apilocationvalidator', $data));
	}
}
