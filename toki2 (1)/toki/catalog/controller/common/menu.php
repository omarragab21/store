<?php
class ControllerCommonMenu extends Controller {
    public function index() {
        $this->load->language('common/menu');

        // Menu
        $this->load->model('catalog/category');

        $this->load->model('catalog/product');

        $data['special'] = $this->url->link('product/special', '', true);
        $data['latest'] = $this->url->link('product/latest', '', true);
        $data['latest'] = $this->url->link('product/latest', '', true);
        $data['bestseller'] = $this->url->link('product/bestseller', '', true);

        $data['contact'] = $this->url->link('information/contact');
        $data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));

        $data['home'] = $this->url->link('common/home');
        $data['products_cat'] = $this->url->link('product/category&path=0');
        $data['manufacturer'] = $this->url->link('product/manufacturer');

        $this->load->model('catalog/information');

        $data['informations'] = array();

        foreach ($this->model_catalog_information->getInformations() as $result) {
            if ( $result['information_id'] == 4 ) {
                $data['informations'][] = array(
                    'title' => $result['title'],
                    'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
                );
            }
        }

        $this->load->model('catalog/category');

        $this->load->model('catalog/product');

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


            $this->load->model('tool/image');
            if($category['image']) {
                $image = $this->model_tool_image->resize($category['image'], 32, 32 );
            } else {
                $image = $this->model_tool_image->resize('no_image.png', 32, 32 );
            }

            // Level 1
            $data['categories'][] = array(
                'name'     => $category['name'],
                'image'     => $image,
                'children' => $children_data,
                'column'   => $category['column'] ? $category['column'] : 1,
                'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
            );
        }
        }

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


        $data['categories2'] = array();

        $categories_1 = $this->model_catalog_category->getCategories(0);

        foreach ($categories_1 as $category_1) {
            $this->load->model('tool/image');
            if($category_1['image']) {
                $image = $this->model_tool_image->resize($category_1['image'], 32, 32 );
            } else {
                $image = $this->model_tool_image->resize('no_image.png', 32, 32 );
            }
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
                'image'     => $image,
                'children' => $level_2_data,
                'href'     => $this->url->link('product/category', 'path=' . $category_1['category_id'])
            );

        }

        return $this->load->view('common/menu', $data);
    }
}
