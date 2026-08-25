<?php
class ControllerExtensionModuleProductQuickView extends Controller
{

    private $name          = 'Product Quick View';
    private $version       = '1.5.0';
    private $author        = 'MagDevel';
    private $homepage      = 'https://magdevel.com';
    private $support_email = 'support@magdevel.com';
    private $base_name     = 'product_quick_view';

    private $module_dir;
    private $extension_dir;
    private $token_url;
    private $extension_type;
    private $tpl_ext;
    private $base_path;
    private $resource_name;
    private $setting_code;

    private $error = array();

    public function __construct($registry)
    {
        parent::__construct($registry);

        if (version_compare(VERSION, '3.0', '>=')) {
            $this->module_dir     = 'extension/module';
            $this->extension_dir  = 'marketplace/extension';
            $this->token_url      = 'user_token=' . $this->session->data['user_token'];
            $this->extension_type = '&type=module';
            $this->tpl_ext        = '';
        } elseif (version_compare(VERSION, '2.3', '>=')) {
            $this->module_dir     = 'extension/module';
            $this->extension_dir  = 'extension/extension';
            $this->token_url      = 'token=' . $this->session->data['token'];
            $this->extension_type = '&type=module';
            $this->tpl_ext        = '.tpl';
        } else {
            $this->module_dir     = 'module';
            $this->extension_dir  = 'extension/module';
            $this->token_url      = 'token=' . $this->session->data['token'];
            $this->extension_type = '';
            $this->tpl_ext        = '.tpl';
        }

        $this->base_path     = $this->module_dir . '/' . $this->base_name;
        $this->resource_name = str_replace('_', '-', $this->base_name);
        $this->setting_code  = 'module_' . $this->base_name;
    }

    public function index()
    {
        $this->load->language($this->base_path);

        $language_values = array(
            'heading_title',
            'text_home',
            'text_module',
            'text_edit',
            'button_save',
            'button_apply',
            'button_cancel',
            'button_remove',
            'text_default',
            'text_confirm',
            'text_enabled',
            'text_disabled',
            'text_success',
            'error_permission',

            'text_general_setting',
            'text_status',
            'entry_show_pqv_button',
            'entry_show_on_mobile',
            'text_click_on_pqv_btn',
            'text_click_on_image',
            'entry_open_by_click_on_image',
            'entry_open_by_click_on_link',
            'text_customer_group',
            'entry_customer_group',
            'text_guest',
            'btn_select_all',
            'btn_unselect_all',
            'entry_close_when_added',
            'text_additional_setting',
            'text_auto_select_first_value',
            'text_auto_calculate_total',
            'entry_replace_button',

            'tab_layout',
            'text_popup_layout',
            'entry_popup_heading',
            'text_quick_view_text',
            'text_product_name',
            'text_product_image',
            'text_zoom_by_hover',
            'text_zoomed_image_size',
            'text_width',
            'text_height',
            'entry_show_description',
            'text_full',
            'text_limited',
            'entry_text_limit',
            'entry_show_specification',
            'entry_show_reviews',
            'entry_show_related',
            'entry_show_tags',
            'text_buttons_setting',
            'entry_show_wishlist_btn',
            'entry_show_compare_btn',
            'entry_show_continue_btn',
            'entry_show_more_info_btn',
            'text_quantity_show',

            'text_product_details',
            'entry_show_name',
            'entry_show_rating',
            'text_show_brand',
            'text_show_model',
            'entry_show_identifiers',
            'text_displayed_if_defined',
            'text_show_reward',
            'text_show_stock',
            'text_show_price',
            'text_show_tax',
            'text_show_points',
            'text_show_discounts',

            'text_language_settings',
            'entry_tooltip_quick_view',
            'text_tooltip_quick_view',
            'entry_btn_more_info',
            'text_btn_more_info',
            'entry_btn_continue',
            'text_btn_continue',
            'entry_btn_in_cart',
            'text_btn_in_cart',

            'text_styles_setting',
            'text_common_styles',
            'entry_basic_popup_styles',
            'entry_button_styles',
            'entry_pqv_btn_color',
            'entry_pqv_btn_style',
            'entry_loader_style',
            'entry_popup_window',
            'text_popup_width',
            'text_plain_text',
            'text_link',
            'text_button_size',
            'text_color_scheme',
            'text_bg_color',
            'text_font_color',
            'text_opacity',
            'text_border_style',
            'text_image_border_style',
            'text_border_color',
            'text_border_radius',
            'text_border_size',
            'entry_button_in_cart',
            'text_primary_buttons',
            'entry_button_default',
            'text_qnty_buttons',
            'text_other_buttons',
            'text_additional_styles',
            'text_custom_css',
            'text_custom_css_placeholder',

            'tab_support',
            'text_about',
            'text_module_version',
            'text_author',
            'text_homepage',
            'text_need_help',
            'text_contact_us',
            'text_provide_credentials',
        );

        foreach ($language_values as $value) {
            $data[$value] = $this->language->get($value);
        }

        $this->document->setTitle(strip_tags($data['heading_title']));

        $this->load->model('setting/setting');

        $settings = array();

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $settings = $this->request->post;

            foreach ($settings as $key => $value) {
                $config_key            = $this->setting_code . '_' . $key;
                $settings[$config_key] = $value;
            }

            $this->model_setting_setting->editSetting($this->setting_code, $settings);
            $this->session->data['success'] = $data['text_success'];
            $this->response->redirect($this->url->link($this->extension_dir, $this->token_url . $this->extension_type, 'SSL'));
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $data['text_home'],
            'href' => $this->url->link('common/dashboard', $this->token_url, 'SSL'),
        );

        $data['breadcrumbs'][] = array(
            'text' => $data['text_module'],
            'href' => $this->url->link($this->extension_dir, $this->token_url . $this->extension_type, 'SSL'),
        );

        $data['breadcrumbs'][] = array(
            'text' => $data['heading_title'],
            'href' => $this->url->link($this->base_path, $this->token_url, 'SSL'),
        );

        $data['action']     = $this->url->link($this->base_path, $this->token_url, 'SSL');
        $data['cancel']     = $this->url->link($this->extension_dir, $this->token_url . $this->extension_type, 'SSL');
        $data['apply_url']  = $this->url->link($this->base_path . '/apply&' . $this->token_url, '', 'SSL');
        $data['reload_url'] = $this->url->link($this->base_path . '/index&' . $this->token_url, '', 'SSL');

        $data['module_version'] = $this->version;
        $data['copyright']      = $this->author . ' © 2016-' . date('Y');
        $data['homepage']       = $this->homepage;
        $data['support_href']   = 'mailto:' . $this->support_email . '?Subject=Request Support: ' . $this->name . '&body=Shop: ' . HTTP_CATALOG . ', OpenCart: ' . VERSION . ', ' . $this->name . ': ' . $this->version;
        $data['support_email']  = $this->support_email;

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages(array('sort' => 'code'));

        foreach ($data['languages'] as $key => $language) {
            if (version_compare(VERSION, '2.2', '>=')) {
                $get_flag = 'language/' . $language['code'] . '/' . $language['code'] . '.png';
            } else {
                $get_flag = 'view/image/flags/' . $language['image'];
            }
            if (is_file($get_flag)) {
                $flag_img = $get_flag;
            } else {
                $flag_img = '';
            }
            $data['languages'][$key]['flag_img'] = $flag_img;
        }

        $default_configs = array(
            'status'                    => '0',
            'show_pqv_button'           => '1',
            'show_on_mobile'            => '1',
            'open_by_click_on_image'    => '0',
            'open_by_click_on_link'     => '0',
            'use_for_guests'            => '1',
            'use_for_customer_groups'   => array(),
            'popup_heading_set'         => '1',
            'show_product_image'        => '1',
            'zoom_status'               => '1',
            'zoom_width'                => '500',
            'zoom_height'               => '500',
            'show_description'          => '1',
            'description_limit'         => '500',
            'show_specification'        => '1',
            'show_reviews'              => '1',
            'show_related'              => '0',
            'show_tags'                 => '0',
            'show_name'                 => '1',
            'show_rating'               => '1',
            'show_brand'                => '1',
            'show_model'                => '1',
            'show_identifiers'          => '0',
            'show_reward'               => '1',
            'show_stock'                => '1',
            'show_price'                => '1',
            'show_tax'                  => '1',
            'show_points'               => '1',
            'show_discounts'            => '1',
            'auto_select_first_value'   => '0',
            'auto_calculate_total'      => '1',
            'replace_button'            => '0',
            'close_when_added'          => '0',
            'show_wishlist_btn'         => '1',
            'show_compare_btn'          => '1',
            'show_continue_btn'         => '1',
            'show_more_info_btn'        => '1',
            'show_quantity'             => '1',
            'tooltip_quick_view'        => array(),
            'btn_more_info'             => array(),
            'btn_continue'              => array(),
            'btn_in_cart'               => array(),
            'btn_quick_view_bg_color'   => '#1e91cf',
            'btn_quick_view_opacity'    => '90',
            'btn_quick_view_font_color' => '#ffffff',
            'btn_quick_view_size'       => '60',
            'loader_bg_color'           => '',
            'loader_font_color'         => '',
            'popup_bg_color'            => '',
            'popup_width'               => '',
            'popup_border_radius'       => '',
            'plain_text_color'          => '',
            'link_color'                => '',
            'image_border_color'        => '',
            'image_border_radius'       => '',
            'image_border_size'         => '',
            'buttons_bg_color'          => '',
            'buttons_font_color'        => '',
            'button_default_bg_color'   => '',
            'button_default_font_color' => '',
            'btn_in_cart_bg_color'      => '',
            'btn_in_cart_font_color'    => '',
            'btn_border_radius'         => '',
            'custom_css'                => '',
        );

        $get_setting = $this->model_setting_setting->getSetting($this->setting_code, $this->store_id);

        foreach ($default_configs as $key => $value) {
            $config_key = $this->setting_code . '_' . $key;
            if (empty($get_setting)) {
                $data[$key] = $value;
            } elseif (isset($get_setting[$config_key])) {
                $data[$key] = $get_setting[$config_key];
            } else {
                if (is_array($value)) {
                    $data[$key] = array();
                } else {
                    $data[$key] = '';
                }
            }
        }

        // Get Customer Groups
        if (version_compare(VERSION, '2.1', '>=')) {
            $this->load->model('customer/customer_group');
            $data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();
        } else {
            $this->load->model('sale/customer_group');
            $data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();
        }

        foreach ($data['customer_groups'] as $customer_group) {
            $group_id = $customer_group['customer_group_id'];
            if (isset($data['use_for_customer_groups'][$group_id])) {
                $data['use_for_customer_groups'][$group_id] = $data['use_for_customer_groups'][$group_id];
            } else {
                $data['use_for_customer_groups'][$group_id] = '0';
            }
        }

        $this->document->addStyle('view/javascript/' . $this->resource_name . '/minicolors/jquery.minicolors.css');
        $this->document->addScript('view/javascript/' . $this->resource_name . '/minicolors/jquery.minicolors.min.js');
        $this->document->addStyle('view/stylesheet/' . $this->resource_name . '.css');

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view($this->base_path . $this->tpl_ext, $data));
    }

    public function apply()
    {
        $settings = $this->request->post;
        $json     = array();

        $this->load->language($this->base_path);

        $this->load->model('setting/setting');

        if ($this->validate()) {
            foreach ($settings as $key => $value) {
                $config_key            = $this->setting_code . '_' . $key;
                $settings[$config_key] = $value;
            }
            $this->model_setting_setting->editSetting($this->setting_code, $settings);
            $json['success'] = $this->language->get('text_success') . ' --- [' . date("Y-m-d, H:i:s") . ']';
        } else {
            $json['error'] = $this->error['warning'] . ' --- [' . date("Y-m-d, H:i:s") . ']';
        }

        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', $this->base_path)) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function uninstall()
    {
        if ($this->validate()) {
            $this->load->model('setting/store');
            $this->load->model('setting/setting');

            $this->model_setting_setting->deleteSetting($this->setting_code, 0);

            $stores = $this->model_setting_store->getStores();

            foreach ($stores as $store) {
                $this->model_setting_setting->deleteSetting($this->setting_code, $store['store_id']);
            }
        }
    }
}
