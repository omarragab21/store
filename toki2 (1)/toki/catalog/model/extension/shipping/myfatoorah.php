<?php

require_once DIR_SYSTEM . 'library/myfatoorah/ShippingMyfatoorahApiV2.php';

class ModelExtensionShippingMyfatoorah extends Model {

//---------------------------------------------------------------------------------------------------------------------------------------------------
    function getQuote($address) {
        $id           = 'myfatoorah';
        $this->load->language('extension/shipping/' . $id);
        $this->logger = new Log('myfatoorah_shipping.log');

        $code        = version_compare(VERSION, '3.0.0', '<') ? $id : 'shipping_' . $id;
        $paymentCode = version_compare(VERSION, '3.0.0', '<') ? 'myfatoorah_pg' : 'payment_myfatoorah_pg';

        $shipping_methods = $this->config->get($code . '_shipping');
        if (!$shipping_methods) {
            return [];
        }

        $isLog = $this->config->get($code . '_debug') === '1' ? true : false;

        $apiKey      = $this->config->get($paymentCode . '_apiKey');
        $isTest      = $this->config->get($paymentCode . '_test') === '1' ? true : false;
        $countryMode = $this->config->get($paymentCode . '_countryMode');

        $mfObj = ($isLog) ? new ShippingMyfatoorahApiV2($apiKey, $countryMode, $isTest, $this->logger, 'write') : new ShippingMyfatoorahApiV2($apiKey, $countryMode, $isTest);

        $method_data = [
            'code'       => $id,
            'title'      => $this->language->get('text_description'),
            'quote'      => [],
            'sort_order' => $this->config->get($code . '_sort_order'),
            'error'      => false,
        ];

        if ($this->currency->getValue($this->config->get('config_currency')) != 1) {
            $method_data['error'] = $this->language->get('error_defult_currency_rate');
            return $method_data;
        }

        $shipping = ['1' => 'dhl', '2' => 'aramex'];

        try {
            $currencyRate = $mfObj->getCurrencyRate($this->session->data['currency']);

            foreach ($shipping_methods as $key) {
                $invoiceItemsArr = $this->getShippingItems($mfObj);
                $json            = $mfObj->calculateShippingCharge($this->getShippingData($key, $invoiceItemsArr, $address));

                $realVal        = floor($json->Data->Fees * 1000) / 1000;
                $shippingAmount = $realVal * $currencyRate;

                if ($shippingAmount) {
                    $value = $shipping[$key];

                    $method_data['quote'][$value] = [
                        'code'         => $id . '.' . $value,
                        'title'        => $this->language->get($value),
                        'cost'         => $this->currency->convert($shippingAmount, $this->session->data['currency'], $this->config->get('config_currency')),
                        'tax_class_id' => 0,
                        'mfid'         => $key,
                        'text'         => $this->currency->format($shippingAmount, $this->session->data['currency'], 1.0000000)
                    ];
                }
            }

            if (!$method_data['quote']) {
                $method_data['error'] = $this->language->get('error_no_fees');
            }
        } catch (Exception $ex) {
            $method_data['error'] = $ex->getMessage();
        }

        return $method_data;
    }

//---------------------------------------------------------------------------------------------------------------------------------------------------
    private function getShippingItems($mfObj) {
        $invoiceItemsArr = [];
        $products        = $this->cart->getProducts();
        foreach ($products as $product) {

            if ((float) $product['weight'] <= 0 || (float) $product['width'] <= 0 || (float) $product['length'] <= 0 || (float) $product['height'] <= 0) {
                $err = $this->language->get('error_dimensions_add') . $product['name'];
                $mfObj->log("DimensionsAdd - Error: $err");

                throw new Exception($err);
            }

            $weightRate        = $mfObj->getWeightRate($this->weight->getUnit($product['weight_class_id']));
            $dimensionRate     = $mfObj->getDimensionRate($this->length->getUnit($product['length_class_id']));
            $unitPrice         = $this->currency->convert($product['price'], $this->config->get('config_currency'), $this->session->data['currency']);
            $invoiceItemsArr[] = [
                'ProductName' => $product['name'],
                'Description' => $product['name'],
                'weight'      => $product['weight'] * $weightRate,
                'Width'       => $product['width'] * $dimensionRate,
                'Height'      => $product['length'] * $dimensionRate,
                'Depth'       => $product['height'] * $dimensionRate,
                'Quantity'    => $product['quantity'],
                'UnitPrice'   => round($unitPrice, 3),
            ];
        }
        return $invoiceItemsArr;
    }

//---------------------------------------------------------------------------------------------------------------------------------------------------
    public function getShippingData($key, $invoiceItemsArr, $address) {
        $city     = !empty($address['city']) ? $address['city'] : $address['zone'];
        $postCode = $address['postcode'];
        $country  = $address['iso_code_2'];
        return [
            'ShippingMethod' => $key,
            'Items'          => $invoiceItemsArr,
            'CityName'       => $city,
            'PostalCode'     => $postCode,
            'CountryCode'    => $country
        ];
    }

//---------------------------------------------------------------------------------------------------------------------------------------------------    
}
