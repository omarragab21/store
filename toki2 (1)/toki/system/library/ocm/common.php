<?php
/* Commmon methods are used by both front and back */
namespace OCM;
final class Common {
    private $registry;
    public function __construct($registry) {
        $this->registry = $registry;
        $this->request = $registry->get('request');
        $this->config = $registry->get('config');
    }
    public function getConfig($key, $prefix = '') {
        $prefix = VERSION >= '3.0.0.0' ? $prefix .'_' : '';
        $key = $prefix . $key;
        return $this->config->get($key);
    }
    public function getExtPath($type) {
        if ($type === 'shipping') {
            $key = (VERSION >= '2.3.0.0') ? 'extension/shipping/' : 'shipping/';
        } else if ($type === 'total') {
            $key = (VERSION >= '2.3.0.0') ? 'extension/total/' : 'total/';
        } else if ($type === 'module') {
            $key = (VERSION >= '2.3.0.0') ? 'extension/module/' : 'module/';
        }
        else if ($type === 'payment') {
            $key = (VERSION >= '2.3.0.0') ? 'extension/payment/' : 'payment/';
        }
        return $key;
    }
    public function getSiteURL() {
        $store_url = $this->request->server['HTTPS'] ? HTTPS_SERVER : HTTP_SERVER;
        if (defined('HTTP_CATALOG')) {
            $store_url = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;
        }
        return $store_url;
    }
    public function getVar($name, $more = array()) {
        $value = isset($this->session->data[$name]) ? $this->session->data[$name] : '';
        $value = isset($this->request->post[$name]) ? $this->request->post[$name] : $value;
        $value = isset($this->request->get[$name]) ? $this->request->get[$name] : $value;
        if (!$value && $more) {
            foreach ($more as $type => $name) {
                if ($type == 's') {
                    $value = isset($this->session->data[$name]) ? $this->session->data[$name] : $value;
                }
                if ($type == 'p') {
                    $value = isset($this->request->post[$name]) ? $this->request->post[$name] : $value;
                }
                if ($type == 'g') {
                    $value = isset($this->request->get[$name]) ? $this->request->get[$name] : $value;
                }
                if ($value) {
                    break;
                }
            }
        }
        return $value;
    }
}