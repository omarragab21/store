<?php
class ControllerExtensionModuleXtensionsCheckoutXGoogleCaptcha extends Controller {
    public function index() {
        $this->load->language('extension/captcha/google');

		$data['text_captcha'] = $this->language->get('text_captcha');

		$data['entry_captcha'] = $this->language->get('entry_captcha'); 
		$data['site_key'] = $this->config->get('captcha_google_key');

        $data['route'] = $this->request->get['route']; 

		return $this->load->view($this->config->get('xtensions_view_path').'xgoogle_captcha', $data);
    }

    public function validate() {    	
		if (empty($this->session->data['captcha'])) {
			$this->load->language('extension/captcha/google');

			$recaptcha = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($this->config->get('captcha_google_secret')) . '&response=' . $this->request->post['g-recaptcha-response'] . '&remoteip=' . $this->request->server['REMOTE_ADDR']);
	
			$recaptcha = json_decode($recaptcha, true);
	
			if ($recaptcha['success']) {
				$this->session->data['captcha']	= true;
			} else {
				return $this->language->get('error_captcha');
			}
		}
    }
}
