<?php
class ControllerExtensionModuleXtensionsCheckoutXcart extends Controller {
	public $data = array();	

	public function __construct($registry) {
		parent::__construct($registry);
		$this->data += $this->load->language('checkout/cart');
		$this->data = array_merge($this->data,$this->load->language($this->config->get('xtensions_language_path')));
		$this->data += $this->xtensions_checkout->getXtensionsData($this->config->get('config_store_id'), 'xtensions_best_checkout');		
	}

	public function index($section) {
		$this->load->model('tool/image');
		$this->load->model('tool/upload');
		$this->data['section'] = $section['section'];
		if (isset($this->session->data['xcart_success'])) {
			$this->data['xcart_success'] = $this->session->data['xcart_success'];
			
			unset($this->session->data['xcart_success']);
		} else {
			$this->data['xcart_success'] = '';
		}
		$this->data['products'] = array();
		if ($this->cart->hasProducts() || !empty($this->session->data['vouchers'])) {	
			if(isset($this->data['xconfig']['options']['hide_cart']) && $this->data['xconfig']['options']['hide_cart']){
				return '';
			}
			$this->data['hide_cart_quantity'] = (isset($this->data['xconfig']['options']['hide_cart_quantity']) && $this->data['xconfig']['options']['hide_cart_quantity']);
			$this->data['information_always'] = (isset($this->data['xconfig']['options']['cart_information_always']) && $this->data['xconfig']['options']['cart_information_always']);
			$this->data['hide_wishlist'] = (isset($this->data['xconfig']['options']['hide_wishlist']) && $this->data['xconfig']['options']['hide_wishlist']);
			$this->data['hide_delete'] = (isset($this->data['xconfig']['options']['hide_delete']) && $this->data['xconfig']['options']['hide_delete']);
			$this->data['cart_link'] = $this->url->link('checkout/cart');
			$products = $this->cart->getProducts();
			
			$discounts = array();
			$this->data['hasCoupon'] = false;
			$this->data['has_product_discount'] = false;
			if($this->xtensions_checkout->configExists('xtensions/xlcoupon')){		
				$this->load->config('xtensions/xlcoupon');
				if($this->config->get($this->config->get('xlcoupon_config_pfx').'status') && $this->config->get($this->config->get('xlcoupon_config_pfx').'list_status')){
					if(isset($this->session->data['coupon'])){
						$this->data += $this->load->language($this->config->get('xlcoupon_path'));
						$this->load->model($this->config->get('xlcoupon_path'));
						$coupon_info = $this->{$this->config->get('xlcoupon_model')}->getCoupon($this->session->data['coupon']);
						$this->data['hasCoupon'] = $coupon_info?true:false;
						if(isset($coupon_info['has_product_discount']) && $coupon_info['has_product_discount']){
							$this->data['has_product_discount'] = true;
						}
						$discounts = isset($coupon_info['product'])?$coupon_info['product']:array();
					}
				}
			}
			foreach ($products as $product) {
				$product_total = 0;
				
				foreach ($products as $product_2) {
					if ($product_2['product_id'] == $product['product_id']) {
						$product_total += $product_2['quantity'];
					}
				}
				$error_warning = '';
				if ($product['minimum'] > $product_total) {
					$error_warning = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
				}
				
				if ($product['image']) {
					$image = $this->model_tool_image->resize($product['image'], 60, 60);
				} else {
					$image = '';
				}
				
				$option_data = array();
				
				foreach ($product['option'] as $option) {
					if ($option['type'] != 'file') {
						$value = $option['value'];
					} else {
						$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);
						
						if ($upload_info) {
							$value = $upload_info['name'];
						} else {
							$value = '';
						}
					}
					
					$option_data[] = array(
						'name' => $option['name'],
						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value) 
					);
				}
				
				// Display prices
				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$unit_price = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'));
					
					$price = $this->currency->format($unit_price, $this->session->data['currency']);
					$total = $this->currency->format($unit_price * $product['quantity'], $this->session->data['currency']);
				} else {
					$price = false;
					$total = false;
				}
				
				$recurring = '';
				
				if ($product['recurring']) {
					$frequencies = array(
						'day' => $this->language->get('text_day'),
						'week' => $this->language->get('text_week'),
						'semi_month' => $this->language->get('text_semi_month'),
						'month' => $this->language->get('text_month'),
						'year' => $this->language->get('text_year') 
					);
					
					if ($product['recurring']['trial']) {
						$recurring = sprintf($this->language->get('text_trial_description'), $this->currency->format($this->tax->calculate($product['recurring']['trial_price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['trial_cycle'], $frequencies[$product['recurring']['trial_frequency']], $product['recurring']['trial_duration']) . ' ';
					}
					
					if ($product['recurring']['duration']) {
						$recurring .= sprintf($this->language->get('text_payment_description'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
					} else {
						$recurring .= sprintf($this->language->get('text_payment_cancel'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
					}
				}
				
				$this->data['products'][] = array(
					'cart_id' => $product['cart_id'],
					'product_id' => $product['product_id'],
					'thumb' => $image,
					'name' => $product['name'],
					'model' => $product['model'],
					'option' => $option_data,
					'recurring' => $recurring,
					'quantity' => $product['quantity'],
					'stock' => $product['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
					'reward' => ($product['reward'] ? sprintf($this->language->get('text_points'), $product['reward']) : ''),
					'price' => $price,
					'total' => $total,
					'error_warning' => $error_warning,
					'coupon_discount'=> (isset($discounts[$product['cart_id']]['coupon_discount']) && $discounts[$product['cart_id']]['coupon_discount']>0)?$this->currency->format($discounts[$product['cart_id']]['coupon_discount'],$this->session->data['currency']):'',
					'href' => $this->url->link('product/product', 'product_id=' . $product['product_id']) 
				);
			}
			
			// Gift Voucher
			$this->data['vouchers'] = array();
			
			if (!empty($this->session->data['vouchers'])) {
				$image = (isset($this->data['xconfig']['options']['gift_wrap_image']) && $this->data['xconfig']['options']['gift_wrap_image'])?$this->data['xconfig']['options']['gift_wrap_image']:'catalog/xtensions/giftcard.jpg';				
				$this->data['voucher_image'] = $this->model_tool_image->resize($image, 60, 60);
				foreach ($this->session->data['vouchers'] as $key => $voucher) {
					$this->data['vouchers'][] = array(
						'key' => $key,
						'description' => $voucher['description'],
						'amount' => $this->currency->format($voucher['amount'], $this->session->data['currency']),
						'remove' => $this->url->link('checkout/cart', 'remove=' . $key) 
					);
				}
			}
			$this->data['total_products'] = sprintf($this->language->get('text_cart_total_products'),count($this->data['vouchers'])+count($this->data['products']));	// Cart -  item(s)
			$this->template = $this->config->get('xtensions_view_path').'xcart';
			return $this->xtensions_checkout->renderView($this);
		}else{
			if($this->data['section'] == 'login'){
				return '<script type="text/javascript">location ="'. $this->url->link('checkout/cart')  .'"</script>';	
			}else{
				return 'redirect';
			}			
		}
	}

	public function edit() {		
		if (isset($this->request->post['key']) && isset($this->request->post['quantity'])) {
			$this->cart->update($this->request->post['key'], $this->request->post['quantity']);
			$this->session->data['xcart_success'] = $this->language->get('text_remove');
			$this->xtensions_checkout->addEventActivity(array('cart_edited'));
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($this->xresponse()));
	}

	public function remove() {
		if (isset($this->request->post['key'])) {
			$this->cart->remove($this->request->post['key']);
			unset($this->session->data['vouchers'][$this->request->post['key']]);
			$this->xtensions_checkout->addEventActivity(array('cart_product_removed'));
			$this->session->data['xcart_success'] = $this->language->get('text_remove');
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($this->xresponse()));
	}
	
	public function removeVoucher() {
		if (isset($this->request->post['key'])) {
			$this->cart->remove($this->request->post['key']);
			unset($this->session->data['vouchers'][$this->request->post['key']]);
			$this->xtensions_checkout->addEventActivity(array('cart_product_removed'));
			$this->session->data['xcart_success'] = $this->language->get('text_remove');
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($this->xresponse()));
	}

	public function wishlist() {
		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}
		
		$this->load->model('catalog/product');
		
		$product_info = $this->model_catalog_product->getProduct($product_id);
		
		if ($product_info) {
			if ($this->customer->isLogged()) {
				// Edit customers cart
				$this->load->model('account/wishlist');
				
				$this->model_account_wishlist->addWishlist($this->request->post['product_id']);
			} else {
				if (!isset($this->session->data['wishlist'])) {
					$this->session->data['wishlist'] = array();
				}
				
				$this->session->data['wishlist'][] = $this->request->post['product_id'];
				
				$this->session->data['wishlist'] = array_unique($this->session->data['wishlist']);
			}
			if (isset($this->request->post['key'])) {
				$this->cart->remove($this->request->post['key']);
				$this->xtensions_checkout->addEventActivity(array('cart_product_moved_to_wishlist'));
				unset($this->session->data['vouchers'][$this->request->post['key']]);
				$this->session->data['xcart_success'] = $this->language->get('text_remove');
			}
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($this->xresponse()));
		}
	}
	
	private function xresponse(){
		$json = array();
		if(isset($this->request->post['section']) && $this->request->post['section']=='address'){
			if($this->customer->isLogged()){
				$json['xpayment_address'] = $this->load->controller($this->config->get('xtensions_controller_path').'xpayment_address/matter');
			}else{
				$json['xpayment_address'] = $this->load->controller($this->config->get('xtensions_controller_path').'xguest/matter');
			}
		}
		if(!$this->customer->isLogged()){
			$json['xcart'] = $this->index(array('section'=>'login'));
			$json['xtotals'] = $this->load->controller($this->config->get('xtensions_controller_path').'xcvc/xtotals');
		}else{
			$json['xcart'] = '';
			$json['xtotals'] = '';
		}
		return $json;
	}
}
?>
