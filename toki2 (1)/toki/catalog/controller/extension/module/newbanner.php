<?php
class ControllerExtensionModuleNewbanner extends Controller {
	public function index($setting) {
		static $module = 0;		

		$this->load->model('design/banner');
		$this->load->model('tool/image');

		$this->document->addStyle('catalog/view/javascript/jquery/swiper/css/swiper.min.css');
		$this->document->addStyle('catalog/view/javascript/jquery/swiper/css/opencart.css');
		$this->document->addScript('catalog/view/javascript/jquery/swiper/js/swiper.jquery.js');
		
		$data['banners'] = array();
        $data['direction'] = $this->language->get('direction');

		$results = $this->model_design_banner->getBanner($setting['banner_id']);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$data['banners'][] = array(
					'title' => $result['title'],
					'link'  => $result['link'],
					'image' => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height'])
				);
			}
		}

		$data['module'] = $module++;
        $data['facebook'] = $this->config->get('config_facebook');
        $data['twitter'] = $this->config->get('config_twitter');
        $data['pint'] = $this->config->get('config_pint');
        $data['instagram'] = $this->config->get('config_instagram');
        $data['snapchat'] = $this->config->get('config_snapchat');
        $data['whats_app'] = $this->config->get('config_whats_app');
        $data['javascript'] = $this->config->get('config_javascript');

		return $this->load->view('extension/module/newbanner', $data);
	}
}