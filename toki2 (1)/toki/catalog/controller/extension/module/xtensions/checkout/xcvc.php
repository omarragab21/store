<?php
class ControllerExtensionModuleXtensionsCheckoutXCVC extends Controller {
	public $data = array();	

	public function __construct($registry) {
		parent::__construct($registry);
		$this->data = $this->load->language('checkout/checkout');
		$this->data = array_merge($this->data,$this->load->language($this->config->get('xtensions_language_path')));
		$this->data += $this->xtensions_checkout->getXtensionsData($this->config->get('config_store_id'), 'xtensions_best_checkout');
		$xconfig = $this->data['xconfig'];
		$options = $xconfig['options'];
		$this->data['display_rewards'] = isset($options['xcvc_view']) && isset($options['xcvc_view']) == 'modal' && isset($options['display_rewards']);
		$this->data['display_coupons'] = isset($options['xcvc_view']) && isset($options['xcvc_view']) == 'modal' && isset($options['display_coupons']);
		$this->data['display_vouchers'] = isset($options['xcvc_view']) && isset($options['xcvc_view']) == 'modal' && isset($options['display_vouchers']);
		$this->data['display_comments'] = isset($options['address_type']) && $options['address_type'] == 'block' && isset($options['display_comments']);
	}

	public function index() {
		$this->data['text_comments'] = $this->language->get('text_comments');
		$this->data['button_continue'] = $this->language->get('button_continue');
		
		if (isset($this->session->data['comment'])) {
			$this->data['comment'] = $this->session->data['comment'];
		} else {
			$this->data['comment'] = '';
		}
		$points = $this->customer->getRewardPoints();
		
		$points_total = 0;
		
		foreach ($this->cart->getProducts() as $product) {
			if ($product['points']) {
				$points_total += $product['points'];
			}
		}
		$this->data['reward_status'] = ($points && $points_total && $this->config->get('total_reward_status'));
		if (isset($this->session->data['reward'])) {
			$rewardValidate = $this->validateReward($this->session->data['reward']);
			if ($rewardValidate) {
				$this->data['reward_value'] = $this->session->data['reward'] = $rewardValidate;
			} else {
				$this->data['reward_value'] = $this->session->data['reward'];
			}
		} else {
			$this->data['reward_value'] = '';
		}
		$this->language->load('extension/total/reward');
		$this->data['text_use_reward'] = sprintf($this->language->get('heading_title'), $points);
		
		$this->data['entry_reward'] = sprintf($this->language->get('entry_reward'), $points_total);		
		
		$this->data['coupon_apply_text'] = $this->language->get('coupon_apply_text');
		$this->data['coupon_text'] = $this->language->get('coupon_text');
		$this->data['coupon_placeholder'] = $this->language->get('coupon_placeholder');
		$this->data['voucher_apply_text'] = $this->language->get('voucher_apply_text');
		$this->data['voucher_text'] = $this->language->get('voucher_text');
		$this->data['voucher_placeholder'] = $this->language->get('voucher_placeholder');
		$this->data['reward_apply_text'] = $this->language->get('reward_apply_text');
		$this->data['reward_text'] = $this->language->get('reward_text');
		$this->data['comment_apply_text'] = $this->language->get('comment_apply_text');
		$this->data['comment_text'] = $this->language->get('comment_text');
		
		//Xlcoupon- start
		if($this->xtensions_checkout->configExists('xtensions/xlcoupon')){		
			$this->load->config('xtensions/xlcoupon');
			if($this->config->get($this->config->get('xlcoupon_config_pfx').'status') && $this->config->get($this->config->get('xlcoupon_config_pfx').'list_status')){
				$this->data['display_coupons'] = false;
			}
		}
		//Xlcoupon- end
		if (!empty($this->session->data['voucher'])) {
			$this->data['voucher_value'] = $this->session->data['voucher'];
		} else {
			$this->data['voucher_value'] = '';
		}
		
		if (!empty($this->session->data['coupon'])) {
			$this->data['coupon_value'] = $this->session->data['coupon'];
		} else {
			$this->data['coupon_value'] = '';
		}
		
		$this->template = $this->config->get('xtensions_view_path').'xcvc';
		return $this->xtensions_checkout->renderView($this);
	}

	public function xoptions() {
		$this->data['misc_options'] = $misc_options = $this->data['xconfig']['options'];
		//Xlcoupon- start
		$this->data['dropdown_coupons'] = false;
		if($this->xtensions_checkout->configExists('xtensions/xlcoupon')){		
			$this->load->config('xtensions/xlcoupon');
			if($this->config->get($this->config->get('xlcoupon_config_pfx').'status') && $this->config->get($this->config->get('xlcoupon_config_pfx').'list_status')){
				$this->data['dropdown_coupons'] = true;
				$this->data += $this->load->language($this->config->get('xlcoupon_path'));
				$this->data['link_coupon'] = $this->url->link($this->config->get('xlcoupon_path').'/displayList'.'&xpage=xcheckout');
			}
		}
		//Xlcoupon- end
		$this->data['is_coupon_set'] = ((isset($this->session->data['coupon']) && $this->session->data['coupon'])?true:false);		
		if (isset($misc_options['display_coupons']) || isset($misc_options['display_vouchers']) || isset($misc_options['display_rewards']) || isset($misc_options['display_comments'])) {
			$this->data['inline_view'] = isset($misc_options['xcvc_view']) && $misc_options['xcvc_view'] == 'inline' ? true : false;
			$this->data['display_rewards'] = $this->data['display_coupons'] = $this->data['display_vouchers'] = true;
			
			if (isset($misc_options['display_rewards'])) {
				if (isset($this->session->data['reward'])) {
					$rewardValidate = $this->validateReward($this->session->data['reward']);
					if ($rewardValidate) {
						$this->data['reward_value'] = $this->session->data['reward'] = $rewardValidate;
					} else {
						$this->data['reward_value'] = $this->session->data['reward'];
					}
				} else {
					$this->data['reward_value'] = '';
				}
				$points = $this->customer->getRewardPoints();
				
				$points_total = 0;
				
				foreach ($this->cart->getProducts() as $product) {
					if ($product['points']) {
						$points_total += $product['points'];
					}
				}
				$this->data['display_rewards'] = ($points && $points_total && $this->config->get('total_reward_status'));
				$this->data['text_use_reward'] = sprintf($this->language->get('text_rewards_label'), $points, $points_total);
			} else {
				$this->data['display_rewards'] = false;
			}
			if (isset($misc_options['display_vouchers'])) {
				if (isset($this->session->data['voucher'])) {
					$this->data['voucher_value'] = $this->session->data['voucher'];
				} else {
					$this->data['voucher_value'] = '';
				}
			} else {
				$this->data['display_vouchers'] = false;
			}
			if (isset($misc_options['display_coupons'])) {
				if (!empty($this->session->data['coupon'])) {
					$this->data['coupon_value'] = $this->session->data['coupon'];
				} else {
					$this->data['coupon_value'] = '';
				}
			} else {
				$this->data['display_coupons'] = false;
			}
			$this->data['display_comments'] = isset($misc_options['address_type']) && $misc_options['address_type'] == 'block' && isset($misc_options['display_comments']);
			$this->template = $this->config->get('xtensions_view_path').'xoptions';
			return $this->xtensions_checkout->renderView($this);
		} else {
			return '';
		}
	}

	public function xtotals() {
		$misc_options = $this->data['xconfig']['options'];
		$free_shipping_title = '';
		if(isset($misc_options['shipping']['zero_shipping_status']) && $misc_options['shipping']['zero_shipping_status'] && isset($misc_options['shipping']['zero_shipping_title'][(int)$this->config->get('config_language_id')]) && $free_shipping_title = $misc_options['shipping']['zero_shipping_title'][(int)$this->config->get('config_language_id')]){
			$free_shipping_title = $free_shipping_title;
		}
		$this->data['text_totals'] = $this->language->get('text_totals');		
		$totals = array();
		$total = 0;
		$taxes = $this->cart->getTaxes();
		// Because __call can not keep var references so we put them into an array.
		$total_data = array(
			'totals' => &$totals,
			'taxes' => &$taxes,
			'total' => &$total 
		);
		$this->load->model('setting/extension');
		$results = $this->model_setting_extension->getExtensions('total');
		$sort_order = array();
		
		foreach ($results as $key => $value) {
			$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
		}
		
		array_multisort($sort_order, SORT_ASC, $results);
		
		foreach ($results as $result) {
			if ($this->config->get('total_'.$result['code'] . '_status')) {
				$this->load->model('extension/total/' . $result['code']);
				
				$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
			}
		}
		
		$sort_order = array();
		
		foreach ($totals as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}
		
		array_multisort($sort_order, SORT_ASC, $totals);
		$this->data['totals'] = array();
		foreach ($totals as $total) {			
			if($free_shipping_title && $total['code'] == 'shipping' && $total['value'] == 0){
				$total_value = '<span class="freeshipping">' . $free_shipping_title . '</span>';
			}else{
				$total_value = $this->currency->format($total['value'], $this->session->data['currency']);
			}
			$this->data['totals'][] = array(
				'title' => $total['title'],
				'text' => $total_value 
			);
		}
		$this->xtensions_checkout->setTotals($total['value']);
		$this->data['final_price'] = $this->currency->format($total['value'], $this->session->data['currency']);
		$this->session->data['xtensions_total_price'] = number_format($this->currency->convert($total['value'],$this->config->get('config_currency'),$this->session->data['currency']), (int)$this->currency->getDecimalPlace($this->session->data['currency']), $this->language->get('decimal_point'), $this->language->get('thousand_point'));
		$this->xtensions_checkout->addActivity(array('total','totals'=>$this->data['totals']));
		$this->template = $this->config->get('xtensions_view_path').'xtotals';
		return $this->xtensions_checkout->renderView($this);
	}

	public function validateCoupon() {
		$json = array();
		$this->load->model('extension/total/coupon');
		$coupon_info = $this->model_extension_total_coupon->getCoupon($this->request->post['coupon']);		
		if ($coupon_info) {
			$this->session->data['coupon'] = $this->request->post['coupon'];
			$json['applied'] = $this->language->get('coupon_successful');
			$this->xtensions_checkout->addEventActivity(array('coupon_success'));
		} else if (!empty($this->session->data['coupon']) && !$this->request->post['coupon']) {
			unset($this->session->data['coupon']);
			$json['applied'] = $this->language->get('coupon_removed');
			$this->xtensions_checkout->addEventActivity(array('coupon_removed'));
		} else if (!$this->request->post['coupon']) {
			unset($this->session->data['coupon']);
			$json['error']['error'] = $this->language->get('coupon_blank');
			$this->xtensions_checkout->addEventActivity(array('coupon_blank'));
		} else {
			unset($this->session->data['coupon']);
			$json['error']['error'] = $this->language->get('coupon_invalid');
			$this->xtensions_checkout->addEventActivity(array('coupon_invalid'));
		}
		$json['xtotals'] = $this->xtotals();
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function validateVoucher() {
		$json = array();
		$this->load->model('extension/total/voucher');
		$voucher_info = $this->model_extension_total_voucher->getVoucher($this->request->post['voucher']);		
		if ($voucher_info) {
			$this->session->data['voucher'] = $this->request->post['voucher'];
			$json['applied'] = $this->language->get('voucher_successful');
			$this->xtensions_checkout->addEventActivity(array('voucher_success'));
		} else if (!empty($this->session->data['voucher']) && !$this->request->post['voucher']) {
			unset($this->session->data['voucher']);
			$json['applied'] = $this->language->get('voucher_removed');
			$this->xtensions_checkout->addEventActivity(array('voucher_removed'));
		} else if (!$this->request->post['voucher']) {
			unset($this->session->data['voucher']);
			$json['error']['error'] = $this->language->get('voucher_blank');
			$this->xtensions_checkout->addEventActivity(array('voucher_blank'));
		} else {
			unset($this->session->data['voucher']);
			$json['error']['error'] = $this->language->get('voucher_invalid');
			$this->xtensions_checkout->addEventActivity(array('voucher_invalid'));
		}
		$json['xtotals'] = $this->xtotals();
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function validateReward($reward = 0) {
		$json = array();		
		$points = $this->customer->getRewardPoints();
		
		$points_total = 0;
		
		foreach ($this->cart->getProducts() as $product) {
			if ($product['points']) {
				$points_total += $product['points'];
			}
		}
		if (!$reward) {
			if (empty($this->request->post['reward']) && isset($this->session->data['reward'])) {
				unset($this->session->data['reward']);
				$json['applied'] = $this->language->get('reward_removed');
				$this->xtensions_checkout->addEventActivity(array('reward_removed'));
			} else if (empty($this->request->post['reward'])) {
				unset($this->session->data['reward']);
				$json['error']['error'] = $this->language->get('reward_blank');
				$this->xtensions_checkout->addEventActivity(array('reward_blank'));
			} else if ($this->request->post['reward'] > $points) {
				$json['error']['error'] = sprintf($this->language->get('reward_error_points'), $this->request->post['reward']);
				$this->xtensions_checkout->addEventActivity(array('reward_more_than_capacity'));
			} else if ($this->request->post['reward'] > $points_total) {
				$json['error']['error'] = sprintf($this->language->get('reward_maximum'), $points_total);
				$this->xtensions_checkout->addEventActivity(array('reward_more_than_products'));
			} else {
				$json['applied'] = $this->language->get('reward_successful');
				$this->session->data['reward'] = abs($this->request->post['reward']);
				$this->xtensions_checkout->addEventActivity(array('reward_success'));
			}
			$json['xtotals'] = $this->xtotals();
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		} else {
			if ($reward > $points_total) {
				return $points_total;
			} else {
				return $reward;
			}
		}
	}

	public function addComment() {
		$json = array();		
		$this->session->data['comment'] = strip_tags($this->request->post['comment']);
		$json['applied'] = $this->language->get('comment_saved');
		if(utf8_strlen($this->request->post['comment'])>0){
			$this->xtensions_checkout->addEventActivity(array('order_comment_added'));
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
?>
