<?php
class ControllerCheckoutSuccess extends Controller {
	public function index() {
		$this->load->language('checkout/success');

		if (isset($this->session->data['order_id'])) {

        if ($this->config->get('module_marketplace_status') && $this->config->get('module_wk_crosssell_crosssell_status')) {
          $cart_data = array();
          if($this->cart->getProducts()) {
            $cart_data = $this->cart->getProducts();
          }

          $existing_cart_id = array();
          foreach ($cart_data as $value) {
            $existing_cart_id[] = $value['cart_id'];
          }

          $this->load->model('account/product_saved_option');

          foreach ($this->model_account_product_saved_option->getMappedCart() as $value) {
            $mapped_cart = json_decode($value['cart_id']);
            $total_mapped_cart = count($mapped_cart);

            $total_found = 0;

            foreach ($mapped_cart as $cart_id_value) {

              if(in_array($cart_id_value,$existing_cart_id)) {
                $total_found++;
              }
            }
            if($total_mapped_cart == $total_found) {
              $this->model_account_product_saved_option->substractQuantity($value['type'], $value['i_id']);
              $this->model_account_product_saved_option->deleteMapping($value['id']);
            }
          }
        }
        
			$this->cart->clear();


            if($this->registry->has('xtensions_checkout') && $this->xtensions_checkout->isActive('xtensions_best_checkout',$this->config->get('config_store_id'))){
				$this->xtensions_checkout->unsetters($this);
			}
            
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
			unset($this->session->data['totals']);
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_basket'),
			'href' => $this->url->link('checkout/cart')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_checkout'),
			'href' => $this->url->link('checkout/checkout', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_success'),
			'href' => $this->url->link('checkout/success')
		);

		if ($this->customer->isLogged()) {
			$data['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('account/account', '', true), $this->url->link('account/order', '', true), $this->url->link('account/download', '', true), $this->url->link('information/contact'));
		} else {
			$data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));
		}

		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/success', $data));
	}
}