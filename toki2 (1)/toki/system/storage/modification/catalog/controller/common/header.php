<?php
class ControllerCommonHeader extends Controller {
    public function index() {
        // Analytics
        $this->load->model('setting/extension');
$this->load->model('setting/setting');
        $data['analytics'] = array();

        $analytics = $this->model_setting_extension->getExtensions('analytics');

        foreach ($analytics as $analytic) {
            if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
                $data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
            }
        }

        if ($this->request->server['HTTPS']) {
            $server = $this->config->get('config_ssl');
        } else {
            $server = $this->config->get('config_url');
        }

        if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
            $this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
        }

        $data['title'] = $this->document->getTitle();

        $data['base'] = $server;
        $data['description'] = $this->document->getDescription();
        $data['keywords'] = $this->document->getKeywords();
        $data['links'] = $this->document->getLinks();

        // Product Quick View BEGIN
        $data['pqv_status'] = $this->config->get('module_product_quick_view_status');
        if ($data['pqv_status']) {
            $pqv_data = $this->load->controller('extension/module/product_quick_view');
            $pqv_data = str_replace(array("\r", "\n"), "", $pqv_data);
            $data['pqv_data'] = preg_replace('/\s\s+/', '', $pqv_data);
        }
        // Product Quick View END

        $data['styles'] = $this->document->getStyles();
        $data['scripts'] = $this->document->getScripts('header');
        $data['lang'] = $this->language->get('code');
        $data['direction'] = $this->language->get('direction');

        $data['main_title'] = $this->config->get('config_main_title' . $this->config->get('config_language_id'));

$data['config_javascript'] = html_entity_decode($this->config->get('config_javascript'));  


        $data['name'] = $this->config->get('config_name');

        if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
            $data['logo'] = $server . 'image/' . $this->config->get('config_logo');
        } else {
            $data['logo'] = '';
        }

        $this->load->language('common/header');

        // Wishlist
        if ($this->customer->isLogged()) {
            $this->load->model('account/wishlist');

            $data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
        } else {
            $data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
        }

        $data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));

        $data['home'] = $this->url->link('common/home');
        $data['wishlist'] = $this->url->link('account/wishlist', '', true);
        $data['special'] = $this->url->link('product/special', '', true);
        $data['logged'] = $this->customer->isLogged();
        $data['account'] = $this->url->link('account/account', '', true);
        $data['register'] = $this->url->link('account/register', '', true);
        $data['login'] = $this->url->link('account/login', '', true);
        $data['order'] = $this->url->link('account/order', '', true);
        $data['transaction'] = $this->url->link('account/transaction', '', true);
        $data['download'] = $this->url->link('account/download', '', true);
        $data['logout'] = $this->url->link('account/logout', '', true);
        $data['shopping_cart'] = $this->url->link('checkout/cart');
        $data['checkout'] = $this->url->link('checkout/checkout', '', true);
        $data['contact'] = $this->url->link('information/contact');
        $data['telephone'] = $this->config->get('config_telephone');
        $data['seller'] = $this->url->link('account/seller', '', 'SSL');

        $data['all_cat'] = 'index.php?route=product/category&path=0';


        $data['language'] = $this->load->controller('common/language');
        $data['currency'] = $this->load->controller('common/currency');
        $data['search'] = $this->load->controller('common/search');
        $data['cart'] = $this->load->controller('common/cart');
        $data['menu'] = $this->load->controller('common/menu');
$data['content_header'] = $this->load->controller('common/content_header');

        $data['facebook'] = $this->config->get('config_facebook');
        $data['twitter'] = $this->config->get('config_twitter');
        $data['pint'] = $this->config->get('config_pint');
        $data['instagram'] = $this->config->get('config_instagram');
        $data['youtube'] = $this->config->get('config_youtube');
        $data['snapchat'] = $this->config->get('config_snapchat');
        $data['whatsapp'] = $this->config->get('config_whatsapp');
       // $data['javascript'] = $this->config->get('config_javascript');


        if (isset($this->request->get['route'])) {
            if (isset($this->request->get['product_id'])) {
                $class = '-' . $this->request->get['product_id'];
            } elseif (isset($this->request->get['path'])) {
                $class = '-' . $this->request->get['path'];
            } elseif (isset($this->request->get['manufacturer_id'])) {
                $class = '-' . $this->request->get['manufacturer_id'];
            } else {
                $class = '';
            }

            $data['class'] = str_replace('/', '-', $this->request->get['route']) . $class;
        } else {
            $data['class'] = 'common-home';
        }


        $data['informations3'] = array();
        foreach ($this->model_catalog_information->getInformations() as $result) {
            if ($result['information_id'] == 39 || $result['information_id'] == 6) {
                $data['informations3'][] = array(
                    'title' => $result['title'],
                    'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
                );
            }
        }


        $data['categories2'] = array();

        $categories_1 = $this->model_catalog_category->getCategories(0);

        foreach ($categories_1 as $category_1) {
            $level_2_data = array();

            $categories_2 = $this->model_catalog_category->getCategories($category_1['category_id']);

            foreach ($categories_2 as $category_2) {
                $level_3_data = array();

                $categories_3 = $this->model_catalog_category->getCategories($category_2['category_id']);

                foreach ($categories_3 as $category_3) {
                    $level_3_data[] = array(
                        'name' => $category_3['name'],
                        'href' => $this->url->link('product/category', 'path=' . $category_1['category_id'] . '_' . $category_2['category_id'] . '_' . $category_3['category_id'])
                    );
                }

                $level_2_data[] = array(
                    'name'     => $category_2['name'],
                    'children' => $level_3_data,
                    'href'     => $this->url->link('product/category', 'path=' . $category_1['category_id'] . '_' . $category_2['category_id'])
                );
            }

            $data['categories2'][] = array(
                'name'     => $category_1['name'],
                'children' => $level_2_data,
                'href'     => $this->url->link('product/category', 'path=' . $category_1['category_id'])
            );
        }

        // 3 Level Category Search
       // $data['categories'] = array();

        $data['categories_1'] = array();
        $categories_1 = $this->model_catalog_category->getCategories(0);

        foreach ($categories_1 as $category_1) {
            $level_2_data = array();

            $categories_2 = $this->model_catalog_category->getCategories($category_1['category_id']);

            foreach ($categories_2 as $category_2) {
                $level_3_data = array();

                $categories_3 = $this->model_catalog_category->getCategories($category_2['category_id']);

                foreach ($categories_3 as $category_3) {
                    $level_3_data[] = array(
                        'category_id' => $category_3['category_id'],
                        'name'        => $category_3['name'],
                    );
                }

                $level_2_data[] = array(
                    'category_id' => $category_2['category_id'],
                    'name'        => $category_2['name'],
                    'children'    => $level_3_data
                );
            }

            $data['categories_1'][] = array(
                'category_id' => $category_1['category_id'],
                'name'        => $category_1['name'],
                'children'    => $level_2_data
            );
        }


        return $this->load->view('common/header', $data);
    }




}



