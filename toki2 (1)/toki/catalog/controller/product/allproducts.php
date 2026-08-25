<?php

require_once(DIR_SYSTEM . 'library/additional_pages/abstract_additional_page_controller.class.php');

class ControllerProductAllProducts extends AbstractAdditionalPageController
{
    const PAGE_NAME = AdditionalPageName::ALL_PRODUCTS;

	public $page_name = self::PAGE_NAME;

    public function index()
    {
        $data = $this->data;

        $filter_data = $this->getFilterData($default_sort = 'p.sort_order', $default_order = 'ASC');

        // $product_total = $this->model_module_additional_pages->getTotalProducts($filter_data, self::PAGE_NAME);
        // $results = $this->model_module_additional_pages->getLatest($filter_data);
        $product_total = $this->model_catalog_product->getTotalProducts($filter_data);
        $results = $this->model_catalog_product->getProducts($filter_data);

        $data['products'] = $this->getProductsResults($results);

        $data['sort'] = $filter_data['sort'];
        $data['order'] = $filter_data['order'];
        $data['limit'] = $filter_data['limit'];
        $page = $this->getCurrentPageNumber();

        $url = '';
        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        $sorts = array();
        $this->appendSortBy($sorts, $url, $this->language->get('text_default'), 'p.sort_order', 'ASC');
        $this->appendSortBy($sorts, $url, $this->language->get('text_name_asc'), 'pd.name', 'ASC');
        $this->appendSortBy($sorts, $url, $this->language->get('text_name_desc'), 'pd.name', 'DESC');
        $this->appendSortBy($sorts, $url, $this->language->get('text_price_asc'), 'p.price', 'ASC');
        $this->appendSortBy($sorts, $url, $this->language->get('text_price_desc'), 'p.price', 'DESC');

        if ($this->config->get('config_review_status')) {
            $this->appendSortBy($sorts, $url, $this->language->get('text_rating_desc'), 'rating', 'DESC');
            $this->appendSortBy($sorts, $url, $this->language->get('text_rating_asc'), 'rating', 'ASC');
        }

        $this->appendSortBy($sorts, $url, $this->language->get('text_model_asc'), 'p.model', 'ASC');
        $this->appendSortBy($sorts, $url, $this->language->get('text_model_desc'), 'p.model', 'DESC');

        $data['sorts'] = $sorts;

        $data['limits'] = $this->getLimits();

        $this->applyPagination($data, $product_total, $page, $data['limit']);

        $this->loadPageElements($data);

        $this->setOutput($data);
    }
}
