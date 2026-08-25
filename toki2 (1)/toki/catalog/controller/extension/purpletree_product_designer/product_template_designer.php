<?php
class ControllerExtensionPurpletreeProductDesignerProductTemplateDesigner extends Controller{
	public function index()
	{  
	$data = array();
	if(!$this->customer->isLogged()) {
		   $data['login'] = 1;
	    } else {
			   	$this->load->model('extension/purpletree_product_designer/product_template_designer');
			 if(isset($this->session->data['downloadpdf'])) {
				$data['downloadpdf'] = $this->session->data['downloadpdf'];
				unset($this->session->data['downloadpdf']);
		   }
		   if(isset($this->session->data['saveddata'])) {
					$this->model_extension_purpletree_product_designer_product_template_designer->saveCanvasDatacustomer($this->session->data['saveddata']);
				unset($this->session->data['saveddata']);
		   }
		   if(isset($this->session->data['downloadpdfemail'])) {
		    $data['downloadpdfemail'] = $this->session->data['downloadpdfemail'];
			$this->emailToClient($data['downloadpdfemail']);
			$data['ptssuccess'] = 'Email Sent Successfully.';
		   unset($this->session->data['downloadpdfemail']);
		   }
		}
		if ($this->request->server['HTTPS']) {
			$data['baseurl'] = $this->config->get('config_ssl');
		} else {
			$data['baseurl'] = $this->config->get('config_url');
		}
		$this->load->language('product/product');
		$this->load->language('purpletree_product_designer/product_designer');
		//add cutom options
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);
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
	
			$this->load->model('catalog/product');
			$product_id = isset($this->request->get['product_id'])?$this->request->get['product_id']:'';
			$product_info = array();
			$online_product_design_status = 1;
			if($product_id != '') {
				$product_info = $this->model_catalog_product->getProduct($product_id);
				if($this->config->get('module_purpletree_product_designer_status')){
					$online_product_design_status = $this->model_catalog_product->getProductDesignStatus($product_id);
				}
			}

			$online_product_design_status = 1;
	if (!empty($product_info) && $online_product_design_status == 1) {
		$data['product_url'] = $this->url->link('product/product', 'product_id=' . $product_id);
		//product
		$data['breadcrumbs'][] = array(
			'text' => $product_info['name'],
			'href' => $this->url->link('product/product', 'product_id=' . $product_id)
		);
		$this->document->setTitle($product_info['meta_title']);
		$this->document->setDescription($product_info['meta_description']);
		$this->document->setKeywords($product_info['meta_keyword']);

		//in condition
	
		if($this->config->get('module_purpletree_product_designer_jquery')) {
			$this->document->addScript('catalog/view/theme/default/stylesheet/ptsproductdesign/jquery/jquery-2.1.1.min.js');
		}
			if($this->config->get('module_purpletree_product_designer_jquery_ui')) {
			//$this->document->addScript('catalog/view/theme/default/stylesheet/ptsproductdesign/jquery/jquery-ui.js'); 
		}
		//in condition
		if($this->config->get('module_purpletree_product_designer_bootstrapjs')) {
			$this->document->addScript('catalog/view/theme/default/stylesheet/ptsproductdesign/jquery/bootstrap.min.js'); 
		}
		$this->document->addScript('catalog/view/theme/default/stylesheet/ptsproductdesign/jquery/datetimepicker/moment/moment.min.js');
		$this->document->addScript('catalog/view/theme/default/stylesheet/ptsproductdesign/jquery/datetimepicker/moment/moment-with-locales.min.js');
		$this->document->addScript('catalog/view/theme/default/stylesheet/ptsproductdesign/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
		
		$this->document->addStyle('catalog/view/theme/default/stylesheet/ptsproductdesign/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

		$data['heading_title'] = $product_info['name'];
		//product
			//add cutom options
			$this->load->language('extension/purpletree_product_designer/product_designer');		
			$this->document->addScript('catalog/view/theme/default/stylesheet/ptsproductdesign/jquery/canvas.min.js'); 
			$this->document->addScript('catalog/view/theme/default/stylesheet/ptsproductdesign/jquery/pts-customiseControls.js');  
			$this->document->addScript('catalog/view/theme/default/stylesheet/ptsproductdesign/jquery/colorpicker/js/jscolor.js'); 
			$this->document->addStyle('catalog/view/theme/default/stylesheet/ptsproductdesign/jquery/colorpicker/css/colorpicker.css'); 
			$this->document->addStyle('catalog/view/theme/default/stylesheet/ptsproductdesign/css/jquery-ui.css'); 
			$this->document->addStyle('catalog/view/theme/default/stylesheet/ptsproductdesign/css/font-awesome.min.css');
			$direction = $this->language->get('direction');
			if ($direction=='rtl'){			
				$this->document->addStyle('catalog/view/theme/default/stylesheet/ptsproductdesign/css/bootstrap.min-a.css');
			} else {
				$this->document->addStyle('catalog/view/theme/default/stylesheet/ptsproductdesign/css/bootstrap.min.css');
			}			
			//$this->document->addStyle('catalog/view/theme/default/stylesheet/stylesheet.css'); 
			$this->document->addStyle('catalog/view/theme/default/stylesheet/ptsproductdesign/font-awesome/css/font-awesome.min.css'); 
			if ($direction=='rtl'){
				$this->document->addStyle('catalog/view/theme/default/stylesheet/ptsproductdesign/css/prductdesigner-a.css'); 
			}else{
				$this->document->addStyle('catalog/view/theme/default/stylesheet/ptsproductdesign/css/prductdesigner.css'); 
			}
			$this->document->addStyle('catalog/view/theme/default/stylesheet/ptsproductdesign/css/font-face.css'); 
			$this->load->model('extension/purpletree_product_designer/product_template_designer');
			$this->load->model('extension/purpletree_product_designer/product_designer');
			$clipArtImages=array();
			$logger = new Log('error.log'); 
			
			$clipArtImages=$this->model_extension_purpletree_product_designer_product_designer->getClipartImages();
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
					'svg_image'=>$this->config->get('config_ssl') . 'image/'.$pts_value['clipart_image']
					);
				}
			}
		
			if(!isset($this->request->get['product_id'])){
				$this->response->redirect($this->url->link('product/product', '', true));
			} 
			$data['product_id']=$this->request->get['product_id'];
			$this->load->model('tool/image');
			$template_id ='0';
			if(isset($this->request->get['template_id'])){
			$template_id=$this->request->get['template_id'];
			} 
			$getCartDatacustom = $this->model_extension_purpletree_product_designer_product_template_designer->getCartDatacustom($this->request->get['product_id']);
					$designfromcart='';
				if(!empty($getCartDatacustom)) {
					$designfromcart = $getCartDatacustom['design'];
				}

			$logger->write("Start Logger");
			$logger->write($designfromcart);
			$data['template_id'] = $template_id;

			$saved_id = isset($this->request->get['saved_id'])?$this->request->get['saved_id']:'';
			if($saved_id){
			$productDesignImage= $this->model_extension_purpletree_product_designer_product_template_designer->getCanvasData($saved_id);
			$canvasJsonDecodeData=json_decode($productDesignImage);
			if(!empty($canvasJsonDecodeData)){
				foreach($canvasJsonDecodeData as $canKey=>$canVal){
					if(!empty($canVal)){					
					$canFilter[]=stripslashes(html_entity_decode($canVal));
					}
				  }
		     	}
			}
			//saved data codei
			$product_id=$this->request->get['product_id'];
			
	// Product Design Data old		
	if($product_id and $template_id=='0'){
			$product_design_images1= $this->model_extension_purpletree_product_designer_product_designer->getProductDesignImages($this->request->get['product_id']);
			$sortCanData=array();

			 if(!empty($product_design_images1)){
				foreach($product_design_images1 as $product_design){
				
				if($product_design['use_img']==0){
						$product_design['can_left']   ='';	
						$product_design['can_top']    ='';	
						$product_design['can_width']  ='';	
						$product_design['can_height'] ='';	
					}
			$sortCanData[]= array(
							'image'=>$product_design['design_image'],
							'use_img'=>$product_design['use_img'],
							'width'=>$product_design['can_width'],
							'height'=>$product_design['can_height'],
							'lable'=>$product_design['lable'],
							'dpi'=>$product_design['dpi'],
							'safe_lines'=>$product_design['safe_line'],
							'bleed_size'=>$product_design['bleed_line'],
							'fold_line'=>$product_design['fold_line'],
							'alwaysontop'=>$product_design['alwaysontop'],
							'sort_order'=>$product_design['sort_order'],
							'can_left'=>$product_design['can_left'],
							'can_top'=>$product_design['can_top'],
							'can_width'=>$product_design['can_width'],
							'can_height'=>$product_design['can_height'],
							'canvas_width'=>$product_design['canvas_width'],
							'canvas_height'=>$product_design['canvas_height'],
							'canvasJsonData'=>'',
							);
				}
			}
			$product_design_images11= $this->model_extension_purpletree_product_designer_product_template_designer->getCanvasOldData($this->request->get['product_id']);
			$mergeCanvas=array();
			if(!empty($product_design_images11)){
				foreach($product_design_images11 as $kkkey=>$vvvvalue){
				$mergeCanvas=$vvvvalue;
				}
			}

			$mergeCanvas['product_template_design']=json_encode($sortCanData);	
			$product_design_images=array();
			$product_design_images=$mergeCanvas;
			$designerOld=1;
	}

	// Product Design Data old		

		if($product_id and $template_id!='0'){
			$product_design_images= $this->model_extension_purpletree_product_designer_product_template_designer->getTemplateProduct($product_id,$template_id);
			$designerOld=0;
		} 
			$max_height=array();
			$pt_design = array();
			$pt_design = json_decode($product_design_images['product_template_design'],true);

		if($saved_id){
				if(!empty($pt_design)){
			foreach($pt_design as $key=>$val){
					$canFilters=array();
					--$key;
				if(!empty($canFilter[$key])){
					$canFilters=$canFilter[$key];
				}
				$val['fold_line']=0;
				if(isset($val['fold_line'])){
					$val['fold_line']=$val['fold_line'];
				}
				if($designerOld==0){
					$val['can_left']='';
					$val['can_top']='';
					$val['can_width']='';
					$val['can_height']='';
					$val['canvas_width']='';
					$val['canvas_height']='';
				}
				$x[]=array(
					'image' => $val['image'],
					'lable' => $val['lable'],
					'use_img' => $val['use_img'],
					'width' => $val['width'],
					'height' => $val['height'],
					'dpi' => $val['dpi'],
					'safe_lines'  => $val['safe_lines'],
					'bleed_size'  => $val['bleed_size'],
					'fold_line'   => $val['fold_line'],
					'alwaysontop' => $val['alwaysontop'],
					'sort_order'  => $val['sort_order'],
					'can_left'=>$val['can_left'],
					'can_top'=>$val['can_top'],
					'can_width'=>$val['can_width'],
					'can_height'=>$val['can_height'],
					'canvas_width'=>$val['canvas_width'],
					'canvas_height'=>$val['canvas_height'],
					'canvasJsonData'=>$canFilters
				);
			}
			$pt_design = array();
			$pt_design = $x;
			}
		}

			if(isset($designfromcart) && ($designfromcart == 3 || $designerOld) && $template_id=='0') {
				$pt_design3 = $this->model_extension_purpletree_product_designer_product_template_designer->getProductDesignrcust($this->request->get['product_id']);

				$pt_design = array();
				if(!empty($pt_design3)) {
				foreach($pt_design3 as $kkeyy=>$pt_design31 ) {
					$canFilters=array();
					if(isset($canFilter[$kkeyy])){
					$canFilters=$canFilter[$kkeyy];
					}
					
					if($pt_design31['use_img']==0){
						$pt_design31['can_left']   ='';	
						$pt_design31['can_top']    ='';	
						$pt_design31['can_width']  ='';	
						$pt_design31['can_height'] ='';	
					} 
						$fold_line = array();
					if(!empty($pt_design31['fold_line'])){
						$fold_line = $pt_design31['fold_line'];
					}
					
					
					$pt_design[] = array(
										'image' 	  => $pt_design31['design_image'],
										'lable'       => $pt_design31['lable'],
										'use_img'     => $pt_design31['use_img'],
										'width'       => $pt_design31['canvas_width'],
										'height'      => $pt_design31['canvas_height'],
										'can_left'    => $pt_design31['can_left'],
										'can_top' 	  => $pt_design31['can_top'],
										'can_width'   => $pt_design31['can_width'],
										'can_height'  => $pt_design31['can_height'],
										'dpi' 		  => $pt_design31['dpi'],
										'safe_lines'  => $pt_design31['safe_line'],
										'bleed_size'  => $pt_design31['bleed_line'],
										'fold_line'   => $fold_line,
										'alwaysontop' => $pt_design31['alwaysontop'],
										'sort_order'  => $pt_design31['sort_order'],
										'canvasJsonData' => $canFilters
										);
				}

			$product_design_images['total_layers'] = $pt_design3[0]['total_layers'];
			$product_design_images['total_text_layers'] = $pt_design3[0]['total_text_layers'];
			$product_design_images['total_clipart_layers'] = $pt_design3[0]['total_clipart_layers'];
			$product_design_images['total_image_layers'] = $pt_design3[0]['total_image_layers'];
			$product_design_images['total_shapes_layers'] = $pt_design3[0]['total_shapes_layers'];
			$product_design_images['total_icons_layers'] = $pt_design3[0]['total_icons_layers'];
			}
			}

			if(!empty($pt_design)){
				foreach($pt_design as $ptsKkey => $ptData){
					$canvas_area=array();
					if(!empty($ptData['canvasJsonData'])){
					 $jsonData = stripslashes(html_entity_decode($ptData['canvasJsonData']));

						$cndata= json_decode($jsonData,true);

					/* 	if(!empty($canFilter[$ptsKkey])){
							foreach($canFilter[$ptsKkey] as $key1=>$val11){
								$canvasType=array();
								$canvasType=explode('_',$val11['name']);
							 $canvas_area[$canvasType[0]][]=$val11;
							   }
						} else { */
							if(!empty($cndata)){
							/* foreach($cndata as $key1=>$val1){
								$canvasType=array();
								$canvasType=explode('_',$val1['name']);
							 $canvas_area[$canvasType[0]][]=$val1;
							   } */
							   $canvas_area = $cndata;
						    } /* else {
								
							} */
						// }
					}
						$alwaysontop = 0;
					if(isset($ptData['alwaysontop'])){
						$alwaysontop = $ptData['alwaysontop'];
					}
					
						$can_left='';
					if(isset($ptData['can_left'])){
						$can_left=$ptData['can_left'];
					}

					$can_top='';
					if(isset($ptData['can_top'])){
						$can_top=$ptData['can_top'];
					}
					
					$can_width='';
					if(isset($ptData['can_width'])){
						$can_width=$ptData['can_width'];
					}
					
					$can_height='';
					if(isset($ptData['can_height'])){
						$can_height=$ptData['can_height'];
					}
					
						$fold_line='';
					if(isset($ptData['fold_line'])){
						$fold_line=$ptData['fold_line'];	
					}
					/* if($designerOld && $designfromcart!=3){
						$ptData['width']=$ptData['canvas_width'];
						$ptData['height']=$ptData['canvas_height'];
					} */
					
					$canvasAreaData[]= array(
				    'template_id' => $template_id,
					'product_id'  => $product_id,
					'lable' 	  => $ptData['lable'],
					'dpi'         => $ptData['dpi'],
					'safe_lines'  => $ptData['safe_lines'],
					'bleed_size'  => $ptData['bleed_size'],
					'fold_line'   => $fold_line,
					'alwaysontop' => $alwaysontop,
					'design_image'=> $ptData['image'],
					'use_img'=> $ptData['use_img'],
					'canvaswidth'=> $ptData['width'],
					'canvasheight'=> $ptData['height'],
					'can_left'    => $can_left,
					'can_top' 	  => $can_top,
					'can_width'   => $can_width,
					'can_height'  => $can_height,
					'sort_order'  => $ptData['sort_order'],
					'canvas_area' => $canvas_area
					);
					}
				}	
				$data['product_design_images'] = array();
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
						$design_image = '';
						if($image){
						$design_image = $this->config->get('config_ssl') . 'image/' . $image;
						}
					} else {
						$design_image = '';
						if($image){
						$design_image = $this->config->get('config_url') . 'image/' . $image;
						}
					}
					$img_width = 1050;
					$img_height = 1050;
					if (is_file(DIR_IMAGE . $image)) {
					list($img_width,$img_height,$type)=getImageSize(DIR_IMAGE.$image);
					}
					//
		$mobile_browser = 0;
		   if (isset($_SERVER['HTTP_USER_AGENT'])) {
		if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|android|iemobile)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
			$mobile_browser = 1;
		}
		}
		if (isset($_SERVER['HTTP_ACCEPT'])) {
		if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
		$mobile_browser = 1;
		}
		}
		 if (isset($_SERVER['HTTP_USER_AGENT'])) { 
		$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
		$mobile_agents = array(
			'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
			'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
			'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
			'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
			'newt','noki','palm','pana','pant','phil','play','port','prox',
			'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
			'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
			'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
			'wapr','webc','winw','winw','xda ','xda-');
		 
		if (in_array($mobile_ua,$mobile_agents)) {
			$mobile_browser = 1;
		}
		 
		if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'opera mini') > 0) {
			$mobile_browser = 1;

		}
		}
					//
						//$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
				/* 	if($mobile_browser ==  1) {
						if($img_width > 1050) {
					
						$img_height = ($img_height*1050)/$img_width;
						$img_width = 1050;
						} elseif($img_height > 1050) {
						$img_width = ($img_width*1050)/$img_height;
						$img_height = 1050;
						    
						}
					} */
					if(!$product_design_image['use_img']){
						$img_width=$product_design_image['canvaswidth'];
						$img_height=$product_design_image['canvasheight'];
					}
					
					if($product_design_image['use_img']==1){
					$img_width=$img_width+$product_design_image['bleed_size'];
					$img_height=$img_height+$product_design_image['bleed_size'];
					}
				/* 	 if($product_design_image['use_img']==0){
						$img_height = $product_design_image['canvasheight'];
						$img_width  = $product_design_image['canvaswidth'];
					}  */
					 $div_height= $img_height;
					 $fix_canvas_width=550;
					 $div_width=$img_width;
						$canvas_left= 0;
					 if($product_design_image['can_left']){
						$canvas_left= ($product_design_image['can_left'])*($div_width/$img_width);
					 }
						$canvas_top= 0;
					 if($product_design_image['can_top']){
						$canvas_top= ($product_design_image['can_top'])*($div_height/$img_height);
					 } else {
						 $product_design_image['can_top'] = 0;
					 }
						$canvas_width= 0;
					 if($product_design_image['can_width']){
						$canvas_width= ($product_design_image['can_width']*$div_width)/$img_width;
					 } else {
						 $product_design_image['can_width'] = 0;
					 }
						$canvas_height= 0;
					 if($product_design_image['can_height']){
						$canvas_height= ($product_design_image['can_height'])*($div_height/$img_height);
					 } else {
						 $product_design_image['can_height'] = 0;
					 }
					  $scaleforlongimage = 1;
					  $div_width1 = $div_width;
						$div_height1 = $div_height;
					 if($fix_canvas_width<$img_width){
						$div_width1 = $fix_canvas_width;
						$div_height1 = ($div_width1*$img_height)/$img_width;
						$scaleforlongimage = $img_width/$div_width1;
					 }
					// $img_width,$img_height
					// $div_width1 ,$div_height1
					$text_width_scale=1;
					$text_height_scale=1;
					
					if($div_width1 > 0){
					$text_width_scale=1+($img_width/$div_width1);
					}
					
					if($div_height1 > 0){
					$text_height_scale=1+($img_height/$div_height1);
					}

					 $canvasLeft=0;
					$canvasLeft = $product_design_image['safe_lines']+$product_design_image['bleed_size'];
					$canvasTop =$canvasLeft;
					$canvasWidth =($div_width-$canvasLeft*2);
					$canvasHeight =($div_height-$canvasLeft*2);
					if(isset($designfromcart) && ($designfromcart == 3 || $designerOld) && $template_id=='0') {
					$canvasLeft =$canvas_left*$scaleforlongimage;
					$canvasTop =$product_design_image['can_top']*$scaleforlongimage;
					$canvasWidth =$product_design_image['can_width']*$scaleforlongimage;
					$canvasHeight =$product_design_image['can_height']*$scaleforlongimage;
					}
					
					if(!$product_design_image['use_img']){
					$canvasLeft = ($product_design_image['safe_lines']+$product_design_image['bleed_size']);
					$canvasTop =$canvasLeft;
					$canvasWidth =($div_width-$canvasLeft*2);
					$canvasHeight =($div_height-$canvasLeft*2);
					}

					$cropLeft = $product_design_image['bleed_size'];
					$bleedLeft=0;
					$safe_line = false;
					$crop_line = false;
					$bleed_line= true;	
					
					if((int)$product_design_image['safe_lines']){
					$safe_line=true;	
					}
					if($product_design_image['use_img']!=0){
					$safe_line=true;	
					}
					if((int)$product_design_image['bleed_size']){
					$crop_line=true;	
					}

						$fold_line=array();
						if(isset($product_design_image['fold_line'])){
							if($product_design_image['fold_line']!='' && $product_design_image['fold_line']!=0){
								if( !empty($product_design_image['fold_line'])) {
									$fold_line=explode(',',$product_design_image['fold_line']);
								}
							}
						}
	
			$data['product_design_images'][] = array(
						'image'          =>  $image,
						'use_img'        =>  $product_design_image['use_img'],
						'design_images'  =>  $design_image,
						'div_width'      =>  $img_width,
						'div_height'     =>  $img_height,
						'div_width1'     =>  $div_width1,
						'div_height1'    =>  $div_height1,
						'lable' 	     =>  $product_design_image['lable'],
						'dpi' 	         =>  $product_design_image['dpi'],
						'safe_lines'     =>  $product_design_image['safe_lines'],
						'bleed_size'     =>  $product_design_image['bleed_size'],
						'fold_line'      =>  $fold_line,
						'alwaysontop'    =>  $product_design_image['alwaysontop'],
						'safe_line'      =>  $safe_line,
						'crop_line'      =>  $crop_line,
						'bleed_line'     =>  $bleed_line,
						'can_left'   	 =>  $canvasLeft,
						'can_top'    	 =>  $canvasTop,
						'can_width'  	 =>  $canvasWidth,
						'can_height' 	 =>  $canvasHeight,
						'crop_left'  	 =>  $cropLeft,
						'crop_top'   	 =>  $cropLeft,
						'crop_width' 	 => ($div_width-$cropLeft*2),
						'crop_height'	 => ($div_height-$cropLeft*2),
						'bleed_left' 	 =>  $bleedLeft,
						'bleed_top'  	 =>  $bleedLeft,
						'bleed_width' 	 => ($div_width-$bleedLeft*2),
						'bleed_height'	 => ($div_height-$bleedLeft*2),
						'can_left1'  	 =>  $product_design_image['can_left'],
						'can_top1' 	     =>  $product_design_image['can_top'],
						'can_width1' 	 =>  $product_design_image['can_width'],
						'can_height1'	 =>  $product_design_image['can_height'],
						'canvas_left'	 =>  $canvas_left,
						'canvas_top' 	 =>  $canvas_top,
						'canvas_width'	 =>  $canvas_width,
						'canvas_height'	 =>  $canvas_height,
						'image_width'	 =>  $img_width, 
						'image_height'	 =>  $img_height,
						'sort_order'	 =>  $product_design_image['sort_order'],
						'canvas_area'	 =>  $product_design_image['canvas_area'],
						'text_w_scale'	 =>  $text_width_scale,
						'text_h_scale'	 =>  $text_height_scale
					);				

					
					$max_height[] =$div_height;
				}
			}	 
// echo "<pre>";
// print_r($data['product_design_images']);
// die;
			$getProductDesignLimit = $this->model_extension_purpletree_product_designer_product_designer->getProductDesignLimit($this->request->get['product_id']);
			
			$data['total_layers']=999;
			$data['total_text_layers']=99;
			$data['total_clipart_layers']=99;
			$data['total_image_layers']=99;
			$data['total_icons_layers']=99;
			$data['total_shapes_layers']=99;

			if(isset($product_design_images['total_layers'])){
			$data['total_layers']=$product_design_images['total_layers'];
			}
			
			if(isset($product_design_images['total_text_layers'])){
			$data['total_text_layers']=$product_design_images['total_text_layers'];
			}
			
			if(isset($product_design_images['total_clipart_layers'])){
			$data['total_clipart_layers']=$product_design_images['total_clipart_layers'];
			}
			
			if(isset($product_design_images['total_image_layers'])){
			$data['total_image_layers']=$product_design_images['total_image_layers'];
			}
			
			if(isset($product_design_images['total_icons_layers'])){
			$data['total_icons_layers']=$product_design_images['total_icons_layers'];
			}
			
			if(isset($product_design_images['total_shapes_layers'])){
			$data['total_shapes_layers']=$product_design_images['total_shapes_layers'];
			}

			$data['wm_status']=$this->config->get('module_purpletree_product_designer_wm_status');
			$data['wm_text']=$this->config->get('module_purpletree_product_designer_wm_text');
			$data['max_height'] = 0;			
			if(!empty($max_height)) {
			$data['max_height']=max($max_height)+81;			
			}
			$data['plusvallue']=300;	
			$fontss = $this->model_extension_purpletree_product_designer_product_designer->getFonts();
			
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
			//product
			$data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
			$data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));

			$this->load->model('catalog/review');

			$data['tab_review']     = sprintf($this->language->get('tab_review'), $product_info['reviews']);
			$data['product_id'] 	= (int)$this->request->get['product_id'];
			$data['manufacturer'] 	= $product_info['manufacturer'];
			$data['manufacturers']  = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
			$data['model'] 	= $product_info['model'];
			$data['reward'] = $product_info['reward'];
			$data['points'] = $product_info['points'];
			$data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');

			if ($product_info['quantity'] <= 0) {
				$data['stock'] = $product_info['stock_status'];
			} elseif ($this->config->get('config_stock_display')) {
				$data['stock'] = $product_info['quantity'];
			} else {
				$data['stock'] = $this->language->get('text_instock');
			}

			$this->load->model('tool/image');

			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['price'] = false;
			}

			if ((float)$product_info['special']) {
				$data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['special'] = false;
			}

			if ($this->config->get('config_tax')) {
				$data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
			} else {
				$data['tax'] = false;
			}

			$discounts = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id']);

			$data['discounts'] = array();

			foreach ($discounts as $discount) {
				$data['discounts'][] = array(
					'quantity' => $discount['quantity'],
					'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'])
				);
			}

			$data['options'] = array();

			foreach ($this->model_catalog_product->getProductOptions($this->request->get['product_id']) as $option) {
				$product_option_value_data = array();

				foreach ($option['product_option_value'] as $option_value) {
					if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
						if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
							$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
						} else {
							$price = false;
						}

						$product_option_value_data[] = array(
							'product_option_value_id' => $option_value['product_option_value_id'],
							'option_value_id'         => $option_value['option_value_id'],
							'name'                    => $option_value['name'],
							'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
							'price'                   => $price,
							'price_prefix'            => $option_value['price_prefix']
						);
					}
				}

				$data['options'][] = array(
					'product_option_id'    => $option['product_option_id'],
					'product_option_value' => $product_option_value_data,
					'option_id'            => $option['option_id'],
					'name'                 => $option['name'],
					'type'                 => $option['type'],
					'value'                => $option['value'],
					'required'             => $option['required']
				);
			}

			if ($product_info['minimum']) {
				$data['minimum'] = $product_info['minimum'];
			} else {
				$data['minimum'] = 1;
			}

			$data['review_status'] = $this->config->get('config_review_status');

			if ($this->config->get('config_review_guest') || $this->customer->isLogged()) {
				$data['review_guest'] = true;
			} else {
				$data['review_guest'] = false;
			}

			if ($this->customer->isLogged()) {
				$data['customer_name'] = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();
			} else {
				$data['customer_name'] = '';
			}

			$data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);
			$data['rating'] = (int)$product_info['rating'];

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
			} else {
				$data['captcha'] = '';
			}

			$data['share'] = $this->url->link('product/product', 'product_id=' . (int)$this->request->get['product_id']);

			$data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($this->request->get['product_id']);

			$data['products'] = array();

			$results = $this->model_catalog_product->getProductRelated($this->request->get['product_id']);

			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}

				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $rating,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}

			$data['tags'] = array();

			if ($product_info['tag']) {
				$tags = explode(',', $product_info['tag']);

				foreach ($tags as $tag) {
					$data['tags'][] = array(
						'tag'  => trim($tag),
						'href' => $this->url->link('product/search', 'tag=' . trim($tag))
					);
				}
			}
			$direction = $this->language->get('direction');
			if ($direction=='rtl'){	
				$data['direction'] = $this->language->get('direction');
			}else{
				$data['direction'] = '';
			}

			$data['recurrings'] = $this->model_catalog_product->getProfiles($this->request->get['product_id']);
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
			$icons = array();
			$icons = $this->model_extension_purpletree_product_designer_product_template_designer->getIcons();

			if(!empty($icons)){
				foreach($icons as $iconKey=>$iconVal){
					$iconsData[$iconVal['icon_category']][]=array(
						'class'=>$iconVal['icon_class'],
						'name'=>$iconVal['icon_name'],
						'unicode'=>$iconVal['icon_code'],
					);
				}
			}
			
/* 	$data['ptsIconsData']=array(
			'Category A'=> array( 
					array(
						'class'=>'fa-address-book',
						'name'=>'address-book',
						'unicode'=>'f2b9',
						
						),
						array(
						'class'=>'fa-address-book-o',
						'name'=>'address-book',
						'unicode'=>'f2ba'
						),
						array(
						'class'=>'fa-adn',
						'name'=>'adn',
						'unicode'=>'f170'
						),
						array(
						'class'=>'fa-amazon',
						'name'=>'amazon',
						'unicode'=>'f270'
						),
						array(
						'class'=>'fa-apple',
						'name'=>'apple',
						'unicode'=>'f179'
						),
						array(
						'class'=>'fa-automobile',
						'name'=>'automobile',
						'unicode'=>'f1b9'
						),
						array(
						'class'=>'fa-barcode',
						'name'=>'barcode',
						'unicode'=>'f0a3'
						),
						array(
						'class'=>'fa-certificate',
						'name'=>'certificate',
						'unicode'=>'f02a'
						)
				),
			'Category B'=>array(			
					array(
						'class'=>' fa-circle-o',
						'name'=>'circle',
						'unicode'=>'f10c'
						),
						array(
						'class'=>' fa-comment-o',
						'name'=>'comment',
						'unicode'=>'f0e5'
						),
						array(
						'class'=>'fa-credit-card',
						'name'=>'credit-card',
						'unicode'=>'f09d'
						),
						array(
						'class'=>'fa-envelope-o',
						'name'=>'envelope',
						'unicode'=>'f003'
						)
					)
				); */
				
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
			$data['designResults'] = json_encode($data['product_design_images']);
			$data['action'] = $this->url->link('account/login', '', true);
			$data['session_id'] = $this->session->getId();
			$data['customer_id']=0;
			if($this->customer->getId()){
			$data['customer_id']=$this->customer->getId();
			}
			$data['help_image'] = HTTPS_SERVER.'image/'.$this->config->get('module_purpletree_product_designer_help_img');
			$data['use_help_image'] = $this->config->get('module_purpletree_product_designer_help_img');
			
			
			$data['buy_design_button_status'] = $this->config->get('module_purpletree_product_designer_buy_design');
			$data['button_cart']      =$this->language->get('button_cart');
			if($data['buy_design_button_status']==1){
			$data['button_buy_design']      =$this->language->get('button_buy_design');
			}

			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
			$this->response->setOutput($this->load->view('extension/purpletree_product_designer/product_template_designer', $data));
		} else {
					$this->load->language('error/not_found');
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
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

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('product/product', $url . '&product_id=' . $product_id)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['continue'] = $this->url->link('common/home');
			$data['text_error'] = $this->language->get('text_error');
			$data['heading_title'] = $this->language->get('heading_title');
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}
	public function search() {
		$json = array();
		$this->load->model('tool/image');
		if(isset($this->request->post['art_search_keyword'])){
			$this->load->model('extension/purpletree_product_designer/product_template_designer');
					$clipArtImages=$this->model_extension_purpletree_product_designer_product_template_designer->getClipartAjaxImages($this->request->post['art_search_keyword']);
			if(!empty($clipArtImages)){		
				foreach($clipArtImages as $pts_key=>$pts_value){
					$ext= substr(basename($pts_value['clipart_image']), strrpos(basename($pts_value['clipart_image']), '.')+1);
					if($ext=='svg'){
					$thumb_clipart_image='image/'.$pts_value['clipart_image'];	
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
			$this->load->model('extension/purpletree_product_designer/product_template_designer');
					$icons=$this->model_extension_purpletree_product_designer_product_template_designer->getIconByAjax($this->request->post['icon_search_keyword']);
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
		public function get_client_ip() {
		$ipaddress = '';
			if (getenv('HTTP_CLIENT_IP'))
				$ipaddress = getenv('HTTP_CLIENT_IP');
			else if(getenv('HTTP_X_FORWARDED_FOR'))
				$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
			else if(getenv('HTTP_X_FORWARDED'))
				$ipaddress = getenv('HTTP_X_FORWARDED');
			else if(getenv('HTTP_FORWARDED_FOR'))
				$ipaddress = getenv('HTTP_FORWARDED_FOR');
			else if(getenv('HTTP_FORWARDED'))
			   $ipaddress = getenv('HTTP_FORWARDED');
			else if(getenv('REMOTE_ADDR'))
				$ipaddress = getenv('REMOTE_ADDR');
			else
				$ipaddress = 'UNKNOWN';
			return $ipaddress;
	}
		public function validate() {
		if($this->config->get('module_purpletree_product_designer_status')) {
			if($this->config->get('module_purpletree_product_designer_p_d')) {
				$module	    	= 'oc_purpletree_customproductdesigner';

				if($_SERVER['HTTP_HOST'] == 'localhost') {
					$domain = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
				
				} else {
					$domain = 'http://'.$_SERVER['HTTP_HOST'];
				} 
				$valuee = $this->config->get('module_purpletree_product_designer_p_d');
				 $ip_address = $this->get_client_ip();
				$url = "https://www.process.purpletreesoftware.com/occheckdata.php";
				$handle=curl_init($url);					
				curl_setopt($handle, CURLOPT_VERBOSE, true);
				curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($handle, CURLOPT_POSTFIELDS,
							"process_data=$valuee&domain_name=$domain&ip_address=$ip_address&module_name=$module");
				$result = curl_exec($handle);
				$result1 = json_decode($result);
				if(curl_error($handle))
				{
					return false;
				}
				$ip_a = $_SERVER['HTTP_HOST'];
				if ($result1->status == 'success') {
					return true;
				}
			}
		}
	}
	
		public function UploadCanvasImage() {
		$json = array();
		if($this->validate()) {
			$imageData = $this->request->post['imageData'];
			$ext='';
			$ext = $this->request->post['ext'];
			$id = $this->request->post['id'];
			if(!empty($imageData)){
			$image = explode(',', $imageData);	
			$imgContent = base64_decode($image[1]);
			$datetime=date('dmYHis').rand();
			$image_root = "catalog/ptsdesigner/pts_design_".$datetime."_".$id.$ext; 
			$fileImgPath = DIR_IMAGE.$image_root; 
			file_put_contents($fileImgPath,$imgContent);
			$json['image_root']=$image_root;
			$json['status']="success";
			$json['success']="Image/Images saved successfully";
			} else {
			$json['status']="error";
			$json['msg']="Image is not valid";	
			}
		} else {
			$json['status']="error";
			$json['msg']="Invalid License for Purpletree Custom Product Designer.";
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
/* 	public function UploadCanvasImage() {
		$json = array();
			if($this->validate()) {
		$returnimages = array();
		$baseFromJavascript=array();

		$baseFromJavascriptBg = $this->request->post['imageBgData'];
		$baseFromJavascript = $this->request->post['imageData'];
		$imageWithoutWM = $this->request->post['imageWithoutWM'];
		
		$baseFromlabels = $this->request->post['labels'];
		if(!empty($baseFromJavascriptBg)){
		foreach($baseFromJavascriptBg as $k => $imageBgData){
			$base_to_php = explode(',', $baseFromJavascript[$k]);
			$imageWithoutWMark = explode(',', $imageWithoutWM[$k]);
			$base_to_php_bg = explode(',', $imageBgData);
			$dataBg = base64_decode($base_to_php_bg[1]);
			$data = base64_decode($base_to_php[1]);
			$withoutWMData = base64_decode($imageWithoutWMark[1]);
			$datetime=date('dmYHis');
			$filepath1Bg = "catalog/ptsdesigner/pts_design_".$datetime."_".$k."_bg.png"; 
			$filepath1 = "catalog/ptsdesigner/pts_design_".$datetime."_".$k.".png"; 
			$fielWithoutWM = "catalog/ptsdesigner/pts_design_".$datetime."_".$k."_wwm.png"; 
			$filepathBg = DIR_IMAGE.$filepath1Bg; 
			$filepath = DIR_IMAGE.$filepath1; 
			$fileWWMpath = DIR_IMAGE.$fielWithoutWM; 
			file_put_contents($filepathBg,$dataBg);
			file_put_contents($filepath,$data);
			file_put_contents($fileWWMpath,$withoutWMData);
			$returnimages[] = array(
			'image_label' =>$baseFromlabels[$k],
			'product_image' =>$filepath1Bg,
			'product_without_bg_image' =>$filepath1,
			'imageWithoutWM' =>$fielWithoutWM
			);
		}
		}
		$json['status']="success";
		$json['success']="Image/Images saved successfully";
		$json['data'] = $returnimages;
		} else {
		$json['status']="error";
		$json['msg']="Invalid License for Purpletree Custom Product Designer.";
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	} */	
	
		public function productDesignerPdfMail() {		
		if($this->validate()) {
		$customerName=$this->customer->getFirstName();
		$emailAddress=$this->customer->getEmail();	
		require_once( DIR_SYSTEM.'library/purpletree_product_designer/fpdf_protection.php' );
		$returnimages = array();
		$baseFromJavascript=array();
		$baseFromJavascriptBg = '';
		$baseFromJavascript = '';
		$baseFromlabels = '';
		if(isset($this->request->post['imageBgData'])){
			$baseFromJavascriptBg = $this->request->post['imageBgData'];
		}
		if(isset($this->request->post['imageData'])){
			$baseFromJavascript = $this->request->post['imageData'];
		}
		if(isset($this->request->post['labels'])){
			$baseFromlabels = $this->request->post['labels'];
		}
			$product_id = 0;
		if(isset($this->request->post['product_id'])){
			$product_id = $this->request->post['product_id'];
		}
		$template_id = 0;
		if(isset($this->request->post['template_id'])){
			$template_id = $this->request->post['template_id'];
		}
		if(!empty($baseFromJavascriptBg)){
	$urlpdf = $this->createPDF($baseFromJavascriptBg,$baseFromlabels,$product_id,$template_id);
		//
if ($this->customer->isLogged()) {
		$this->emailToClient($urlpdf);
			$json['status']="success";
		$json['msg']="Product design pdf has been send successfully.";
		} else {
			$canvasFilterDatas = array();
			$saved_id = 0;
			if(isset($this->request->post['fetchData']) && !empty($this->request->post['fetchData'])){
				foreach($this->request->post['fetchData'] as $ckey=>$cValue){
					$canvasFilterDatas[]= $cValue['value'];	
					}
				$canvasFilterData['product_id'] = $product_id;
				$canvasFilterData['template_id'] = $template_id;
				$canvasFilterData['jsonData'] = json_encode($canvasFilterDatas);
				 	$this->load->model('extension/purpletree_product_designer/product_template_designer');
				$saved_id = $this->model_extension_purpletree_product_designer_product_template_designer->saveCanvasDatasession($canvasFilterData);
			}
			$json['action'] = $this->url->link('account/login', '', true);
			$json['status'] = "login";
			$json['error'] = "Please Login to Email PDF.";
			$this->session->data['downloadpdfemail'] = $urlpdf;
			$this->session->data['redirect'] = $this->url->link('extension/purpletree_product_designer/product_template_designer', 'product_id='.$product_id.'&template_id='.$template_id.'&saved_id='.$saved_id, true);
		}
//		
		//pdf Mail
		//PDF Generator
	
		} else {
		 $json['status']="error";
		$json['msg']="Image is not upload";	 
		}
		
		} else {
		$json['status']="error";
		$json['msg']="Invalid License for Purpletree Custom Product Designer.";
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	public function createPDF($baseFromJavascriptBg,$baseFromlabels,$product_id=0,$template_id=0) {
			foreach($baseFromJavascriptBg as $k => $imageBgData){
			$base_to_php_bg = explode(',', $imageBgData);
			$dataBg = base64_decode($base_to_php_bg[1]);
			$label= $baseFromlabels[$k];
			$datetime=date('dmYHis');
			$filepath1Bg = "catalog/ptsdesigner/pts_design_".$datetime."_".$k."_bg.png"; 
			$filepathBg = DIR_IMAGE.$filepath1Bg; 
			file_put_contents($filepathBg,$dataBg);
			$pdfData[] = array('image_label' =>$baseFromlabels[$k],'product_image' =>$filepath1Bg);
		}
		//PDF Generator
		
	if(isset($pdfData)){
		if ($this->request->server['HTTPS']) {
					$image_full_d = HTTPS_SERVER . 'image/';
				} else {
					$image_full_d = HTTP_SERVER . 'image/';
				}
				
		foreach($pdfData as $orderitems_d) {	
	
						$labelll = explode('_', $orderitems_d['image_label']);
						$resolution = '';
						if(isset($labelll[2]) && isset($labelll[1]) && is_numeric($labelll[2])) {
							$resolution = $labelll[1];
						}
						$onepixel = 1;
						if($resolution != '') {
							 $onepixel = 25.4/$resolution;
						}
						list($width, $height, $type, $attr) = getimagesize($image_full_d.$orderitems_d['product_image']);

						$img_width[]=$width*$onepixel;
						$img_height[]=$height*$onepixel;
					}
					$max_width = 0;			
					if(!empty($img_width)) {
						$max_width = max($img_width)*(0.464583);
					}
					$max_height = 0;			
					if(!empty($img_height)) {
						$max_height = max($img_height)*(0.464583);
					}
					
					$this->load->model('extension/purpletree_product_designer/product_template_designer');
					$size_unit = $this->model_extension_purpletree_product_designer_product_template_designer->getCanvasSizeUnit($product_id);
				
					if($template_id){
					$size_unit = $this->model_extension_purpletree_product_designer_product_template_designer->getCanvasTemplateSizeUnit($product_id,$template_id);
					}
					if($size_unit==1){
						$size_unit='mm';		
					} else if($size_unit==2){
						$size_unit='cm';
					} else if($size_unit==3){
						$size_unit='foot';
					} else if($size_unit==4){
						$size_unit='in';
					} else {
						$size_unit='mm';	
					}
					
				$pdf = new FPDF_Protection('L',$size_unit,array($max_width,$max_height));
				$pdf->SetProtection(array()); 
				foreach($pdfData as $keyyy => $orderitems_d) {
						$sizee = array($img_height[$keyyy],$img_width[$keyyy]);
							$orentation = "L";
						if($img_height[$keyyy] > $img_width[$keyyy]) {
							$orentation = "P";
						}
						$pdf->AddPage($orentation,$sizee);
						$pdf->Image($image_full_d.$orderitems_d['product_image'],0,0,$img_width[$keyyy],$img_height[$keyyy]);
					}	
			$pdfDownloadDir=DIR_IMAGE.'purpletree_product_designer_pdf/';
			if (!is_dir($pdfDownloadDir)) {
			mkdir($pdfDownloadDir, 0777);
			}
			$pdfFileName='purpletree_product_design'.date("dmYhis").'.pdf';
			$image_new='purpletree_product_designer_pdf/'.$pdfFileName;
			if ($this->request->server['HTTPS']) {
			$urlpdf =  $this->config->get('config_ssl') . 'image/' . $image_new;
			} else {
			$urlpdf = $this->config->get('config_url') . 'image/' . $image_new;
			}
			$pdf->Output("F",$pdfDownloadDir.$pdfFileName,true);
			return $urlpdf;
	}
	}
	public function downloadPdf() {
		//$json = array();
			if($this->validate()) {
		require_once( DIR_SYSTEM.'library/purpletree_product_designer/fpdf_protection.php' );
		$baseFromJavascriptBg = '';
		$baseFromlabels = '';
		if(isset($this->request->post['imageBgData'])){
			$baseFromJavascriptBg = $this->request->post['imageBgData'];
		}
		if(isset($this->request->post['labels'])){
			$baseFromlabels = $this->request->post['labels'];
		}
		$product_id = 0;
		if(isset($this->request->post['product_id'])){
			$product_id = $this->request->post['product_id'];
		}
		$template_id = 0;
		if(isset($this->request->post['template_id'])){
			$template_id = $this->request->post['template_id'];
		}
		if(!empty($baseFromJavascriptBg)){
		$urlpdf = $this->createPDF($baseFromJavascriptBg,$baseFromlabels,$product_id,$template_id);
	
		//PDF Generator
		if ($this->customer->isLogged()) {
			$json['status']="success";
		$json['success']="Image/Images saved successfully";
		$json['pdf_url'] = $urlpdf;
		} else {
			$canvasFilterDatas = array();
			$saved_id = 0;
			if(isset($this->request->post['fetchData']) && !empty($this->request->post['fetchData'])){
				foreach($this->request->post['fetchData'] as $ckey=>$cValue){
					$canvasFilterDatas[]= $cValue['value'];	
					}
				$canvasFilterData['product_id'] = $product_id;
				$canvasFilterData['template_id'] = $template_id;
				$canvasFilterData['jsonData'] = json_encode($canvasFilterDatas);
				 	$this->load->model('extension/purpletree_product_designer/product_template_designer');
				$saved_id = $this->model_extension_purpletree_product_designer_product_template_designer->saveCanvasDatasession($canvasFilterData);
			}
		$json['status'] = "login";
		$json['error'] = "Please Login to Download PDF.";
			$this->session->data['downloadpdf'] = $urlpdf;
			$this->session->data['redirect'] = $this->url->link('extension/purpletree_product_designer/product_template_designer', 'product_id='.$product_id.'&template_id='.$template_id.'&saved_id='.$saved_id, true);
		}
		} else {
		$json['status']="error";
		$json['msg']="Image is not upload";
		}
		
		} else {
		$json['status']="error";
		$json['msg']="Invalid License for Purpletree Custom Product Designer.";
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}	
	public function emailToClient($urlpdf) {
			$customerName = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();
	$emailAddress=$this->customer->getEmail();
	
	$designeratatchment = explode('/',$urlpdf);
	$designeratatchment1 = end($designeratatchment);
	$pdfDownloadDir = DIR_IMAGE.'purpletree_product_designer_pdf/'.$designeratatchment1;
	$html='';
	$html.='<p>Customer Name: '.$customerName.'</p>';
	$html.='<p>Customer Email: '.$emailAddress.'</p>';
	$html.='<p>Download Product Designer PDF attached</p>';
	$html.='Thanks';
	
	//pdf Mail
		$mail = new Mail($this->config->get('config_mail_engine'));
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

		$mail->setTo($emailAddress);
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
		$mail->setSubject('Product designer mail');
		$mail->addAttachment($pdfDownloadDir);
		$mail->setHtml($html);
		$mail->send();
	}
	public function review() {
		$this->load->language('product/product');

		$this->load->model('catalog/review');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['reviews'] = array();

		$review_total = $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['product_id']);

		$results = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id'], ($page - 1) * 5, 5);

		foreach ($results as $result) {
			$data['reviews'][] = array(
				'author'     => $result['author'],
				'text'       => nl2br($result['text']),
				'rating'     => (int)$result['rating'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$pagination = new Pagination();
		$pagination->total = $review_total;
		$pagination->page = $page;
		$pagination->limit = 5;
		$pagination->url = $this->url->link('product/product/review', 'product_id=' . $this->request->get['product_id'] . '&page={page}');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($review_total - 5)) ? $review_total : ((($page - 1) * 5) + 5), $review_total, ceil($review_total / 5));

		$this->response->setOutput($this->load->view('product/review', $data));
	}

	public function write() {
		$this->load->language('product/product');

		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25)) {
				$json['error'] = $this->language->get('error_name');
			}

			if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
				$json['error'] = $this->language->get('error_text');
			}

			if (empty($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {
				$json['error'] = $this->language->get('error_rating');
			}

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

				if ($captcha) {
					$json['error'] = $captcha;
				}
			}

			if (!isset($json['error'])) {
				$this->load->model('catalog/review');

				$this->model_catalog_review->addReview($this->request->get['product_id'], $this->request->post);

				$json['success'] = $this->language->get('text_success');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getRecurringDescription() {
		$this->load->language('product/product');
		$this->load->model('catalog/product');

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		if (isset($this->request->post['recurring_id'])) {
			$recurring_id = $this->request->post['recurring_id'];
		} else {
			$recurring_id = 0;
		}

		if (isset($this->request->post['quantity'])) {
			$quantity = $this->request->post['quantity'];
		} else {
			$quantity = 1;
		}

		$product_info = $this->model_catalog_product->getProduct($product_id);
		
		$recurring_info = $this->model_catalog_product->getProfile($product_id, $recurring_id);

		$json = array();

		if ($product_info && $recurring_info) {
			if (!$json) {
				$frequencies = array(
					'day'        => $this->language->get('text_day'),
					'week'       => $this->language->get('text_week'),
					'semi_month' => $this->language->get('text_semi_month'),
					'month'      => $this->language->get('text_month'),
					'year'       => $this->language->get('text_year'),
				);

				if ($recurring_info['trial_status'] == 1) {
					$price = $this->currency->format($this->tax->calculate($recurring_info['trial_price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$trial_text = sprintf($this->language->get('text_trial_description'), $price, $recurring_info['trial_cycle'], $frequencies[$recurring_info['trial_frequency']], $recurring_info['trial_duration']) . ' ';
				} else {
					$trial_text = '';
				}

				$price = $this->currency->format($this->tax->calculate($recurring_info['price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

				if ($recurring_info['duration']) {
					$text = $trial_text . sprintf($this->language->get('text_payment_description'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				} else {
					$text = $trial_text . sprintf($this->language->get('text_payment_cancel'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				}

				$json['success'] = $text;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	 public function uploadImage() {
		$uploadedImages =$this->request->post['uploadfile'];
		$uploadedImg = explode(',', $uploadedImages);
		$image = base64_decode($uploadedImg[1]);
		$datetime=date('dmYHis');
		$path = "catalog/ptsdesigner/pts_design_".$datetime."_uploaded_image.png"; 
		$filepath = DIR_IMAGE.$path; 
		file_put_contents($filepath,$image);
		$url=HTTPS_SERVER.'image/'.$path;
		$json['url']=$url;
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}	

	public function uploadCanvasData() {
		$this->load->model('extension/purpletree_product_designer/product_template_designer');
		$product_id=0;
		$canvasFilterDatas[]=array();
		if(!empty($this->request->post['product_id'])){
			$product_id=$this->request->post['product_id'];
		} else {
			$json['error']='Product is not found';
		}
		$template_id=0;
		if(!empty($this->request->post['template_id']) ||  $this->request->post['template_id']=='0'){
			$template_id=$this->request->post['template_id'];
		} else {
			$json['error']='template id is not found';
		}
		if(!empty($this->request->post['fetchData'])){
			foreach($this->request->post['fetchData'] as $ckey=>$cValue){
			$canvasFilterDatas[]= $cValue['value'];	
			}
		$canvasFilterData['product_id']=$product_id;
		$canvasFilterData['template_id']=$template_id;
		$canvasFilterData['jsonData']=json_encode($canvasFilterDatas);
		$saved_id = $this->model_extension_purpletree_product_designer_product_template_designer->saveCanvasData($canvasFilterData);	
		if ($this->customer->isLogged()) {
		$json['status']="success";
		$json['success']='Data has been saved';
		$json['url'] = $this->url->link('extension/purpletree_product_designer/product_template_designer', 'product_id='.$product_id.'&template_id='.$template_id.'&saved_id='.$saved_id, true);
		} else {
		$json['status'] = "login";
		$json['error'] = "Please login to save canvas";
		$json['action'] = $this->url->link('account/login', '', true);
		$this->session->data['saveddata'] = $saved_id;
		$this->session->data['redirect'] = $this->url->link('extension/purpletree_product_designer/product_template_designer', 'product_id='.$product_id.'&template_id='.$template_id.'&saved_id='.$saved_id, true);
		}
		} else {
			$json['error']='Canvas data is not found';
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
?>