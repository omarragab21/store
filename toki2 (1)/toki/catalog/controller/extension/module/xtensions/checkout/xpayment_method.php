<?php
class ControllerExtensionModuleXtensionsCheckoutXPaymentMethod extends Controller {
	public $data = array();
	
	public function __construct($registry){
		parent::__construct($registry);
		$this->data = $this->load->language('checkout/checkout');
		$this->data = array_merge($this->data,$this->load->language($this->config->get('xtensions_language_path')));
		$this->data += $this->xtensions_checkout->getXtensionsData($this->config->get('config_store_id'), 'xtensions_best_checkout');
	}
	
	public function index() {
		unset($this->session->data['payment_method']);
		unset($this->session->data['payment_methods']);
		if(isset($this->request->post['comment'])){
			$this->session->data['comment'] = strip_tags($this->request->post['comment']);
		}
		$this->session->data['agree'] = true;
		$this->data['isLogged'] = $this->customer->isLogged();
		$xconfig = $this->data['xconfig'];
		$this->data['misc_options'] = $misc_options = $xconfig['options'];
		$this->data['payment_images'] = isset($misc_options['payment']['images'])?$misc_options['payment']['images']:array();
		$disabled_payments = isset($misc_options['payment']['disable'])?$misc_options['payment']['disable']:array();
		$payment_address = array();
		if(isset($this->session->data['payment_address'])){
			$payment_address = $this->session->data['payment_address'];
		}
		$this->data['display_payment_address'] = $this->data['display_shipping_method'] = $this->data['display_shipping_address'] = false;
		if(isset($misc_options['display_shipping_address']) && $misc_options['display_shipping_address']){
			if($this->cart->hasShipping()){
				if(isset($this->session->data['shipping_address'])){
					$this->data['display_shipping_address'] = true;
					$shipping_address = $this->session->data['shipping_address'];
					$this->data['shipping_address_title']= $this->language->get('text_saddress');
					$this->data['shipaddress'] = $shipping_address['formatted_address'];
					if(isset($misc_options['display_shipping_method']) && $misc_options['display_shipping_method']){
						$this->data['display_shipping_method'] = true;
						$this->data['shipping_method_title']= $this->language->get('text_selected_shipping_method');
						$this->data['shipping_method']= $this->session->data['shipping_method']['title'];
						$this->data['shipping_method_cost']= $this->session->data['shipping_method']['text'];						
					}
				}				
			}
		}
		if(isset($misc_options['display_payment_address']) && $misc_options['display_payment_address'] && $payment_address){
			$this->data['display_payment_address'] = true;
			$this->data['payaddress'] = $payment_address['formatted_address'];
			$this->data['payment_address_title']= $this->language->get('text_paddress');
		}

		$this->session->data['comment']=isset($this->session->data['comment'])?$this->session->data['comment']:'';
		if (!empty($payment_address)) {
			// Totals
			// Totals
			$totals = array();
			$taxes = $this->cart->getTaxes();
			$total = 0;

			// Because __call can not keep var references so we put them into an array.
			$total_data = array(
				'totals' => &$totals,
				'taxes'  => &$taxes,
				'total'  => &$total
			);

			$this->load->model('setting/extension');

			$sort_order = array();

			$results = $this->model_setting_extension->getExtensions('total');

			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get('total_'.$value['code'] . '_sort_order');
			}

			array_multisort($sort_order, SORT_ASC, $results);

			foreach ($results as $result) {
				if ($this->config->get('total_' . $result['code'] . '_status')) {
					$this->load->model('extension/total/' . $result['code']);

					// We have to put the totals in an array so that they pass by reference.
					$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
				}
			}
			
			// Payment Methods
			$method_data = array();
			$results = $this->model_setting_extension->getExtensions('payment');
			$cart_has_recurring = $this->cart->hasRecurringProducts();
			foreach ($results as $result) {
				if(is_array($disabled_payments) && in_array($result['code'], $disabled_payments)){
					continue;
				}
				if ($this->config->get('payment_' . $result['code'] . '_status')) {
					$this->load->model('extension/payment/' . $result['code']);

					$method = $this->{'model_extension_payment_' . $result['code']}->getMethod($payment_address, $total);

					if ($method) {
						if($cart_has_recurring > 0){
							if (property_exists($this->{'model_extension_payment_' . $result['code']},'recurringPayments')) {
								if($this->{'model_extension_payment_' . $result['code']}->recurringPayments() == true){
									$method_data[$result['code']] = $method;
								}
							}
						} else {
							$method_data[$result['code']] = $method;
						}
					}
				}
			}

			$sort_order = array();

			foreach ($method_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $method_data);

			$this->session->data['payment_methods'] = $method_data;
			$this->data['total'] = $this->currency->format($total, $this->session->data['currency']);
		}

		$this->data['text_payment_method'] = $this->language->get('text_payment_method');

		$this->data['button_continue'] = $this->language->get('button_continue');

		if (empty($this->session->data['payment_methods'])) {
			$this->data['error_payment_warning'] = sprintf($this->language->get('error_no_payment'), $this->url->link('information/contact'));
			$this->xtensions_checkout->addEventActivity(array('no_payment_method'));
		} else {
			$this->data['error_payment_warning'] = '';
		}

		if (isset($this->session->data['payment_methods'])) {
			$this->data['payment_methods'] = $this->session->data['payment_methods'];
		} else {
			$this->data['payment_methods'] = array();
		}

		if(isset($misc_options['payment']['default']) && isset($this->data['payment_methods'][$misc_options['payment']['default']])){
			$this->data['code'] = $misc_options['payment']['default'];
		}else if (isset($this->session->data['payment_method']['code'])) {
			$this->data['code'] = $this->session->data['payment_method']['code'];
		} else {
			$this->data['code'] = '';
		}
		$children = array($this->config->get('xtensions_controller_path').'xcvc/xoptions');
		array_push($children, array('key'=>$this->config->get('xtensions_controller_path').'xcart','data'=>array('section'=>'payment')));		
		array_push($children,$this->config->get('xtensions_controller_path').'/xcvc/xtotals');
			
		$this->data += $this->xtensions_checkout->getChildren($children);
		if(isset($misc_options['payment_type_mobile']) && $this->xtensions_checkout->isMobile()){
			$this->data['accordion_view'] = $misc_options['payment_type_mobile']=='accordion'?true:false;
		}else{
			$this->data['accordion_view'] = $misc_options['payment_type']=='accordion'?true:false;
		}
		$this->template = $this->config->get('xtensions_view_path').'xpayment_method';
		$this->xtensions_checkout->addEventActivity(array('entered_step_3','payment_method'=>array('code'=>$this->data['code'])));
		if(isset($this->request->get['direct'])){
			$json['xpayment_method'] = $this->xtensions_checkout->renderView($this);
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}else{
			return $this->xtensions_checkout->renderView($this);			
		}
	}

	public function validate() {
		$this->language->load('checkout/checkout');

		$json = array();

		// Validate if payment address has been set.
		$this->load->model('account/address');
		$payment_address = (isset($this->session->data['payment_address'])?$this->session->data['payment_address']:'');

		if (empty($payment_address)) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		}

		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');
		}

		// Validate minimum quantity requirments.
		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$json['redirect'] = $this->url->link('checkout/cart');

				break;
			}
		}

		if (!$json) {
			if (!isset($this->request->post['payment_method'])) {
				$json['error']['warning_payment'] = $this->language->get('error_payment');
			} elseif (!isset($this->session->data['payment_methods'][$this->request->post['payment_method']])) {
				$json['error']['warning_payment'] = $this->language->get('error_payment');
			}
			if (!$json) {
				$this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];
				$json += $this->xtensions_checkout->getChildren(array($this->config->get('xtensions_controller_path').'xcvc/xtotals'));
				$json['totals'] = $this->currency->format($this->xtensions_checkout->getTotals(), $this->session->data['currency']);
				$this->xtensions_checkout->addEventActivity(array('payment_method_changed'));
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
?>
