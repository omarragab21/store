<?php

class ControllerExtensionShippingApplyvalidation extends Controller {

    public function index() {

        $address = array();
        if (!isset($this->request->request['city'])) {
            $this->request->request['city'] = "";
        }
        if (!isset($this->request->post['post_code'])) {
            $this->request->post['post_code'] = "";
        }
        $address['city'] = $this->request->request['city'];
        $address['post_code'] = $this->request->post['post_code'];
        $address['country_code'] = $this->request->post['country_code'];
        $this->load->model('extension/shipping/apilocationvalidator');
        $result = $this->model_extension_shipping_apilocationvalidator->validateAddress($address);
        if (count($result) > 0 && $result != false) {
            echo json_encode($result);
            die();
        } else {
            echo json_encode(array());
        }
    }

}
