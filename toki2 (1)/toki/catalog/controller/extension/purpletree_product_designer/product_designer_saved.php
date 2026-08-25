<?php
class ControllerExtensionPurpletreeProductDesignerProductDesignerSaved extends Controller{

	private $error = array();

		public function deleteRecord() {
					$this->load->language('purpletree_product_designer/product_designer_saved');
			if (isset($this->request->get['id'])) {
				$this->load->model('extension/purpletree_product_designer/product_designer_saved');
				$order_total = $this->model_extension_purpletree_product_designer_product_designer_saved->deleteRecord($this->request->get['id']);
				$this->session->data['success'] = $this->language->get('text_success');
			}
				$this->response->redirect($this->url->link('extension/purpletree_product_designer/product_designer_saved', '', true));
		}
		public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('extension/purpletree_product_designer/product_designer_saved', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('purpletree_product_designer/product_designer_saved');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/purpletree_product_designer/product_designer_saved', $url, true)
		);

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		$data['orders'] = array();

		$this->load->model('extension/purpletree_product_designer/product_designer_saved');
		$order_total = $this->model_extension_purpletree_product_designer_product_designer_saved->getTotalRecords();
		$results = $this->model_extension_purpletree_product_designer_product_designer_saved->getRecords(($page - 1) * 10, 10);
		foreach ($results as $result) {
			$data['orders'][] = array(
				'pdsid'   => $result['pdsid'],
				'product_name'   => $result['name'],
				'status'      => ($result['pdsstatus'] == 0)?$this->language->get('Disabled'):$this->language->get('enabled'),
				'date_modified'  => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
				'date_added'     => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'view'       => $this->url->link('extension/purpletree_product_designer/product_template_designer', 'product_id=' . $result['product_id'].'&template_id=' . $result['template_id'].'&saved_id=' . $result['id'], true),
				'delete'       => $this->url->link('extension/purpletree_product_designer/product_designer_saved/deleteRecord', 'id=' . $result['id'], true),
			);
		}

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('extension/purpletree_product_designer/product_designer_saved', 'page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($order_total - 10)) ? $order_total : ((($page - 1) * 10) + 10), $order_total, ceil($order_total / 10));

		//$data['continue'] = $this->url->link('account/account', '', true);
	$direction = $this->language->get('direction');
			if ($direction=='rtl'){			
				$this->document->addStyle('catalog/view/theme/default/stylesheet/ptsproductdesign/css/bootstrap.min-a.css');
			} else {
				$this->document->addStyle('catalog/view/theme/default/stylesheet/ptsproductdesign/css/bootstrap.min.css');
			}
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('extension/purpletree_product_designer/product_designer_saved', $data));
	}
}
?>