<?php
class ModelExtensionModuleTabby extends Model {

    public function getIsConfigured() {
        $this->load->model('setting/setting');
        
        return (
            $this->config->get('module_tabby_status') == 1 &&
            !empty($this->config->get('module_tabby_public_key')) &&
            !empty($this->config->get('module_tabby_secret_key'))
        );
    }
    public function getConfigureWarningMessage() {
        $this->load->language('extension/module/tabby');

        return sprintf(
            $this->language->get('warning_not_configured'), 
            $this->url->link('extension/module/tabby', 'user_token=' . $this->session->data['user_token'], true)
        );
    }
    public function getTransaction($order_id) {
    	$query = "SELECT * FROM `" . DB_PREFIX . "tabby_transaction` WHERE id = (SELECT max(id) FROM `" . DB_PREFIX . "tabby_transaction` WHERE order_id='" . (int)$order_id . "')";
        return $this->db->query($query)->row['body'];
    }
    public function getTransactions($order_id) {
        $txn = $this->getTransaction($order_id);

        $transactions = [];
        if ($txn && !empty($txn['body'])) {
            $txn = json_decode($txn['body']);
            $transactions[] = [
                "id"        => $txn->id,
                "order_id"  => $order_id,
                "type"      => 'AUTH',
                "amount"    => $txn->amount,
                "currency"  => $txn->currency,
                "date"      => $txn->created_at
            ];
            foreach ($txn->captures as $capture) {
                $transactions[] = [
                    "id"        => $capture->id,
                    "payment_id"=> $txn->id,
                    "order_id"  => $order_id,
                    "type"      => 'CAPTURE',
                    "amount"    => $capture->amount,
                    "currency"  => $txn->currency,
                    "date"      => $capture->created_at
                ];
            }
            foreach ($txn->refunds as $refund) {
                $transactions[] = [
                    "id"        => $refund->id,
                    "payment_id"=> $txn->id,
                    "capture_id"=> $refund->capture_id,
                    "order_id"  => $order_id,
                    "type"      => 'REFUND',
                    "amount"    => $refund->amount,
                    "currency"  => $txn->currency,
                    "date"      => $refund->created_at
                ];
            }
        }

        return $transactions;
    }
    public function addTransaction($data) {
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
    public function createTables() {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tabby_transaction` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `create_date` DATETIME NULL DEFAULT NULL,
            `update_date` DATETIME NULL DEFAULT NULL,
            `body` TEXT,
            `order_id` int(11) NOT NULL,
            `status` VARCHAR(16) NOT NULL,
            `source` VARCHAR(16) NOT NULL,
            `transaction_id` varchar(64) NOT NULL,

            PRIMARY KEY (`id`),
            KEY (order_id),
            KEY (transaction_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        $this->db->query("DELETE FROM `" . DB_PREFIX . "layout_module` WHERE `code` = 'tabby'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "module` WHERE `code` = 'tabby'");

        $tabby_module = array(
            "name"  => "Tabby Checkout",
            "status"=> "1"
        );
        if (version_compare(VERSION, '2.1', '<')) {
            $str = serialize($tabby_module);
        } else {
            $str = json_encode($tabby_module);
        }

        $this->db->query("INSERT INTO `" . DB_PREFIX . "module` (`module_id`, `name`, `code`, `setting`)
                        VALUES (NULL, 'Tabby Checkout', 'tabby', '".$str."')");

	    $this->db->query("INSERT INTO `" . DB_PREFIX . "layout_module` (`layout_module_id`, `layout_id`, `code`, `position`, `sort_order`) 
						VALUES (NULL, '7', 'tabby.".$this->db->getLastId()."', 'content_top', '0')");
    }

	public function registerWebhooks() {
        $sk = $this->config->get('module_tabby_secret_key');
        if (empty($sk)) return;
    	$codes = array('AE', 'SA', 'KW', 'BH');
		$url = HTTPS_CATALOG . 'index.php?route=extension/payment/tabby/callback';
        $is_test = (bool)preg_match("#^sk_test_#", $sk);
    	foreach ($codes as $code) {
		    $check_webhook = false;
		    $webhooks = json_decode($this->exec('GET', $code), true);
            $this->ddlog('info', 'Checking webhooks for ' . $code, null, ['webhooks' => $webhooks]);
            if (array_key_exists('status', $webhooks) && $webhooks['status'] == 'error') continue;
		    foreach ($webhooks as $webhook) {
				if (isset($webhook['url']) && $webhook['url'] == $url) {
                    if ($webhook['is_test'] != $is_test) {
                        $this->ddlog('info', 'Updating webhook for ' . $code, null, [
                            'code'      => $code,
                            'url'       => $url,
                            'is_test'   => $is_test,
                            'id'        => $webhook['id']
                        ]);
			            $this->exec('PUT', $code, ['url' => $url, 'is_test' => $is_test], $webhook['id']);
                    }
					$check_webhook = true;
				}
		    }
		    if (!$check_webhook) {
                $this->ddlog('info', 'Creating webhook for ' . $code, null, [
                    'code'      => $code,
                    'url'       => $url,
                    'is_test'   => $is_test
                ]);
			    $this->exec('POST', $code, ['url' => $url, 'is_test' => $is_test]);
		    }
	    }
	}

	public function exec($method, $code, $data = null, $id = null) {

		$authorization = 'Authorization: Bearer ' . $this->config->get('module_tabby_secret_key');
		$merchant_code = 'X-Merchant-Code: ' . $code;
    	$headers = array($merchant_code, $authorization);
    	if ($method != 'GET') {
		    $headers[] = 'Content-Type: application/json';
		    $postfields = json_encode($data);
	    }

		$curl = curl_init();

    	$curl_options = array(
		    CURLOPT_URL => 'https://api.tabby.ai/api/v1/webhooks' . ($id ? '/' . $id : ''),
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_ENCODING => '',
		    CURLOPT_MAXREDIRS => 10,
		    CURLOPT_TIMEOUT => 0,
		    CURLOPT_FOLLOWLOCATION => true,
		    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		    CURLOPT_CUSTOMREQUEST => $method,
		    CURLOPT_HTTPHEADER => $headers
	    );
		if ($method != 'GET') {
			$curl_options[CURLOPT_POSTFIELDS] = $postfields;
		}

		curl_setopt_array($curl, $curl_options);

		$response = curl_exec($curl);

		curl_close($curl);
		return $response;

	}

    public function dropTables() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "tabby_transaction`");
    }
    public function ddlog($status = "error", $message = "Something went wrong", $e = null, $data = null) {
        if (!$this->model_ddlog) {
            require_once(DIR_CATALOG . '/model/extension/module/tabby_ddlog.php');

            $this->registry->set('model_ddlog', new ModelExtensionModuleTabbyDdlog($this->registry));
        };
        $this->model_ddlog->log($status, $message, $e, $data);
    }
}
