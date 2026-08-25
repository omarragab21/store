<?php

/*
 *******************************************************************************
 *  Module: Additional pages (Bestseller, Latest, Mostviewed, All products)
 *
 *  Web-site: http://opencart-modules.com
 *  Email: dev.dashko@gmail.com
 *  © Leonid Dashko
 *
 *  Below source-code or any part of the source-code cannot be resold or distributed.
 ******************************************************************************
 */

require_once(DIR_SYSTEM . 'library/additional_pages/page_name.class.php');

class ControllerModuleAdditionalPages extends Controller
{
    protected $errors = array();
    protected $module_name;

    public function __construct($registry)
    {
        parent::__construct($registry);

        require_once(DIR_SYSTEM . 'library/additional_pages/page_name.class.php');

        $this->module_name = 'module/additional_pages';

        if (version_compare(VERSION, '2.2.0.0', '>=')) {
            $this->module_name = 'extension/module/additional_pages';
        }
    }

    public function index()
    {
        $data = array();
        $this->load->model('setting/setting');

        $this->loadLang($data);

        $this->document->setTitle($this->language->get('heading_title'));

        $input = $this->request->post;

        if ($this->isPostRequest() && $this->validate($input)) {
            $input['additional_pages_settings'] = $input['settings'];
            $this->model_setting_setting->editSetting('additional_pages', $input);

            $this->session->data['success'] = $this->language->get('success_msg');
            $this->response->redirect($this->url->link($this->module_name, 'user_token=' . $this->session->data['user_token'], 'SSL'));
        }

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('modules'),
            'href' => $this->getModulesLink()
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link($this->module_name, 'user_token=' . $this->session->data['user_token'], 'SSL')
        );

        # Show Success message
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        # Show Errors messages
        if (isset($this->session->data['errors'])) {
            $data['errors'] = $this->session->data['errors'];
            unset($this->session->data['errors']);
        } else {
            $data['errors'] = '';
        }

        $data['current_settings'] = $this->config->get('additional_pages_settings');
        $data['module_settings'] = array(
            AdditionalPageName::BESTSELLERS => array('limit_by'),
            AdditionalPageName::POPULAR => array('limit_by'),
            AdditionalPageName::LATEST => array('limit_by')
        );

        foreach ($data['module_settings'] as $page_name => $setting_names) {
            $data['text'][$page_name] = $this->language->get($page_name);
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['action'] = $this->url->link($this->module_name, 'user_token=' . $this->session->data['user_token'], 'SSL');
        $data['cancel_link'] = $this->getModulesLink();
        $data['link_to_support'] = 'http://opencart-modules.com/en/tab-modules?lang=' . trim($this->config->get('config_admin_language'));

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/additional_pages', $data));
    }

    public function getModulesLink()
    {
        if (version_compare(VERSION, '2.3.0.0', '<')) {
            return $this->url->link('extension/module', 'user_token=' . $this->session->data['user_token'], 'SSL');
        }
        return $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', 'SSL');
    }

    private function isPostRequest()
    {
        return $this->request->server['REQUEST_METHOD'] == 'POST';
    }

    private function validate(&$input)
    {
        if (!$this->user->hasPermission('modify', $this->module_name)) {
            $this->session->data['errors'][] = $this->language->get('error_permission');
            return false;
        }
        return true;
    }

    private function loadLang(&$data)
    {
        $this->load->language('catalog/product');
        $this->load->language($this->module_name);
        $data['l'] = $this->language;
    }
}

# Compatibility with OC >= 2.3.x
class ControllerExtensionModuleAdditionalPages extends ControllerModuleAdditionalPages
{
}
