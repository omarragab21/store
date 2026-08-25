<?php
class ControllerExtensionModuleProductQuickView extends Controller
{

    private $base_name = 'product_quick_view';

    private $module_dir;
    private $base_path;
    private $resource_name;
    private $setting_code;

    public function __construct($registry)
    {
        parent::__construct($registry);

        if (version_compare(VERSION, '2.3', '>=')) {
            $this->module_dir = 'extension/module';
        } else {
            $this->module_dir = 'module';
        }

        $this->base_path     = $this->module_dir . '/' . $this->base_name;
        $this->resource_name = str_replace('_', '-', $this->base_name);
        $this->setting_code  = 'module_' . $this->base_name;
    }

    public function index()
    {
        if (!$this->customer->isLogged()) {
            if (!$this->config->get('module_product_quick_view_use_for_guests')) {
                return false;
            }
        } else {
            $customer_id = (int) $this->customer->getId();
            $this->load->model('account/customer');

            $customer_group = (int) $this->model_account_customer->getCustomer($customer_id);

            $allowed_groups = $this->config->get('module_product_quick_view_use_for_customer_groups');

            if (!isset($allowed_groups[$customer_group])) {
                return false;
            }
        }

        $language_code = $this->session->data['language'];

        $pqv_text_quick_view = $this->config->get('module_product_quick_view_tooltip_quick_view');

        if (!empty($pqv_text_quick_view[$language_code])) {
            $data['pqv_text_quick_view'] = $pqv_text_quick_view[$language_code];
        } else {
            $data['pqv_text_quick_view'] = 'Quick View';
        }

        $pqv_btn_in_cart = $this->config->get('module_product_quick_view_btn_in_cart');

        if (!empty($pqv_btn_in_cart[$language_code])) {
            $data['pqv_btn_in_cart'] = $pqv_btn_in_cart[$language_code];
        } else {
            $data['pqv_btn_in_cart'] = 'In Cart';
        }

        $data['text_loading'] = $this->language->get('text_loading');

        $data['pqv_show_button']    = (int) $this->config->get('module_product_quick_view_show_pqv_button');
        $data['pqv_show_on_mobile'] = (int) $this->config->get('module_product_quick_view_show_on_mobile');
        $data['pqv_click_on_image'] = (int) $this->config->get('module_product_quick_view_open_by_click_on_image');
        $data['pqv_click_on_link']  = (int) $this->config->get('module_product_quick_view_open_by_click_on_link');
        $data['pqv_replace_button'] = (int) $this->config->get('module_product_quick_view_replace_button');

        // Get Styles
        $data = array_merge($data, $this->style());

        // Get current theme directory
        if (version_compare(VERSION, '2.2', '>=')) {
            if ($this->config->get('config_theme') === 'default' || $this->config->get('config_theme') === 'theme_default') {
                $theme_dir = $this->config->get('theme_default_directory');
            } else {
                $theme_dir = str_replace('theme_', '', $this->config->get('config_theme'));
            }
        } else {
            $theme_dir = $this->config->get('config_template');
        }

        // Load resources
        if (version_compare(VERSION, '3.0', '>=')) {
            $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
            $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
        } else {
            $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js');
        }

        $this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');
        $this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
        $this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
        $this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');

        if (file_exists(DIR_TEMPLATE . $theme_dir . '/stylesheet/' . $this->resource_name . '.css')) {
            $this->document->addStyle('catalog/view/theme/' . $theme_dir . '/stylesheet/' . $this->resource_name . '.css');
        } else {
            $this->document->addStyle('catalog/view/theme/default/stylesheet/' . $this->resource_name . '.css');
        }

        if (file_exists(DIR_TEMPLATE . $theme_dir . '/js/' . $this->resource_name . '.js')) {
            $this->document->addScript('catalog/view/theme/' . $theme_dir . '/js/' . $this->resource_name . '.js');
        } else {
            $this->document->addScript('catalog/view/theme/default/js/' . $this->resource_name . '.js');
        }

        // Load template
        if (version_compare(VERSION, '2.2', '>=')) {
            return $this->load->view($this->base_path . '/preload', $data);
        } else {
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/' . $this->base_path . '/preload.tpl')) {
                return $this->load->view($this->config->get('config_template') . '/template/' . $this->base_path . '/preload.tpl', $data);
            } else {
                return $this->load->view('default/template/' . $this->base_path . '/preload.tpl', $data);
            }
        }
    }

    public function popup()
    {
        // Get Product ID
        if (isset($this->request->get['product_id'])) {
            $product_id = (int) $this->request->get['product_id'];
        } else {
            $product_id = 0;
        }

        $data['product_id'] = $product_id;

        $this->load->language('checkout/cart');

        $data['text_total']    = $this->language->get('column_total');
        $data['text_quantity'] = $this->language->get('column_quantity');

        // Get Product Info
        $this->load->model('catalog/product');

        $product_info = $this->model_catalog_product->getProduct($product_id);

        $data['product_page_url'] = $this->url->link('product/product', '&product_id=' . $product_id);
        $data['product_name']     = $product_info['name'];
        $data['manufacturer']     = $product_info['manufacturer'];
        $data['manufacturers']    = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
        $data['model']            = $product_info['model'];
        $data['sku']              = $product_info['sku'];
        $data['upc']              = $product_info['upc'];
        $data['ean']              = $product_info['ean'];
        $data['jan']              = $product_info['jan'];
        $data['isbn']             = $product_info['isbn'];
        $data['mpn']              = $product_info['mpn'];
        $data['reward']           = $product_info['reward'];
        $data['points']           = $product_info['points'];

        $this->load->language('product/product');

        $data['text_select']            = $this->language->get('text_select');
        $data['text_manufacturer']      = $this->language->get('text_manufacturer');
        $data['text_model']             = $this->language->get('text_model');
        $data['text_reward']            = $this->language->get('text_reward');
        $data['text_points']            = $this->language->get('text_points');
        $data['text_stock']             = $this->language->get('text_stock');
        $data['text_discount']          = $this->language->get('text_discount');
        $data['text_tax']               = $this->language->get('text_tax');
        $data['text_option']            = $this->language->get('text_option');
        $data['text_minimum']           = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
        $data['text_login']             = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));
        $data['text_note']              = $this->language->get('text_note');
        $data['text_tags']              = $this->language->get('text_tags');
        $data['text_related']           = $this->language->get('text_related');
        $data['text_payment_recurring'] = $this->language->get('text_payment_recurring');
        $data['text_loading']           = $this->language->get('text_loading');
        $data['entry_qty']              = $this->language->get('entry_qty');
        $data['entry_name']             = $this->language->get('entry_name');
        $data['entry_review']           = $this->language->get('entry_review');
        $data['button_cart']            = $this->language->get('button_cart');
        $data['entry_rating']           = $this->language->get('entry_rating');
        $data['entry_good']             = $this->language->get('entry_good');
        $data['entry_bad']              = $this->language->get('entry_bad');
        $data['button_wishlist']        = $this->language->get('button_wishlist');
        $data['button_compare']         = $this->language->get('button_compare');
        $data['button_upload']          = $this->language->get('button_upload');
        $data['button_continue']        = $this->language->get('button_continue');

        if ($product_info['quantity'] <= 0) {
            $data['stock'] = $product_info['stock_status'];
        } elseif ($this->config->get('config_stock_display')) {
            $data['stock'] = $product_info['quantity'];
        } else {
            $data['stock'] = $this->language->get('text_instock');
        }

        $data['tab_description'] = $this->language->get('tab_description');
        $data['tab_attribute']   = $this->language->get('tab_attribute');
        $data['tab_review']      = sprintf($this->language->get('tab_review'), $product_info['reviews']);

        // Get module config
        $module_configs = array(
            'show_pqv_button'         => '1',
            'open_by_click_on_image'  => '0',
            'open_by_click_on_link'   => '0',
            'popup_heading_set'       => '1',
            'show_product_image'      => '1',
            'zoom_status'             => '1',
            'zoom_width'              => '500',
            'zoom_height'             => '500',
            'show_description'        => '1',
            'description_limit'       => '500',
            'show_specification'      => '1',
            'show_reviews'            => '1',
            'show_related'            => '0',
            'show_tags'               => '0',
            'show_name'               => '1',
            'show_rating'             => '1',
            'show_brand'              => '1',
            'show_model'              => '1',
            'show_identifiers'        => '0',
            'show_reward'             => '1',
            'show_stock'              => '1',
            'show_price'              => '1',
            'show_tax'                => '1',
            'show_points'             => '1',
            'show_discounts'          => '1',
            'auto_select_first_value' => '0',
            'auto_calculate_total'    => '1',
            'replace_button'          => '0',
            'close_when_added'        => '0',
            'show_wishlist_btn'       => '1',
            'show_compare_btn'        => '1',
            'show_continue_btn'       => '1',
            'show_more_info_btn'      => '1',
            'show_quantity'           => '1',
            'tooltip_quick_view'      => array(),
            'btn_more_info'           => array(),
            'btn_continue'            => array(),
            'btn_in_cart'             => array(),
        );

        foreach ($module_configs as $key => $value) {
            $config_key = $this->setting_code . '_' . $key;
            if ($this->config->get($config_key) !== null) {
                $data[$key] = $this->config->get($config_key);
            } else {
                $data[$key] = $value;
            }
        }

        if ($data['zoom_status'] === '1') {
            $data['zoom_class'] = 'pqv-zoom';
        } else {
            $data['zoom_class'] = '';
        }

        $language_code = $this->session->data['language'];

        if (!empty($this->config->get('module_product_quick_view_tooltip_quick_view')[$language_code])) {
            $data['pqv_text_quick_view'] = $this->config->get('module_product_quick_view_tooltip_quick_view')[$language_code];
        } else {
            $data['pqv_text_quick_view'] = 'Quick View';
        }

        if ($data['popup_heading_set'] === '1') {
            $data['popup_heading'] = $data['pqv_text_quick_view'];
        } else {
            $data['popup_heading'] = $data['product_name'];
        }

        if (!empty($this->config->get('module_product_quick_view_btn_more_info')[$language_code])) {
            $data['btn_more_info'] = $this->config->get('module_product_quick_view_btn_more_info')[$language_code];
        } else {
            $data['btn_more_info'] = 'More Details';
        }

        if (!empty($this->config->get('module_product_quick_view_btn_continue')[$language_code])) {
            $data['btn_continue'] = $this->config->get('module_product_quick_view_btn_continue')[$language_code];
        } else {
            $data['btn_continue'] = $data['button_continue'];
        }

        $pqv_btn_in_cart = $this->config->get('module_product_quick_view_btn_in_cart');

        if (!empty($pqv_btn_in_cart[$language_code])) {
            $data['btn_in_cart'] = $pqv_btn_in_cart[$language_code];
        } else {
            $data['btn_in_cart'] = 'In Cart';
        }

        $data['is_in_cart'] = false;

        if ($data['replace_button']) {
            $products_in_cart = array();

            foreach ($this->cart->getProducts() as $product) {
                $products_in_cart[] = $product['product_id'];
            }

            if ($products_in_cart && in_array($product_id, $products_in_cart)) {
                $data['is_in_cart'] = true;
            }
        }

        if ($data['show_description']) {
            $data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');
        } else {
            $data['description'] = utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, (int) $data['description_limit']) . '...';
        }

        $data['review_status'] = $this->config->get('config_review_status');

        if ($this->config->get('config_review_guest') || $this->customer->isLogged()) {
            $data['review_guest'] = true;
        } else {
            $data['review_guest'] = false;
        }

        if ($this->customer->isLogged()) {
            $data['customer_name'] = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();
        } else {
            $data['customer_name'] = '';
        }

        $data['reviews'] = sprintf($this->language->get('text_reviews'), (int) $product_info['reviews']);
        $data['rating']  = (int) $product_info['rating'];

        $this->load->model('tool/image');

        $image_count = 0;

        $data['main_image'] = $product_info['image'];

        if ($data['main_image']) {
            $data['thumb'] = $this->model_tool_image->resize($data['main_image'], $data['zoom_width'], $data['zoom_height']);
            $image_count++;
        } else {
            $data['thumb'] = $this->model_tool_image->resize('no_image.png', $data['zoom_width'], $data['zoom_height']);
        }

        $data['images'] = array();

        $product_images = $this->model_catalog_product->getProductImages($product_id);

        foreach ($product_images as $result) {
            $data['images'][] = array(
                'thumb' => $this->model_tool_image->resize($result['image'], $data['zoom_width'], $data['zoom_height']),
            );
            $image_count++;
        }

        $data['image_count'] = $image_count;

        if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
            $data['price']         = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            $data['price_default'] = $data['price'];
        } else {
            $data['price']         = false;
            $data['price_default'] = false;
        }

        $data['raw_price'] = $this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'));

        if ((float) $product_info['special']) {
            $data['special']         = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            $data['special_default'] = $data['special'];
        } else {
            $data['special']         = false;
            $data['special_default'] = false;
        }

        if ($this->config->get('config_tax')) {
            $data['tax'] = $this->currency->format((float) $product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
        } else {
            $data['tax'] = false;
        }

        $discounts = $this->model_catalog_product->getProductDiscounts($product_id);

        $data['discounts'] = array();

        foreach ($discounts as $discount) {
            $data['discounts'][] = array(
                'quantity' => $discount['quantity'],
                'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),
            );
        }

        if ($product_info['minimum']) {
            $data['minimum'] = $product_info['minimum'];
        } else {
            $data['minimum'] = 1;
        }

        $data['recurrings']       = $this->model_catalog_product->getProfiles($product_id);
        $data['share']            = $this->url->link('product/product', 'product_id=' . (int) $product_id);
        $data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($product_id);

        $data['related_products'] = array();

        $related_products = $this->model_catalog_product->getProductRelated($product_id);

        foreach ($related_products as $result) {
            if ($result['image']) {
                $image = $this->model_tool_image->resize($result['image'], 120, 120);
            } else {
                $image = $this->model_tool_image->resize('placeholder.png', 120, 120);
            }

            if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $price = false;
            }

            if ((float) $result['special']) {
                $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $special = false;
            }

            if ($this->config->get('config_tax')) {
                $tax = $this->currency->format((float) $result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
            } else {
                $tax = false;
            }

            if ($this->config->get('config_review_status')) {
                $rating = (int) $result['rating'];
            } else {
                $rating = false;
            }

            $data['related_products'][] = array(
                'product_id'  => $result['product_id'],
                'thumb'       => $image,
                'name'        => $result['name'],
                'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get($this->config->get('config_theme') . '_product_description_length')) . '..',
                'price'       => $price,
                'special'     => $special,
                'tax'         => $tax,
                'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
                'rating'      => $rating,
                'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id']),
            );
        }

        $data['tags'] = array();

        $tags_count = 0;

        if ($product_info['tag']) {
            $tags = explode(',', $product_info['tag']);

            foreach ($tags as $tag) {
                $data['tags'][] = array(
                    'tag'  => trim($tag),
                    'href' => $this->url->link('product/search', 'tag=' . trim($tag)),
                );
                $tags_count++;
            }
        }

        $data['tags_count'] = $tags_count;

        // Get Options
        $data['options'] = array();

        foreach ($this->model_catalog_product->getProductOptions($product_id) as $option) {
            $product_option_value_data = array();

            foreach ($option['product_option_value'] as $option_value) {
                if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                    if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float) $option_value['price']) {
                        $price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
                    } else {
                        $price = false;
                    }

                    $product_option_value_data[] = array(
                        'price_value'             => $option_value['price'],
                        'points_value'            => intval($option_value['points_prefix'] . $option_value['points']),
                        'product_option_value_id' => $option_value['product_option_value_id'],
                        'option_value_id'         => $option_value['option_value_id'],
                        'name'                    => $option_value['name'],
                        'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
                        'price'                   => $price,
                        'price_prefix'            => $option_value['price_prefix'],
                    );
                }
            }

            $data['options'][] = array(
                'product_option_id'    => $option['product_option_id'],
                'product_option_value' => $product_option_value_data,
                'option_id'            => $option['option_id'],
                'name'                 => $option['name'],
                'type'                 => $option['type'],
                'value'                => $option['value'],
                'required'             => $option['required'],
            );
        }

        // Live Price Update
        if ($data['price']) {
            $data['price'] = '<span class=\'autocalc-product-price\'>' . $data['price'] . '</span>';
        }
        if ($data['special']) {
            $data['special'] = '<span class=\'autocalc-product-special\'>' . $data['special'] . '</span>';
        }
        if ($data['points']) {
            $data['points'] = '<span class=\'autocalc-product-points\'>' . $data['points'] . '</span>';
        }
        if ($data['tax']) {
            $data['tax'] = '<span class=\'autocalc-product-tax\'>' . $data['tax'] . '</span>';
        }

        $data['price_value']   = (float) $product_info['price'];
        $data['special_value'] = (float) $product_info['special'];
        $data['tax_value']     = (float) $product_info['special'] ? $product_info['special'] : $product_info['price'];
        $data['points_value']  = (float) $product_info['points'];

        $var_currency = array();

        $currency_code = !empty($this->session->data['currency']) ? $this->session->data['currency'] : $this->config->get('config_currency');
        $symbol_left   = $this->currency->getSymbolLeft($currency_code);
        $symbol_right  = $this->currency->getSymbolRight($currency_code);

        $var_currency['symbol_left']  = str_replace("'", "\'", $symbol_left);
        $var_currency['symbol_right'] = str_replace("'", "\'", $symbol_right);

        $var_currency['value']          = $this->currency->getValue($currency_code);
        $var_currency['decimals']       = $this->currency->getDecimalPlace($currency_code);
        $var_currency['decimal_point']  = $this->language->get('decimal_point');
        $var_currency['thousand_point'] = $this->language->get('thousand_point');

        $data['autocalc_currency'] = $var_currency;
        $data['dicounts_unf']      = $discounts;
        $data['tax_class_id']      = $product_info['tax_class_id'];
        $data['tax_rates']         = $this->tax->getRates(0, $product_info['tax_class_id']);

        // Load template
        if (version_compare(VERSION, '2.2', '>=')) {
            $this->response->setOutput($this->load->view($this->base_path . '/popup', $data));
        } else {
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/' . $this->base_path . '/popup.tpl')) {
                $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/' . $this->base_path . '/popup.tpl', $data));
            } else {
                $this->response->setOutput($this->load->view('default/template/' . $this->base_path . '/popup.tpl', $data));
            }
        }
    }

    public function style()
    {
        $style_configs = array(
            'btn_quick_view_bg_color'      => '',
            'btn_quick_view_opacity'       => '',
            'btn_quick_view_font_color'    => '',
            'btn_quick_view_size'          => '',
            'loader_bg_color'              => '',
            'loader_font_color'            => '',
            'popup_bg_color'               => '',
            'popup_width'                  => '',
            'popup_border_radius'          => '',
            'plain_text_color'             => '',
            'link_color'                   => '',
            'image_border_color'           => '',
            'image_border_radius'          => '',
            'image_border_size'            => '',
            'buttons_bg_color'             => '',
            'buttons_font_color'           => '',
            'button_default_bg_color'      => '',
            'button_default_font_color'    => '',
            'btn_in_cart_bg_color'         => '',
            'btn_in_cart_font_color'       => '',
            'btn_border_radius'            => '',
            'related_bg_color'             => '',
            'related_font_color'           => '',
            'related_thumb_bg_color'       => '',
            'related_thumb_font_color'     => '',
            'related_addtocart_bg_color'   => '',
            'related_addtocart_font_color' => '',
            'related_border_color'         => '',
            'related_border_radius'        => '',
            'related_border_size'          => '',
            'custom_css'                   => '',
        );

        foreach ($style_configs as $key => $value) {
            $config_key = $this->setting_code . '_' . $key;
            if ($this->config->get($config_key) !== null) {
                $data['pqv_' . $key] = $this->config->get($config_key);
            } else {
                $data['pqv_' . $key] = $value;
            }
        }

        // Set default size for Quick View button and for its font
        if ($data['pqv_btn_quick_view_size'] <= 0) {
            $data['pqv_btn_quick_view_size'] = 60;
        }
        $data['pqv_btn_quick_view_font_size'] = (int) ($data['pqv_btn_quick_view_size'] / 2.2);

        // Set opacity
        $pqv_btn_opacity = (int) $data['pqv_btn_quick_view_opacity'];

        if ($pqv_btn_opacity >= 0 && $pqv_btn_opacity <= 100) {
            $data['pqv_btn_quick_view_opacity']    = $pqv_btn_opacity / 100;
            $data['pqv_btn_quick_view_opacity_ie'] = $pqv_btn_opacity;
        } else {
            $data['pqv_btn_quick_view_opacity']    = '.9';
            $data['pqv_btn_quick_view_opacity_ie'] = '90';
        }

        return $data;
    }
}
