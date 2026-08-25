<?php
class ControllerExtensionModuleXtensionsCheckoutXFooter extends Controller {
	public $data=array();
	public function index() {
		$this->load->model('catalog/information');
		$this->data['informations'] = array();
		$xconfig = $this->xtensions_checkout->getXtensionsData($this->config->get('config_store_id'),'xtensions_best_checkout');
		$xoptions = $xconfig['xconfig']['options'];
		foreach ($this->model_catalog_information->getInformations() as $result) {
			if (isset($xoptions['information']) && in_array($result['information_id'], $xoptions['information'])) {
				$this->data['informations'][] = array(
					'title' 		=> $result['title'],
					'href'  		=> $this->url->link('information/information', 'information_id=' . $result['information_id']),
					'modal_href' 	=> $this->url->link('information/information/agree', 'information_id=' . $result['information_id'], 'SSL')
				);
			}
		}
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}
		
		$this->language->load('common/footer');
		$this->data['text_contact'] = $this->language->get('text_contact');
		$this->data['contact'] = (isset($xoptions['footer']['contact_us']) && $xoptions['footer']['contact_us'])?$this->url->link('information/contact'):'';
		$this->data['link_modal'] = (isset($xoptions['footer']['link_modal']) && $xoptions['footer']['link_modal'])?true:false;
		$this->data['telephone'] = (isset($xoptions['footer']['contact_telephone']) && $xoptions['footer']['contact_telephone'])?$xoptions['footer']['contact_telephone']:((isset($xoptions['footer']['contact_telephone']) && !$xoptions['footer']['contact_telephone'])?'':$this->config->get('config_telephone'));
		$this->data['email'] = (isset($xoptions['footer']['contact_email']) && $xoptions['footer']['contact_email'])?$xoptions['footer']['contact_email']:((isset($xoptions['footer']['contact_email']) && !$xoptions['footer']['contact_email'])?'':$this->config->get('config_email'));
		if(isset($xoptions['footer']['images'])){
			foreach ($xoptions['footer']['images'] as $image){
				$this->data['payment_images'][]= array('url'=>$server .'image/'.$image);
			}
		}else {
			$this->data['payment_images'][] = array('url'=>'');
		}
		$this->template = $this->config->get('xtensions_view_path').'xfooter';
		return $this->xtensions_checkout->renderView($this);
	}
}
?>
