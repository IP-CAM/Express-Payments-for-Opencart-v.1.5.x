<?php
class ControllerPaymentEripExpressPay extends Controller {
    private $error = array();

    public function index() {
		define("ERIP_EXPRESSPAY_VERSION", "2.4");
		$this->load->language('payment/erip_expresspay');
		
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('erip_expresspay', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title']      = $this->language->get('heading_title');
		$this->data['button_save']        = $this->language->get('button_save');
		$this->data['button_cancel']      = $this->language->get('button_cancel');
		$this->data['error_warning']      = $this->language->get('error_warning');
		$this->data['token_label']     	  = $this->language->get('token_label');
		$this->data['token_comment']   	  = $this->language->get('token_comment');
		$this->data['secret_key_label']       = $this->language->get('secret_key_label');
		$this->data['secret_key_comment']     = $this->language->get('secret_key_comment');
		$this->data['sign_invoices_label']     = $this->language->get('sign_invoices_label');
		$this->data['text_enabled']       = $this->language->get('text_enabled');
		$this->data['text_disabled']      = $this->language->get('text_disabled');
		$this->data['status_label']       = $this->language->get('status_label');
		$this->data['sort_order_label']   = $this->language->get('sort_order_label');
		$this->data['text_all_zones']     = $this->language->get('text_all_zones');
		$this->data['pending_status']     = $this->language->get('pending_status');
		$this->data['cancel_status']	  = $this->language->get('cancel_status');
		$this->data['processing_status']  = $this->language->get('processing_status');
		$this->data['handler_url']  	  = str_replace('/admin', '', HTTPS_SERVER . 'index.php?route=payment/erip_expresspay/notify');
		$this->data['handler_label']  	  = $this->language->get('handler_label');
		$this->data['test_mode_label']  	  = $this->language->get('test_mode_label');
		$this->data['name_editable_label']  	  = $this->language->get('name_editable_label');
		$this->data['address_editable_label']  	  = $this->language->get('address_editable_label');
		$this->data['amount_editable_label']  	  = $this->language->get('amount_editable_label');
		$this->data['url_api_label']  	  = $this->language->get('url_api_label');
		$this->data['url_sandbox_api_label']  	  = $this->language->get('url_sandbox_api_label');
		$this->data['secret_key_notify_label']  	  = $this->language->get('secret_key_notify_label');
		$this->data['secret_key_notify_comment']  	  = $this->language->get('secret_key_notify_comment');
		$this->data['sign_notify_label']  	  = $this->language->get('sign_notify_label');
		$this->data['text_contacts']  	  = $this->language->get('text_contacts');
		$this->data['text_phone']  	  = $this->language->get('text_phone');
		$this->data['settings_module_label']  	  = $this->language->get('settings_module_label');
		$this->data['name_editable_comment']  	  = $this->language->get('name_editable_comment');
		$this->data['address_editable_comment']  	  = $this->language->get('address_editable_comment');
		$this->data['amount_editable_comment']  	  = $this->language->get('amount_editable_comment');
		$this->data['sign_comment']  	  = $this->language->get('sign_comment');
		$this->data['text_version']  	  = $this->language->get('text_version');
		$this->data['text_about']  	  = $this->language->get('text_about');
		$this->data['message_success_label']  	  = $this->language->get('message_success_label');

		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/erip_expresspay', 'token=' . $this->session->data['token'], 'SSL'),      		
      		'separator' => ' :: '
   		);

		$this->data['action'] = $this->url->link('payment/erip_expresspay', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
	
		if (isset($this->request->post['erip_token'])) {
			$this->data['erip_token_value'] = $this->request->post['erip_token'];
		} else {
			$this->data['erip_token_value'] = $this->config->get('erip_token');
		}

		if (isset($this->request->post['erip_secret_key'])) {
			$this->data['erip_secret_key_value'] = $this->request->post['erip_secret_key'];
		} else {
			$this->data['erip_secret_key_value'] = $this->config->get('erip_secret_key');
		}
		
		if (isset($this->request->post['erip_sign_invoices'])) {
			$this->data['erip_sign_invoices_value'] = $this->request->post['erip_sign_invoices'];
		} else {
			$this->data['erip_sign_invoices_value'] = $this->config->get('erip_sign_invoices');
		}

		if (isset($this->request->post['erip_sign_notify'])) {
			$this->data['erip_sign_notify_value'] = $this->request->post['erip_sign_notify'];
		} else {
			$this->data['erip_sign_notify_value'] = $this->config->get('erip_sign_notify');
		}
		
		if (isset($this->request->post['erip_message_success'])) {
			$this->data['erip_message_success_value'] = $this->request->post['erip_message_success'];
		} else {
			$erip_message_success = $this->config->get('erip_message_success');
			
			$this->data['erip_message_success_value'] = ( !empty($erip_message_success) ) ? $this->config->get('erip_message_success') : $this->language->get('message_success');
		}

		if (isset($this->request->post['erip_secret_key_notify'])) {
			$this->data['erip_secret_key_notify_value'] = $this->request->post['erip_secret_key_notify'];
		} else {
			$this->data['erip_secret_key_notify_value'] = $this->config->get('erip_secret_key_notify');
		}

		if (isset($this->request->post['erip_sign_notify'])) {
			$this->data['erip_sign_notify_value'] = $this->request->post['erip_sign_notify'];
		} else {
			$this->data['erip_sign_notify_value'] = $this->config->get('erip_sign_notify');
		}
		
		if (isset($this->request->post['erip_test_mode'])) {
			$this->data['erip_test_mode_value'] = $this->request->post['erip_test_mode'];
		} else {
			$this->data['erip_test_mode_value'] = $this->config->get('erip_test_mode');
		}

		if (isset($this->request->post['erip_name_editable'])) {
			$this->data['erip_name_editable_value'] = $this->request->post['erip_name_editable'];
		} else {
			$this->data['erip_name_editable_value'] = $this->config->get('erip_name_editable');
		}

		if (isset($this->request->post['erip_address_editable'])) {
			$this->data['erip_address_editable_value'] = $this->request->post['erip_address_editable'];
		} else {
			$this->data['erip_address_editable_value'] = $this->config->get('erip_address_editable');
		}

		if (isset($this->request->post['erip_amount_editable'])) {
			$this->data['erip_amount_editable_value'] = $this->request->post['erip_amount_editable'];
		} else {
			$this->data['erip_amount_editable_value'] = $this->config->get('erip_amount_editable');
		}
		
		if (isset($this->request->post['erip_url_api'])) {
			$this->data['erip_url_api_value'] = $this->request->post['erip_url_api'];
		} else {
			$this->data['erip_url_api_value'] = $this->config->get('erip_url_api');
		}

		if (isset($this->request->post['erip_url_sandbox_api'])) {
			$this->data['erip_url_sandbox_api_value'] = $this->request->post['erip_url_sandbox_api'];
		} else {
			$this->data['erip_url_sandbox_api_value'] = $this->config->get('erip_url_sandbox_api');
		}

		if (isset($this->request->post['erip_sort_order'])) {
			$this->data['erip_sort_order'] = $this->request->post['erip_sort_order'];
		} else {
			$this->data['erip_sort_order'] = $this->config->get('erip_sort_order');
		}
		if (isset($this->request->post['erip_expresspay_status'])) {
			$this->data['erip_expresspay_status'] = $this->request->post['erip_expresspay_status'];
		} else {
			$this->data['erip_expresspay_status'] = $this->config->get('erip_expresspay_status');
		}
	
		if (isset($this->request->post['erip_pending_status_id'])) {
			$this->data['erip_pending_status_id'] = $this->request->post['erip_pending_status_id'];
		} elseif ($this->config->has('erip_pending_status_id')) {
			$this->data['erip_pending_status_id'] = $this->config->get('erip_pending_status_id');
		} else {
			$this->data['erip_pending_status_id'] = '1';
		}

		if (isset($this->request->post['erip_cancel_status_id'])) {
			$this->data['erip_cancel_status_id'] = $this->request->post['erip_cancel_status_id'];
		} elseif ($this->config->has('erip_cancel_status_id')) {
			$this->data['erip_cancel_status_id'] = $this->config->get('erip_cancel_status_id');
		} else {
			$this->data['erip_cancel_status_id'] = '10';
		}

		if (isset($this->request->post['erip_processing_status_id'])) {
			$this->data['erip_processing_status_id'] = $this->request->post['erip_processing_status_id'];
		} elseif ($this->config->has('erip_processing_status_id')) {
			$this->data['erip_processing_status_id'] = $this->config->get('erip_processing_status_id');
		} else {
			$this->data['erip_processing_status_id'] = '2';
		}

		$this->template = 'payment/erip_expresspay.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
		$this->response->setOutput($this->render());
    }

	private function validate() {
		$this->error = false;

		if (!$this->error)
			return true;
		else
			return false;
	}
}
?>