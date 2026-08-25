<?php
class ModelExtensionModuleTabbyDdlog extends Model {
    const VERSION = '1.3.0';

    public function log($status = "error", $message = "Something went wrong", $e = null, $data = null) {

        $url = "https://http-intake.logs.datadoghq.eu/v1/input";

        $curl_options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(),
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 10
        );

        $curl_options[CURLOPT_HTTPHEADER][] = 'DD-API-KEY: a06dc07e2866305cda6ed90bf4e46936';
        $curl_options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';

        $storeURL = parse_url($this->getBaseUrl());

        $log = array(
            "status"  => $status,
            "message" => $message,

            "service"  => "opencart",
            "sversion"  => VERSION,
            "hostname" => $storeURL["host"],

            "ddsource" => "php",
            "ddtags"   => "env:prod,version:" . self::VERSION
        );

        if ($e) {
            $log["error.kind"]    = $e->getCode();
            $log["error.message"] = $e->getMessage();
            $log["error.stack"]   = $e->getTraceAsString();
        }

        if ($data) {
            $log["data"] = $data;
        }

        $params = json_encode($log);

        $curl_options[CURLOPT_POSTFIELDS] = $params;

        $ch = curl_init();
        curl_setopt_array($ch, $curl_options);
        $response = curl_exec($ch);

        $result = [];
        $curl_info = curl_getinfo($ch);

        if ($curl_info['http_code'] != 200) {
            $this->errors[] = array('name' => 'http_code', 'message' => $curl_info['http_code'], 'response' => $response);
        }

        if (curl_errno($ch)) {
            $curl_code = curl_errno($ch);

            $constant = get_defined_constants(true);
            $curl_constant = preg_grep('/^CURLE_/', array_flip($constant['curl']));

            $this->errors[] = array('name' => $curl_constant[$curl_code], 'message' => curl_strerror($curl_code));
        }

        try {
            $result = json_decode($response);
        } catch (Exception $e) {
            $this->errors[] = ['name' => 'json', 'message' => $e->getMessage()];
        }

        if(empty($this->errors)) {
            return $result;
        } else {
            $res = new stdClass();
            $res->status = 'error';
            $res->errors = $this->errors;
            return $res;
        }
    }
    protected function getBaseUrl() {
        $server = null;
        if ($this->request->server['HTTPS']) {
            $server = $this->config->get('config_ssl') ?: HTTPS_CATALOG;
        } else {
            $server = $this->config->get('config_url') ?: HTTP_CATALOG;
        }
        return $server;
    }
}

