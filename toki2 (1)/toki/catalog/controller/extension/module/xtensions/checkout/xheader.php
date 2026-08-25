<?php
class ControllerExtensionModuleXtensionsCheckoutXHeader extends Controller {
	public $data=array();
	public function index() {
		$this->data['title'] = $this->document->getTitle();
		$this->language->load('common/header');
		$this->language->load($this->config->get('xtensions_language_path'));
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (isset($this->session->data['error']) && !empty($this->session->data['error'])) {
			$this->data['error'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} else {
			$this->data['error'] = '';
		}

		$this->data['base'] = $server;
		$this->data['description'] = $this->document->getDescription();
		$this->data['keywords'] = $this->document->getKeywords();
		$this->data['links'] = $this->document->getLinks();
		$this->data['lang'] = $this->language->get('code');
		$this->data['direction'] = $this->language->get('direction');
		$this->data['analytics'] = array();
		$this->load->model('setting/extension');
		$analytics = $this->model_setting_extension->getExtensions('analytics');

		foreach ($analytics as $analytic) {
			if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
				$this->data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
			}
		}
		$this->data['name'] = $this->config->get('config_name');

		if ($this->config->get('config_icon') && file_exists(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->data['icon'] = $server . 'image/' . $this->config->get('config_icon');
		} else {
			$this->data['icon'] = '';
		}

		if ($this->config->get('config_logo') && file_exists(DIR_IMAGE . $this->config->get('config_logo'))) {
			$this->data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$this->data['logo'] = '';
		}

		$this->language->load('common/header');
		

		$this->data['text_home'] = $this->language->get('text_home');
		$this->data['text_shopping_cart'] = $this->language->get('text_shopping_cart');
		if($this->customer->isLogged()){
			if($this->customer->getFirstName()){
				$display_name=$this->customer->getFirstName();
			}
			else{
				$display_name=substr($this->customer->getEmail(),0,strpos($this->customer->getEmail(),'@'));
			}
			$this->data['text_logged'] = sprintf('<span class=""><a href="%s"><i class="fa fa-user"></i> %s</a> | <a href="%s">'.$this->language->get('text_logout').' <i class="fa fa-sign-out fa-lg"></i></a></span>', $this->url->link('account/account', '', 'SSL'), $display_name, $this->url->link('account/logout', '', 'SSL'));
		}else{
			$this->data['text_logged'] = '<i class="fa fa-user"></i> '.$this->language->get('text_guest_header');
		}

		$this->data['text_ssl_msg'] =$this->language->get('text_ssl_header');

		$this->data['home'] = $this->url->link('common/home');
		$this->data['logged'] = $this->customer->isLogged();
		$this->data['shopping_cart'] = $this->url->link('checkout/cart');
		$modData = $this->xtensions_checkout->getXtensionsData($this->config->get('config_store_id'), 'xtensions_best_checkout');
		$this->data['custom_css'] = isset($modData['xconfig']['design']['css'])?html_entity_decode($modData['xconfig']['design']['css'], ENT_QUOTES, 'UTF-8'):'';
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$connection = 'SSL';
		} else {
			$connection = 'NONSSL';
		}
		if(isset($modData['xconfig']['options']['show_country_code']) && $modData['xconfig']['options']['show_country_code']){
			$this->document->addStyle('catalog/view/javascript/xtensions/countrycode/css/intlTelInput.css');
			$this->document->addScript('catalog/view/javascript/xtensions/countrycode/js/intlTelInput.js');
		}		
		if (!isset($this->request->get['route'])) {
			$this->data['redirect'] = $this->url->link('common/home');
		} else {
			$url_data = $this->request->get;

			unset($url_data['_route_']);

			$route = $url_data['route'];

			unset($url_data['route']);

			$url = '';

			if ($url_data) {
				$url = '&' . urldecode(http_build_query($url_data, '', '&'));
			}

			$this->data['redirect'] = $this->url->link($route, $url, $connection);
		}

		//currency starts
		$this->language->load('common/currency');

		$this->data['text_currency'] = $this->language->get('text_currency');

		$this->data['action'] = $this->url->link($this->config->get('xtensions_controller_path').'xheader/currency', '', $connection);

		$this->data['currency_code'] = $this->session->data['currency'];

		$this->load->model('localisation/currency');

		$this->data['currencies'] = array();

		$results = $this->model_localisation_currency->getCurrencies();

		foreach ($results as $result) {
			if ($result['status']) {
				$this->data['currencies'][] = array(
					'title'        => $result['title'],
					'code'         => $result['code'],
					'symbol_left'  => $result['symbol_left'],
					'symbol_right' => $result['symbol_right']				
				);
			}
		}


		//currency ends

		//language starts
		$this->language->load('common/language');

		$this->data['text_language'] = $this->language->get('text_language');

		$this->data['action_lang'] = $this->url->link($this->config->get('xtensions_controller_path').'xheader/language', '', $connection);

		$this->data['language_code'] = $this->session->data['language'];

		$this->load->model('localisation/language');

		$this->data['languages'] = array();

		$results = $this->model_localisation_language->getLanguages();

		foreach ($results as $result) {
			if ($result['status']) {
				$this->data['languages'][] = array(
					'name'  => $result['name'],
					'code'  => $result['code'],
				);
			}
		}
		//language ends

		// Daniel's robot detector
		$status = true;

		if (isset($this->request->server['HTTP_USER_AGENT'])) {
			$robots = explode("\n", trim($this->config->get('config_robots')));

			foreach ($robots as $robot) {
				if ($robot && strpos($this->request->server['HTTP_USER_AGENT'], trim($robot)) !== false) {
					$status = false;

					break;
				}
			}
		}
		$this->data['stores'] = array();
		//$this->document->addStyle('catalog/view/javascript/xtensions/stylesheet/less/animate.css');
		$this->document->addStyle('catalog/view/javascript/xtensions/stylesheet/less/css/ionicons.min.css');
		require_once DIR_SYSTEM.'library/xtensions/lessstyling.php';
		$styleFolder = 'catalog/view/javascript/xtensions/stylesheet/less/';		
		if((isset($modData['xconfig']['design']['type']) && $modData['xconfig']['design']['type']!='custom')){			
			$xtensions_design = $this->config->get('xtensions_design');
			$colors = $xtensions_design[$modData['xconfig']['design']['type']]['colors'];
		}else{
			$colors = array_merge($this->config->get('xtensions_custom_color'),(isset($modData['xconfig']['colors'])?$modData['xconfig']['colors']:array()));
		}
		if(isset($modData['xconfig']['last_saved'])){
			$last_saved = $modData['xconfig']['last_saved'];
		}else{
			$last_saved = '';
		}
		$style_files[] = array('input'=>$styleFolder . 'xtensions.less','output'=>$styleFolder . 'xtensions.'.$this->config->get('config_store_id').$last_saved.'.css','output_default'=>$styleFolder . 'xtensions.css');
		if ($this->data['direction']=='rtl'){
			$style_files[] = array('input'=>$styleFolder . 'xtensions-rtl.less','output'=>$styleFolder . 'xtensions-rtl.'.$this->config->get('config_store_id').$last_saved.'.css','output_default'=>$styleFolder . 'xtensions-rtl.css');
		}
		foreach ($style_files as $files){	
			if(file_exists($files['input']) && is_writable($styleFolder)){ // Check does .less file is available and stylesheet folder is writable
	
				if(file_exists($files['output'])){
	
					$this->document->addStyle($files['output']);
					continue;
				}
	
				$this->addDesign($colors,$files['input'],$files['output']);
			}else{
				$this->document->addStyle($files['output_default']);
			}
		}
		$this->data['styles'] = $this->document->getStyles();
		$this->data['scripts'] = $this->document->getScripts();
		$this->language->load('checkout/checkout');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->language->load($this->config->get('xtensions_language_path'));
		$this->data['text_checkout_option'] = sprintf($this->language->get('text_checkout_option'), 1);
		$this->data['text_checkout_account'] = sprintf($this->language->get('text_checkout_account'), 2);
		$this->data['text_checkout_confirm'] = sprintf($this->language->get('text_checkout_confirm'), 3);


		$this->data['logged'] = $this->customer->isLogged();
		$this->data['hide_top_bar'] = isset($modData['xconfig']['options']['header']['hide_top_bar']) && $modData['xconfig']['options']['header']['hide_top_bar'];
		$this->data['hide_currency_switcher'] = isset($modData['xconfig']['options']['header']['hide_currency_switcher']) && $modData['xconfig']['options']['header']['hide_currency_switcher'];
		$this->data['hide_language_switcher'] = isset($modData['xconfig']['options']['header']['hide_language_switcher']) && $modData['xconfig']['options']['header']['hide_language_switcher'];
		if (isset($modData['xconfig']['options']['header']['override_store_logo']) && $modData['xconfig']['options']['header']['override_store_logo'] && $modData['xconfig']['options']['header']['logo_image'] && file_exists(DIR_IMAGE . $modData['xconfig']['options']['header']['logo_image'])) {
			$this->data['logo'] = $server . 'image/' . $modData['xconfig']['options']['header']['logo_image'];
		}
		
		$this->xtensions_checkout->addActivity($this->xtensions_checkout->getAllMainActivity());

		$this->template = $this->config->get('xtensions_view_path').'xheader';
		return $this->xtensions_checkout->renderView($this);
	}

	public function currency(){
		if (isset($this->request->post['currency_code'])) {
			$this->session->data['currency'] = $this->request->post['currency_code'];
			$this->xtensions_checkout->addEventActivity(array('currency_changed'));
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
		}

		if (isset($this->request->post['redirect'])) {
			$this->xtensions_checkout->redirect($this->request->post['redirect']);
		} else {
			$this->xtensions_checkout->redirect($this->url->link('common/home'));
		}
	}

	public function language(){
		if (isset($this->request->post['language_code'])) {
			$this->session->data['language'] = $this->request->post['language_code'];
			$this->xtensions_checkout->addEventActivity(array('language_changed'));
		}

		if (isset($this->request->post['redirect_language'])) {
			$this->xtensions_checkout->redirect($this->request->post['redirect_language']);
		} else {
			$this->xtensions_checkout->redirect($this->url->link('common/home'));
		}
	}
	
	private function addDesign($colors,$input,$output){
		$lessNew		= new lesscstyling($input);
		$lessParse	= $lessNew->parse(null, $colors); // Parse variable
		$hashCss = file_exists($output) ? sha1_file($output) : '';
		$hashLess = sha1($lessParse);
		if ($hashCss != $hashLess) { // Check does the Hash above is different. If yes, generate new stylesheet.
			file_put_contents($output, $lessParse);
		}
		$this->document->addStyle($output);
	}
}
?>
