<?php

require_once DIR_SYSTEM . 'library/myfatoorah/PaymentMyfatoorahApiV2.php';

class ModelExtensionPaymentMyfatoorahPG extends Model {

    private $id   = 'myfatoorah_pg';
    private $path = 'extension/payment/';
    private $ocCode;

//---------------------------------------------------------------------------------------------------------------------------------------------------
    public function getMethod($address, $total) {

        $this->ocCode = (version_compare(VERSION, '3.0.0', '<')) ? $this->id : 'payment_' . $this->id;

        $this->language->load($this->path . $this->id);

        $methodData = [
            'code'       => $this->id,
            'title'      => $this->language->get('text_title'),
            'terms'      => '',
            'sort_order' => $this->config->get($this->ocCode . '_sort_order'),
        ];

        //if this is not the (admin @editOrder) view i.e. it is the (catalog @checkout) view
        if (!isset($this->session->data['api_id'])) {
            $types = $this->config->get($this->ocCode . '_payment_type');

            if ($types == 'multigateways') {
                return $this->getMethodData($methodData);
            }
        }

        return $methodData;
    }

//---------------------------------------------------------------------------------------------------------------------------------------------------
    public function getMethodData($methodData) {

        try {
            $apiKey      = $this->config->get($this->ocCode . '_apiKey');
            $isTest      = $this->config->get($this->ocCode . '_test') === '1' ? true : false;
            $countryMode = $this->config->get($this->ocCode . '_countryMode');

            $mfObj          = new PaymentMyfatoorahApiV2($apiKey, $countryMode, $isTest);
            $paymentMethods = $mfObj->getPaymentMethodsForDisplay();

            $formScript = '';
            if (!empty($paymentMethods['form'])) {
                $scriptType = ($this->config->get($this->ocCode . '_test') === '1') ? 'demo' : 'portal';
                $formScript = '<script src="https://' . $scriptType . '.myfatoorah.com/cardview/v1/session.js"></script>';
            }


            $count = count($paymentMethods['all']);
            if ($count == 0) {
                $methodData['error'] = $this->language->get('error_no_myfatoorah_payments');
            } else if ($count == 1) {
                $text                = ($this->language->get('code') == 'ar') ? $paymentMethods['all'][0]->PaymentMethodAr : $paymentMethods['all'][0]->PaymentMethodEn;
                $icon                = '<img style="width: 3.5rem; margin-inline-end: 0.313rem;" class="mf-pm-icon" src="' . $paymentMethods['all'][0]->ImageUrl . '" title="' . $text . '" alt="' . $text . '">';
                $methodData['title'] = $icon . $text . $formScript;
            } else {
                $methodData['title'] .= $formScript;
            }
        } catch (Exception $ex) {
            $methodData['error'] = $this->language->get($ex->getMessage());
        }

        return $methodData;
    }

//---------------------------------------------------------------------------------------------------------------------------------------------------
}
