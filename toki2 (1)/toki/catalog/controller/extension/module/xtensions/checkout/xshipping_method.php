<?php
class ControllerExtensionModuleXtensionsCheckoutXShippingMethod extends Controller {
	public $data = array();	

	public function index() {
		$this->language->load('checkout/checkout');
		$this->language->load($this->config->get('xtensions_language_path'));
		
		if (isset($this->session->data['shipping_methods'])) {
			$this->data['shipping_methods'] = $this->session->data['shipping_methods'];
		} else {
			$this->data['shipping_methods'] = array();
		}
		
		if (isset($this->session->data['shipping_method']['code'])) {
			$shipping1 = explode('.', $this->session->data['shipping_method']['code']);
			if (!isset($shipping1[0]) || !isset($shipping1[1]) || !isset($this->session->data['shipping_methods'][$shipping1[0]]) || !isset($this->session->data['shipping_methods'][$shipping1[0]]['quote'][$shipping1[1]])) {
				$this->data['shipping_code'] = '';
			} else {
				$this->data['shipping_code'] = $this->session->data['shipping_method']['code'];
			}
		} else {
			$this->data['shipping_code'] = '';
		}
		$this->data['text_shipping_method'] = $this->language->get('text_shipping_method');
		$this->data += $this->xtensions_checkout->getXtensionsData($this->config->get('config_store_id'), 'xtensions_best_checkout');
		$xconfig = $this->data['xconfig'];
		$this->data['misc_options'] = $misc_options = $xconfig['options'];
		$this->data['address_block'] = ($misc_options['address_type']  == 'block')?true:false;
		
		$this->template = $this->config->get('xtensions_view_path').'xshipping_method';
		return $this->xtensions_checkout->renderView($this);
	}

	public function validate($setting=array()) {
		//unset($this->session->data['shipping_method']);
		$this->language->load('checkout/checkout');
		$this->language->load($this->config->get('xtensions_language_path'));
		
		$json = array();
		if (isset($this->request->post['comment'])) {
			$this->session->data['comment'] = strip_tags($this->request->post['comment']);
		}
		// Validate if shipping is required. If not the customer should not have reached this page.
		if (!$this->cart->hasShipping()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		}
		
		// Validate if shipping address has been set.
		$this->load->model('account/address');
		if (isset($this->session->data['shipping_address'])) {
			$shipping_address = $this->session->data['shipping_address'];
		}
		
		if (empty($shipping_address)) {
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
		if (!isset($json['redirect'])) {
			$xconfig_data = $this->xtensions_checkout->getXtensionsData($this->config->get('config_store_id'), 'xtensions_best_checkout');
			$misc_options = $xconfig_data['xconfig']['options'];
			$disabled_shippings = isset($misc_options['shipping']['disable'])?$misc_options['shipping']['disable']:array();
			$free_shipping_title = '';
			if(isset($misc_options['shipping']['zero_shipping_status']) && $misc_options['shipping']['zero_shipping_status'] && isset($misc_options['shipping']['zero_shipping_title'][(int)$this->config->get('config_language_id')]) && $free_shipping_title = $misc_options['shipping']['zero_shipping_title'][(int)$this->config->get('config_language_id')]){
				$free_shipping_title = $free_shipping_title;
			}
			if (!empty($shipping_address)) {
				// Shipping Methods
				$quote_data = array();
				$this->load->model('setting/extension');
				$results = $this->model_setting_extension->getExtensions('shipping');
				foreach ($results as $result) {
					if(is_array($disabled_shippings) && in_array($result['code'], $disabled_shippings)){
						continue;
					}
					if ($this->config->get('shipping_' . $result['code'] . '_status')) {
						$this->load->model('extension/shipping/' . $result['code']);
						
						$quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($shipping_address);
						
						if ($quote) {
							$newquote = $quote;
							foreach ($quote['quote'] as $key=>$sub_quote){
								if($free_shipping_title && $sub_quote['cost'] == 0){
									$newquote['quote'][$key] = $sub_quote;
									$newquote['quote'][$key]['text'] = '<span class="freeshipping">' . $free_shipping_title . '</span>';
								}
							}
							$quote_data[$result['code']] = array(
								'title' => $newquote['title'],
								'quote' => $newquote['quote'],
								'sort_order' => $quote['sort_order'],
								'error' => $quote['error'] 
							);
						}
					}
				}
				
				$sort_order = array();
				
				foreach ($quote_data as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}
				
				array_multisort($sort_order, SORT_ASC, $quote_data);
				
				$this->session->data['shipping_methods'] = $quote_data;			
				
				$has_shipping_set = false;
				if ($quote_data && !isset($this->session->data['shipping_method'])) {
					foreach ($quote_data as $quote) {
						foreach ($quote['quote'] as $quote) {
							if($quote && isset($quote['code']) && $quote['code']){
								$shippings = $quote['code'];
								$has_shipping_set = true;
								break;
							}
						}
						if($has_shipping_set){
							break;
						}
					}
					$shipping = explode('.', $shippings);
					$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
				} else if (!$quote_data) {
					unset($this->session->data['shipping_method']);
				} else if (isset($this->session->data['shipping_method']) && isset($this->session->data['shipping_method']['code'])) {
					$shipping = explode('.', $this->session->data['shipping_method']['code']);
					if (!isset($quote_data[$shipping[0]]) || !isset($quote_data[$shipping[0]]['quote'][$shipping[1]])) {
						foreach ($quote_data as $quote) {
							foreach ($quote['quote'] as $quote) {
								$shippings = $quote['code'];
								break;
							}
							break;
						}
						$shipping = explode('.', $shippings);						
					}
					$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
				}
			}
			if (!isset($this->request->post['shipping_method']) && !isset($this->session->data['shipping_method'])) {
				$json['error']['warning'] = $this->language->get('error_shipping');
			} else {
				if (isset($this->request->post['shipping_method'])) {
					$shipping_method = $this->request->post['shipping_method'];
				} else {
					$shipping_method = $this->session->data['shipping_method']['code'];
				}
				$shipping = explode('.', $shipping_method);
				
				if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
					$json['error']['warning'] = $this->language->get('error_shipping');
				}
			}
			
			if (!isset($json['redirect']) && !isset($json['error'])) {
				$shipping_address = array();
				if (isset($this->session->data['shipping_address'])) {
					$shipping_address = $this->session->data['shipping_address'];
				}
				if (isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
					$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
				}
			}
		}
		if(!isset($setting['direct'])){
			if(isset($this->request->get['refresh']) && $this->request->get['refresh']=='true'){
				$json['shipping_method'] = $this->index();
				$json['shipping_methods'] = (isset($this->session->data['shipping_methods']) && $this->session->data['shipping_methods']) ? true : false;			
				if (!$json['shipping_methods']) {
					$this->xtensions_checkout->addEventActivity(array('no_shipping_method'));
					$json['warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
				}else{
					$this->xtensions_checkout->addEventActivity(array('shipping_method_changed'));
				}
			}else{
				$this->xtensions_checkout->addEventActivity(array('shipping_method_changed'));
			}
			$json += $this->xtensions_checkout->getChildren(array($this->config->get('xtensions_controller_path').'xcvc/xtotals'));
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}
}
?>
