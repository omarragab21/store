<?php

class ControllerExtensionShippingAramexcalculator extends Controller
{
    public function index()
    {
        $data = array();
        if (!isset($this->session->data['user_token'])) {
            $this->session->data['user_token'] = null;
        }
        $data['aramexcalculator'] = $this->config->get('shipping_aramex_aramexcalculator');
        $data['aramex_loader'] = "catalog/view/javascript/aramex/jqueryui/aramex_loader.gif";
        $data['preloader'] = "catalog/view/javascript/aramex/jqueryui/preloader.gif";
        $data['aramexcalculator_rate'] = $this->url->link('extension/shipping/aramexcalculator/rate','user_token=' . $this->session->data['user_token'],true);
        $data['aramex_searchautocities'] = $this->url->link('extension/shipping/searchautocities','user_token=' . $this->session->data['user_token'], true);
        $this->document->addScript('../../catalog/view/javascript/aramex/jqueryui/jquery-ui.js');
        $this->document->addStyle('../../catalog/view/javascript/aramex/jqueryui/jquery-ui.css');
        $this->load->model('localisation/country');
        $countries = array();
        foreach ($this->model_localisation_country->getCountries() as $country) {
            $countries[$country['country_id']] = $country['name'];
        }
        $data['countries'] = $countries;
        if ($this->customer->isLogged()) {
            $address_id = $this->customer->getAddressId();
            $this->load->model('account/address');
            $address = $this->model_account_address->getAddress($address_id);
        }
        $data['customer_city'] = isset($address['city']) ? $address['city'] : "";
        $data['customer_country'] = isset($address['country_id']) ? $address['country_id'] : "";
        $data['customer_postcode'] = isset($address['postcode']) ? $address['postcode'] : "";
        $data['product_id'] = $this->request->get['product_id'];
        $data['currency'] = $this->session->data['currency'];
        $data['type'] = ".aramex_popup";
        return ($this->load->view('extension/shipping/aramexcalculator', $data));
    }
    public function rate()
    {
        $this->load->model('extension/shipping/aramexcalculator');
        $this->model_extension_shipping_aramexcalculator->calculateRate();

    }
}
