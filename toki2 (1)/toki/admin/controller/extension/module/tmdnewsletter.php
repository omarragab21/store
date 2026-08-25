<?php
class ControllerExtensionModuleTmdnewsletter extends Controller {
	private $error = array();
  public function install()
	{
	$this->load->model('extension/tmdnewsletter');
	$this->model_extension_tmdnewsletter->install();
	}
	public function uninstall()
	{
	$this->load->model('extension/tmdnewsletter');
	$this->model_extension_tmdnewsletter->uninstall();
	}
	public function index() {
		$this->load->language('extension/module/tmdnewsletter');

		$this->document->setTitle($this->language->get('heading_title1'));

		$this->load->model('setting/setting');
		$this->load->model('extension/mail');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if(isset($this->request->post['tmdnewsletter_status']))
			{
				$status=$this->request->post['tmdnewsletter_status'];
			}

			$postdata['module_tmdnewsletter_status']=$status;
			$this->model_setting_setting->editSetting('module_tmdnewsletter',$postdata);

			$this->model_setting_setting->editSetting('tmdnewsletter', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			if(isset($this->request->get['status']))
			{
			$this->response->redirect($this->url->link('extension/module/tmdnewsletter', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
			}
			else{
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
			}

		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_yes_required'] = $this->language->get('text_yes_required');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_guest'] = $this->language->get('text_guest');
		$data['text_register'] = $this->language->get('text_register');
		$data['text_verification_mail'] = $this->language->get('text_verification_mail');
		$data['text_after_confirmation'] = $this->language->get('text_after_confirmation');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_layout'] = $this->language->get('tab_layout');
		$data['tab_footer'] = $this->language->get('tab_footer');
		$data['tab_popup'] = $this->language->get('tab_popup');
		$data['tab_verifypage'] = $this->language->get('tab_verifypage');
		$data['tab_unsubscribe'] = $this->language->get('tab_unsubscribe');
		$data['tab_customcss'] = $this->language->get('tab_customcss');
		$data['tab_analytics'] = $this->language->get('tab_analytics');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_subject'] = $this->language->get('entry_subject');
		$data['entry_message'] = $this->language->get('entry_message');
		$data['entry_desc'] = $this->language->get('entry_desc');
		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_verification_req'] = $this->language->get('entry_verification_req');
		$data['entry_confirmation_req'] = $this->language->get('entry_confirmation_req');
		$data['entry_displaytitle'] = $this->language->get('entry_displaytitle');
		$data['entry_displayname'] = $this->language->get('entry_displayname');
		$data['entry_displaydesc'] = $this->language->get('entry_displaydesc');
		$data['entry_displayicon'] = $this->language->get('entry_displayicon');
		$data['entry_iconclass'] = $this->language->get('entry_iconclass');
		$data['entry_successmessage'] = $this->language->get('entry_successmessage');
		$data['entry_button'] = $this->language->get('entry_button');
		$data['entry_applylayout'] = $this->language->get('entry_applylayout');
		$data['entry_bgcolor'] = $this->language->get('entry_bgcolor');
		$data['entry_fontcolor'] = $this->language->get('entry_fontcolor');
		$data['entry_popupstatus'] = $this->language->get('entry_popupstatus');
		$data['entry_logo'] = $this->language->get('entry_logo');
		$data['entry_bgimage'] = $this->language->get('entry_bgimage');
		$data['entry_re_open'] = $this->language->get('entry_re_open');
		$data['entry_displaylogo'] = $this->language->get('entry_displaylogo');
		$data['entry_show_dec_btn'] = $this->language->get('entry_show_dec_btn');
		$data['entry_verify_succmsg'] = $this->language->get('entry_verify_succmsg');
		$data['entry_decline_succmsg'] = $this->language->get('entry_decline_succmsg');
		$data['entry_unsubscribe_succmsg'] = $this->language->get('entry_unsubscribe_succmsg');
		$data['entry_invalid_msg'] = $this->language->get('entry_invalid_msg');
		$data['entry_confirm_btn'] = $this->language->get('entry_confirm_btn');
		$data['entry_decline_btn'] = $this->language->get('entry_decline_btn');
		$data['entry_customcss'] = $this->language->get('entry_customcss');
		$data['entry_sendto'] = $this->language->get('entry_sendto');
		$data['entry_date'] = $this->language->get('entry_date');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_sendto'] = $this->language->get('column_sendto');
		$data['column_email'] = $this->language->get('column_email');
		$data['column_account'] = $this->language->get('column_account');
		$data['column_ip'] = $this->language->get('column_ip');
		$data['column_date'] = $this->language->get('column_date');
		$data['column_switched'] = $this->language->get('column_switched');
		$data['column_clicked'] = $this->language->get('column_clicked');
		$data['column_sendmessages'] = $this->language->get('column_sendmessages');
		$data['column_template'] = $this->language->get('column_template');
		$data['column_senddate'] = $this->language->get('column_senddate');
		$data['column_date'] = $this->language->get('column_date');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_action'] = $this->language->get('column_action');


		$data['help_review_guest'] = $this->language->get('help_review_guest');
		$data['help_verification_mail'] = $this->language->get('help_verification_mail');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_savestay'] = $this->language->get('button_savestay');
		$data['button_view'] = $this->language->get('button_view');
		$data['button_filter'] = $this->language->get('button_filter');
		$data['button_remove'] = $this->language->get('button_remove');
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
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/tmdnewsletter', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/tmdnewsletter', 'user_token=' . $this->session->data['user_token'], true);

		$data['staysave'] = $this->url->link('extension/module/tmdnewsletter', '&status=1&user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$tmdnewsletter_footer_applyonlayout = $this->config->get('tmdnewsletter_footer_applyonlayout');
		if (isset($this->request->post['tmdnewsletter_footer_applyonlayout'])) {
			$data['tmdnewsletter_footer_applyonlayout'] = $this->request->post['tmdnewsletter_footer_applyonlayout'];
		} elseif(!empty($tmdnewsletter_footer_applyonlayout)) {
			$data['tmdnewsletter_footer_applyonlayout'] = $this->config->get('tmdnewsletter_footer_applyonlayout');
		}else{
			$data['tmdnewsletter_footer_applyonlayout']=array();
		}

		$tmdnewsletter_popup_applyonlayout = $this->config->get('tmdnewsletter_popup_applyonlayout');
		if (isset($this->request->post['tmdnewsletter_popup_applyonlayout'])) {
			$data['tmdnewsletter_popup_applyonlayout'] = $this->request->post['tmdnewsletter_popup_applyonlayout'];
		} elseif(!empty($tmdnewsletter_popup_applyonlayout)) {
			$data['tmdnewsletter_popup_applyonlayout'] = $this->config->get('tmdnewsletter_popup_applyonlayout');
		}else{
			$data['tmdnewsletter_popup_applyonlayout']=array();
		}
		if (isset($this->request->post['tmdnewsletter_status'])) {
			$data['tmdnewsletter_status'] = $this->request->post['tmdnewsletter_status'];
		} else {
			$data['tmdnewsletter_status'] = $this->config->get('tmdnewsletter_status');
		}
		if (isset($this->request->post['tmdnewsletter_verification'])) {
			$data['tmdnewsletter_verification'] = $this->request->post['tmdnewsletter_verification'];
		} else {
			$data['tmdnewsletter_verification'] = $this->config->get('tmdnewsletter_verification');
		}

		if (isset($this->request->post['tmdnewsletter_verificationmail'])) {
			$data['tmdnewsletter_verificationmail'] = $this->request->post['tmdnewsletter_verificationmail'];
		} else {
			$data['tmdnewsletter_verificationmail'] = $this->config->get('tmdnewsletter_verificationmail');
		}
		if (isset($this->request->post['tmdnewsletter_confirmation'])) {
			$data['tmdnewsletter_confirmation'] = $this->request->post['tmdnewsletter_confirmation'];
		} else {
			$data['tmdnewsletter_confirmation'] = $this->config->get('tmdnewsletter_confirmation');
		}
		if (isset($this->request->post['tmdnewsletter_confirmationmail'])) {
			$data['tmdnewsletter_confirmationmail'] = $this->request->post['tmdnewsletter_confirmationmail'];
		} else {
			$data['tmdnewsletter_confirmationmail'] = $this->config->get('tmdnewsletter_confirmationmail');
		}
		if (isset($this->request->post['tmdnewsletter_displaytitle'])) {
			$data['tmdnewsletter_displaytitle'] = $this->request->post['tmdnewsletter_displaytitle'];
		} else {
			$data['tmdnewsletter_displaytitle'] = $this->config->get('tmdnewsletter_displaytitle');
		}

	if (isset($this->request->post['tmdnewsletter_displayname'])) {
			$data['tmdnewsletter_displayname'] = $this->request->post['tmdnewsletter_displayname'];
		} else {
			$data['tmdnewsletter_displayname'] = $this->config->get('tmdnewsletter_displayname');
		}
		if (isset($this->request->post['tmdnewsletter_displaydesc'])) {
			$data['tmdnewsletter_displaydesc'] = $this->request->post['tmdnewsletter_displaydesc'];
		} else {
			$data['tmdnewsletter_displaydesc'] = $this->config->get('tmdnewsletter_displaydesc');
		}
		if (isset($this->request->post['tmdnewsletter_displayicon'])) {
			$data['tmdnewsletter_displayicon'] = $this->request->post['tmdnewsletter_displayicon'];
		} else {
			$data['tmdnewsletter_displayicon'] = $this->config->get('tmdnewsletter_displayicon');
		}
		if (isset($this->request->post['tmdnewsletter_iconclass'])) {
			$data['tmdnewsletter_iconclass'] = $this->request->post['tmdnewsletter_iconclass'];
		} else {
			$data['tmdnewsletter_iconclass'] = $this->config->get('tmdnewsletter_iconclass');
		}
		if (isset($this->request->post['tmdnewsletter_layout'])) {
			$data['tmdnewsletter_layout'] = $this->request->post['tmdnewsletter_layout'];
		} else {
			$data['tmdnewsletter_layout'] = $this->config->get('tmdnewsletter_layout');
		}
		if (isset($this->request->post['tmdnewsletter_layoutstatus'])) {
			$data['tmdnewsletter_layoutstatus'] = $this->request->post['tmdnewsletter_layoutstatus'];
		} else {
			$data['tmdnewsletter_layoutstatus'] = $this->config->get('tmdnewsletter_layoutstatus');
		}
		if (isset($this->request->post['tmdnewsletter_footer'])) {
			$data['tmdnewsletter_footer'] = $this->request->post['tmdnewsletter_footer'];
		} else {
			$data['tmdnewsletter_footer'] = $this->config->get('tmdnewsletter_footer');
		}
		if (isset($this->request->post['tmdnewsletter_footerstatus'])) {
			$data['tmdnewsletter_footerstatus'] = $this->request->post['tmdnewsletter_footerstatus'];
		} else {
			$data['tmdnewsletter_footerstatus'] = $this->config->get('tmdnewsletter_footerstatus');
		}
		if (isset($this->request->post['tmdnewsletter_footer_displayname'])) {
			$data['tmdnewsletter_footer_displayname'] = $this->request->post['tmdnewsletter_footer_displayname'];
		} else {
			$data['tmdnewsletter_footer_displayname'] = $this->config->get('tmdnewsletter_footer_displayname');
		}
		if (isset($this->request->post['tmdnewsletter_footer_displaytitle'])) {
			$data['tmdnewsletter_footer_displaytitle'] = $this->request->post['tmdnewsletter_footer_displaytitle'];
		} else {
			$data['tmdnewsletter_footer_displaytitle'] = $this->config->get('tmdnewsletter_footer_displaytitle');
		}
		if (isset($this->request->post['tmdnewsletter_footer_backgroundcolor'])) {
			$data['tmdnewsletter_footer_backgroundcolor'] = $this->request->post['tmdnewsletter_footer_backgroundcolor'];
		} else {
			$data['tmdnewsletter_footer_backgroundcolor'] = $this->config->get('tmdnewsletter_footer_backgroundcolor');
		}
		if (isset($this->request->post['tmdnewsletter_footer_fontcolor'])) {
			$data['tmdnewsletter_footer_fontcolor'] = $this->request->post['tmdnewsletter_footer_fontcolor'];
		} else {
			$data['tmdnewsletter_footer_fontcolor'] = $this->config->get('tmdnewsletter_footer_fontcolor');
		}






		if (isset($this->request->post['tmdnewsletter_popupstatus'])) {
			$data['tmdnewsletter_popupstatus'] = $this->request->post['tmdnewsletter_popupstatus'];
		} else {
			$data['tmdnewsletter_popupstatus'] = $this->config->get('tmdnewsletter_popupstatus');
		}
		if (isset($this->request->post['tmdnewsletter_popup_displaytitle'])) {
			$data['tmdnewsletter_popup_displaytitle'] = $this->request->post['tmdnewsletter_popup_displaytitle'];
		} else {
			$data['tmdnewsletter_popup_displaytitle'] = $this->config->get('tmdnewsletter_popup_displaytitle');
		}
		if (isset($this->request->post['tmdnewsletter_popup_displayname'])) {
			$data['tmdnewsletter_popup_displayname'] = $this->request->post['tmdnewsletter_popup_displayname'];
		} else {
			$data['tmdnewsletter_popup_displayname'] = $this->config->get('tmdnewsletter_popup_displayname');
		}

		if (isset($this->request->post['tmdnewsletter_popup_reopen'])) {
			$data['tmdnewsletter_popup_reopen'] = $this->request->post['tmdnewsletter_popup_reopen'];
		} else {
			$data['tmdnewsletter_popup_reopen'] = $this->config->get('tmdnewsletter_popup_reopen');
		}
		if (isset($this->request->post['tmdnewsletter_popup'])) {
			$data['tmdnewsletter_popup'] = $this->request->post['tmdnewsletter_popup'];
		} else {
			$data['tmdnewsletter_popup'] = $this->config->get('tmdnewsletter_popup');
		}


		if (isset($this->request->post['tmdnewsletter_verify'])) {
			$data['tmdnewsletter_verify'] = $this->request->post['tmdnewsletter_verify'];
		} else {
			$data['tmdnewsletter_verify'] = $this->config->get('tmdnewsletter_verify');
		}
		if (isset($this->request->post['tmdnewsletter_verify_displaytitle'])) {
			$data['tmdnewsletter_verify_displaytitle'] = $this->request->post['tmdnewsletter_verify_displaytitle'];
		} else {
			$data['tmdnewsletter_verify_displaytitle'] = $this->config->get('tmdnewsletter_verify_displaytitle');
		}
		if (isset($this->request->post['tmdnewsletter_verify_displaydiscription'])) {
			$data['tmdnewsletter_verify_displaydiscription'] = $this->request->post['tmdnewsletter_verify_displaydiscription'];
		} else {
			$data['tmdnewsletter_verify_displaydiscription'] = $this->config->get('tmdnewsletter_verify_displaydiscription');
		}

		if (isset($this->request->post['tmdnewsletter_verify_displaydecline'])) {
			$data['tmdnewsletter_verify_displaydecline'] = $this->request->post['tmdnewsletter_verify_displaydecline'];
		} else {
			$data['tmdnewsletter_verify_displaydecline'] = $this->config->get('tmdnewsletter_verify_displaydecline');
		}
		if (isset($this->request->post['tmdnewsletter_verify_displaylogo'])) {
			$data['tmdnewsletter_verify_displaylogo'] = $this->request->post['tmdnewsletter_verify_displaylogo'];
		} else {
			$data['tmdnewsletter_verify_displaylogo'] = $this->config->get('tmdnewsletter_verify_displaylogo');
		}

		if (isset($this->request->post['tmdnewsletter_customcss'])) {
			$data['tmdnewsletter_customcss'] = $this->request->post['tmdnewsletter_customcss'];
		} else {
			$data['tmdnewsletter_customcss'] = $this->config->get('tmdnewsletter_customcss');
		}

		if (isset($this->request->post['tmdnewsletter_popuplogo'])) {
			$data['tmdnewsletter_popuplogo'] = $this->request->post['tmdnewsletter_popuplogo'];
		} else {
			$data['tmdnewsletter_popuplogo'] = $this->config->get('tmdnewsletter_popuplogo');
		}
		if (isset($this->request->post['tmdnewsletter_popupbgimage'])) {
			$data['tmdnewsletter_popupbgimage'] = $this->request->post['tmdnewsletter_popupbgimage'];
		} else {
			$data['tmdnewsletter_popupbgimage'] = $this->config->get('tmdnewsletter_popupbgimage');
		}

		if (isset($this->request->post['tmdnewsletter_unsbscribe_displaytitle'])) {
			$data['tmdnewsletter_unsbscribe_displaytitle'] = $this->request->post['tmdnewsletter_unsbscribe_displaytitle'];
		} else {
			$data['tmdnewsletter_unsbscribe_displaytitle'] = $this->config->get('tmdnewsletter_unsbscribe_displaytitle');
		}

		if (isset($this->request->post['tmdnewsletter_unsbscribe_displaydiscription'])) {
			$data['tmdnewsletter_unsbscribe_displaydiscription'] = $this->request->post['tmdnewsletter_unsbscribe_displaydiscription'];
		} else {
			$data['tmdnewsletter_unsbscribe_displaydiscription'] = $this->config->get('tmdnewsletter_unsbscribe_displaydiscription');
		}

		if (isset($this->request->post['tmdnewsletter_unsbscribe_displaylogo'])) {
			$data['tmdnewsletter_unsbscribe_displaylogo'] = $this->request->post['tmdnewsletter_unsbscribe_displaylogo'];
		} else {
			$data['tmdnewsletter_unsbscribe_displaylogo'] = $this->config->get('tmdnewsletter_unsbscribe_displaylogo');
		}
		if (isset($this->request->post['tmdnewsletter_unsbscribe'])) {
			$data['tmdnewsletter_unsbscribe'] = $this->request->post['tmdnewsletter_unsbscribe'];
		} else {
			$data['tmdnewsletter_unsbscribe'] = $this->config->get('tmdnewsletter_unsbscribe');
		}
		if (isset($this->request->post['tmdnewsletter_agree'])) {
			$data['tmdnewsletter_agree'] = $this->request->post['tmdnewsletter_agree'];
		} else {
			$data['tmdnewsletter_agree'] = $this->config->get('tmdnewsletter_agree');
		}

		$tmdnewsletter_unsbscribe_reasons = $this->config->get('tmdnewsletter_unsbscribe_reason');
		if (isset($this->request->post['tmdnewsletter_unsbscribe_reason'])) {
			$data['tmdnewsletter_unsbscribe_reasons'] = $this->request->post['tmdnewsletter_unsbscribe_reason'];
		} elseif(!empty($tmdnewsletter_unsbscribe_reasons)) {
			$data['tmdnewsletter_unsbscribe_reasons'] = $this->config->get('tmdnewsletter_unsbscribe_reason');
		}else{
			$data['tmdnewsletter_unsbscribe_reasons']=array();
		}


		$this->load->model('tool/image');
		$this->load->model('extension/subscriber');

		$tmdnewsletter_popuplogo = $this->config->get('tmdnewsletter_popuplogo');
		if (isset($this->request->post['tmdnewsletter_popuplogo']) && is_file(DIR_IMAGE . $this->request->post['tmdnewsletter_popuplogo'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['tmdnewsletter_popuplogo'], 100, 100);
		} elseif (!empty($tmdnewsletter_popuplogo) && is_file(DIR_IMAGE . $tmdnewsletter_popuplogo)) {
			$data['thumb'] = $this->model_tool_image->resize($this->config->get('tmdnewsletter_popuplogo'), 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		$tmdnewsletter_popupbgimage = $this->config->get('tmdnewsletter_popupbgimage');
		if (isset($this->request->post['tmdnewsletter_popupbgimage']) && is_file(DIR_IMAGE . $this->request->post['tmdnewsletter_popupbgimage'])) {
			$data['thumbs'] = $this->model_tool_image->resize($this->request->post['tmdnewsletter_popupbgimage'], 100, 100);
		} elseif (!empty($tmdnewsletter_popupbgimage) && is_file(DIR_IMAGE . $tmdnewsletter_popupbgimage)) {
			$data['thumbs'] = $this->model_tool_image->resize($this->config->get('tmdnewsletter_popupbgimage'), 100, 100);
		} else {
			$data['thumbs'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);



		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/tmdnewsletter', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/tmdnewsletter')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
