<?php
require_once DIR_SYSTEM . 'library/myfatoorah/MyfatoorahApiV2.php';

class ControllerExtensionPaymentMyfatoorahPG extends Controller {

    private $id     = 'myfatoorah_pg';
    private $error  = array();
    private $fields = [
        'apiKey',
        'status', 'countryMode', 'test', 'sort_order', 'debug',
        'payment_type', 'saveCard', 'webhook_secret_key', 'initial_order_status_id', 'order_status_id', 'failed_order_status_id'
    ];

//-----------------------------------------------------------------------------------------------------------------------------------------
    public function index() {
        $path = "extension/payment/$this->id";

        //Load language
        $data = $this->language->load($path);

        //diff from oc version
        if (version_compare(VERSION, '3.0.0', '<')) {
            $ocUserToken    = 'token=' . $this->session->data['token'];
            $ocExLink       = 'extension/extension&type=payment';
            $ocCode         = $data['ocCode'] = $this->id;
        } else {
            $ocUserToken    = 'user_token=' . $this->session->data['user_token'];
            $ocExLink       = 'marketplace/extension&type=payment';
            $ocCode         = $data['ocCode'] = 'payment_' . $this->id;
        }

        //Set document title
        $this->document->setTitle($this->language->get('heading_title'));

        //Remove success message on reload
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        //If isset request to change settings
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate($ocCode)) {

            //Edit settings
            $this->load->model('setting/setting');
            $this->model_setting_setting->editSetting($ocCode, $this->request->post);

            //disable MyFatoorah shipping too if the MyFatoorah payment is disable
            if ($this->request->post[$ocCode . '_status'] == 0) {
                $shippingCode = (version_compare(VERSION, '3.0.0', '<')) ? 'myfatoorah' : 'shipping_myfatoorah';

                $shippingData = $this->model_setting_setting->getSetting($shippingCode);

                $shippingData[$shippingCode . '_status'] = 0;
                $this->model_setting_setting->editSetting($shippingCode, $shippingData);
            }

            //Set success message
            $this->session->data['success'] = $this->language->get('text_success');

            //Return to extensions page
            $this->response->redirect($this->url->link($path, $ocUserToken, true));
        }

        //Load errors if exist
        $data['error_warning'] = (isset($this->error['warning'])) ? $this->error['warning'] : '';

        $data['error_apiKey'] = (isset($this->error['apiKey'])) ? $this->error['apiKey'] : '';

        //Load action buttons urls
        $data['action'] = $this->url->link($path, $ocUserToken, true);
        $data['cancel'] = $this->url->link($ocExLink, $ocUserToken, true);

        //Load breadcrumbs
        $data['breadcrumbs']   = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', $ocUserToken, true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $data['cancel']
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $data['action']
        );

        //Set default values for fields
        foreach ($this->fields as $field) {
            $key          = $ocCode . '_' . $field;
            $data[$field] = (isset($this->request->post[$key])) ? $this->request->post[$key] : $this->config->get($key);
        }

        //Lookups
        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $data['mfCountries'] = MyfatoorahApiV2::getMyFatoorahCountries();
        $data['nameIndex'] = 'countryName' . ucfirst($this->language->get('code'));
 
        //Default values
        if (empty($data['sort_order'])) {
            $data['sort_order'] = 1;
        }
        if (empty($data['debug'])) {
            $data['debug'] = 1;
        }
        if (empty($data['initial_order_status_id'])) {
            $data['initial_order_status_id'] = 1;
        }
        if (empty($data['order_status_id'])) {
            $data['order_status_id'] = 2;
        }
        if (empty($data['failed_order_status_id'])) {
            $data['failed_order_status_id'] = 10;
        }

        //Load default layout, must be in the end
        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view($path, $data));
    }

//-----------------------------------------------------------------------------------------------------------------------------------------
    protected function validate($ocCode) {

        if ($this->request->post[$ocCode . '_status'] == 1 && !trim($this->request->post[$ocCode . '_apiKey'])) {
            $this->error['apiKey']  = $this->error['warning'] = $this->language->get('error_apiKey');
        }

        return !$this->error;
    }

//-----------------------------------------------------------------------------------------------------------------------------------------    
    public function install() {
        $ocCode = (version_compare(VERSION, '3.0.0', '<')) ? '' : 'payment_';

        if ($this->config->get($ocCode . 'myfatoorah_v2_status')) {
            $this->migrateFrom('myfatoorah_v2', $ocCode);
        } else if ($this->config->get($ocCode . 'myfatoorah_embedded_status')) {
            $this->migrateFrom('myfatoorah_embedded', $ocCode);
        } else if ($this->config->get($ocCode . 'myfatoorah_direct_status')) {
            $this->migrateFrom('myfatoorah_direct', $ocCode);
        }
    }

    private function migrateFrom($from, $ocCode) {
        foreach ($this->fields as $field) {
            $data[$ocCode . $this->id . '_' . $field] = $this->config->get($ocCode . $from . '_' . $field);
        }

        if (empty($data[$ocCode . $this->id . '_countryMode'])) {
            $data[$ocCode . $this->id . '_countryMode'] = 'KWT';
        }

        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting($ocCode . $this->id, $data);
    }

//-----------------------------------------------------------------------------------------------------------------------------------------
}
