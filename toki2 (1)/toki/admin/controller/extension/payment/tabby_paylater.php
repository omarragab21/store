<?php
class ControllerExtensionPaymentTabbyPaylater extends Controller {
	private $error = array();
    protected $code = 'tabby_paylater';
    const DEFAULT_TITLE = "Pay in 14 days";
	
	public function index() {
		$this->load->language('extension/payment/' . $this->code);

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		$this->load->model('extension/module/tabby');

        if (!$this->model_extension_module_tabby->getIsConfigured()) {
            $this->error['warning'] = $this->model_extension_module_tabby->getConfigureWarningMessage();
        }
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting($this->getSettingPrefix() . $this->code, $this->request->post);

			$this->session->data['success'] = $this->language->get('success_save');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}
			
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extensions'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/' . $this->code, 'user_token=' . $this->session->data['user_token'], true)
		);
						
		$data['action'] = $this->url->link('extension/payment/' . $this->code, 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$data['server'] = HTTPS_SERVER;
			$data['catalog'] = HTTPS_CATALOG;
		} else {
			$data['server'] = HTTP_SERVER;
			$data['catalog'] = HTTP_CATALOG;
		}
		
        $data['module_code'] = $this->code;
        $data['setting_prefix'] = $this->getSettingPrefix();

        foreach (['status', 'merchant_code_currency', 'geo_zone_id', 'title', 'terms', 'sort_order'] as $field) {
            $fname = $this->getSettingPrefix() . $this->code . '_' . $field;

		    if (isset($this->request->post[$fname])) {
			    $data[$field] = $this->request->post[$fname];
		    } elseif ($this->config->get($fname)) {
			    $data[$field] = $this->config->get($fname);
            } else {
                $data[$field] = null;
            }
        }
        
        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (empty($data['title'])) {
            $data['title'] = static::DEFAULT_TITLE;
        }

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
					
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/tabby', $data));
	}

	public function install() {
		$this->load->model('extension/module/tabby');
		$this->model_extension_module_tabby->registerWebhooks();
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/' . $this->code)) {
			$this->error['warning'] = $this->language->get('error_permission');
        }

		return !$this->error;
	}
    public function getSettingPrefix() {
        if (version_compare(VERSION, '3.0.0.0') > 0) {
            return 'payment_';
        } else {
            return '';
        }
    }
    public function order() {

        $data = [];

		$this->load->language('extension/module/tabby');

        if (isset($_POST['txn'])) {
	        $txn = json_decode($_POST['txn'], true);
        } else {
	        $this->load->model('extension/module/tabby');
	        $txn = $this->model_extension_module_tabby->getTransaction($this->request->get['order_id']);
        }

        if (!empty($txn)) {
	        $data['txn']  = json_decode($txn, true);
	        $data['captures_total'] = 0;
	        $data['refunds_total'] = 0;
	        if (!empty($data['txn']['captures'])) {
		        foreach ($data['txn']['captures'] as $capture) {
			        $data['captures_total'] += $capture['amount'];
		        }
	        }
	        if (!empty($data['txn']['refunds'])) {
		        foreach ($data['txn']['refunds'] as $refund) {
			        $data['refunds_total'] += $refund['amount'];
		        }
	        }
	        $data['max_amount'] = $data['txn']['amount'] - $data['captures_total'];
	        $data['max_refund_amount'] = $data['txn']['amount'] - $data['refunds_total'];
        } else {
	        $data['text_no_transaction'] = $this->language->get('text_no_transaction');
        }
        $this->response->setOutput($this->load->view('extension/payment/tabby_order', $data));
		return $this->load->view('extension/payment/tabby_order', $data);
    }
	
}
