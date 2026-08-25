<?php
class ControllerExtensionModuleBanner3 extends Controller {
	public function index($setting) {
		static $module = 0;
$this->load->language('extension/module/slideshow');
		$this->load->model('design/banner');
		$this->load->model('tool/image');

		$this->document->addStyle('catalog/view/javascript/jquery/swiper/css/swiper.min.css');
		$this->document->addStyle('catalog/view/javascript/jquery/swiper/css/opencart.css');
		$this->document->addScript('catalog/view/javascript/jquery/swiper/js/swiper.jquery.js');

		$data['banners'] = array();

		$results = $this->model_design_banner->getBanner($setting['banner_id']);

		$results2 = $this->model_design_banner->getBann($setting['banner_id']);
		// print_r($results2);
		$data['bann_name'] = $results2['name'];
		$data['bann_name2'] = $results2['name2'];
		$data['bann_blink'] = $results2['blink'];

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$data['banners'][] = array(
					'title' => $result['title'],
					'link'  => $result['link'],
					'description' => $result['description'],
					'image' => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height'])
				);
			}
		}

		$data['module'] = $module++;

		return $this->load->view('extension/module/banner3', $data);
	}
}