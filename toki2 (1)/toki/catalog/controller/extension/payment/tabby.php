<?php
class ControllerExtensionPaymentTabby extends Controller {
    var $code = 'tabby';

	public function index() {
        $this->load->model('extension/module/tabby');
        $data = $this->model_extension_module_tabby->getCheckoutData();
        //$data['tabby_checkout_session_id'] = $this->session->data['tabby_checkout_session_id'];
        
		return $this->load->view('extension/payment/tabby', $data);
	}

	public function confirm() {
		$json = array();

        // use only for current payment method
		if (preg_match('#^' . $this->code . '#', $this->session->data['payment_method']['code'])) {
            $this->load->model('extension/module/tabby');
		
			$res = $this->model_extension_module_tabby->execute("GET", $_GET['payment_id']);

            $this->load->model('checkout/order');
            $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);

            if (empty($res) || $res->status == 'REJECTED' || $res->status == 'error') {
	            if (!empty($res->errors)) {
		            $json['error'] = '';
		            foreach ($res->errors as $error) {
			            $json['error'] .= $error['name'] . ': ' . $error['message'] . "\r\n";
		            }
	            } else {
		            $json['error'] = "Transaction not found.";
	            }
                $payment_methods = $this->session->data['payment_methods'];
	            $this->session->data['payment_methods'] = [];
	            $payment_method = $this->session->data['payment_method']['code'];
	            foreach ($payment_methods as $method => $value) {
	            	if ($method != $payment_method) {
			            $this->session->data['payment_methods'][$method] = $value;
 		            }
	            }
	            $this->session->data['payment_methods_not_unset'] = true;
	            $json['redirect'] = $this->url->link('checkout/checkout');
            } elseif ($res->status !== 'AUTHORIZED') {
                $json['error'] = "Transaction state is not valid.";
            } elseif ($res->amount != $this->formatAmount($this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false)) || $res->currency != $order['currency_code']) {
                $json['error'] = "Transaction amount or currency issue.";
            } else {
                // post order
			    $this->model_checkout_order->addOrderHistory(
                    $this->session->data['order_id'], 
                    $this->config->get('module_tabby_order_status_id'),
                    sprintf("Authorization transaction #%s. Amount %s %s", $_GET['payment_id'], $res->amount, $res->currency)
                );
                // assign transaction to order
	            $transaction_status = $this->model_extension_module_tabby->getTransactionStatus($_GET['payment_id']);
	            if ($transaction_status == 'created') {
		            $this->model_extension_module_tabby->updateTransaction([
			            'order_id' => $this->session->data['order_id'],
			            'body'     => json_encode($res),
			            'status'   => 'authorized',
						'source'   => 'checkout'
		            ]);
	            }

	            if ($this->config->get('module_tabby_capture_on') == "order_placed") {
		            $capture_exec = $this->model_extension_module_tabby->capture($_GET['payment_id'], $res->amount);
                    if (array_key_exists('error', $capture_exec) && !empty($capture_exec['error'])) $json['error'] = $capture_exec['error'];
	            }
			    $json['redirect'] = $this->url->link('checkout/success');

            }
            
            $json['success'] = !array_key_exists('error', $json);
		}

        // direct call with redirect=1
        if (array_key_exists('redirect', $_GET) && $_GET['redirect'] == 1) {
            if ($json['success']) {
                $this->response->redirect($this->url->link('checkout/success'));
            } else {
                $this->response->redirect($this->url->link('checkout/checkout'));
            }
            return;
        }
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));		
	}

	public function create() {
		$json = array();

		// use only for current payment method
		if (preg_match('#^' . $this->code . '#', $this->session->data['payment_method']['code'])) {
			$this->load->model('extension/module/tabby');

			$res = $this->model_extension_module_tabby->execute("GET", $_GET['payment_id']);

			$this->load->model('checkout/order');
			$order = $this->model_checkout_order->getOrder($this->session->data['order_id']);

			if (empty($res) || $res->status == 'error') {
				if (!empty($res->errors)) {
					$json['error'] = '';
					foreach ($res->errors as $error) {
						$json['error'] .= $error['name'] . ': ' . $error['message'] . "\r\n";
					}
				} else {
					$json['error'] = "Transaction not found.";
				}
				$payment_methods = $this->session->data['payment_methods'];
				$this->session->data['payment_methods'] = [];
				$payment_method = $this->session->data['payment_method']['code'];
				foreach ($payment_methods as $method => $value) {
					if ($method != $payment_method) {
						$this->session->data['payment_methods'][$method] = $value;
					}
				}
				$this->session->data['payment_methods_not_unset'] = true;
				$json['redirect'] = $this->url->link('checkout/checkout');
			} elseif ($res->status !== 'CREATED') {
				$json['error'] = "Transaction state is not valid.";
			} elseif ($res->amount != $this->formatAmount($this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false)) || $res->currency != $order['currency_code']) {
				$json['error'] = "Transaction amount or currency issue.";
			} else {
				// assign transaction to order
				$this->model_extension_module_tabby->addTransaction([
					'order_id'          => $this->session->data['order_id'],
					'transaction_id'    => $_GET['payment_id'],
					'body'              => json_encode($res),
	                'status'            => strtolower($res->status),
					'source'            => 'checkout'
				]);

			}

			$json['success'] = !array_key_exists('error', $json);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function callback() {

		$callback_json = @file_get_contents('php://input');

		$webhook = json_decode($callback_json);

		if (isset($webhook->id)) {
			$this->load->model('extension/module/tabby');
			$this->load->model('checkout/order');

            $transaction = $this->model_extension_module_tabby->getTabbyTransaction($webhook->id);

			$order_id = $transaction->order->reference_id;
			$transaction_id = $webhook->id;
			$status = $transaction->status;
			$amount = $transaction->amount;
			$currency = $transaction->currency;
			$sid = json_decode($webhook->description);

			@file_put_contents("test/" . substr($transaction_id, 0, 8) . "_" . date("H-i-s") . "_callback.json", $callback_json);

			$order = $this->model_checkout_order->getOrder($order_id);
			$transaction_status = $this->model_extension_module_tabby->getTransactionStatus($transaction_id);

			if (!empty($order) && in_array($status, ['AUTHORIZED', 'CLOSED']) && $amount == $this->formatAmount($this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false)) && $currency == $order['currency_code']) {
					// post order
				if ($transaction_status == 'created') {
					$this->model_checkout_order->addOrderHistory(
						$order_id,
						$this->config->get('module_tabby_order_status_id'),
						sprintf("Authorization webhook #%s. Amount %s %s", $transaction_id, $amount, $currency)
					);
					// assign transaction to order
					$this->model_extension_module_tabby->addTransaction([
						'order_id'       => $order_id,
						'transaction_id' => $transaction_id,
						'body'           => json_encode($transaction),
						'status'         => $status,
						'source'         => 'webhook'
					]);
				}

				//$this->cart->clear();
				$session_id = $sid->sid;
				$customer_id = $order['customer_id'];
				$sql = "DELETE FROM " . DB_PREFIX . "cart WHERE api_id = '0' AND customer_id = '" . (int)$customer_id . "' AND session_id = '" . $this->db->escape($session_id) . "'";
				$this->db->query($sql);

                if ($this->config->get('module_tabby_capture_on') == "order_placed") {
                    $capture_exec = $this->model_extension_module_tabby->capture($transaction_id, $amount);
                }
			}
		}
	}

	public function error() {

		$json = array();
		if (isset($_GET['errorType'])) {
			$error = $_GET['errorType'];
		}

		if (!empty($error)) {
			$json['error'] = "Error: " . $error;
			$payment_methods = $this->session->data['payment_methods'];
			$this->session->data['payment_methods'] = [];
			$payment_method = $this->session->data['payment_method']['code'];
			foreach ($payment_methods as $method => $value) {
				if ($method != $payment_method) {
					$this->session->data['payment_methods'][$method] = $value;
				}
			}
			$this->session->data['payment_methods_not_unset'] = true;
			//$json['redirect'] = $this->url->link('checkout/checkout');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function formatAmount($amount) {
        return number_format($amount, 2, '.', '');
    }
}
