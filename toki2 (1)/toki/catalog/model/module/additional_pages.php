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

class ModelModuleAdditionalPages extends Model
{
    const DEBUG = false;

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->load->model('catalog/product');

        require_once(DIR_SYSTEM . 'library/additional_pages/page_name.class.php');
    }

    public function getLatest($data = array())
    {
        $customer_group_id = $this->getCustomerGroupId();
        $config_lang_id = $this->getConfigLanguageId();
        $config_store_id = $this->getConfigStoreId();

        $key = sprintf('product.latestp.%s.%s.%s.%s', md5(serialize(array($data, $this->getSettings()))), $config_lang_id, $config_store_id, $customer_group_id);
        $product_data = $this->cache->get($key);

        $global_limit = $this->getGlobalLimit(AdditionalPageName::LATEST);
        $filter_by_last_x_months = $this->getSqlFilterByLastMonths(AdditionalPageName::LATEST);

        if (!$product_data || self::DEBUG) {
            $sql = "
              SELECT *
              FROM (
                SELECT p.product_id,
                  p.sort_order,
                  p.date_added,
                  p.model,
                  pd.name,
                  p.quantity,
                  p.price,
                  (
                    SELECT price
                    FROM " . DB_PREFIX . "product_discount pd2
                    WHERE pd2.product_id = p.product_id
                      AND pd2.customer_group_id = '" . (int)$customer_group_id . "'
                      AND pd2.quantity = '1'
                      AND (
                        (pd2.date_start = '0000-00-00' OR pd2.date_start < NOW())
                        AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())
                      )
                    ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1
                  ) AS discount,
                  (
                    SELECT price
                    FROM " . DB_PREFIX . "product_special ps
                    WHERE ps.product_id = p.product_id
                      AND ps.customer_group_id = '" . (int)$customer_group_id . "'
                      AND (
                        (ps.date_start = '0000-00-00' OR ps.date_start < NOW())
                        AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())
                      )
                    ORDER BY ps.priority ASC, ps.price ASC LIMIT 1)
                  AS special,
                  (
                    SELECT AVG(rating) AS total
                    FROM " . DB_PREFIX . "review r1
                    WHERE r1.product_id = p.product_id
                      AND r1.status = '1'
                    GROUP BY r1.product_id
                  ) AS rating
                  
                FROM " . DB_PREFIX . "product p
                LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
                LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
                WHERE p.status = '1'
                  AND p.date_available <= NOW()
                  AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
                  AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
                  " . $filter_by_last_x_months . "
                ORDER BY p.date_added DESC
                $global_limit
              ) p ";

            $this->applyOrderBy($sql, $data, $default_order_by_column = " sort_order");
            $this->applyPageLimit($sql, $data);

            $query = $this->db->query($sql);

            $product_data = array();
            foreach ($query->rows as &$result) {
                $product_data[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
            }

            $this->cache->set($key, $product_data);
        }

        return $product_data;
    }

    public function getBestSellers($data)
    {
        $customer_group_id = $this->getCustomerGroupId();
        $config_lang_id = $this->getConfigLanguageId();
        $config_store_id = $this->getConfigStoreId();

        $key = sprintf('product.bestsellers.%s.%s.%s.%s', md5(serialize(array($data, $this->getSettings()))), $config_lang_id, $config_store_id, $customer_group_id);

        $product_data = $this->cache->get($key);

        if (!$product_data || self::DEBUG) {
            $global_limit = $this->getGlobalLimit(AdditionalPageName::BESTSELLERS);
            $filter_by_last_x_months = $this->getSqlFilterByLastMonths(AdditionalPageName::BESTSELLERS);

            $sql = "
              SELECT * FROM (
                SELECT
                  DISTINCT p.product_id
                FROM (
                  SELECT
                    p.product_id,
                    p.sort_order,
                    p.price,
                    p.model, 
                    (
                      SELECT price
                      FROM " . DB_PREFIX . "product_discount pd2
                      WHERE pd2.product_id = p.product_id
                        AND pd2.customer_group_id = '" . (int)$customer_group_id . "'
                        AND pd2.quantity = '1'
                        AND (
                          (pd2.date_start = '0000-00-00' OR pd2.date_start < NOW())
                          AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())
                        )
                      ORDER BY pd2.priority ASC, pd2.price ASC
                      LIMIT 1
                    ) AS discount, 
                    (
                      SELECT ps.price
                      FROM " . DB_PREFIX . "product_special ps
                      WHERE ps.product_id = p.product_id
                        AND ps.customer_group_id = '" . (int)$customer_group_id . "'
                        AND (
                          (ps.date_start = '0000-00-00' OR ps.date_start < NOW())
                          AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())
                        )
                      ORDER BY ps.priority ASC, ps.price ASC
                      LIMIT 1
                    ) AS special,  
                    (
                      SELECT AVG(rating)
                      FROM " . DB_PREFIX . "review r1
                      WHERE r1.product_id = p.product_id
                        AND r1.status = '1'
                      GROUP BY r1.product_id
                    ) AS rating, 
                    SUM(op.quantity) AS total
              
                  FROM " . DB_PREFIX . "order_product op
                  LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id)
                  LEFT JOIN `" . DB_PREFIX . "product` p ON (op.product_id = p.product_id)
                  LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
                  WHERE o.order_status_id > '0'
                    AND p.status = '1'
                    AND p.date_available <= NOW()
                    AND p2s.store_id = '" . $config_store_id . "'
                    " . $filter_by_last_x_months . "
                  GROUP BY op.product_id
                  ORDER BY total DESC
                ) p
                LEFT JOIN " . DB_PREFIX . "product_description pd
                  ON (p.product_id = pd.product_id AND pd.language_id = '" . $config_lang_id . "')
            ";
            $this->applyOrderBy($sql, $data, $default_order_by_column = ' p.date_added', $default_tail_order_by = ' DESC, LCASE(name) ASC');

            $sql .= " $global_limit ) products_alias ";

            $this->applyPageLimit($sql, $data);

            $query = $this->db->query($sql);

            $product_data = array();
            foreach ($query->rows as &$result) {
                $product_data[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
            }

            $this->cache->set($key, $product_data);
        }

        return $product_data;
    }

    public function getTotalBestSellers($data)
    {
        $customer_group_id = $this->getCustomerGroupId();
        $config_store_id = $this->getConfigStoreId();
        $config_lang_id = $this->getConfigLanguageId();

        $key = sprintf('product.totalbestsellers.%s.%s.%s.%s', md5(serialize(array($data, $this->getSettings()))), $config_lang_id, $config_store_id, $customer_group_id);
        $total = $this->cache->get($key);

        if (!$total || self::DEBUG) {
            $global_limit = $this->getGlobalLimit(AdditionalPageName::BESTSELLERS);
            $filter_by_last_x_months = $this->getSqlFilterByLastMonths(AdditionalPageName::BESTSELLERS);

            $sql = "
              SELECT COUNT(total) as total
              FROM (
                SELECT COUNT(DISTINCT op.product_id) AS total
                FROM " . DB_PREFIX . "order_product op
                LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id)
                LEFT JOIN `" . DB_PREFIX . "product` p ON (op.product_id = p.product_id)
                LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
                WHERE o.order_status_id > '0'
                  AND p.status = '1'
                  AND p.date_available <= NOW() 
                  AND p2s.store_id = '" . $config_store_id . "'
                  " . $filter_by_last_x_months . "
              GROUP BY op.product_id
              ORDER BY total DESC
              " . $global_limit . "
            ) bp ";
            $query = $this->db->query($sql);
            $total = $query->row['total'];

            $this->cache->set($key, $total);
        }

        return $total;
    }

    public function getMostViewed($data)
    {
        $customer_group_id = $this->getCustomerGroupId();
        $config_lang_id = $this->getConfigLanguageId();
        $config_store_id = $this->getConfigStoreId();

        $global_limit = $this->getGlobalLimit(AdditionalPageName::POPULAR);
        $filter_by_last_x_months = $this->getSqlFilterByLastMonths(AdditionalPageName::POPULAR);

        $sql = "
          SELECT *
          FROM (
            SELECT p.product_id, 
              (
                SELECT AVG(rating) AS total
                FROM " . DB_PREFIX . "review r1
                WHERE r1.product_id = p.product_id AND r1.status = '1'
                GROUP BY r1.product_id
              ) AS rating,
              (
                SELECT price FROM " . DB_PREFIX . "product_discount pd2 
                WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . $customer_group_id . "' 
                  AND pd2.quantity = '1'
                  AND (pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())
                ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1
              ) AS discount, 
              (
                SELECT price
                FROM " . DB_PREFIX . "product_special ps 
                WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . $customer_group_id . "' 
                  AND (ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()) 
                ORDER BY ps.priority ASC, ps.price ASC LIMIT 1
              ) AS special,
              p.sort_order,
              p.viewed,
              p.price,
              p.model
            FROM " . DB_PREFIX . "product p   
            LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) 
            WHERE p.status = '1'
              AND p.date_available <= NOW()
              AND p2s.store_id = '" . $config_store_id . "'
              " . $filter_by_last_x_months . "
            GROUP BY p.product_id
            ORDER by p.viewed DESC
            " . $global_limit . "
          ) p
          LEFT JOIN " . DB_PREFIX . "product_description pd
            ON (p.product_id = pd.product_id AND pd.language_id = '" . $config_lang_id . "') ";

        $this->applyOrderBy($sql, $data, $default_order_by_column = ' p.sort_order', $default_tail_order_by = ' ASC, LCASE(name) ASC ');
        $this->applyPageLimit($sql, $data);

        $query = $this->db->query($sql);

        $product_data = array();
        foreach ($query->rows as $result) {
            $product_data[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
        }

        return $product_data;
    }

    public function getTotalMostViewed($data)
    {
        $global_limit = $this->getGlobalLimit(AdditionalPageName::POPULAR);
        $filter_by_last_x_months = $this->getSqlFilterByLastMonths(AdditionalPageName::POPULAR);

        $sql = "
          SELECT COUNT(mv.product_id) as total 
          FROM (
            SELECT DISTINCT p.product_id
            FROM " . DB_PREFIX . "product p 
            LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) 
            WHERE p.status = '1'
              AND p.date_available <= NOW()  
              AND p2s.store_id = '" . $this->getConfigStoreId() . "' 
              " . $filter_by_last_x_months . "
            GROUP BY p.product_id
            ORDER BY p.viewed DESC
            " . $global_limit . "
          ) mv ";

        $query = $this->db->query($sql);
        $total = $query->row['total'];
        return $total;
    }

    /**
     * Taken from catalog/models/catalog/product.php in order to include the limit of possible products from the config.
     *
     * @param array $data - filter parameters
     * @param string $page_name - name of additional page
     * @return int $total_products
     */
    public function getTotalProducts($data = array(), $page_name = AdditionalPageName::ALL_PRODUCTS)
    {
        //$sql = "SELECT COUNT(DISTINCT p.product_id) AS total";
        $sql = "SELECT COUNT(product_id) as total FROM ( SELECT DISTINCT p.product_id ";

        if (!empty($data['filter_category_id'])) {
            if (!empty($data['filter_sub_category'])) {
                $sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
            } else {
                $sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
            }

            if (!empty($data['filter_filter'])) {
                $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
            } else {
                $sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
            }
        } else {
            $sql .= " FROM " . DB_PREFIX . "product p";
        }

        $sql .= "
            LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
            LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
            WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
              AND p.status = '1'
              AND p.date_available <= NOW()
              AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

        if (!empty($data['filter_category_id'])) {
            if (!empty($data['filter_sub_category'])) {
                $sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
            } else {
                $sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
            }

            if (!empty($data['filter_filter'])) {
                $implode = array();

                $filters = explode(',', $data['filter_filter']);

                foreach ($filters as $filter_id) {
                    $implode[] = (int)$filter_id;
                }

                $sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
            }
        }

        if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
            $sql .= " AND (";

            if (!empty($data['filter_name'])) {
                $implode = array();

                $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

                foreach ($words as $word) {
                    $implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
                }

                if ($implode) {
                    $sql .= " " . implode(" AND ", $implode) . "";
                }

                if (!empty($data['filter_description'])) {
                    $sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
                }
            }

            if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
                $sql .= " OR ";
            }

            if (!empty($data['filter_tag'])) {
                $sql .= "pd.tag LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_tag'])) . "%'";
            }

            if (!empty($data['filter_name'])) {
                $sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                $sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
            }

            $sql .= ")";
        }

        if (!empty($data['filter_manufacturer_id'])) {
            $sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
        }

        $sql .= sprintf(' %s ', $this->getSqlFilterByLastMonths($page_name));
        $sql .= sprintf(' %s ', $this->getGlobalLimit($page_name));

        $sql .= ' ) total_self_alias ';

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    private function getGlobalLimit($page_name)
    {
        $module_settings = $this->getSettings();
        $limit_by = @$module_settings[$page_name]['limit_by'];

        if ($limit_by == 'quantity') {
            return sprintf(' LIMIT %s ', @$module_settings[$page_name]['max_quantity']);
        }
    }

    private function getSqlFilterByLastMonths($page_name = AdditionalPageName::ALL_PRODUCTS)
    {
        $module_settings = $this->getSettings();
        $limit_by = @$module_settings[$page_name]['limit_by'];

        if ($limit_by == 'last_months') {
            $last_x_months = @$module_settings[$page_name]['last_x_months'];
            return sprintf(' AND (p.date_added >= NOW() - INTERVAL %s MONTH AND p.date_added <= NOW()) ', $last_x_months);
        }
    }

    private function applyOrderBy(&$sql, &$data, $default_order_by_column, $default_tail_order_by = ' ASC, LCASE(name) ASC')
    {
        $sort_data = array(
            'p.total',
            'pd.name',
            'quantity',
            'ps.price',
            'rating',
            'p.sort_order',
            'p.model'
        );

        $sql .= " ORDER BY ";

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            if ($data['sort'] == 'pd.name') {
                $sql .= " LCASE('pd.name')";
            } elseif ($data['sort'] == 'ps.price') {
                $sql .= "
                    (CASE
                        WHEN special IS NOT NULL
                            THEN special
                        WHEN discount IS NOT NULL
                            THEN discount
                        ELSE p.price END
                    )";
            } else {
                $sql .= " " . $data['sort'];
            }
        } else {
            $sql .= $default_order_by_column;
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC, LCASE(name) DESC";
        } else {
            $sql .= $default_tail_order_by;
        }
    }

    /**
     * Apply limit in order to request products for specific page
     *
     * @return void
     */
    private function applyPageLimit(&$sql, &$data)
    {
        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }
    }

    private function getSettings()
    {
        return $this->config->get('additional_pages_settings');
    }

    /**
     * @return int $customer_group_id (current customer group ID)
     */
    private function getCustomerGroupId()
    {
        if ($this->customer->isLogged()) {
            return $this->customer->getGroupId();
        }
        return $this->config->get('config_customer_group_id');
    }

    private function getConfigLanguageId()
    {
        return (int)$this->config->get('config_language_id');
    }

    private function getConfigStoreId()
    {
        return (int)$this->config->get('config_store_id');
    }
}
