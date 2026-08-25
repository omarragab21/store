<?php
class ControllerExtensionPurpletreeProductDesignerClipart extends Controller {	
	private $error = array();
	public function index() {
		$this->load->language('purpletree_product_designer/clipart');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/purpletree_product_designer/clipart');		
		$url = '';
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/purpletree_product_designer/clipart', '	=' . $this->session->data['user_token'] . $url, true)
		);
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		$data['text_clipart'] = $this->language->get('text_clipart');
		$data['cancel'] = $this->url->link('extension/purpletree_product_designer/clipart', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['action'] = $this->url->link('extension/purpletree_product_designer/clipart/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		
       // Images
	   $this->load->model('tool/image');
		if (isset($this->request->post['product_image'])) {
			$product_images = $this->request->post['product_image'];
		} elseif (!isset($this->request->post['product_image'])) {
			$product_images = $this->model_extension_purpletree_product_designer_clipart->getClipartImages();
		} else {
			$product_images = array();
		}
		$data['product_images'] = array();
        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		foreach ($product_images as $product_image) {
			
			if (is_file(DIR_IMAGE . $product_image['clipart_image'])) {
				$image = $product_image['clipart_image'];
				$thumb = $product_image['clipart_image'];
			} else {
				$image = '';
				$thumb = 'no_image.png';
			}
			$ext= substr(basename($image), strrpos(basename($image), '.')+1);
			if($ext!='svg'){
				$pts_thumb= $this->model_tool_image->resize($thumb, 100, 100);
			}else {
			$pts_thumb=HTTPS_CATALOG . 'image/'.$image;	
			}
			$data['product_images'][] = array(
			    'clipart_id' =>  $product_image['clipart_id'],
				'image'      => $image,
				'thumb'      => $pts_thumb,
				'ext'      => $ext,
				'svg_img'=>HTTPS_CATALOG . 'image/'.$image
			);
		}		
		$data['user_token'] = $this->session->data['user_token'];
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/purpletree_product_designer/clipart', $data));
	}	
	
	public function add(){
		
		
		$this->load->language('purpletree_product_designer/clipart');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/purpletree_product_designer/clipart');		
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$this->model_extension_purpletree_product_designer_clipart->addClipart($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$url = '';			
			
			$this->response->redirect($this->url->link('extension/purpletree_product_designer/clipart', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->index();
	}
	
	public function clip_delete(){
		$json['status'] = 'error'; 
		$json['message'] = 'Something went wrong'; 
		if (isset($this->request->get['clipart_id']) && $this->request->get['clipart_id'] != '') {
			$this->load->model('extension/purpletree_product_designer/clipart');
		    $this->load->language('purpletree_product_designer/clipart');
			  $this->model_extension_purpletree_product_designer_clipart->deleteClipart($this->request->get['clipart_id']);
			  $json['clipart_id'] = $this->request->get['clipart_id'];
			  $json['status'] = 'success';
			  $json['message'] = $this->language->get('text_delete');				
				 
		}	
	$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
