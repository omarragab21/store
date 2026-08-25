<?php

require_once(DIR_SYSTEM . 'library/additional_pages/page_name.class.php');

class AbstractAdditionalPageController extends Controller
{
    public $url_params = '';

    protected $module_name;
    protected $data;
    protected $enable_compatibility_with_money_maker;

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->module_name = 'product/' . $this->page_name;

        $this->enable_compatibility_with_money_maker = (bool) @$this->config->get('additional_pages_settings')['enable_compatibility_with_money_maker'];

        $this->loadLanguage();
        $this->loadModels();

        $this->document->setTitle($this->language->get('heading_title'));

        $this->url_params = $this->getAllUrlParams();

        $data = array();
        $data['breadcrumbs'] = $this->getBreadcrumbs();

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_empty'] = $this->language->get('text_empty');
        $data['text_quantity'] = $this->language->get('text_quantity');
        $data['text_manufacturer'] = $this->language->get('text_manufacturer');
        $data['text_model'] = $this->language->get('text_model');
        $data['text_price'] = $this->language->get('text_price');
        $data['text_tax'] = $this->language->get('text_tax');
        $data['text_points'] = $this->language->get('text_points');
        $data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
        $data['text_sort'] = $this->language->get('text_sort');
        $data['text_limit'] = $this->language->get('text_limit');

        $data['button_cart'] = $this->language->get('button_cart');
        $data['button_wishlist'] = $this->language->get('button_wishlist');
        $data['button_compare'] = $this->language->get('button_compare');
        $data['button_list'] = $this->language->get('button_list');
        $data['button_grid'] = $this->language->get('button_grid');
        $data['button_continue'] = $this->language->get('button_continue');

        $data['compare'] = $this->url->link('product/compare');

        if ($this->enable_compatibility_with_money_maker) {
            $data['moneymaker2_text_old_price'] = $this->language->get('text_old_price');
            $data['moneymaker2_modules_quickorder_enabled'] = $this->config->get('moneymaker2_modules_quickorder_enabled');

            if ($data['moneymaker2_modules_quickorder_enabled']) {
                $data['moneymaker2_modules_quickorder_display_catalog'] = $this->config->get('moneymaker2_modules_quickorder_display_catalog');
                $data['moneymaker2_modules_quickorder_button_title'] = $this->config->get('moneymaker2_modules_quickorder_button_title');
                $data['moneymaker2_modules_quickorder_button_title'] = isset($data['moneymaker2_modules_quickorder_button_title'][$this->config->get('config_language_id') ]) ? $data['moneymaker2_modules_quickorder_button_title'][$this->config->get('config_language_id') ] : null;
            }

            $data['moneymaker2_catalog_categories_images_hide'] = $this->config->get('moneymaker2_catalog_categories_images_hide');
            $data['moneymaker2_catalog_categories_move_description'] = $this->config->get('moneymaker2_catalog_categories_move_description');
            $data['moneymaker2_common_categories_icons_enabled'] = $this->config->get('moneymaker2_common_categories_icons_enabled');
            $data['moneymaker2_common_categories_icons'] = $this->config->get('moneymaker2_common_categories_icons');
            $data['moneymaker2_catalog_default_view'] = $this->config->get('moneymaker2_catalog_layout_default');
            $data['moneymaker2_catalog_layout_switcher_hide'] = $this->config->get('moneymaker2_catalog_layout_switcher_hide');
            $data['moneymaker2_common_buy_hide'] = $this->config->get('moneymaker2_common_buy_hide');
            $data['moneymaker2_common_wishlist_hide'] = $this->config->get('moneymaker2_common_wishlist_hide');
            $data['moneymaker2_common_wishlist_caption'] = $this->config->get('moneymaker2_common_wishlist_caption');
            $data['moneymaker2_common_compare_hide'] = $this->config->get('moneymaker2_common_compare_hide');
            $data['moneymaker2_common_compare_caption'] = $this->config->get('moneymaker2_common_compare_caption');
            $data['moneymaker2_common_cart_outofstock_disabled'] = $this->config->get('moneymaker2_common_cart_outofstock_disabled');
            $data['moneymaker2_common_price_detached'] = $this->config->get('moneymaker2_common_price_detached');
            $data['moneymaker2_stickers_mode'] = $this->config->get('moneymaker2_modules_stickers_mode');
            $data['moneymaker2_stickers_size_catalog'] = $this->config->get('moneymaker2_modules_stickers_size_catalog');
        }

        $this->data = $data;
    }

    protected function loadLanguage()
    {
        $this->load->language($this->module_name);

        if ($this->enable_compatibility_with_money_maker) {
            $this->load->language('product/product');
            $this->load->language('extension/module/moneymaker2');
        }
    }

    protected function loadModels()
    {
        $this->load->model('module/additional_pages');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');

        if ($this->enable_compatibility_with_money_maker) {
            $this->load->model('catalog/information');
        }
    }

    protected function loadPageElements(&$data)
    {
        $data['continue'] = $this->url->link('common/home');

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');

        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
    }

    public function getFilterData($default_sort = 'p.date_added', $default_order = 'DESC')
    {
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = $default_sort;
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = $default_order;
        }

        $page = $this->getCurrentPageNumber();
        $limit = $this->getCurrentLimit();

        $filter_data = array(
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        );
        return $filter_data;
    }

    protected function getCurrentPageNumber()
    {
        $page_num = 1;
        if (isset($this->request->get['page'])) {
            $page_num = $this->request->get['page'];
        }
        return $page_num;
    }

    protected function getCurrentLimit()
    {
        $limit = $this->getConfigProductLimit();

        if (isset($this->request->get['limit'])) {
            $lim = $this->request->get['limit'];
            if ($lim == '15' || $lim == '25' || $lim == '50' || $lim == '75' || $lim == '100') {
                $limit = (int)$this->request->get['limit'];
            }
        }
        return $limit;
    }

    protected function getConfigProductLimit()
    {
    	if (version_compare(VERSION, '3.0.0.0', '>=')) {
			return $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');

    	} else if (version_compare(VERSION, '2.2.0.0', '>=')) {
            return $this->config->get($this->config->get('config_theme') . '_product_limit');
        }
        
        return $this->config->get('config_product_limit');
    }

    protected function getConfigProductDescriptionLength()
    {
        if (version_compare(VERSION, '2.2.0.0', '>=')) {
            return $this->config->get($this->config->get('config_theme') . '_product_description_length');
        }
        return $this->config->get('config_product_description_length');
    }

    /**
     * @return string $url_params (concatenated GET parameters)
     */
    protected function getAllUrlParams()
    {
        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        return $url;
    }

    public function getBreadcrumbs()
    {
        $breadcrumbs = array();

        $breadcrumbs[] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
        );
        $breadcrumbs[] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link($this->module_name, $this->url_params),
        );

        return $breadcrumbs;
    }

    public function getLimits()
    {
        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $possibleLimits = array_unique(array($this->getConfigProductLimit(), 25, 50, 75, 100));

        sort($possibleLimits);

        $limits = array();
        foreach ($possibleLimits as $value) {
            $limits[] = array(
                'text' => $value,
                'value' => $value,
                'href' => $this->url->link($this->module_name, $url . '&limit=' . $value),
            );
        }

        return $limits;
    }


    public function getProductsResults(&$results)
    {
        $products = array();
        foreach ($results as &$result) {
            $products[] = $this->getProductInfo($result);
        }

        return $products;
    }

    protected function getProductInfo(&$result)
    {
        $config_img_width = $this->config->get('config_image_product_width');
        $config_img_height = $this->config->get('config_image_product_height');
        $config_customer_price = $this->config->get('config_customer_price');
        $config_tax = $this->config->get('config_tax');
        $config_review_status = $this->config->get('config_review_status');

        $config_theme = $this->config->get('config_theme');

        if (version_compare(VERSION, '3.0.0.0', '>=')) {
            $config_img_width = $this->config->get('theme_' . $config_theme . '_image_product_width');
            $config_img_height = $this->config->get('theme_' . $config_theme . '_image_product_height');

        } else if (version_compare(VERSION, '2.2.0.0', '>=')) {
            $config_img_width = $this->config->get($config_theme . '_image_product_width');
            $config_img_height = $this->config->get($config_theme . '_image_product_height');
        }

        if ($result['image']) {
            $image = $this->model_tool_image->resize($result['image'], $config_img_width, $config_img_height);
        } else {
            $image = $this->model_tool_image->resize('placeholder.png', $config_img_width, $config_img_height);
        }

        if (($config_customer_price && $this->customer->isLogged()) || !$config_customer_price) {
            $price = $this->formatPrice($result['price'], $result['tax_class_id'], $config_tax);
        } else {
            $price = false;
        }

        if ((float)$result['special']) {
            $special = $this->formatPrice((float)$result['special'], $result['tax_class_id'], $config_tax);
        } else {
            $special = false;
        }

        if ($this->config->get('config_tax')) {
            $tax = $this->formatPriceForTax((float)$result['special'] ? $result['special'] : $result['price']);
        } else {
            $tax = false;
        }

        if ($config_review_status) {
            $rating = (int)$result['rating'];
        } else {
            $rating = false;
        }

                    if($result['quantity'] <= 0){
                       // $result['stock'] =  $result['stock_status'];
                        $result['stock'] = $this->language->get('text_stock_out');
                    }else{
                        $result['stock'] = false;
                    }

                    if(strtotime($result['date_added']) > (time() - (60*60*24*7) )){
                $is_new = true;
            } else {
                $is_new = false;
            }

$this->load->model('catalog/category');
$categories = $this->model_catalog_product->getCategories($result['product_id']);
if($categories){
$categories_info = $this->model_catalog_category->getCategory($categories[0]['category_id']);
$category_adi = $categories_info['name'];
$category_link = $this->url->link('product/category', 'path=' . $categories_info['category_id']);
}else{
$category_adi = '';
$category_link = '';
}  

        $money_maker_product_settings = array();

        if ($this->enable_compatibility_with_money_maker) {
            $moneymaker2_stickers = array();

            if ($special) {
                if ($this->config->get('moneymaker2_modules_stickers_specials_enabled')) {
                    $moneymaker2_modules_stickers_specials_caption = $this->config->get('moneymaker2_modules_stickers_specials_caption');
                    $moneymaker2_modules_stickers_specials_discount = $this->config->get('moneymaker2_modules_stickers_specials_discount') ? ($this->config->get('moneymaker2_modules_stickers_specials_discount_mode') ? "-" . round(100 - (($result['special'] / $result['price']) * 100)) . "%" : "-" . $this->currency->format((($result['special']) - ($result['price'])) * (-1) , $this->session->data['currency'])) : '';
                    $moneymaker2_stickers[] = array(
                        'type' => 'special',
                        'icon' => $this->config->get('moneymaker2_modules_stickers_specials_icon') ,
                        'caption' => $this->config->get('moneymaker2_modules_stickers_specials_discount') ? "<b>" . $moneymaker2_modules_stickers_specials_discount . "</b> " . (isset($moneymaker2_modules_stickers_specials_caption[$this->config->get('config_language_id') ]) ? $moneymaker2_modules_stickers_specials_caption[$this->config->get('config_language_id') ] : null) : (isset($moneymaker2_modules_stickers_specials_caption[$this->config->get('config_language_id') ]) ? $moneymaker2_modules_stickers_specials_caption[$this->config->get('config_language_id') ] : null) ,
                    );
                }
            }

            if ($result['viewed']) {
                if ($this->config->get('moneymaker2_modules_stickers_popular_enabled')) {
                    if ($result['viewed'] >= $this->config->get('moneymaker2_modules_stickers_popular_limit')) {
                        $moneymaker2_modules_stickers_popular_caption = $this->config->get('moneymaker2_modules_stickers_popular_caption');
                        $moneymaker2_stickers[] = array(
                            'type' => 'popular',
                            'icon' => $this->config->get('moneymaker2_modules_stickers_popular_icon') ,
                            'caption' => isset($moneymaker2_modules_stickers_popular_caption[$this->config->get('config_language_id') ]) ? $moneymaker2_modules_stickers_popular_caption[$this->config->get('config_language_id') ] : null,
                        );
                    }
                }
            }

            if ($result['rating']) {
                if ($this->config->get('moneymaker2_modules_stickers_rated_enabled')) {
                    if ($result['rating'] >= $this->config->get('moneymaker2_modules_stickers_rated_limit')) {
                        $moneymaker2_modules_stickers_rated_caption = $this->config->get('moneymaker2_modules_stickers_rated_caption');
                        $moneymaker2_stickers[] = array(
                            'type' => 'rated',
                            'icon' => $this->config->get('moneymaker2_modules_stickers_rated_icon') ,
                            'caption' => isset($moneymaker2_modules_stickers_rated_caption[$this->config->get('config_language_id') ]) ? $moneymaker2_modules_stickers_rated_caption[$this->config->get('config_language_id') ] : null,
                        );
                    }
                }
            }

            if ($result['date_available']) {
                if ($this->config->get('moneymaker2_modules_stickers_new_enabled')) {
                    if ((round((strtotime(date("Y-m-d")) - strtotime($result['date_available'])) / 86400)) <= $this->config->get('moneymaker2_modules_stickers_new_limit')) {
                        $moneymaker2_modules_stickers_new_caption = $this->config->get('moneymaker2_modules_stickers_new_caption');
                        $moneymaker2_stickers[] = array(
                            'type' => 'new',
                            'icon' => $this->config->get('moneymaker2_modules_stickers_new_icon') ,
                            'caption' => isset($moneymaker2_modules_stickers_new_caption[$this->config->get('config_language_id') ]) ? $moneymaker2_modules_stickers_new_caption[$this->config->get('config_language_id') ] : null,
                        );
                    }
                }
            }

            if (isset($result[$this->config->get('moneymaker2_modules_stickers_custom1_field') ]) && $result[$this->config->get('moneymaker2_modules_stickers_custom1_field') ]) {
                if ($this->config->get('moneymaker2_modules_stickers_custom1_enabled')) {
                    $moneymaker2_modules_stickers_custom1_caption = $this->config->get('moneymaker2_modules_stickers_custom1_caption');
                    $moneymaker2_stickers[] = array(
                        'type' => 'custom1',
                        'icon' => $this->config->get('moneymaker2_modules_stickers_custom1_icon') ,
                        'caption' => isset($moneymaker2_modules_stickers_custom1_caption[$this->config->get('config_language_id') ]) ? $moneymaker2_modules_stickers_custom1_caption[$this->config->get('config_language_id') ] : null,
                    );
                }
            }

            if (isset($result[$this->config->get('moneymaker2_modules_stickers_custom2_field') ]) && $result[$this->config->get('moneymaker2_modules_stickers_custom2_field') ]) {
                if ($this->config->get('moneymaker2_modules_stickers_custom2_enabled')) {
                    $moneymaker2_modules_stickers_custom2_caption = $this->config->get('moneymaker2_modules_stickers_custom2_caption');
                    $moneymaker2_stickers[] = array(
                        'type' => 'custom2',
                        'icon' => $this->config->get('moneymaker2_modules_stickers_custom2_icon') ,
                        'caption' => isset($moneymaker2_modules_stickers_custom2_caption[$this->config->get('config_language_id') ]) ? $moneymaker2_modules_stickers_custom2_caption[$this->config->get('config_language_id') ] : null,
                    );
                }
            }

            if ($result['quantity'] <= 0) {
                $moneymaker2_stock = "<span class='stock'><span>" . $result['stock_status'] . "</span></span>";
            } else {
                $moneymaker2_stock = "<span class='stock instock'><!--" . $this->language->get('text_stock') . "--> <span>" . $this->language->get('text_instock') . "</span></span>";
            }

            if ($this->config->get('moneymaker2_catalog_products_code_field') && isset($result[$this->config->get('moneymaker2_catalog_products_code_field') ]) && $result[$this->config->get('moneymaker2_catalog_products_code_field') ] && (!$this->config->get('moneymaker2_catalog_products_list_code_hide') || !$this->config->get('moneymaker2_catalog_products_grid_code_hide'))) {
                $moneymaker2_code = "<span class='code'>" . $this->language->get('text_model') . " <span>" . $result[$this->config->get('moneymaker2_catalog_products_code_field') ] . "</span></span>";
            } else {
                $moneymaker2_code = '';
            }

            $moneymaker2_addtocart_tooltip = '';

            if ($this->data['moneymaker2_common_cart_outofstock_disabled'] && $result['quantity'] <= 0) {
                if (!$this->data['moneymaker2_common_price_detached']) {
                    $moneymaker2_addtocart_tooltip = "<p class='text-muted'>" . $this->data['button_cart'] . "</p>";
                }

                $moneymaker2_addtocart_tooltip.= "<p>" . $moneymaker2_stock . "</p>";
            } else if (!$this->data['moneymaker2_common_price_detached']) {
                $moneymaker2_addtocart_tooltip.= "<p>" . $this->data['button_cart'] . "</p>";
            }

            if ($price && $special) {
                $moneymaker2_addtocart_tooltip.= "<p>" . $this->data['moneymaker2_text_old_price'] . " " . $price . "</p>";
            }

            $moneymaker2_addtocart_class = 'btn ';

            if ($this->data['moneymaker2_common_cart_outofstock_disabled'] && $result['quantity'] <= 0) {
                $moneymaker2_addtocart_class.= 'disabled ';
            }

            if (!$special) {
                $moneymaker2_addtocart_class.= 'btn-primary';
            } else {
                $moneymaker2_addtocart_class.= 'btn-danger';
            }

            if ($this->data['moneymaker2_modules_quickorder_enabled']) {
                $moneymaker2_quickorder_tooltip = '';
                if ($this->data['moneymaker2_common_cart_outofstock_disabled'] && $result['quantity'] <= 0) {
                    $moneymaker2_quickorder_tooltip.= "<p class='text-muted'>" . $this->data['moneymaker2_modules_quickorder_button_title'] . "</p>";
                    $moneymaker2_quickorder_tooltip.= "<p>" . $moneymaker2_stock . "</p>";
                }
                else {
                    $moneymaker2_quickorder_tooltip.= "<p>" . $this->data['moneymaker2_modules_quickorder_button_title'] . "</p>";
                }

                $moneymaker2_quickorder_class = 'btn btn-default';
                if ($this->data['moneymaker2_common_cart_outofstock_disabled'] && $result['quantity'] <= 0) {
                    $moneymaker2_quickorder_class.= ' disabled';
                }
            }


            // Add 
            $money_maker_product_settings = array(
                'stickers' => $moneymaker2_stickers,
                'stock' => $moneymaker2_stock,
                'code' => $moneymaker2_code,
                'sold' => $this->data['moneymaker2_common_cart_outofstock_disabled'] && $result['quantity'] <= 0 ? true : false,
                'quantity' => $result['quantity'],
                'sort_order' => $result['sort_order'],
                'review_count' => $result['reviews'],
                'addtocart_tooltip' => $moneymaker2_addtocart_tooltip,
                'addtocart_class' => $moneymaker2_addtocart_class,
                'quickorder_tooltip' => $this->data['moneymaker2_modules_quickorder_enabled'] ? $moneymaker2_quickorder_tooltip : '',
                'quickorder_class' => $this->data['moneymaker2_modules_quickorder_enabled'] ? $moneymaker2_quickorder_class : '',
                'quickorder_tax' => (($this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], 0) , $this->session->data['currency']) !== $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], 1) , $this->session->data['currency'])) && (!$this->config->get('config_tax'))) ? true : false,
            );
        }

        return array_merge($money_maker_product_settings, array(
            'product_id' => $result['product_id'],
            'thumb' => $image,
            'timer' => @$result['timer'],
            'name' => $result['name'],
            'quantity' => $result['quantity'],
                    'stock' => $result['stock'],
                    'category_adi' => $category_adi,
                    'category_link' => $category_link, 
                    'is_new'      => $is_new,
                    
            'description' => utf8_substr(
                    strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')),
                    0,
                    $this->getConfigProductDescriptionLength()
                ) . '..',
            'price' => $price,
            'special' => $special,
            'tax' => $tax,
            'minimum' => $result['minimum'] > 0 ? $result['minimum'] : 1,
            'rating' => $rating,
            'href' => $this->url->link('product/product', 'product_id=' . $result['product_id'] . $this->url_params),
        ));
    }

    protected function formatPrice($price, $tax_class_id, $config_tax)
    {
        if (version_compare(VERSION, '2.2.0.0', '>=')) {
            $price = $this->currency->format(
                $this->tax->calculate($price, $tax_class_id, $config_tax),
                $this->session->data['currency']
            );
        } else {
            $price = $this->currency->format($this->tax->calculate($price, $tax_class_id, $config_tax));
        }
        return $price;
    }

    protected function formatPriceForTax($price)
    {
        if (version_compare(VERSION, '2.2.0.0', '>=')) {
            return $this->currency->format($price, $this->session->data['currency']);
        }
        return $this->currency->format($price);
    }

    protected function applyPagination(&$data, $product_total, $page, $limit)
    {
        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        $pagination = new Pagination();
        $pagination->total = $product_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->url = $this->url->link($this->module_name, $url . '&page={page}');

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'),
            ($product_total)
                ? (($page - 1) * $limit) + 1
                : 0,
            ((($page - 1) * $limit) > ($product_total - $limit))
                ? $product_total
                : ((($page - 1) * $limit) + $limit),
            $product_total,
            ceil($product_total / $limit));

        // http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
        if ($page == 1) {
            $this->document->addLink($this->url->link($this->module_name, '', 'SSL'), 'canonical');
        } elseif ($page == 2) {
            $this->document->addLink($this->url->link($this->module_name, '', 'SSL'), 'prev');
        } else {
            $this->document->addLink($this->url->link($this->module_name, 'page=' . ($page - 1), 'SSL'), 'prev');
        }

        if ($limit && ceil($product_total / $limit) > $page) {
            $this->document->addLink($this->url->link($this->module_name, 'page=' . ($page + 1), 'SSL'), 'next');
        }
    }

    protected function appendSortBy(&$sorts, &$other_url_params, $text, $field, $order_type)
    {
        $sorts[] = array(
            'text' => $text,
            'value' => sprintf('%s-%s', $field, $order_type),
            'href' => $this->url->link($this->module_name, sprintf('sort=%s&order=%s%s', $field, $order_type, $other_url_params))
        );
    }

    protected function setOutput(&$data)
    {
        return $this->response->setOutput($this->load->view('product/special', $data));
    }
}
