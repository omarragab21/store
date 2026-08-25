<?php
class ControllerExtensionModuleTabby extends Controller {
    const SUPPORTED_COUNTRIES = array('AE', 'SA', 'BH', 'KW');
	private $error = array();

	public function index() {
		$this->load->language('extension/module/tabby');

		$this->load->model('setting/setting');
		$this->load->model('design/layout');

		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_tabby', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

        require_once(DIR_CATALOG . '/model/extension/module/tabby_ddlog.php');

		$data['heading_title'] = $this->language->get('heading_title') . ' ' . ModelExtensionModuleTabbyDdlog::VERSION;

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extensions'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true),
			'separator' => ' :: '
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/tabby', 'user_token=' . $this->session->data['user_token'], true),
			'separator' => ' :: '
		);

		$data['action'] = $this->url->link('extension/module/tabby', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		$data['user_token'] = $this->session->data['user_token'];

        // allowed countries
        if (isset($this->request->post['module_tabby_countries'])) {
            $selected_countries = $this->request->post['module_tabby_countries'];
        } elseif ($this->config->get('module_tabby_countries')) {
            $selected_countries = $this->config->get('module_tabby_countries');
        } else {
            $selected_countries = self::SUPPORTED_COUNTRIES;
        }

        $this->load->model('localisation/country'); 

        if (!is_array($selected_countries)) $selected_countries = explode(',', $selected_countries);
        $allowed_countries = array();
        foreach ($this->model_localisation_country->getCountries() as $country) {
            if (in_array($country['iso_code_2'], self::SUPPORTED_COUNTRIES)) {
                $allowed_countries[$country['iso_code_2']] = $country;
                $allowed_countries[$country['iso_code_2']]['selected'] = false;
                if (in_array($country['iso_code_2'], $selected_countries)) {
                    $allowed_countries[$country['iso_code_2']]['selected'] = true;
                }
            };
        }
        $data['countries'] = $allowed_countries;

		$this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        // order statuses
        $defaults = [
            'status'                    => 1,
            'debug'                     => 1,
            'public_key'                => '',
            'secret_key'                => '',
            'promo'                     => 1,
            'cart_promo'                => 1,
            'promo_limit'               => 0,
            'promo_theme'               => '',
            'merchant_code_currency'    => 0,
            'integration_type'          => 1,
            'sort_order'                => 1,
            'deletion_timeout'          => 20,
            'order_status_id'           => 1, 
            'capture_status_id'         => 2, 
            'refund_status_id'          => 11, 
            'cancel_status_id'          => 7, 
            'capture_on'                => 'order_placed'
        ];

        foreach (array_keys($defaults) as $field) {
            $fname = 'module_tabby_' . $field;
            if (isset($this->request->post[$fname])) {
                $data[$field] = $this->request->post[$fname];
            } elseif (!is_null($this->config->get($fname))) {
                $data[$field] = $this->config->get($fname);
            } elseif (array_key_exists($field, $defaults)) {
                $data[$field] = $defaults[$field];
            } else {
                $data[$field] = null;
            }
        }
        
        $data['captures'] = array('order_placed', 'shipment', 'invoice');

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/tabby', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/tabby')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

    public function install() {
        $this->load->model('extension/module/tabby');

        $this->model_extension_module_tabby->createTables();
	    $this->model_extension_module_tabby->registerWebhooks();
    }

    public function uninstall() {
        //$this->load->model('extension/module/tabby');

        //$this->model_extension_module_tabby->dropTables();
    }
}
