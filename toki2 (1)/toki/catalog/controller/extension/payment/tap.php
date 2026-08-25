<?php
class ControllerExtensionPaymentTap extends Controller {
	public function index() {
		$data['button_confirm'] = $this->language->get('button_confirm');
		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		//$data['amount'] = $order_info['total'];
		$data['amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		$data['products'] = $this->cart->getProducts();
		$data['order_id'] = $this->session->data['order_id'];
		//echo '<pre>'; var_dump($data['total']);exit;
		$data['test_public_key'] = $this->config->get('payment_tap_test_public_key');
		$data['entry_post_url'] = $this->config->get('payment_tap_post_url');
		$data['entry_ui_mode'] = $this->config->get('payment_tap_ui_mode');
			//var_dump($this->config->get('payment_tap_test'));exit;	
		if ($this->config->get('payment_tap_test'))
			{
				$active_sk = $this ->config->get('payment_tap_test_secret_key');
				$active_pk = $this ->config->get('payment_tap_test_public_key');	
			}


			else{
				$active_sk = $this ->config->get('payment_tap_secret_live_key');
				$active_pk = $this ->config->get('payment_tap_public_live_key');
				
			}

			$data ['active_sk'] = $active_sk;
			$data ['active_pk'] = $active_pk;
	

//var_dump($data['order_id']);exit;
		
		$data['itemprice1'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		$data['itemname1'] ='Order ID - '.$order_info['order_id'];
		$data['currencycode'] = $order_info['currency_code'];
		$data['ordid'] = $order_info['order_id'];
		$data['entey_charge_mode'] = $this->config->get('payment_tap_charge_mode');

		//var_dump($data['itemprice1']);exit;
		$data['cstemail'] = $order_info['email'];
		$data['cstname'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
		$data['cstmobile'] = $order_info['telephone'];
		$data['cntry'] = $order_info['payment_iso_code_2'];
		$ref = '';
		if($data['currencycode']=="KWD"){
			$Total_price = number_format((float)$data['amount'], 3, '.', '');
		}
		else{
			$Total_price = number_format((float)$data['amount'], 2, '.', '');
		}
		
        $amount = number_format((float)$data['amount'], 2, '.', '');
        $Hash = 'x_publickey'.$active_pk.'x_amount'.$Total_price.'x_currency'.$data['currencycode'].'x_transaction'.$ref.'x_post'.$data['entry_post_url'];
       $data['hashstring'] = hash_hmac('sha256', $Hash, $active_sk);

		$data['returnurl'] = $this->url->link('extension/payment/tap/callback');
		//$data['returnurl'] = $this->url->link('extension/payment/tap/callback');
       return $this->load->view('extension/payment/tap', $data);

	}

	public function callback() {
		$this->load->language('payment/tap');
		if (isset($this->request->get['tap_id'])) {
			$tap_id = $this->request->get['tap_id'];
			$order_id = $this->session->data['order_id'];
		} else {
			$order_id = 0;
		}
		if ($this->config->get('payment_tap_test'))
			{
				$active_sk = $this ->config->get('payment_tap_test_secret_key');
			}


			else{
				$active_sk = $this ->config->get('payment_tap_secret_live_key');
				
			}

			$data ['active_sk'] = $active_sk;
		//$active_sk = $this->config->get('payment_tap_test_secret_key');
		//echo $this->request->get['tap_id'];exit;
		$curl = curl_init();

		curl_setopt_array($curl, array(
  			CURLOPT_URL => "https://api.tap.company/v2/charges/".$tap_id,
  				CURLOPT_RETURNTRANSFER => true,
  				CURLOPT_ENCODING => "",
  				CURLOPT_MAXREDIRS => 10,
  				CURLOPT_TIMEOUT => 30,
  				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  				CURLOPT_CUSTOMREQUEST => "GET",
  				CURLOPT_POSTFIELDS => "{}",
  				CURLOPT_HTTPHEADER => array(
    					"authorization: Bearer ".$active_sk
  				),
			)
		);

		$response = curl_exec($curl);
		//var_dump($_GET['tap_id']);exit;
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
  			echo "cURL Error #:" . $err;
		} else {
  			$response = json_decode($response);
		}
		
		//echo $response->status;
		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($order_id);
		// $charge_id = $_GET['tap_id'];
		// $payment_Method = $response->source->payment_method;

		// $payment_Ref = $response->reference->payment;
		$Comment = 'Tap payment successful'.("<br>").('ID').(':'). ($_GET['tap_id'].("<br>").('Payment Type :') . ($response->source->payment_method).("<br>").('Payment Ref:'). ($response->reference->payment));
        // var_dump($Comment);exit;
		if ($order_info && $response->status == 'CAPTURED') {
			$error = '';
			
			$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_tap_order_status_id'), $Comment);

			$this->response->redirect($this->url->link('checkout/success'));
		} else {
			$error = $this->language->get('text_unable');
		}

		if ($error) {
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
				'href' => $this->url->link('checkout/checkout', '', 'SSL')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_failed'),
				'href' => $this->url->link('checkout/success')
			);

			$data['heading_title'] = $this->language->get('text_failed');

			$data['text_message'] = sprintf($this->language->get('text_failed_message'), $error, $this->url->link('information/contact'));

			$data['button_continue'] = $this->language->get('button_continue');

			$data['continue'] = $this->url->link('common/home');
			
			//var_dump('entey_charge_mode'); exit;



			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

            if (isset($this->request->post['payment_tap_charge_mode'])) {
			$data['payment_tap_charge_mode'] = $this->request->post['payment_tap_charge_mode'];

		    } else {
			$data['payment_tap_charge_mode'] = $this->config->get('payment_tap_charge_mode');
		    }

			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/success')) {
				$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/common/success', $data));
			} else {
				$this->response->setOutput($this->load->view('common/success', $data));
			}
		} 
	}
}