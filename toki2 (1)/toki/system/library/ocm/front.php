<?php
namespace OCM;
final class Front {
    private $registry;
    private $modules = array(
        'xshippingpro' => array('type' => 'shipping', 'flag' => 'script'),
        'xoffer'       => array('type' => 'total', 'flag' => 'script'),
        'xpayment'     => array('type' => 'payment', 'flag' => 'script'),
        'xgift'        => array('type' => 'module', 'flag' => 'script,shortcode'),
        'xlevel'       => array('type' => 'module', 'flag' => 'shortcode,order'),
        'xbundle'      => array('type' => 'module', 'flag' => 'script'),
        'xdiscount'    => array('type' => 'module', 'flag' => 'script'),
        'restricted'   => array('type' => 'module', 'flag' => 'script'),
        'xform'        => array('type' => 'module', 'flag' => 'script,shortcode'),
        'refertoget'   => array('type' => 'module', 'flag' => 'shortcode,order')
    );
    public function __construct($registry) {
        $this->registry = $registry;
        $this->config = $registry->get('config');
        $this->load = $registry->get('load');
        $this->route = $this->getRoute();
        $this->setting_ext = (VERSION >= '3.0.0.0') ? 'setting' : 'extension';
        $this->registry->set('ocm_front', $this);
        $this->common = new Common($registry);
    }
    public function __get($name) {
        return $this->registry->get($name);
    }
    public function getConfig($key, $prefix = '') {
        return $this->common->getConfig($key, $prefix);
    }
    public function view($route, $data = array()) {
        if (VERSION < '2.2.0.0') {
            $theme = file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/' . $route . '.tpl') ? $this->config->get('config_template') : 'default';
            $route = $theme . '/template/' . $route . '.tpl';
        }
        return $this->load->view($route, $data);
    }

    public function getTotals($taxes, $total_mods = array()) {
        if (VERSION >= '2.3.0.0') {
            $path = 'extension/total/';
            $model = 'model_extension_total_';
        } else {
            $path = 'total/';
            $model = 'model_total_';
        }
        $_before_shipping = 0;
        $total = 0;
        $totals = array();
        if (VERSION >= '2.2.0.0') {
            $total_data = array(
                'totals' => &$totals,
                'taxes'  => &$taxes,
                'total'  => &$total
            );
        }

        if (!$total_mods) {
            $this->load->model($this->setting_ext . '/extension');
            $extension = $this->{'model_' . $this->setting_ext . '_extension'};
            $total_mods = $extension->getExtensions('total');
        }

        $sort_order = array();
        foreach ($total_mods as $key => $value) {
            $sort_order[$key] = $this->getConfig($value['code'] . '_sort_order', 'total');
        }
        array_multisort($sort_order, SORT_ASC, $total_mods);
        foreach ($total_mods as $total_mod) {
            $code = $total_mod['code'];
            if ($code == 'shipping') {
                $_before_shipping = $total;
            }
            if ($this->getConfig($code . '_status', 'total')) {
                $this->load->model($path . $code);
                if (VERSION >= '2.2.0.0') {
                    $this->{$model . $code}->getTotal($total_data);
                } else {
                    $this->{$model . $code}->getTotal($totals, $total, $taxes);
                }
            }
        }

        if (VERSION < '2.2.0.0') {
            $total_data = array(
                'totals' => $totals,
                'taxes'  => $taxes,
                'total'  => $total
            );
        }
        $total_data['_before_shipping'] = $_before_shipping;
        return $total_data;
    }
    private function getRoute() {
        $route =  isset($this->request->get['route']) ? $this->request->get['route'] : '';
        if (!$route && isset($this->request->get['_route_']) && $this->request->get['_route_']) {
            $route =  $this->request->get['_route_'];
        }
        if (!$route && isset($this->request->request['_route_']) && $this->request->request['_route_']) {
            $route =  $this->request->request['_route_'];
        }
        return $route;
    }
    public function isAdmin() {
        $this->route = $this->getRoute(); // recall it as route may not defiend while instantiating
        return strpos($this->route,'api') !== false ? true : false;
    }
    public function isCartPage() {
        $this->route = $this->getRoute();
        return strpos($this->route,'checkout/cart') !== false ? true : false;
    }
    public function isQuotePage() {
        $this->route = $this->getRoute();
        return strpos($this->route,'shipping/quote') !== false ? true : false;
    }
    public function isCheckoutPage() {
        $this->route = $this->getRoute();
        return !!preg_match('/.*checkout.*\/.*checkout.*/', $this->route);
    }
    public function isProductPage() {
        $this->route = $this->getRoute();
        return strpos($this->route,'product/product') !== false ? true : false;
    }
    public function sendMail($data) {
        if (VERSION > '2.0.1.1') {
            $mail = new \Mail();
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
            $mail->smtp_password = $this->html_decode($this->config->get('config_mail_smtp_password'));
            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
        } else {
            $mail = new \Mail($this->config->get('config_mail'));
        }

        if (isset($data['attachment']) && $data['attachment']) {
            $attachments = $data['attachment'];
            if (!is_array($attachments)) {
                $attachments = array($attachments);
            }
            foreach ($attachments as $attachment) {
                $mail->addAttachment($attachment);
            }
        }
        if (isset($data['replyto']) && $data['replyto']) {
            $mail->setReplyTo($data['replyto']);
        }

        if (isset($data['from']) && $data['from']) {
            $mail->setFrom($data['from']);
        } else {
            $mail->setFrom($this->config->get('config_email'));
        }

        if (isset($data['sender']) && $data['sender']) {
            $mail->setSender($data['sender']);
        } else {
            $mail->setSender($this->config->get('config_meta_title'));
        }
        $mail->setSubject($data['subject']);
        $mail->setHtml($data['message']);
        
        $receivers = $data['to'];
        if (!is_array($receivers)) {
            $receivers = array($receivers);
        }
        foreach ($receivers as $to) {
            if ($to) {
                $mail->setTo($to);
                $mail->send();
            }
        }
    }

    private function parseAttr($attr) {
        if (!$attr) return false;
        $return = array();
        $attr = preg_replace('/\s{2,}/', ' ', $attr);
        $rex = '/(\w+)\s?="(.*?)"/m';
        if (preg_match_all($rex, $attr, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                if (isset($match[1]) && isset($match[2]) && $match[1] && $match[2]) {
                    $return[$match[1]] = $match[2];
                }
            }
        } else {
            if (strpos($attr, ' ') === false) {
                $return[$attr] = $attr;
            } else {
                $parts = explode(' ', $attr);
                foreach ($parts as $part) {
                    $return[$part] = 1;
                }
            }
        }
        return $return;
    }
    /*
      Perf: Only parsing one shortcode per page deliberately 
    */
    public function parseShorcde(&$data) {
        $rex = '/\[(\w+)\s?([^]]*)\](.*?)\[\/\w+\]/m';
        $key = '';
        if (isset($data['description'])) $key = 'description';
        if (isset($data['content'])) $key = 'content';
        if (isset($data['text'])) $key = 'text';
        if ($key && preg_match($rex, $data[$key], $matches)) {
            $name = $matches[1];
            $attr = isset($matches[2]) ? $this->parseAttr($matches[2]) : false;
            $text = isset($matches[3]) ? $matches[3] : '';
            $module = isset($this->modules[$name]) ? $this->modules[$name] : false;
            $option = array();
            $option['text'] = $text;
            $option['attr'] = $attr;
            if ($module && strpos($module['flag'], 'shortcode') !== false && $this->getConfig($name . '_status', $module['type'])) {
                $path = $this->common->getExtPath($module['type']);
                $response = $this->load->controller($path . $name . '/getShorcode', $option);
                $data[$key] = str_replace($matches[0], $response, $data[$key]);
            }
        }
    }
    public function getScript() {
        $script = '';
        foreach ($this->modules as $name => $module) {
            if (strpos($module['flag'], 'script') !== false && $this->getConfig($name . '_status', $module['type'])) {
                $path = $this->common->getExtPath($module['type']);
                $this->load->model($path . $name);
                $key = 'model_' . str_replace('/', '_', $path) . $name;
                $method_exist = property_exists($this->{$key}, 'getScript') || method_exists($this->{$key}, 'getScript');
                if ($method_exist) {
                    $script .= $this->{$key}->getScript();
                }
            }
        }
        return $script;
    }
    public function onOrderHistory($customer_id, $order_id, $order_status_id) {
        foreach ($this->modules as $name => $module) {
            if (strpos($module['flag'], 'order') !== false && $this->getConfig($name . '_status', $module['type'])) {
                $path = $this->common->getExtPath($module['type']);
                $this->load->model($path . $name);
                $key = 'model_' . str_replace('/', '_', $path) . $name;
                $method_exist = property_exists($this->{$key}, 'onOrderHistory') || method_exists($this->{$key}, 'onOrderHistory');
                if ($method_exist) {
                    $this->{$key}->onOrderHistory($customer_id, $order_id, $order_status_id);
                }
            }
        }
    }

    /* Cart Related methods */
    private function getCartCondition() {
        $where = " AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'";
        if (VERSION >= '2.3.0.0') {
            $where .= " AND api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "'";
        }
        return $where;
    }
    public function getCartProducts() {
        $return = array();
        if (VERSION < '2.1.0.0') {
            foreach ($this->session->data['cart'] as $key => $quantity) {
                $product = unserialize(base64_decode($key));
                $product['cart_id'] = $key;
                $product['option'] = isset($product['option']) ? $product['option'] : array();
                $return[] = $product;
            }
        } else {
            $rows = $this->db->query("SELECT `option`, `product_id`, `cart_id` FROM " . DB_PREFIX . "cart WHERE 1" . $this->getCartCondition())->rows;
            foreach ($rows as $product) {
                $product['option'] = json_decode($product['option'], true);
                $return[] = $product;
            }
        }
        return $return;
    }
    public function getCartProductById($cart_id) {
        $product = array();
        if (VERSION < '2.1.0.0') {
            if (isset($this->session->data['cart'][$cart_id])) {
                $product = unserialize(base64_decode($cart_id));
                $product['cart_id'] = $cart_id;
                $product['option'] = isset($product['option']) ? $product['option'] : array();
            }
        }
        else {
            $product = $this->db->query("SELECT product_id, `option` FROM " . DB_PREFIX . "cart WHERE cart_id = '".(int)$cart_id."'")->row;
            if ($product) {
                $product['option'] = json_decode($product['option'], true);
            }
        }
        return $product;
    }
    public function deleteCartProduct($cart_id) {
        if (VERSION < '2.1.0.0') {
            unset($this->session->data['cart'][$cart_id]);
        } else {
            $this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int)$cart_id . "'");
        }
    }
    public function updateCartOption($cart_id, $option) {
        if (VERSION < '2.1.0.0') {
            if (isset($this->session->data['cart'][$cart_id])) {
                $qnty = $this->session->data['cart'][$cart_id];
                $product = unserialize(base64_decode($cart_id));
                $product['option'] = $option;
                $cart_id = base64_encode(serialize($product));
                $this->session->data['cart'][$cart_id] = $qnty;
            }
        } else {
            $this->db->query("UPDATE " . DB_PREFIX . "cart SET `option` = '" . $this->db->escape(json_encode($option)) . "' WHERE cart_id = '" . (int)$cart_id . "'" . $this->getCartCondition());
        }
    }
    public function writeLog($debugging, $name) {
        if (!$debugging || $this->isAdmin()) return false;
        $log_file = DIR_LOGS . $name . '.log';
        $log_handle = fopen($log_file, 'w');
        $ocm_logs = '';
        foreach($debugging as $debug) {
           $ocm_logs .= '<blockquote class="blockquote">
                           <b>Method Name:</b> '.$debug['name'].'<br />
                           <b>Method ID:</b> '.$debug['index'].'<br />
                           <b>Was Restricted By Rules:</b> '.implode(',&nbsp;&nbsp;',$debug['filter']).'
                         </blockquote>';
        }
        fwrite($log_handle, $ocm_logs);
        fclose($log_handle);
    }
    public function html_decode($data) {
        if (is_array($data)) {
            foreach ($data as &$value) {
               $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
            }
        } else {
            $data = html_entity_decode($data, ENT_QUOTES, 'UTF-8');
        }
        return $data;
    }
    public function setCache($key, $value) {
        $this->request->post[$key] = $value;
    }
    public function getCache($key) {
        return isset($this->request->post[$key]) ? $this->request->post[$key] : '';
    }
    public function isCacheAvail($key) {
        return isset($this->request->post[$key]);
    }
    public function redirectToSelf() {
        $protocol = $this->request->server['HTTPS'] ? 'https://' : 'http://';
        $this->response->redirect($protocol . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI']);
    }
    public function interpolate($data, $placeholder, $replacer) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $_value = str_replace($placeholder, $replacer, $value);
                if ($_value === 'true' || $_value === true)  $_value = true;
                if ($_value === 'false' || $_value === false) $_value = false;
                $data[$key] = ($_value !== $value) ? $_value : '';
            }
        } else {
            $_value = str_replace($placeholder, $replacer, $data);
            if ($_value === 'true' || $_value === true)  $_value = true;
            if ($_value === 'false' || $_value === false) $_value = false;
            $data = ($_value !== $data) ? $_value : '';
        }
        return $data;
    }
}