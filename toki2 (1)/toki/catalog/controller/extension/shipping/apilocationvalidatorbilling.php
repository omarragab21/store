<?php
class ControllerExtensionShippingApilocationvalidatorbilling extends Controller {
	public function index() {

            $data = array();
            if(!isset($this->session->data['user_token'])){
                $this->session->data['user_token'] = null;
            }

            $data['aramex_loader'] = "catalog/view/javascript/aramex/jqueryui/aramex_loader.gif";
            $data['url_autosities'] = $this->url->link('extension/shipping/searchautocities','user_token=' . $this->session->data['user_token'], true);
            $data['url_applyvalidation'] = $this->url->link('extension/shipping/applyvalidation','token=' . $this->session->data['user_token'], true);
            $data['type'] = "#collapse-payment-address";
            $data['button'] = "#button-payment-address";
				// CODE HERE IF LOWER
				return($this->load->view('extension/shipping/apilocationvalidator', $data));

	}
}
