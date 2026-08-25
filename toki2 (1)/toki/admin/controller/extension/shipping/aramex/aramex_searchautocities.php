<?php

class ControllerExtensionShippingAramexAramexSearchautocities extends Controller {

    public function index() {
        $countryCode = $this->request->get['country_code'];
        $term = $this->request->get['term'];
        $this->load->model('extension/shipping/apilocationvalidator');
        $cities = $this->model_extension_shipping_apilocationvalidator->fetchCities($countryCode, $term);
        if (count($cities) > 0 && $cities != false) {
            if(is_array($cities)){
                $cities = array_unique($cities);
            }else{
                $cities_temp = $cities;
                $cities = array();
                $cities[] = $cities_temp;
            }
            $sortCities = array();
            foreach ($cities as $v) {
                $sortCities[] = ucwords(strtolower($v));
            }
            asort($sortCities, SORT_STRING);
            $to_return = [];
            foreach ($sortCities as $val) {
                $to_return[] = $val;
            }
            echo json_encode($to_return);
            die();
        }
        else {
            echo json_encode(array());
        }
    }

}
