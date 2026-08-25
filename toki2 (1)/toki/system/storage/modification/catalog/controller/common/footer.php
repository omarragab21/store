<?php
class ControllerCommonFooter extends Controller {
    public function index() {
        $this->load->language('common/footer');


                  $ocm = ($ocm = $this->registry->get('ocm_front')) ? $ocm : new OCM\Front($this->registry);
                  $data['_ocm_script'] = $ocm->getScript();
            
        $this->load->model('catalog/information');
        $this->load->model('catalog/category');

        $this->load->model('catalog/product');
        $data['direction'] = $this->language->get('direction');
$data['config_javascript2'] = html_entity_decode($this->config->get('config_javascript2'));  



$information_id = 4;
$information_info = $this->model_catalog_information->getInformation($information_id);
$data['description'] = utf8_substr(html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8'), 0, 300) . '';


        $data['informations'] = array();

        foreach ($this->model_catalog_information->getInformations() as $result) {
            if ($result['bottom']) {
                $data['informations'][] = array(
                    'title' => $result['title'],
                    'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
                );
            }
        }

        $data['informations1'] = array();
        foreach ($this->model_catalog_information->getInformations() as $result) {
            if ($result['information_id'] == 24 || $result['information_id'] == 25 || $result['information_id'] ==  26 || $result['information_id'] ==  27   ) {
                $data['informations1'][] = array(
                    'title' => $result['title'],
                    'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
                );
            }
        }

        $data['informations2'] = array();
        foreach ($this->model_catalog_information->getInformations() as $result) {
            if ($result['information_id'] == 28 || $result['information_id'] ==     29 || $result['information_id'] ==  30 || $result['information_id'] ==  31 || $result['information_id'] ==  32  ) {
                $data['informations2'][] = array(
                    'title' => $result['title'],
                    'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
                );
            }
        }


        $data['informations3'] = array();
        foreach ($this->model_catalog_information->getInformations() as $result) {
            if ($result['information_id'] == 14 || $result['information_id'] == 15 || $result['information_id'] == 16  || $result['information_id'] == 17 || $result['information_id'] == 18 || $result['information_id'] == 19 || $result['information_id'] == 20 || $result['information_id'] == 21 || $result['information_id'] == 22) {
                $data['informations3'][] = array(
                    'title' => $result['title'],
                    'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
                );
            }
        }

        $data['informations4'] = array();
        foreach ($this->model_catalog_information->getInformations() as $result) {
            if ($result['information_id'] == 38 || $result['information_id'] ==     3 || $result['information_id'] ==   5   ) {
                $data['informations4'][] = array(
                    'title' => $result['title'],
                    'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
                );
            }
        }
        $data['informations5'] = array();
        foreach ($this->model_catalog_information->getInformations() as $result) {
            if ($result['information_id'] == 33 || $result['information_id'] ==     34 || $result['information_id'] ==  35 || $result['information_id'] ==  36  || $result['information_id'] ==     37  ) {
                $data['informations5'][] = array(
                    'title' => $result['title'],
                    'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
                );
            }
        }

        $data['categories'] = array();

        $categories = $this->model_catalog_category->getCategories(0);

        foreach ($categories as $category) {
            if ($category['top']) {
                // Level 2
                $children_data = array();

                $children = $this->model_catalog_category->getCategories($category['category_id']);

                foreach ($children as $child) {
                    $filter_data = array(
                        'filter_category_id'  => $child['category_id'],
                        'filter_sub_category' => true
                    );

                    $children_data[] = array(
                        'name'  => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
                        'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
                    );
                }

                // Level 1
                $data['categories'][] = array(
                    'name'     => $category['name'],
                    'children' => $children_data,
                    'column'   => $category['column'] ? $category['column'] : 1,
                    'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
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


        $data['language'] = $this->load->controller('common/language');
        $data['contact'] = $this->url->link('information/contact');
        $data['return'] = $this->url->link('account/return/add', '', true);
        $data['sitemap'] = $this->url->link('information/sitemap');
        $data['tracking'] = $this->url->link('information/tracking');
        $data['manufacturer'] = $this->url->link('product/manufacturer');
        $data['voucher'] = $this->url->link('account/voucher', '', true);
        $data['affiliate'] = $this->url->link('affiliate/login', '', true);
        $data['special'] = $this->url->link('product/special');
        $data['account'] = $this->url->link('account/account', '', true);
        $data['order'] = $this->url->link('account/order', '', true);
        $data['wishlist'] = $this->url->link('account/wishlist', '', true);
        $data['newsletter'] = $this->url->link('account/newsletter', '', true);
        $data['seller'] = $this->url->link('account/seller', '', 'SSL');


        $data['address'] = nl2br($this->config->get('config_address'));
        $data['telephone'] = $this->config->get('config_telephone');
        $data['email'] = $this->config->get('config_email');
        $data['store'] = $this->config->get('config_name');


        $data['facebook'] = $this->config->get('config_facebook');
        $data['twitter'] = $this->config->get('config_twitter');
        $data['pint'] = $this->config->get('config_pint');
        $data['instagram'] = $this->config->get('config_instagram');
        $data['youtube'] = $this->config->get('config_youtube');
        $data['snapchat'] = $this->config->get('config_snapchat');
        $data['whatsapp'] = $this->config->get('config_whatsapp');
       // $data['javascript'] = $this->config->get('config_javascript');


        $data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));









        // Whos Online
        if ($this->config->get('config_customer_online')) {
            $this->load->model('tool/online');

            if (isset($this->request->server['REMOTE_ADDR'])) {
                $ip = $this->request->server['REMOTE_ADDR'];
            } else {
                $ip = '';
            }

            if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
                $url = ($this->request->server['HTTPS'] ? 'https://' : 'http://') . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
            } else {
                $url = '';
            }

            if (isset($this->request->server['HTTP_REFERER'])) {
                $referer = $this->request->server['HTTP_REFERER'];
            } else {
                $referer = '';
            }

            $this->model_tool_online->addOnline($ip, $this->customer->getId(), $url, $referer);
        }

        $data['scripts'] = $this->document->getScripts('footer');
$data['content_footer'] = $this->load->controller('common/content_footer');     
$data['content_foot2'] = $this->load->controller('common/content_foot2');       

				//newsletter 7-5-2019 code start
			$data['tmdnewsletter'] = $this->load->controller('extension/newsletter_popup');

		//newsletter 7-5-2019 code end
	
		
        return $this->load->view('common/footer', $data);
    }
}
