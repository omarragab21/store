<?php
class ControllerExtensionPurpletreeProductDesignerProductDesignerFonts extends Controller {
	
	private $error = array();

	public function index() {
		$this->load->language('purpletree_product_designer/fonts');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/purpletree_product_designer/font');
		
		$this->load->model('catalog/product');

		$this->getList(); 
	}
	public function add(){
		
		$this->load->language('purpletree_product_designer/fonts');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/purpletree_product_designer/font');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

			$this->model_extension_purpletree_product_designer_font->addFont($this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$url = '';			
			
			$this->response->redirect($this->url->link('extension/purpletree_product_designer/product_designer_fonts', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}
	public function edit() {

		$this->load->language('purpletree_product_designer/fonts');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/purpletree_product_designer/font');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			
			$this->model_extension_purpletree_product_designer_font->editFont($this->request->get['font_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/purpletree_product_designer/product_designer_fonts', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}
	public function delete() {
		$this->load->language('purpletree_multivendor/fonts');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/purpletree_product_designer/font');

		if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $font_id) {
				$this->model_extension_purpletree_product_designer_font->deleteFonts($font_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/purpletree_product_designer/product_designer_fonts', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
	protected function getList() {
		
		$this->load->language('purpletree_multivendor/fonts');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/purpletree_product_designer/font');

		$url ='';

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/purpletree_product_designer/product_designer_fonts', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
		$data['add'] = $this->url->link('extension/purpletree_product_designer/product_designer_fonts/add', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['delete'] = $this->url->link('extension/purpletree_product_designer/product_designer_fonts/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$this->load->model('extension/purpletree_product_designer/clipart');
		$filter_data = array(
			'start'                => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                => $this->config->get('config_limit_admin'),
		);
		 // Fonts
		if (isset($this->request->post['product_font'])) {
			$product_font = $this->request->post['product_font'];
		} elseif (!isset($this->request->post['product_font'])) {
			$product_font = $this->model_extension_purpletree_product_designer_font->getFont($filter_data);
		} else {
			$product_font = array();
		}
		$data['product_font'] = array();

		foreach ($product_font as $product_font) {
		
			$data['product_fonts'][] = array(
			    'font_id' =>  $product_font['font_id'],
			    'font_name' =>  $product_font['font_name'],
				'edit'        => $this->url->link('extension/purpletree_product_designer/product_designer_fonts/edit', 'user_token=' . $this->session->data['user_token'] . '&font_id=' . $product_font['font_id'] . $url, true)
			);
		}		

		$data['user_token'] = $this->session->data['user_token'];

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

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';
		$product_total = $this->model_extension_purpletree_product_designer_font->getTotalFonts();
		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/purpletree_product_designer/product_designer_fonts', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));
		//echo"<pre>"; print_r($data['pagination']); die;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/purpletree_product_designer/product_designer_font_list', $data));
	}
	public function getForm() {
		$data['text_form'] = !isset($this->request->get['geo_zone_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['font_name'])) {
			$data['error_font_name'] = $this->error['font_name'];
		} else {
			$data['error_font_name'] = '';
		}

		$url = '';

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/purpletree_product_designer/product_designer_fonts', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['font_id'])) {
			$data['action'] = $this->url->link('extension/purpletree_product_designer/product_designer_fonts/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/purpletree_product_designer/product_designer_fonts/edit', 'user_token=' . $this->session->data['user_token'] . '&font_id=' . $this->request->get['font_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('extension/purpletree_product_designer/product_designer_fonts', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['font_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$font_data = $this->model_extension_purpletree_product_designer_font->getSingleFont($this->request->get['font_id']);
		}
		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['font_name'])) {
			$data['font_name'] = $this->request->post['font_name'];
		} elseif (!empty($font_data)) {
			$data['font_name'] = $font_data['font_name'];
		} else {
			$data['font_name'] = '';
		}			

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('extension/purpletree_product_designer/product_designer_font_form', $data));
	}

	protected function validateForm() {
		
		if ((utf8_strlen($this->request->post['font_name']) < 1)) {
			$this->error['font_name'] = $this->language->get('error_font_name');
		}
		return !$this->error; 
	}
}
