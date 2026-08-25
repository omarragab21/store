<?php
class ControllerExtensionModuleProductcategory extends Controller {
	public function index($setting) {
		static $module = 0;
		$this->load->language('extension/module/productcategory');

		$data['lang'] = $this->language->get('code');

		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_tax'] = $this->language->get('text_tax');
		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['categories'] = array();

		if (!$setting['limit']) {
			$setting['limit'] = 4;
		}

		if (!empty($setting['category'])) {
			$categories = array_slice($setting['category'], 0, (int)$setting['limit']);

			foreach ($categories as $category_id) {
				$category_info = $this->model_catalog_category->getCategory($category_id);

				if ($category_info) {

					$products = array();

					$filter_data = array(
						'filter_category_id' => $category_id,
						'filter_sub_category' => true,
						'start'              => 0,
						'limit'              => $setting['limit']
					);

					$product_total = $this->model_catalog_product->getTotalProducts($filter_data);

					$results = $this->model_catalog_product->getProducts($filter_data);


					foreach ($results as $result) {
						if($result){
						if ($result['image']) {
							$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
						} else {
							$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
						}

						if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
							$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
						} else {
							$price = false;
						}

						if ((float)$result['special']) {
							$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
						} else {
							$special = false;
						}

						if ($this->config->get('config_tax')) {
							$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
						} else {
							$tax = false;
						}

						if ($this->config->get('config_review_status')) {
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
						$products[] = array(
							'product_id'  => $result['product_id'],
							'thumb'       => $image,
							'name'        => $result['name'],
							'quantity' => $result['quantity'],
							'stock' => $result['stock'],
							'express' => $result['express'],
							'category_adi' => $category_adi,
							'category_link' => $category_link, 
							'is_new'      => $is_new,
							'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
							'price'       => $price,
							'special'     => $special,
							'tax'         => $tax,
							'rating'      => $rating,
							'reviews_num'      =>  (int)$result['reviews'],
							'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
							);
						}
				 	}
					$data['categories'][] = array(
						'category_id' => $category_info['category_id'],
						'products'	  => $products,
						'name'        => $category_info['name'],
						'href'        => $this->url->link('product/category', 'path=' . $category_info['category_id'])
					);
				}
			}
		}
		if ($data['categories']) {
			$data['module'] = $module++;
				return $this->load->view('extension/module/productcategory', $data);

		}

		
	}
}
