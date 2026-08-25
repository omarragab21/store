<?php
class ControllerExtensionpurPletreeProductDesignerProducttemplate extends Controller {
		private $error = array();
		
		public function index() {
			if(!$this->config->get('module_purpletree_product_designer_template')) {
				$this->response->redirect($this->url->link('extension/module/purpletree_product_designer', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}
			$this->load->language('purpletree_product_designer/admintemplate');
			
			$this->document->setTitle($this->language->get('heading_title1'));
			
			$this->load->model('catalog/product');
			$this->getList();
		}
		
		public function delete() {
			$this->load->language('purpletree_product_designer/admintemplate');
			
			$this->document->setTitle($this->language->get('heading_title1'));
			
			$this->load->model('catalog/product');
			$this->load->model('extension/purpletree_product_designer/pts_product_template');
			
			if (isset($this->request->post['selected']) && $this->validateDelete()) {
				foreach ($this->request->post['selected'] as $id) {
					$this->model_extension_purpletree_product_designer_pts_product_template->deletesellertempProduct($id);
				}
				
				$this->session->data['success'] = $this->language->get('text_success');
				
				$url = '';
				
				if (isset($this->request->get['filter_name'])) {
					$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
				}
				if (isset($this->request->get['template_name'])) {
					$url .= '&template_name=' . urlencode(html_entity_decode($this->request->get['template_name'], ENT_QUOTES, 'UTF-8'));
				}
				if (isset($this->request->get['filter_status'])) {
					$url .= '&filter_status=' . $this->request->get['filter_status'];
				}
				
				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}
				
				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}
				
				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}
				
				$this->response->redirect($this->url->link('extension/purpletree_product_designer/producttemplate', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}
			
			$this->getList();
		}
		
		protected function getList() {
			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
				} else {
				$filter_name = '';
			}
			
			if (isset($this->request->get['template_name'])) {
				$template_name = $this->request->get['template_name'];
				} else {
				$template_name = '';
			}
			
			if (isset($this->request->get['filter_status'])) {
				$filter_status = $this->request->get['filter_status'];
				} else {
				$filter_status = '';
			}		
			
			if (isset($this->request->get['sort'])) {
				$sort = $this->request->get['sort'];
				} else {
				$sort = 'pd.name';
			}
			
			if (isset($this->request->get['order'])) {
				$order = $this->request->get['order'];
				} else {
				$order = 'ASC';
			}
			
			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
				} else {
				$page = 1;
			}
			
			$url = '';
				
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['template_name'])) {
				$url .= '&template_name=' . urlencode(html_entity_decode($this->request->get['template_name'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			$data['breadcrumbs'] = array();
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
			);
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_template_product'),
			'href' => $this->url->link('extension/purpletree_product_designer/producttemplate', 'user_token=' . $this->session->data['user_token'] . $url, true)
			);
			
			$data['add'] = $this->url->link('extension/purpletree_product_designer/producttemplate/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
			$data['delete'] = $this->url->link('extension/purpletree_product_designer/producttemplate/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
			
			$data['products'] = array();
			
			$filter_data = array(
			'filter_name'	  => $filter_name,
			'template_name'	  => $template_name,
			'filter_status'   => $filter_status,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
			);
			
			$this->load->model('tool/image');
			$this->load->model('extension/purpletree_product_designer/pts_product_template');
			
			$product_total = $this->model_extension_purpletree_product_designer_pts_product_template->geSellerproducttemptotal($filter_data);
			
			$results = $this->model_extension_purpletree_product_designer_pts_product_template->geSellerproducttemp($filter_data);
	
			foreach ($results as $result) {
				if ($result['template_image'] != '' && is_file(DIR_IMAGE . $result['template_image'])) {
					$image = $this->model_tool_image->resize($result['template_image'], 40, 40);
					} else {
					$image = $this->model_tool_image->resize('no_image.png', 40, 40);
				}
				
				$data['products'][] = array(
				'id' => $result['template_id'],
				'template_name' => $result['template_name'],
				'sort_order' => $result['sort_order'],
				'product_name' => $result['product_name'],
				'image'      => $image,
				'model'       => $result['model'],
				'template_status'      => $result['template_status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'editdetail'       => $this->url->link('extension/purpletree_product_designer/producttemplate/editTemplate', 'user_token=' . $this->session->data['user_token']. '&product_id=' . $result['product_id'] . '&id=' . $result['template_id'] . $url, true),
				'edit'       => $this->url->link('catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $result['product_id'] . $url, true),
				
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
			
			$url = '';	
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['template_name'])) {
				$url .= '&template_name=' . urlencode(html_entity_decode($this->request->get['template_name'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			
			if ($order == 'ASC') {
				$url .= '&order=DESC';
				} else {
				$url .= '&order=ASC';
			}
			
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			$data['sort_name'] = $this->url->link('extension/purpletree_product_designer/producttemplate', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.name' . $url, true);
			$data['sort_model'] = $this->url->link('extension/purpletree_product_designer/producttemplate', 'user_token=' . $this->session->data['user_token'] . '&sort=p.model' . $url, true);
			$data['sort_price'] = $this->url->link('extension/purpletree_product_designer/producttemplate', 'user_token=' . $this->session->data['user_token'] . '&sort=pvtps.price' . $url, true);
			$data['sort_status'] = $this->url->link('extension/purpletree_product_designer/producttemplate', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
			$data['sort_quantity'] = $this->url->link('extension/purpletree_product_designer/producttemplate', 'user_token=' . $this->session->data['user_token'] . '&sort=pvtps.quantity' . $url, true);
			$data['sort_order'] = $this->url->link('extension/purpletree_product_designer/producttemplate', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);
			
			$url = '';
			
			if (isset($this->request->get['filter_store'])) {
				$url .= '&filter_store=' . urlencode(html_entity_decode($this->request->get['filter_store'], ENT_QUOTES, 'UTF-8'));
			}		
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['template_name'])) {
				$url .= '&template_name=' . urlencode(html_entity_decode($this->request->get['template_name'], ENT_QUOTES, 'UTF-8'));
			}
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
			
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			$pagination = new Pagination();
			$pagination->total = $product_total;
			$pagination->page = $page;
			$pagination->limit = $this->config->get('config_limit_admin');
			$pagination->url = $this->url->link('extension/purpletree_product_designer/producttemplate', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
			
			$data['pagination'] = $pagination->render();
			
			$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));

			$data['filter_name'] = $filter_name;
			$data['template_name'] = $template_name;
			$data['filter_status'] = $filter_status;
			
			$data['sort'] = $sort;
			$data['order'] = $order;
			
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			$this->response->setOutput($this->load->view('extension/purpletree_product_designer/producttemplate', $data));
		}
		public function productForm() {
			$this->load->language('purpletree_product_designer/admintemplate');
			
			$this->document->setTitle($this->language->get('heading_title1'));
			
			$this->load->model('extension/purpletree_product_designer/pts_product_template');
			$this->load->model('localisation/stock_status');
			
			if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
				$product_info = $this->model_extension_purpletree_product_designer_pts_product_template->getTemplateDetail($this->request->get['id']);
			}
			
			$url ='';
			
			$data['breadcrumbs'] = array();
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
			);
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_detailform'),
			'href' => $this->url->link('extension/purpletree_product_designer/producttemplate', 'user_token=' . $this->session->data['user_token'] . $url, true)
			);
			if (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
				} else {
				$data['error_warning'] = '';
			  }
			
			if (isset($this->error['price'])) {
				$data['price_error'] = $this->error['price'];
				} else {
				$data['price_error'] = '';
			}		
			if (isset($this->error['quantity'])) {
				$data['quantity_error'] = $this->error['quantity'];
				} else {
				$data['quantity_error'] = '';
			}		
			
			if (isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];
				unset($this->session->data['success']);
				} else {
				$data['success'] = '';
			}


			if (isset($this->request->post['template_name'])) {
				$data['template_name'] = $this->request->post['template_name'];
				} elseif (!empty($product_info)) {
				$data['template_name'] = $product_info['template_name'];
				} else {
				$data['template_name'] = '';
			}

			if (isset($this->request->post['product_designer_total_layers'])) {
							$data['product_designer_total_layers'] = $this->request->post['product_designer_total_layers'];
						} elseif (!empty($product_info)) {
							$data['product_designer_total_layers'] = $product_info['total_layers']; 
						} else {
							$data['product_designer_total_layers'] = '999';
						}
						
				if (isset($this->request->post['product_designer_total_text_layers'])) {
							$data['product_designer_total_text_layers'] = $this->request->post['product_designer_total_text_layers'];
						} elseif (!empty($product_info)) {
							$data['product_designer_total_text_layers'] = $product_info['total_text_layers'];
						} else {
							$data['product_designer_total_text_layers'] = '99';
						}
						
						if (isset($this->request->post['product_designer_total_clipart_layers'])) {
							$data['product_designer_total_clipart_layers'] = $this->request->post['product_designer_total_clipart_layers'];
						} elseif (!empty($product_info)) {
							$data['product_designer_total_clipart_layers'] = $product_info['total_clipart_layers'];
						} else {
							$data['product_designer_total_clipart_layers'] = '99';
						}
						
						if (isset($this->request->post['product_designer_total_image_layers'])) {
							$data['product_designer_total_image_layers'] = $this->request->post['product_designer_total_image_layers'];
						} elseif (!empty($product_info)) {
							$data['product_designer_total_image_layers'] = $product_info['total_image_layers'];
						} else {
							$data['product_designer_total_image_layers'] = '99';
						}
						if (isset($this->request->post['product_designer_total_shapes_layers'])) {
							$data['product_designer_total_shapes_layers'] = $this->request->post['product_designer_total_shapes_layers'];
						} elseif (!empty($product_info)) {
							$data['product_designer_total_shapes_layers'] = $product_info['total_shapes_layers'];
						} else {
							$data['product_designer_total_shapes_layers'] = '99';
						}
						if (isset($this->request->post['product_designer_total_icons_layers'])) {
							$data['product_designer_total_icons_layers'] = $this->request->post['product_designer_total_icons_layers'];
						} elseif (!empty($product_info)) {
							$data['product_designer_total_icons_layers'] = $product_info['total_icons_layers'];
						} else {
							$data['product_designer_total_icons_layers'] = '99';
						}	
						
						if (isset($this->request->post['product_designer_canvas_size'])) {
							$data['product_designer_canvas_size'] = $this->request->post['product_designer_canvas_size'];
						} elseif (!empty($product_info)) {
							$data['product_designer_canvas_size'] = $product_info['size_unit'];
						} else {
							$data['product_designer_canvas_size'] = '';
						}
						if (isset($this->request->post['product_designer_sort_order'])) {
							$data['product_designer_sort_order'] = $this->request->post['product_designer_sort_order'];
						} elseif (!empty($product_info)) {
							$data['product_designer_sort_order'] = $product_info['sort_order'];
						} else {
							$data['product_designer_sort_order'] = '';
						}	
		
			if (isset($this->request->post['status'])) {
				$data['status'] = $this->request->post['status'];
				} elseif (!empty($product_info)) {
				$data['status'] = $product_info['status'];
				} else {
				$data['status'] = true;
			}	
			$this->load->model('tool/image');
			
			if (isset($this->request->post['template_image'])) {
				$data['template_image'] = $this->request->post['template_image'];
				} elseif (!empty($product_info)) {
				$data['template_image'] = $product_info['template_image'];
				} else {
				$data['template_image'] = '';
			}
			
			if (isset($this->request->post['template_image']) && is_file(DIR_IMAGE . $this->request->post['template_image'])) {
				$data['thumb'] = $this->model_tool_image->resize($this->request->post['template_image'], 100, 100);
				} elseif (!empty($product_info) && is_file(DIR_IMAGE . $product_info['template_image'])) {
				$data['thumb'] = $this->model_tool_image->resize($product_info['template_image'], 100, 100);
				} else {
				$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
			}			

			$http_server=explode('/',HTTPS_SERVER);
				array_pop($http_server);
				array_pop($http_server);
				$image_root=implode('/',$http_server);
				$desing_image_root=$image_root."/image/";
				$data['DESIGN_IMAGE_ROOT']=$image_root."/image/";
				
$this->load->model('tool/image');
$temp_design=array();
			if (isset($this->request->post['product_design_image'])) {
				$temp_design=json_decode($this->request->post['product_design_image'],true);

					if(!empty($temp_design)){
						foreach($temp_design as $temp_design_res){
						$thumb=$this->model_tool_image->resize('no_image.png', 100, 100);
						if(is_file(DIR_IMAGE . $temp_design_res['image'])){
						   $thumb = $this->model_tool_image->resize($temp_design_res['image'], 100, 100);
						}
						$image='';
						$imgCan='';
						if(!empty($temp_design_res['image'])){
						$image=$desing_image_root.$temp_design_res['image'];
						$imgCan=$temp_design_res['image'];
						}
							$data['product_design_images'][]=array(
							'thumb'=>$thumb,
							'design_thumb'=>$image,
							'use_img'=>$temp_design_res['use_img'],
							'width'=>$temp_design_res['width'],
							'height'=>$temp_design_res['height'],
							'image'=>$imgCan,
							'canvas_area'=>$temp_design_res['canvas_area'],
							'lable'=>$temp_design_res['lable'],
							'dpi'=>$temp_design_res['dpi'],
							'safe_lines'=>$temp_design_res['safe_lines'],
							'alwaysontop'=>isset($temp_design_res['alwaysontop'])?$temp_design_res['alwaysontop']:0,
							'bleed_size'=>$temp_design_res['bleed_size'],
							'fold_line'=>$temp_design_res['fold_line'],
							'sort_order'=>$temp_design_res['sort_order'],
							'canvasJsonData'=>$temp_design_res['canvasJsonData'],
							);	
						}
					}
				} elseif (!empty($product_info['product_template_design'])) {
					$temp_design=json_decode($product_info['product_template_design'],true);
					if(!empty($temp_design)){
						foreach($temp_design as $temp_design_res){
							$thumb=$this->model_tool_image->resize('no_image.png', 100, 100);
						if(is_file(DIR_IMAGE . $temp_design_res['image'])){
						   $thumb = $this->model_tool_image->resize($temp_design_res['image'], 100, 100);
						}
						$image='';
						$imgCan='';
						if(!empty($temp_design_res['image'])){
						$image=$desing_image_root.$temp_design_res['image'];
						$imgCan=$temp_design_res['image'];
						}
						$canvas_area='';
						if(!empty($temp_design_res['canvas_area'])){
							$canvas_area=$temp_design_res['canvas_area'];
						}
						$canvasJsonData='';
						if(!empty($temp_design_res['canvasJsonData'])){
							$canvasJsonData=$temp_design_res['canvasJsonData'];
						}
						
						 $use_img=0;
						if(!empty($temp_design_res['use_img'])){
							$use_img=$temp_design_res['use_img'];
						} 

						$width='';
						if(!empty($temp_design_res['width'])){
							$width=$temp_design_res['width'];
						}
						$height='';
						if(!empty($temp_design_res['height'])){
							$height=$temp_design_res['height'];
						}
						
						$fold_line=0;
						if(!empty($temp_design_res['fold_line'])){
							$fold_line=$temp_design_res['fold_line'];
						}
						
							$data['product_design_images'][]=array(
							'thumb'=>$thumb,
							'design_thumb'=>$image,
							'use_img'=>$use_img,
							'width'=>($width)?$width:'550',
							'height'=>($height)?$height:'550',
							'image'=>$imgCan,
							'canvas_area'=>$canvas_area,
							'lable'=>$temp_design_res['lable'],
							'dpi'=>$temp_design_res['dpi'],
							'safe_lines'=>$temp_design_res['safe_lines'],
							'alwaysontop'=>isset($temp_design_res['alwaysontop'])?$temp_design_res['alwaysontop']:0,
							'bleed_size'=>$temp_design_res['bleed_size'],
							'fold_line'=>$fold_line,
							'sort_order'=>$temp_design_res['sort_order'],
							'canvasJsonData'=>$canvasJsonData,
							);	
							
							$can_design_area[]=array(
							'canvas_area'=>$canvas_area,
							);	
							
						}
						$data['can_design_area']=json_encode($can_design_area);
						
					}
				} else {	
				$data['product_design_images'] = '';
			}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		
			$data['product_id'] = $this->request->get['product_id'];
			if (isset($this->request->get['id'])) {
				$data['action'] = $this->url->link('extension/purpletree_product_designer/producttemplate/editTemplate', 'user_token=' . $this->session->data['user_token'] . '&id=' . $this->request->get['id'] . $url, true);
			}
			$data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();
			
			if(isset($this->request->get['id'])){	
				$data['template_id'] = $this->model_extension_purpletree_product_designer_pts_product_template->getTemplateId1($this->request->get['id']);
			}
			if(isset($this->request->get['id'])){	
				$data['id'] = $this->request->get['id'];
			} 
			$icons =array();
			$icons = $this->model_extension_purpletree_product_designer_pts_product_template->getIcons();
			
			if(!empty($icons)){
				foreach($icons as $iconKey=>$iconVal){
					$iconsData[$iconVal['icon_category']][]=array(
						'class'=>$iconVal['icon_class'],
						'name'=>$iconVal['icon_name'],
						'unicode'=>$iconVal['icon_code'],
					);
				}
			}
				$cateCount=0;
				if(!empty($iconsData)){
					foreach($iconsData as $cate=>$val){
						$x=array();
						foreach($val as $key1=>$val1){
								$x[$cateCount]=array(
									'class'=>$val1['class'],
									'name'=>$val1['name'],
									'unicode'=>$val1['unicode']
								);
							$cateCount++;
						}
					$data['ptsIcons'][$cate]=$x;
					}	
				}
				$data['baseurl'] = HTTP_CATALOG;
				// echo "<pre>";
				// print_r($data['product_design_images']);
				// die;
			$data['user_token'] = $this->session->data['user_token'];
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			$this->response->setOutput($this->load->view('extension/purpletree_product_designer/templatedetail_form',$data));
		}
		public function editTemplate() {
			if(!$this->config->get('module_purpletree_product_designer_template')) {
				$this->response->redirect($this->url->link('extension/module/purpletree_product_designer', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}
			$this->load->language('purpletree_product_designer/admintemplate');
			$this->load->language('pts_product_designer/pts_product_designer');
			$this->document->setTitle($this->language->get('heading_title1'));
			
			$this->load->model('extension/purpletree_product_designer/pts_product_template');
			
			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateFormTemp()) {

if(!empty($this->request->post['product_design_image'])){
	foreach($this->request->post['product_design_image'] as $keys=>$values){

		++$keys;
		if($values['lable']==''){
			$values['lable']='Label '.$keys;
		}
		
		if(!$values['use_img']){
			$values['use_img']=0;
		
		}
		if(!$values['use_img']){
			$values['image']='';
		}
		
		if(!$values['width']){
			$values['width']='';
		}
		
		if(!$values['height']){
			$values['height']='';
		}
		
		if($values['dpi']==''){
			$values['dpi']=100;
		}
		if($values['safe_lines']==''){
			$values['safe_lines']=0;
		}
		if(!isset($values['alwaysontop']) || $values['alwaysontop']==''){
			$values['alwaysontop']=0;
		}
		if($values['bleed_size']==''){
			$values['bleed_size']=0;
		}
		if($values['fold_line']==''){
			$values['fold_line']=0;
		}
		$designData[$keys] = Array(
            'image'   => $values['image'],
            'use_img' => $values['use_img'],
            'width'   => $values['width'],
            'height'  => $values['height'],
            'lable'   => $values['lable'],
            'dpi'     => $values['dpi'],
            'safe_lines' => $values['safe_lines'],
            'alwaysontop' => $values['alwaysontop'],
            'bleed_size' =>$values['bleed_size'], 
            'fold_line' =>$values['fold_line'], 
            'sort_order' => $values['sort_order'],
            'canvasJsonData' => $values['canvasJsonData'],
			);
	}
}
				$this->request->post['product_template_design']=json_encode($designData);

				if(!isset($this->request->get['id'])){
				$this->model_extension_purpletree_product_designer_pts_product_template->addTemplateData($this->request->post);		
				}

				if(isset($this->request->get['id'])){
				$this->model_extension_purpletree_product_designer_pts_product_template->editsellertempDetail($this->request->get['id'], $this->request->post);
				}

				$this->session->data['success'] = $this->language->get('text_success');
				
				$url = '';
				
				if (isset($this->request->get['filter_name'])) {
					$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
				}
				
				if (isset($this->request->get['template_name'])) {
					$url .= '&template_name=' . urlencode(html_entity_decode($this->request->get['template_name'], ENT_QUOTES, 'UTF-8'));
				}
				
				if (isset($this->request->get['filter_status'])) {
					$url .= '&filter_status=' . $this->request->get['filter_status'];
				}
				
				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}
				
				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}
				
				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}
				
				$this->response->redirect($this->url->link('extension/purpletree_product_designer/producttemplate', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}
			
			$this->productForm();
		}
		protected function validateFormTemp() {
			if (!$this->user->hasPermission('modify', 'extension/purpletree_product_designer/producttemplate')) {
				$this->error['warning'] = $this->language->get('error_permission');
			}

			if ($this->error && !isset($this->error['warning'])) {
				$this->error['warning'] = $this->language->get('error_warning');
			}		
			
			return !$this->error;
		}
		protected function validateDelete() {
			if (!$this->user->hasPermission('modify', 'extension/purpletree_product_designer/producttemplate')) {
				$this->error['warning'] = $this->language->get('error_permission');
			}
			
			return !$this->error;
		}
		
		public function uploadImage() {
			$uploadedImages=$this->request->post['uploadfile'];
			$uploadedImg = explode(',', $uploadedImages);
			$image = base64_decode($uploadedImg[1]);
			$datetime=date('dmYHis');
			$path = "catalog/ptsdesigner/pts_design_".$datetime."_uploaded_image.png"; 
			$filepath = DIR_IMAGE.$path; 
			file_put_contents($filepath,$image);
			$url=HTTPS_CATALOG.'image/'.$path;
			$json['url']=$url;
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	public function veiwAdminCanvas() {
		$this->load->language('purpletree_product_designer/admintemplate');
		$data['confirm_message']      =$this->language->get('confirm_message');
		$data['bg_conf_message']      =$this->language->get('bg_conf_message');
		$data['text_edit']		      =$this->language->get('text_edit');
		$data['text_bg_color']		  =$this->language->get('text_bg_color');
		$data['text_bg_reset']		  =$this->language->get('text_bg_reset');
		$data['text_rotation']		  =$this->language->get('text_rotation');
		$data['text_horizontal_width']=$this->language->get('text_horizontal_width');
		$data['text_vertical_height'] =$this->language->get('text_vertical_height');
		$data['text_font_size'] 	  =$this->language->get('text_font_size');
		$data['text_color'] 		  =$this->language->get('text_color');
		$data['text_font_family']     =$this->language->get('text_font_family');
		$data['text_shadow']          =$this->language->get('text_shadow');
		$data['text_style']           =$this->language->get('text_style');
		$data['text_shadow_right']    =$this->language->get('text_shadow_right');
		$data['text_shadow_bottom']   =$this->language->get('text_shadow_bottom');
		$data['text_shadow_blur']     =$this->language->get('text_shadow_blur');
		$data['text_shadow_color']    =$this->language->get('text_shadow_color');
		$data['text_align']           =$this->language->get('text_align');
		$data['text_line_height']     =$this->language->get('text_line_height');
		$data['text_outline_width']   =$this->language->get('text_outline_width');
		$data['text_outline_color']   =$this->language->get('text_outline_color');
		$data['text_add_text']        =$this->language->get('text_add_text');
		$data['text_add_clipart']     =$this->language->get('text_add_clipart');
		$data['text_upload_image']    =$this->language->get('text_upload_image');
		$data['text_layers']          =$this->language->get('text_layers');
		$data['text_demo']            =$this->language->get('text_demo');
		$data['text_apply']           =$this->language->get('text_apply');
		$data['text_select_image']    =$this->language->get('text_select_image');
		$data['text_upload_photo']    =$this->language->get('text_upload_photo');
		$data['text_photo_uploaded']  =$this->language->get('text_photo_uploaded');
		$data['text_choose_file']     =$this->language->get('text_choose_file');
		$data['text_file_type']       =$this->language->get('text_file_type');
		$data['text_file_types']      =$this->language->get('text_file_types');
		$data['text_file_formate']    =$this->language->get('text_file_formate');
		$data['text_copyright']       =$this->language->get('text_copyright');
		$data['text_drop']            =$this->language->get('text_drop');
		$data['text_drop_info']       =$this->language->get('text_drop_info');
		$data['text_upload_files']    =$this->language->get('text_upload_files');
		$data['text_font_family']     =$this->language->get('text_font_family');
		$data['text_select_font']     =$this->language->get('text_select_font');
		$data['text_font_rang']       =$this->language->get('text_font_rang');
		$data['text_close']           =$this->language->get('text_close');
		$data['text_view']            =$this->language->get('text_view');
		$data['text_design_total_layer_limit']=$this->language->get('text_design_total_layer_limit');
		$data['text_total_clipart_layer_limit']=$this->language->get('text_total_clipart_layer_limit');
		$data['text_total_image_layer_limit'] =$this->language->get('text_total_image_layer_limit');
		$data['text_total_text_layer_limit']=$this->language->get('text_total_text_layer_limit');

			$product_design_images=$this->request->post;
			/* echo "<pre>";
			print_r($product_design_images);
			die; */
			$max_height=array();
			$pt_design = array();
			$pt_design = $product_design_images['product_template_design'];
			if(!empty($pt_design)){
				foreach($pt_design as $ptData){
					$canvas_area=array();
					if(!empty($ptData['canvasJsonData'])){
					 $jsonData = stripslashes(html_entity_decode($ptData['canvasJsonData']));

						$cndata= json_decode($jsonData,true);
						
						if(!empty($cndata)){
							$canvas_area = $cndata;
						}
					}
					$canvasJsonData='';
					if(!empty($ptData['canvasJsonData'])){
						$canvasJsonData=$ptData['canvasJsonData'];
					}
					  $fold_line =0;
					if(isset($ptData['fold_line'])){
					  $fold_line = $ptData['fold_line'];
					}
					$canvasAreaData[]= array(
				    'id' => $ptData['canId'],
				    'useImg'  => $ptData['use_image'],
				    'canvasWidth'  => $ptData['canvas_width'],
				    'canvasHeight' => $ptData['canvas_height'],
				    'template_id' => $product_design_images['template_id'],
					'product_id'  => $product_design_images['product_id'],
					'lable' 	  => $ptData['lable'],
					'dpi'         => $ptData['dpi'],
					'safe_lines'  => $ptData['safe_lines'],
					'alwaysontop'  => isset($ptData['alwaysontop'])?$ptData['alwaysontop']:0,
					'bleed_size'  => $ptData['bleed_size'],
					'fold_line'  =>$fold_line,
					'design_image'=> $ptData['image'],
					'can_left'    => 20,
					'can_top' 	  =>25,
					'can_width'   => 510,
					'can_height'  => 298,
					'sort_order'  => $ptData['sort_order'],
					'canvas_area' => $canvas_area,
					'canvasJsonData' =>$canvasJsonData,
					);
					}
				}
		if(!empty($canvasAreaData)){
				foreach ($canvasAreaData as $product_design_image) {
					if (is_file(DIR_IMAGE . $product_design_image['design_image'])) {
						$image = $product_design_image['design_image'];
						$thumb = $product_design_image['design_image'];
					} else {
						$image = '';
						$thumb = 'no_image.png';
					}
					
					if ($this->request->server['HTTPS']) {
						$design_image = HTTP_CATALOG . 'image/' . $image;
					} else {
						$design_image = HTTP_CATALOG . 'image/' . $image;
					}
					
					$img_width = 550;
					$img_height = 550;
					if (is_file(DIR_IMAGE . $image)) {
					list($img_width,$img_height,$type)=getImageSize(DIR_IMAGE.$image);
					}
					if(!$product_design_image['useImg']){
						$img_width=$product_design_image['canvasWidth'];
						$img_height=$product_design_image['canvasHeight'];
					}
					if($product_design_image['useImg']==1){
					 $img_width=$img_width+$product_design_image['bleed_size'];
					 $img_height=$img_height+$product_design_image['bleed_size'];
					}
					 
					 $div_width=$img_width;
					 $div_height= $img_height;
					 $fix_canvas_width=550;
						$canvas_left= 0;
					 if($product_design_image['can_left']){
						$canvas_left= ($product_design_image['can_left'])*($div_width/$img_width);
					 }
						$canvas_top= 0;
					 if($product_design_image['can_top']){
						$canvas_top= ($product_design_image['can_top'])*($div_height/$img_height);
					 }
						$canvas_width= 0;
					 if($product_design_image['can_width']){
						$canvas_width= ($product_design_image['can_width']*$div_width)/$img_width;
					 }
						$canvas_height= 0;
					 if($product_design_image['can_height']){
						$canvas_height= ($product_design_image['can_height'])*($div_height/$img_height);
					 }
					  $scaleforlongimage = 1;
					  $div_width1 = $div_width;
						$div_height1 = $div_height;
					 if($fix_canvas_width<$img_width){
						$div_width1 = $fix_canvas_width;
						$div_height1 = ($div_width1*$img_height)/$img_width;
						$scaleforlongimage = $img_width/$div_width1;
					 }
					 
					 $text_width_scale=1;
					$text_height_scale=1;
					
					if($div_width1 > 0){
					$text_width_scale=1+($img_width/$div_width1);
					}
					
					if($div_height1 > 0){
					$text_height_scale=1+($img_height/$div_height1);
					}
					
					$canvasLeft = $product_design_image['safe_lines']+$product_design_image['bleed_size'];
					
					$cropLeft = $product_design_image['bleed_size'];
					$bleedLeft=0;
					
					$safe_line = false;
					$crop_line = false;
					$bleed_line= true;	
					
					if((int)$product_design_image['safe_lines']){
					$safe_line=true;	
					}
					if((int)$product_design_image['bleed_size']){
					$crop_line=true;	
					}
					$fold_line=array();
					if(isset($product_design_image['fold_line']) && !empty($product_design_image['fold_line'])){
					$fold_line= explode(',',$product_design_image['fold_line']);	
					}
					
					$data['product_design_images'][] = array(
						'id'           => $product_design_image['id'],
						'image'        => $image,
						'useImg'       => $product_design_image['useImg'],
						'width'        => $product_design_image['canvasWidth'],
						'height'       => $product_design_image['canvasHeight'],
						'design_images'=>  $design_image,
						'div_width'    =>  $img_width,
						'div_width1'=>  $div_width1,
						'div_height'=>  $img_height,
						'div_height1'=>  $div_height1,
						'lable' 	=> $product_design_image['lable'],
						'dpi' 	=> $product_design_image['dpi'],
						'safe_lines'=> $product_design_image['safe_lines'],
						'alwaysontop'=> isset($product_design_image['alwaysontop'])?$product_design_image['alwaysontop']:0,
						'bleed_size' => $product_design_image['bleed_size'],
						'fold_line' => $fold_line,
						'safe_line' => $safe_line,
						'crop_line' => $crop_line,
						'bleed_line' => $bleed_line,
						'can_left'  => $canvasLeft,
						'can_top' => $canvasLeft,
						'can_width' => ($div_width-$canvasLeft*2),
						'can_height'=> ($div_height-$canvasLeft*2),
						'crop_left'  => $cropLeft,
						'crop_top' => $cropLeft,
						'crop_width' => ($div_width-$cropLeft*2),
						'crop_height'=> ($div_height-$cropLeft*2),
						'bleed_left'  => $bleedLeft,
						'bleed_top' => $bleedLeft,
						'bleed_width' => ($div_width-$bleedLeft*2),
						'bleed_height'=> ($div_height-$bleedLeft*2),
						'can_left1'  => $product_design_image['can_left'],
						'can_top1' => $product_design_image['can_top'],
						'can_width1' => $product_design_image['can_width'],
						'can_height1'=> $product_design_image['can_height'],
						'canvas_left'=> $canvas_left,
						'canvas_top'=> $canvas_top,
						'canvas_width'=> $canvas_width,
						'canvas_height'=> $canvas_height,
						'image_width'=> $img_width, 
						'image_height'=> $img_height,
						'sort_order'=> $product_design_image['sort_order'],
						'canvas_area'=> $product_design_image['canvas_area'],
						'canvasJsonData'=> $product_design_image['canvasJsonData'],
						'text_w_scale'	 =>  $text_width_scale,
						'text_h_scale'	 =>  $text_height_scale
					);
					$max_height[] =$div_height;
				}
			}	
			// echo "<pre>";
			// print_r($data['product_design_images']);
			// die;		
			$data['total_layers']=999;
			$data['total_text_layers']=99;
			$data['total_clipart_layers']=99;
			$data['total_image_layers']=99;
			$data['total_icons_layers']=99;
			$data['total_shapes_layers']=99;
			$data['wm_status']=$this->config->get('module_purpletree_product_designer_wm_status');
			$data['wm_text']=$this->config->get('module_purpletree_product_designer_wm_text');
			$data['max_height']=max($max_height)+81;			
			$data['plusvallue']=300;	
			$this->load->model('extension/purpletree_product_designer/pts_product_template');
			$fontss = $this->model_extension_purpletree_product_designer_pts_product_template->getFonts();
			$data['fonts'] = array();
			foreach ($fontss as $font) {
				$data['fonts'][] = array(
				   'font_name' => $font['font_name']
				);
			}
		$data['fontsd']=array(
			'Arial'=>'',
			'Arial Narrow'=>'',
			'Calibri'=>'',
			'Cairo'=>'sans-serif',
			'Amiri'=>'serif',
			'Lalezar'=>'cursive',
			'El Messiri'=>'sans-serif',
			'Changa'=>'sans-serif',
			'Tajawal'=>'sans-serif',
			'Scheherazade'=>'serif',
			'Lateef'=>'cursive',
			'Markazi Text'=>'serif',
			'Mada'=>'sans-serif',
			'Noto Sans'=>'sans-serif',
			'Hind'=>'sans-serif',
			'Rajdhani'=>'sans-serif',
			'Mukta'=>'sans-serif',
			'Teko'=>'sans-serif',
			'Kalam'=>'cursive',
			'Martel'=>'serif',
			'Khand'=>'sans-serif',
			'Pragati Narrow'=>'sans-serif',
			'Glegoo'=>'serif',
			'Amita'=>'cursive',
			'Roboto'=>'sans-serif',
			'Noto Sans HK'=>'sans-serif',
			'Open Sans'=>'sans-serif',
			'Lato'=>'sans-serif',
			'Aguafina Script'=>'cursive',
			'Montserrat'=>'sans-serif',
			'Mali'=>'cursive',
			'Source Sans Pro'=>'sans-serif',
			'Roboto Condensed'=>'sans-serif',
			'Roboto Mono'=>'monospace',
			'Roboto Slab'=>'serif',
			'Merriweather'=>'serif',
			'Slabo 27px'=>'serif',
			'Saira Semi Condensed'=>'sans-serif',
			'PT Sans'=>'sans-serif',
			'Ubuntu'=>'sans-serif',
			'Open Sans Condensed'=>'sans-serif',
			'Muli'=>'sans-serif',
			'Dosis'=>'sans-serif',
			'Crimson Text'=>'serif',
			'Indie Flower'=>'cursive',
			'Abel'=>'sans-serif',
			'Pacifico'=>'cursive',
			'Dancing Script'=>'cursive',
			'Merriweather Sans'=>'sans-serif',
			'Swanky and Moo Moo'=>'cursive',
			'Shadows Into Light'=>'cursive',
			'Barlow'=>'sans-serif',
			'Oswald'=>'sans-serif',
			);
			$this->load->model('tool/image');
			$data['product_design_max_min']= '';
			if(NULL !== $this->config->get('module_purpletree_product_designer_text')) {
			$data['product_design_max_min'] = json_encode(array_values($this->config->get('module_purpletree_product_designer_text')));
			}
			
			$data['no_of_canvas']=0;
			if(!empty($data['product_design_images'])){
			$data['no_of_canvas'] = count($data['product_design_images']);
			}
			if(!empty($data['product_design_images'])){
				foreach($data['product_design_images'] as $kkk=> $designResult){
				$designResults[]=$designResult['canvas_area'];
					}
			}
			$clipArtImages=array();
			$clipArtImages=$this->model_extension_purpletree_product_designer_pts_product_template->getClipartImages();
			$this->load->model('tool/image');
			//add not empty condition
			$data['clipArtImages'] = array();
			if(!empty($clipArtImages)) {
				foreach($clipArtImages as $pts_key=>$pts_value){
					$ext= substr(basename($pts_value['clipart_image']), strrpos(basename($pts_value['clipart_image']), '.')+1);
					$data['clipArtImages'][]=array(
					'clipart_id'=>$pts_value['clipart_id'],
					'image_name'=>basename($pts_value['clipart_image']),
					'clipart_image'=>$pts_value['clipart_image'],
					'thumb_clipart_image'=>$this->model_tool_image->resize($pts_value['clipart_image'],150,100),
					'ext'=>$ext,
					'svg_image'=>HTTP_CATALOG . 'image/'.$pts_value['clipart_image']
					);
				}
			}
				$icons =array();
			$icons = $this->model_extension_purpletree_product_designer_pts_product_template->getIcons();
			
			if(!empty($icons)){
				foreach($icons as $iconKey=>$iconVal){
					$iconsData[$iconVal['icon_category']][]=array(
						'class'=>$iconVal['icon_class'],
						'name'=>$iconVal['icon_name'],
						'unicode'=>$iconVal['icon_code'],
					);
				}
			}
				$cateCount=0;
				if(!empty($iconsData)){
					foreach($iconsData as $cate=>$val){
						$x=array();
						foreach($val as $key1=>$val1){
								$x[$cateCount]=array(
									'class'=>$val1['class'],
									'name'=>$val1['name'],
									'unicode'=>$val1['unicode']
								);
							$cateCount++;
						}
					$data['ptsIcons'][$cate]=$x;
					}	
				}
	
			$data['cmykStatus']=0;
			if($this->config->get('module_purpletree_product_designer_cmykcolorpick')){
			$data['cmykStatus']=$this->config->get('module_purpletree_product_designer_cmykcolorpick');
			}
			
			$data['baseurl'] = HTTP_CATALOG;
			$data['designResults'] = json_encode($data['product_design_images']);

			$data['user_token'] = $this->session->data['user_token'];

			$this->response->setOutput($this->load->view('extension/purpletree_product_designer/productcanvasview', $data));
		}
	public function autocomplete() {
			$json = array();
			
			if (isset($this->request->get['filter_name']) || isset($this->request->get['template_name'])) {
				$this->load->model('catalog/product');
				$this->load->model('extension/purpletree_product_designer/pts_product_template');

				if (isset($this->request->get['filter_name'])) {
					$filter_name = $this->request->get['filter_name'];
					} else {
					$filter_name = '';
				}
				
				if (isset($this->request->get['template_name'])) {
					$template_name = $this->request->get['template_name'];
					 $grouppro = 0;
					} else {
					$template_name = '';
					 $grouppro = 1;
				}
				if (isset($this->request->get['filter_status'])) {
					$filter_status = $this->request->get['filter_status'];
					} else {
					$filter_status = '';
				}
				if (isset($this->request->get['limit'])) {
					$limit = $this->request->get['limit'];
					} else {
					$limit = 5;
				}
				
				$filter_data = array(
				'filter_name'  => $filter_name,
				'template_name' => $template_name,
				'filter_status' => $filter_status,
				'start'        => 0,
				'limit'        => $limit,
				'grouppro'        => $grouppro,
				);
				
			
			$results = $this->model_extension_purpletree_product_designer_pts_product_template->geSellerproducttemp($filter_data);
			
				foreach ($results as $result) {
					$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['product_name'], ENT_QUOTES, 'UTF-8')),
					'template_name'      => strip_tags(html_entity_decode($result['template_name'], ENT_QUOTES, 'UTF-8')),
					'status'      => $result['template_status']
					);
				}
			}
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
	}
	public function search() {
		$json = array();
		$this->load->model('tool/image');
		if(isset($this->request->post['art_search_keyword'])){
			$this->load->model('extension/purpletree_product_designer/pts_product_template');
					$clipArtImages=$this->model_extension_purpletree_product_designer_pts_product_template->getClipartAjaxImages($this->request->post['art_search_keyword']);
			if(!empty($clipArtImages)){		
				foreach($clipArtImages as $pts_key=>$pts_value){
					$ext= substr(basename($pts_value['clipart_image']), strrpos(basename($pts_value['clipart_image']), '.')+1);
					if($ext=='svg'){
					$thumb_clipart_image='../image/'.$pts_value['clipart_image'];	
					} else {
					$thumb_clipart_image=$this->model_tool_image->resize($pts_value['clipart_image'],150,100);	
					}
					$json['clipArtImages'][]=array(
					'clipart_id'=>$pts_value['clipart_id'],
					'image_name'=>basename($pts_value['clipart_image']),
					'clipart_image'=>$pts_value['clipart_image'],
					'thumb_clipart_image'=>$thumb_clipart_image
					);
				}
			} 	
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function iconSearch() {
		$json = array();
		$this->load->model('tool/image');
		if(isset($this->request->post['icon_search_keyword'])){
			$this->load->model('extension/purpletree_product_designer/pts_product_template');
					$icons=$this->model_extension_purpletree_product_designer_pts_product_template->getIconByAjax($this->request->post['icon_search_keyword']);
					if(!empty($icons)){
				foreach($icons as $iconKey=>$iconVal){
					$iconsData[$iconVal['icon_category']][]=array(
						'class'=>$iconVal['icon_class'],
						'name'=>$iconVal['icon_name'],
						'unicode'=>$iconVal['icon_code'],
					);
				}
			}
				$cateCount=0;
				if(!empty($iconsData)){
					foreach($iconsData as $cate=>$val){
						$x=array();
						foreach($val as $key1=>$val1){
								$x[$cateCount]=array(
									'class'=>$val1['class'],
									'name'=>$val1['name'],
									'unicode'=>$val1['unicode']
								);
							$cateCount++;
						}
					$json['ptsIcons'][$cate]=$x;
					}	
				}

		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	public function saveCanvasRow() {

		$this->load->model('extension/purpletree_product_designer/pts_product_template');
					
		if(!empty($this->request->post['template_id'])){
			$template_id=$this->request->post['template_id'];
		if(!empty($this->request->post['product_design_image'])){
	foreach($this->request->post['product_design_image'] as $keys=>$values){

		++$keys;
		if($values['lable']==''){
			$values['lable']='Label '.$keys;
		}
		
		if(!$values['use_img']){
			$values['use_img']=0;
		
		}
		if(!$values['use_img']){
			$values['image']='';
		}
		
		if(!$values['width']){
			$values['width']='';
		}
		
		if(!$values['height']){
			$values['height']='';
		}
		
		if($values['dpi']==''){
			$values['dpi']=100;
		}
		if($values['safe_lines']==''){
			$values['safe_lines']=0;
		}
		if(!isset($values['alwaysontop']) || $values['alwaysontop']==''){
			$values['alwaysontop']=0;
		}
		if($values['bleed_size']==''){
			$values['bleed_size']=0;
		}
		$designData[$keys] = Array(
            'image'   => $values['image'],
            'use_img' => $values['use_img'],
            'width'   => $values['width'],
            'height'  => $values['height'],
            'lable'   => $values['lable'],
            'dpi'     => $values['dpi'],
            'safe_lines' => $values['safe_lines'],
            'alwaysontop' => $values['alwaysontop'],
            'alwaysontop' => $values['alwaysontop'],
            'bleed_size' =>$values['bleed_size'], 
            'sort_order' => $values['sort_order'],
            'canvasJsonData' => $values['canvasJsonData'],
			);
		}
		$this->model_extension_purpletree_product_designer_pts_product_template->editCanvasLabel($template_id,json_encode($designData));
		$json['success']="Canvas data has been saved";
		$json['popup_hide']=1;	
	} else {
		$json['error']="Canvas data is empty";
		$json['popup_hide']=0;	
	}
		}else {
		$json['error']="Template id is not found";	
		$json['popup_hide']=0;	
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	} 
}
?>