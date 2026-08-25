<?php
class ModelExtensionModuleTabby extends Model {

    var $tabbyProducts = [
        'tabby_paylater'    => 'payLater',
        'tabby_installments'=> 'installments'
    ];
    var $apiUrl = 'https://api.tabby.ai/api/v1/payments/';
	var $apiCheckoutUrl = 'https://api.tabby.ai/api/v2/checkout';
    var $errors = [];

	public function getCheckoutData() {
        $data = [];

        $data['tabbyApiKey'] = $this->config->get('module_tabby_public_key');
        $data['tabbyDebug']  = $this->config->get('module_tabby_debug');
        $data['merchantCode']  = $this->getMerchantCode();
        $data['useRedirect']  = is_null($this->config->get('module_tabby_integration_type')) ? 1 : $this->config->get('module_tabby_integration_type');
        $data['merchantUrls'] = json_encode(array(
            "success"  => str_replace('&amp;', '&', $this->url->link("extension/payment/tabby/confirm", 'redirect=1', true)),
            "cancel"   => $this->url->link("checkout/checkout", '', true),
            "failure"  => $this->url->link("checkout/checkout", '', true),
        ));
        $data['lang'] = $this->language->get('code');
        $data['tabbyProduct']= array_key_exists($this->session->data['payment_method']['code'], $this->tabbyProducts)
            ? $this->tabbyProducts[$this->session->data['payment_method']['code']] : '';
            //: $this->tabbyProducts['tabby_paylater'];
        $data['tabbyPayment']= $this->getPaymentObject();

        return $data;
	}

	public function createSession() {
		$data = [];

		$data['payment']= $this->getPaymentObjectCreate();
		$data['lang'] = $this->language->get('code');
		$data['merchant_code']  = $this->getMerchantCode();

		$result = $this->execute("POST" , "checkout", $data);

        $this->load->model('extension/module/tabby_ddlog');
        $this->model_extension_module_tabby_ddlog->log('info', 'createSession', null, array(
            'data'      => $data,
            'result'    => $result
        ));

		return $result;
	}

    public function addTransaction($data) {
        $this->load->model('extension/module/tabby_ddlog');
        $this->model_extension_module_tabby_ddlog->log('info', 'addTransaction', null, array(
            'order_id'  => $data['order_id'],
            'payment'   => array(
                'id'    => $data['transaction_id']
            ),
            'body'      => $data['body'],
            'status'    => $data['status'],
            'source'    => $data['source']
        ));
        if ($tid = $this->getTransactionIdByOrder($data['order_id'])) {
            $this->updateTransaction($data);
        } else {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "tabby_transaction`
                (`order_id`, `create_date`, `update_date`, `transaction_id`, `body`, `status`, `source`)
                VALUES (
                    '".$this->db->escape($data['order_id'])."',
                    now(), null,
                    '".$this->db->escape($data['transaction_id'])."',
                    '".$this->db->escape($data['body'])."',
                    '".$this->db->escape($data['status'])."',
                    '".$this->db->escape($data['source'])."'
                )");
        }
    }
	public function updateTransaction($data) {
        if (!array_key_exists('transaction_id', $data)) {
            $data['transaction_id'] = 'not provided';
            try {
                $txn = json_decode($data['body']);
                $data['transaction_id'] = $txn->id;
            } catch (\Exception $e) {
            }
        }

        $this->load->model('extension/module/tabby_ddlog');
        $this->model_extension_module_tabby_ddlog->log('info', 'updateTransaction', null, array(
            'order_id'  => $data['order_id'],
            'payment'   => array(
                'id'    => $data['transaction_id']
            ),
            'body'      => $data['body'],
            'status'    => $data['status']
        ));
		//if ($data['status'] == 'authorized') {
			$sql = "UPDATE `" . DB_PREFIX . "tabby_transaction` SET update_date = now(), body = '" . $this->db->escape($data['body']) . "', status = '" . $this->db->escape($data['status']) . "', transaction_id = '" . $this->db->escape($data['status']) .  "' WHERE order_id = '" . (int)$data['order_id'] . "'";
		//} else {
			//$sql = "UPDATE `" . DB_PREFIX . "tabby_transaction` SET update_date = now(), body = '" . $this->db->escape($data['body']) . "', status = '" . $this->db->escape($data['status']) . "' WHERE order_id = '" . (int)$data['order_id'] . "' AND (status = 'captured' OR status = 'closed' OR status = 'refunded')";
		//}
		$this->db->query($sql);
	}
    protected function getMerchantCode() {
        $merchant_code = array_key_exists('shipping_address', $this->session->data)
            ? $this->session->data['shipping_address']['iso_code_2']
            : $this->session->data['payment_address' ]['iso_code_2'];

        $merchant_code .= $this->config->get('module_tabby_merchant_code_currency') ? '_' . $this->session->data['currency'] : '';

        return $merchant_code;
    }
    protected function getPaymentObject() {
        $payment = [];

        $payment["order"] = []; 
        $payment['order']['reference_id'] = (string)$this->session->data['order_id'];
        $payment["order"]["items"] = []; 

        $this->load->model('checkout/order');

        $order_data = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $payment['amount']      = $this->formatAmount($this->currency->format($order_data['total'], $order_data['currency_code'], $order_data['currency_value'], false));
        $payment['currency']    = $order_data['currency_code'];

        $sid = array('sid' => $this->session->getId());
        $payment['description'] = json_encode($sid);

        $images = [];
        foreach ($this->cart->getProducts() as $product) {
            $images[$product['product_id']] =  $product['image'];
        }

        foreach ($this->getOrderProducts($this->session->data['order_id']) as $product) {
            $product['image'] = $images[$product['product_id']] ?: 'placeholder.png';
            $payment["order"]["items"][] = array(
                'title'         => $product['name'],
                'reference_id'  => $product['model'],
                'unit_price'    => $this->formatAmount($this->currency->format($product['price'] + $product['tax'], $order_data['currency_code'], $order_data['currency_value'], false)),
                'tax_amount'    => $this->formatAmount($this->currency->format($product['tax'], $order_data['currency_code'], $order_data['currency_value'], false)),
                'quantity'      => (int)$product['quantity'],
                'product_url'   => $this->url->link('product/product', 'product_id=' . $product['product_id'], true),
                'image_url'     => $this->getBaseUrl() .'image/'. $product['image']
            );

        }
        // get discount
        $totals = $this->getOrderTotals($this->session->data['order_id']);
        $discount = 0;
        foreach ($totals as $total) {
            $discount += (in_array($total['code'], ['voucher', 'coupon'])) ? -$total['value'] : 0;
        }
        $payment['order']['discount_amount']  = $this->formatAmount($this->currency->format($discount, $order_data['currency_code'], $order_data['currency_value'], false));

        $sub_total  = $this->cart->getSubTotal();
        $total      = $this->cart->getTotal();
        $tax_total  = $total - $sub_total;
        $shipping   = $shipping_cost = 0;

        if ($this->cart->hasShipping()) {
            $shipping_cost  = $this->session->data['shipping_method']['cost'];
            $tax_class      = $this->session->data['shipping_method']['tax_class_id'];

            $shipping       = $this->tax->calculate($shipping_cost, $tax_class, $this->config->get('config_tax')); 
        
            $payment["shipping_address"] = [
                'address'   => $this->session->data['shipping_address']['address_1'] . 
                    (!empty($this->session->data['shipping_address']['address_2']) ? ' ' : '') . 
                    $this->session->data['shipping_address']['address_2'],
                'city'      => $this->session->data['shipping_address']['city']
            ];
        }

        $payment['order']['tax_amount']  = $this->formatAmount($this->currency->format($tax_total + $shipping - $shipping_cost, $order_data['currency_code'], $order_data['currency_value'], false));
        $payment['order']['shipping_amount']  = $this->formatAmount($this->currency->format($shipping, $order_data['currency_code'], $order_data['currency_value'], false));

        $payment['buyer']   = $this->getBuyerObject();
        $payment['order_history'] = $this->getOrderHistoryObject($payment['buyer']);

        return json_encode($payment);
    }

	protected function getPaymentObjectCreate() {
		$payment = [];

		$payment['amount']      = $total      = $this->formatAmount($this->currency->format($this->cart->getTotal(), $this->session->data['currency'], '', false));
		$payment['currency']    = $this->session->data['currency'];

		$sid = array('sid' => $this->session->getId());
		$payment['description'] = json_encode($sid);
		$payment['buyer']   = $this->getBuyerObject();

		$payment["order"] = [];
		$payment["order"]["items"] = [];

		$images = [];
		foreach ($this->cart->getProducts() as $product) {
			$images[$product['product_id']] =  $product['image'];
		}

		foreach ($this->cart->getProducts() as $product) {
			$product['image'] = $images[$product['product_id']] ?: 'placeholder.png';
			$payment["order"]["items"][] = array(
				'title'         => $product['name'],
				'reference_id'  => $product['model'],
				'unit_price'    => $this->formatAmount($this->currency->format($product['price'], $this->session->data['currency'], '', false)),
				'quantity'      => (int)$product['quantity'],
				'product_url'   => $this->url->link('product/product', 'product_id=' . $product['product_id'], true),
				'image_url'     => $this->getBaseUrl() .'image/'. $product['image']
			);

		}

		$sub_total  = $this->cart->getSubTotal();
		$tax_total  = $total - $sub_total;
		$shipping   = $shipping_cost = 0;

		if ($this->cart->hasShipping()) {
			$shipping_cost  = $this->session->data['shipping_method']['cost'];
			$tax_class      = $this->session->data['shipping_method']['tax_class_id'];

			$shipping       = $this->tax->calculate($shipping_cost, $tax_class, $this->config->get('config_tax'));

			$payment["shipping_address"] = [
				'address'   => $this->session->data['shipping_address']['address_1'] .
					(!empty($this->session->data['shipping_address']['address_2']) ? ' ' : '') .
					$this->session->data['shipping_address']['address_2'],
				'city'      => $this->session->data['shipping_address']['city']
			];
		}

		$payment['order']['tax_amount']  = $this->formatAmount($this->currency->format($tax_total + $shipping - $shipping_cost, $this->session->data['currency'], '', false));
		$payment['order']['shipping_amount']  = $this->formatAmount($this->currency->format($shipping, $this->session->data['currency'], '', false));
		$payment['amount'] += $payment['order']['shipping_amount'];
        // already contain currency_value
        $payment['amount'] = $this->formatAmount($payment['amount']);

		$payment['order_history'] = $this->getOrderHistoryObject($payment['buyer']);

		return $payment;
	}

	protected function getOrderHistoryObject($buyer) {
        $order_history = [];

        $this->load->model("checkout/order");

        // get Order details by email and phone

        $where_fields = [
            'email'            => $buyer['email'],
	        'telephone'        => $buyer['phone']
        ];

        $query = "SELECT order_id FROM `" . DB_PREFIX . "order`";
        $where = [];
        foreach ($where_fields as $name => $value) {
            $where[] = "$name = '".$this->db->escape($value)."'";
        }
        
        $order_query = $this->db->query($query . " WHERE order_status_id > 0 AND (" . implode(" OR ", $where) . ")");
        foreach ($order_query->rows as $row) {
            $order = $this->model_checkout_order->getOrder($row['order_id']);
            // TODO: bypass wrong orders
            if (false) continue;
    
            $order_history[] = [
                "amount"            => $this->formatAmount($this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false)),
                "buyer"             => $this->getOrderHistoryBuyerObject($order),
                "items"             => $this->getOrderHistoryItemsObject($order),
                "payment_method"    => $order['payment_code'],
                "purchased_at"      => date(\DateTime::RFC3339, strtotime($order['date_added'])),
                "shipping_address"  => $this->getOrderHistoryShippingAddressObject($order),
                "status"            => $this->getOrderHistoryStatus($order)
            ];
        }
        return $order_history;
    }
    protected function getOrderHistoryStatus($order) {
        $status = 'processing';
        switch ($order['order_status_id']) {
            case 7:
            case 9:
                $status = 'canceled';
                break;
            case 5:
            case 15:
                $status = 'complete';
                break;
            case 11:
            case 12:
            case 16:
                $status = 'refunded';
                break;
            case 1:
                $status = 'new';
        };
        return $status;
    }
    protected function getOrderHistoryShippingAddressObject($order) {
        return [
            'address'   => $order['shipping_address_1'] . ' ' . $order['shipping_address_2'],
            'city'      => $order['shipping_city']
        ];
    }
    protected function getOrderHistoryItemsObject($order) {
        $result = [];

        $products = $this->getOrderProducts($order['order_id']);
        
        foreach ($products as $product) {
            $result[] = [
                'quantity'      => (int)$product['quantity'],
                'title'         => $product['name'],
                'unit_price'    => $this->formatAmount($this->currency->format($product['price'] + $product['tax'], $order['currency_code'], $order['currency_value'], false)),
                'reference_id'  => $product['model'],
                'ordered'       => (int)$product['quantity']
            ];
        }

        return $result;
    }
    protected function getOrderHistoryBuyerObject($order) {
        return [
            'name'  => $order['firstname'] . ' ' . $order['lastname'],
            'phone' => $order['telephone']
        ];
    }

    protected function getBuyerObject() {

        $dob = null;
        $email = null;
        $name = null;
        $phone = null;

        if ($this->customer->isLogged()) {

            $this->load->model('account/customer');

            $ci = $this->model_account_customer->getCustomer($this->customer->getId());

            $name = $ci['firstname'] . ' ' . $ci['lastname'];
            $email = $ci['email'];
            $phone = $ci['telephone'];
        } elseif(array_key_exists('guest', $this->session->data)) {
            $name   = $this->session->data['guest']['firstname'] . ' ' . $this->session->data['guest']['lastname'];
            $email  = $this->session->data['guest']['email'];
            $phone  = $this->session->data['guest']['telephone'];
            if (array_key_exists('order_data', $_POST)) {
                // some situations, variables exists in post, but not updated on order
                $name   = $_POST['order_data']['firstname'] . ' ' . $_POST['order_data']['lastname'];
                $email  = $_POST['order_data']['email'];
                $phone  = $_POST['order_data']['telephone'];
            }
        } elseif (array_key_exists('order_data', $_POST)) {
            // some situations, variables exists in post, but not updated on order
            $name   = $_POST['order_data']['firstname'] . ' ' . $_POST['order_data']['lastname'];
            $email  = $_POST['order_data']['email'];
            $phone  = $_POST['order_data']['telephone'];
        } elseif (defined('JOURNAL3_ACTIVE')) {
            $this->load->model('journal3/order');
            $data = $this->model_journal3_order->load($this->session->data['order_id']);
            $name   = $data['firstname'] . ' ' . $data['lastname'];
            $email  = $data['email'];
            $phone  = $data['telephone'];
        }
        
        return [
            "dob"   => $dob,
            "email" => $email,
            "name"  => $name,
            "phone" => $phone
        ];
    }
    protected function formatAmount($amount) {
        return number_format($amount, 2, '.', '');
    
    }
    public function execute($method, $endpoint, $data = array()) {

        $this->errors = [];

		if ($endpoint == "checkout") {
			$url = $this->apiCheckoutUrl;
		} else {
			$url = $this->apiUrl . $endpoint;
		}

        $curl_options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(),
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 10
        );

        $curl_options[CURLOPT_HTTPHEADER][] = 'Accept-Charset: utf-8';
        $curl_options[CURLOPT_HTTPHEADER][] = 'Accept: application/json';
	    if ($endpoint == "checkout") {
		    $curl_options[CURLOPT_HTTPHEADER][] = 'Authorization: Bearer ' . $this->config->get('module_tabby_public_key');
	    } else {
		    $curl_options[CURLOPT_HTTPHEADER][] = 'Authorization: Bearer ' . $this->config->get('module_tabby_secret_key');
	    }

        if ($method != "GET") {
            $data_json = json_encode($data);
            $curl_options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
            $curl_options[CURLOPT_HTTPHEADER][] = 'Content-Length: ' . strlen($data_json);
            $curl_options[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
            $curl_options[CURLOPT_POSTFIELDS] = $data_json;
        }

        $ch = curl_init();
        curl_setopt_array($ch, $curl_options);
        $response = curl_exec($ch);

        $result = [];
        $curl_info = curl_getinfo($ch);

        if ($curl_info['http_code'] != 200) {
	        $this->errors[] = array('name' => 'http_code', 'message' => $curl_info['http_code'], 'response' => $response);
        }

        if (curl_errno($ch)) {
            $curl_code = curl_errno($ch);

            $constant = get_defined_constants(true);
            $curl_constant = preg_grep('/^CURLE_/', array_flip($constant['curl']));

            $this->errors[] = array('name' => $curl_constant[$curl_code], 'message' => curl_strerror($curl_code));
        }

        try {
            $result = json_decode($response);
        } catch (Exception $e) {
            $this->errors[] = ['name' => 'json', 'message' => $e->getMessage()];
        }

        if(empty($this->errors)) {
	        return $result;
        } else {
            // log error
            $this->load->model('extension/module/tabby_ddlog');
            $this->model_extension_module_tabby_ddlog->log('error', 'request error', null, $this->errors);
	        $res = new stdClass();
	        $res->status = 'error';
	        $res->errors = $this->errors;
	        return $res;
        }
    }

    protected function getBaseUrl() {
        $server = null;
        if ($this->request->server['HTTPS']) {
            $server = $this->config->get('config_ssl');
        } else {
            $server = $this->config->get('config_url');
        }
        return $server;
    }

	public function getTransactionIdByOrder($order_id) {
		$sql = "SELECT `transaction_id` FROM `" . DB_PREFIX . "tabby_transaction` WHERE order_id = '" . $order_id . "' ORDER BY id DESC";
		$res = $this->db->query($sql);
		return isset($res->row['transaction_id']) ? $res->row['transaction_id'] : 0;
	}

    public function getTransactionRecord($transaction_id) {
        $sql = "SELECT * FROM `" . DB_PREFIX . "tabby_transaction` WHERE transaction_id = '" . $transaction_id . "' ORDER BY id DESC";
        $res = $this->db->query($sql);
        return $res->row;
    }
	public function getTransaction($transaction_id) {
        $row = $this->getTransactionRecord($transaction_id);
		return $row['body'];
	}

	public function getTransactionStatus($transaction_id) {
        $row = $this->getTransactionRecord($transaction_id);
		return array_key_exists('status', $row) ? $row['status'] : null;
	}

	public function getOrderIdByTransaction($transaction_id) {
        $row = $this->getTransactionRecord($transaction_id);
		return $row['order_id'];
	}

	public function getTabbyTransaction($transaction_id) {
		$endpoint = $transaction_id;
		return $this->execute("GET", $endpoint);
	}

    public function capture($transaction_id, $amount) {
		$error = true;
	    $message = "";
	    $this->load->model('checkout/order');

		$transaction = $this->getTabbyTransaction($transaction_id);
	    if ($transaction->status != "AUTHORIZED") {
		    $message = 'Payment is not authorized';
	    } else if (count($transaction->captures) > 0) {
		    $message = 'Payment is captured';
		    //$error = false;
	    } else {
		    $endpoint = $transaction_id . "/captures";
		    $params = array(
			    'amount' => $amount
		    );

		    $transaction = $this->execute("POST", $endpoint, $params);
		    if (empty($transaction)) {
			    $message = "Transaction not found.";
		    } elseif ($transaction->status == 'error') {
			    $message = $transaction->error;
		    } elseif ($transaction->status !== 'CLOSED' && $transaction->status !== 'AUTHORIZED') {
			    $message = "Transaction state is not valid.";
		    } else {
		    	$error = false;
		    	$status = $transaction->status == 'CLOSED' 
                    ? ($this->config->get('module_tabby_capture_status_id') ?: 5) 
                    : ($this->config->get('module_tabby_order_status_id'  ) ?: 2);
			    $this->model_checkout_order->addOrderHistory(
					$transaction->order->reference_id, $status,
					sprintf("Capture transaction #%s. Amount %s %s", $transaction->captures[count($transaction->captures) - 1]->id, $amount, $transaction->currency)
				);
			    $this->addTransaction([
				    'order_id'       => $transaction->order->reference_id,
				    'transaction_id' => $transaction_id,
				    'body'           => json_encode($transaction),
				    'status'         => 'captured',
				    'source'         => 'admin'
			    ]);
	        }
		}

        $this->load->model('extension/module/tabby_ddlog');
        $this->model_extension_module_tabby_ddlog->log($error ? 'error' : 'info', 'capture', null, array(
            'payment'   => array(
                'id'    => $transaction_id
            ),
            'amount'    => $amount,
            'message'   => $message,
            'error'     => $error
        ));

	    return array(
		    'error'    => $error,
		    'message'  => $message
	    );
    }

	public function refund($transaction_id, $amount) {
		$error = true;
		$message = "";
		$this->load->model('checkout/order');

		$transaction = $this->getTabbyTransaction($transaction_id);
		if (count($transaction->refunds) > 0) {
			$refunds_total = 0;
			foreach ($transaction->refunds as $refund) {
				$refunds_total += $refund->amount;
			}
		}
		if (count($transaction->captures) == 0) {
			$message = 'Payment is not captured';
		} else if (count($transaction->refunds) > 0 && $refunds_total == $transaction->amount) {
			$message = 'Payment is refunded';
			//$error = false;
		} else {
			$capture_id = $transaction->captures[0]->id;
			$endpoint = $transaction_id . "/refunds";
			$captures_total = 0;
			foreach ($transaction->captures as $capture) {
				$captures_total += $capture->amount;
			}

			$params = array(
				'capture_id' => $capture_id,
				'amount' => $amount
			);

			$transaction = $this->execute("POST", $endpoint, $params);
			if (empty($transaction)) {
				$message = "Transaction not found.";
			} elseif ($transaction->status == 'error') {
				$message = $transaction->error;
			} elseif ($transaction->status !== 'CLOSED') {
				$message = "Transaction state is not valid.";
			} else {
				$error = false;
				$this->model_checkout_order->addOrderHistory(
					$transaction->order->reference_id, $this->config->get('module_tabby_refund_status_id') ?: 11,
					sprintf("Refund transaction #%s. Amount %s %s", $transaction->refunds[count($transaction->refunds) - 1]->id, $amount, $transaction->currency)
				);
				$this->updateTransaction([
					'order_id' => $transaction->order->reference_id,
					'body' => json_encode($transaction),
					'status' => 'refunded'
				]);
			}
		}

        $this->load->model('extension/module/tabby_ddlog');
        $this->model_extension_module_tabby_ddlog->log($error ? 'error' : 'info', 'refund', null, array(
            'payment'   => array(
                'id'    => $transaction_id
            ),
            'amount'    => $amount,
            'message'   => $message,
            'error'     => $error
        ));

		return array(
			'error'    => $error,
			'message'  => $message
		);
	}

	public function close($transaction_id) {
		$error = true;
		$message = "";
		$this->load->model('checkout/order');

		$transaction = $this->getTabbyTransaction($transaction_id);
		if ($transaction->status != "CREATED" && $transaction->status != "AUTHORIZED") {
			$message = 'Transaction state is not valid.';
		} else {
			$endpoint = $transaction_id . "/close";
			$transaction = $this->execute("POST", $endpoint);
			if (empty($transaction)) {
				$message = "Transaction not found.";
			} elseif ($transaction->status == 'error') {
				$message = $transaction->error;
			} elseif ($transaction->status != 'CLOSED') {
				$message = "Transaction state is not valid.";
			} else {
				$error = false;
				$this->model_checkout_order->addOrderHistory(
					$transaction->order->reference_id, $this->config->get('module_tabby_cancel_status_id') ?: 7,
					sprintf("Closed transaction #%s. Amount %s %s", $transaction_id, $transaction->amount, $transaction->currency)
				);
				$this->updateTransaction([
					'order_id' => $transaction->order->reference_id,
					'body' => json_encode($transaction),
					'status' => 'closed'
				]);
			}
		}

        $this->load->model('extension/module/tabby_ddlog');
        $this->model_extension_module_tabby_ddlog->log($error ? 'error' : 'info', 'close', null, array(
            'payment'   => array(
                'id'    => $transaction_id
            ),
            'message'   => $message,
            'error'     => $error
        ));

		return array(
			'error'    => $error,
			'message'  => $message
		);
	}

	public function delete($transaction_id) {
		$error = true;

		$this->load->model('checkout/order');

		$transaction = $this->getTabbyTransaction($transaction_id);

		if ($transaction->status == 'CREATED') {
			$order_id = $this->getOrderIdByTransaction($transaction_id);

			if ($order_id) {
				$this->model_checkout_order->deleteOrder($order_id);

				$this->updateTransaction([
					'order_id' => $order_id,
					'body' => json_encode($transaction),
					'status' => 'deleted'
				]);
				$error = false;
			}
		}

        $this->load->model('extension/module/tabby_ddlog');
        $this->model_extension_module_tabby_ddlog->log($error ? 'error' : 'info', 'delete', null, array(
            'payment'   => array(
                'id'    => $transaction_id
            ),
            'error'     => $error
        ));

		return array(
			'error'  => $error,
		);
	}

	public function getErrors() {
		return $this->errors;
	}

    public function getOrderProducts($order_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

        return $query->rows;
    }

    public function getOrderTotals($order_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order ASC");

        return $query->rows;
    }
}
