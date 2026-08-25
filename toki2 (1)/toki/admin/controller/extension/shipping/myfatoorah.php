<?php

class ControllerExtensionShippingMyfatoorah extends Controller {

    private $id     = 'myfatoorah';
    private $error  = array();
    private $fields = [
        'status', 'sort_order', 'debug',
        'shipping'
    ];

//-----------------------------------------------------------------------------------------------------------------------------------------
    public function index() {
        $path = "extension/shipping/$this->id";

        //Load language
        $data = $this->language->load($path);

        //diff from oc version
        if (version_compare(VERSION, '3.0.0', '<')) {
            $ocUserToken    = 'token=' . $this->session->data['token'];
            $ocExLink       = 'extension/extension&type=shipping';
            $ocCode         = $data['ocCode'] = $this->id;
        } else {
            $ocUserToken    = 'user_token=' . $this->session->data['user_token'];
            $ocExLink       = 'marketplace/extension&type=shipping';
            $ocCode         = $data['ocCode'] = 'shipping_' . $this->id;
        }

        //Set document title
        $this->document->setTitle($this->language->get('heading_title'));

        //Remove success message on reload
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        //If isset request to change settings
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            //Edit settings
            $this->load->model('setting/setting');
            $this->model_setting_setting->editSetting($ocCode, $this->request->post);

            //Set success message
            $this->session->data['success'] = $this->language->get('text_success');

            //Return to extensions page
            $this->response->redirect($this->url->link($path, $ocUserToken, true));
        }

        //Load errors if exist
        $data['error_warning'] = (isset($this->error['warning'])) ? $this->error['warning'] : '';

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
        $data['shippingMethods'] = [
            '1' => $this->language->get('text_dhl'),
            '2' => $this->language->get('text_aramex')
        ];

        //Default values
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = 1;
        }
        if (!isset($data['debug'])) {
            $data['debug'] = 1;
        }

        //Load default layout, must be in the end
        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view($path, $data));
    }

//-----------------------------------------------------------------------------------------------------------------------------------------
    protected function validate() {

        $shippingStatusCode = (version_compare(VERSION, '3.0.0', '<')) ? 'myfatoorah_status' : 'shipping_myfatoorah_status';
        $paymentStatusCode  = (version_compare(VERSION, '3.0.0', '<')) ? 'myfatoorah_pg_status' : 'payment_myfatoorah_pg_status';

        if ($this->request->post[$shippingStatusCode] == 1 && $this->config->get($paymentStatusCode) != 1) {
            $this->error['warning'] = $this->language->get('error_shipping_apiKey');
        }

        return !$this->error;
    }

//-----------------------------------------------------------------------------------------------------------------------------------------
}
