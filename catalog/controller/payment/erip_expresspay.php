<?php
class ControllerPaymentEripExpressPay extends Controller {
	protected function index() {
		$this->data['button_confirm'] = $this->language->get('button_confirm');
		$this->data['redirect'] = $this->url->link('payment/erip_expresspay/send');
		
		if(file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/erip_expresspay.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/erip_expresspay.tpl';
		} else {
			$this->template = 'default/template/payment/erip_expresspay.tpl';
		}

		$this->render();
	}
	
	public function send() {
		$this->log_info('send', 'Initialization request for add invoice');
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		$secret_word = $this->config->get('erip_secret_key');
		$is_use_signature = ( $this->config->get('erip_sign_invoices') == 'on' ) ? true : false;

		$url = ( $this->config->get('erip_test_mode') != 'on' ) ? $this->config->get('erip_url_api') : $this->config->get('erip_url_sandbox_api');
		$url .= "/v1/invoices?token=" . $this->config->get('erip_token');

		$currency = (date('y') > 16 || (date('y') >= 16 && date('n') >= 7)) ? '933' : '974';
        $amount = $this->currency->format($order_info['total'], '', '', false);
        $amount = str_replace('.',',',$amount);
        $request_params = array(
            "AccountNo" => $this->session->data['order_id'],
            "Amount" => $amount,
            "Currency" => $currency,
            "Surname" => $order_info['payment_lastname'],
            "FirstName" => $order_info['payment_firstname'],
            "City" => $order_info['payment_city'],
            "IsNameEditable" => ( ( $this->config->get('erip_name_editable') == 'on' ) ? 1 : 0 ),
            "IsAddressEditable" => ( ( $this->config->get('erip_address_editable') == 'on' ) ? 1 : 0 ),
            "IsAmountEditable" => ( ( $this->config->get('erip_amount_editable') == 'on' ) ? 1 : 0 )
        );

        if($is_use_signature)
        	$url .= "&signature=" . $this->compute_signature_add_invoice($request_params, $secret_word);

        $request_params = http_build_query($request_params);

        $this->log_info('send', 'Send POST request; ORDER ID - ' . $this->session->data['order_id'] . '; URL - ' . $url . '; REQUEST - ' . $request_params);

        $response = "";

        try {
	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $url);
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_params);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	        $response = curl_exec($ch);
	        curl_close($ch);
    	} catch (Exception $e) {
    		$this->log_error_exception('send', 'Get response; ORDER ID - ' . $this->session->data['order_id'] . '; RESPONSE - ' . $response, $e);

    		$this->redirect($this->url->link('payment/erip_expresspay/fail'));
    	}

    	$this->log_info('send', 'Get response; ORDER ID - ' . $this->session->data['order_id'] . '; RESPONSE - ' . $response);

		try {
        	$response = json_decode($response);
    	} catch (Exception $e) {
    		$this->log_error_exception('send', 'Get response; ORDER ID - ' . $this->session->data['order_id'] . '; RESPONSE - ' . $response, $e);

    		$this->redirect($this->url->link('payment/erip_expresspay/fail'));
    	}

        if(isset($response->InvoiceNo))
        	$this->redirect($this->url->link('payment/erip_expresspay/success'));
        else
        	$this->redirect($this->url->link('payment/erip_expresspay/fail'));
	}

	public function success() {
		$this->log_info('send', 'End request for add invoice');
		$this->log_info('success', 'Initialization render success page; ORDER ID - ' . $this->session->data['order_id']);

		$this->cart->clear();

		$this->load->language('payment/erip_expresspay');
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['text_message'] = $this->language->get('text_message');
		$this->document->setTitle($this->data['heading_title']);

		$this->data['breadcrumbs'] = array(); 

		$this->data['breadcrumbs'][] = array(
			'href'      => $this->url->link('common/home'),
			'text'      => $this->language->get('text_home'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'href'      => $this->url->link('checkout/cart'),
			'text'      => $this->language->get('text_basket'),
			'separator' => $this->language->get('text_separator')
		);

		$this->data['breadcrumbs'][] = array(
			'href'      => $this->url->link('checkout/checkout', '', 'SSL'),
			'text'      => $this->language->get('text_checkout'),
			'separator' => $this->language->get('text_separator')
		);	

		$this->data['button_continue'] = $this->language->get('button_continue');
		$this->data['test_mode_label'] = $this->language->get('test_mode_label');
		$this->data['text_send_notify_success'] = $this->language->get('text_send_notify_success');
		$this->data['text_send_notify_cancel'] = $this->language->get('text_send_notify_cancel');
		$this->data['continue'] = $this->url->link('common/home');
		$this->data['test_mode'] = ( $this->config->get('erip_test_mode') == 'on' ) ? true : false;
		$this->data['message_success'] = nl2br($this->config->get('erip_message_success'), true);
		$this->data['order_id'] = $this->session->data['order_id'];
		$this->data['message_success'] = str_replace("##order_id##", $this->data['order_id'], $this->data['message_success']);
		$this->data['is_use_signature'] = ( $this->config->get('erip_sign_invoices') == 'on' ) ? true : false;
		$this->data['signature_success'] = $this->data['signature_cancel'] = "";

		if($this->data['is_use_signature']) {
			$secret_word = $this->config->get('erip_secret_key_notify');
			$this->data['signature_success'] = $this->compute_signature('{"CmdType": 1, "AccountNo": ' . $this->data["order_id"] . '}', $secret_word);
			$this->data['signature_cancel'] = $this->compute_signature('{"CmdType": 2, "AccountNo": ' . $this->data["order_id"] . '}', $secret_word);
		}

		$this->load->model('checkout/order');
		$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('erip_pending_status_id'));

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/erip_expresspay_success.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/erip_expresspay_success.tpl';
		} else {
			$this->template = 'default/template/payment/erip_expresspay_success.tpl';
		}

		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'			
		);

		$this->log_info('success', 'End render success page; ORDER ID - ' . $this->session->data['order_id']);

		$this->response->setOutput($this->render());		
	}

	public function fail() {
		$this->log_info('send', 'End request for add invoice');
		$this->log_info('fail', 'Initialization render fail page; ORDER ID - ' . $this->session->data['order_id']);

		$this->load->language('payment/erip_expresspay');
		$this->data['heading_title'] = $this->language->get('heading_title_error');
		$this->data['text_message'] = $this->language->get('text_message_error');
		$this->document->setTitle($this->data['heading_title']);

		$this->data['breadcrumbs'] = array(); 

		$this->data['breadcrumbs'][] = array(
			'href'      => $this->url->link('common/home'),
			'text'      => $this->language->get('text_home'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'href'      => $this->url->link('checkout/cart'),
			'text'      => $this->language->get('text_basket'),
			'separator' => $this->language->get('text_separator')
		);

		$this->data['breadcrumbs'][] = array(
			'href'      => $this->url->link('checkout/checkout', '', 'SSL'),
			'text'      => $this->language->get('text_checkout'),
			'separator' => $this->language->get('text_separator')
		);	

		$this->data['button_continue'] = $this->language->get('button_continue');
		$this->data['continue'] = $this->url->link('checkout/checkout');

		if(file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/erip_expresspay_failure.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/erip_expresspay_failure.tpl';
		} else {
			$this->template = 'default/template/payment/erip_expresspay_failure.tpl';
		}

		$this->load->model('checkout/order');
		$this->model_checkout_order->update($this->session->data['order_id'], $this->config->get('erip_cancel_status_id'));

		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'	
		);

		$this->log_info('fail', 'End render fail page; ORDER ID - ' . $this->session->data['order_id']);

		$this->response->setOutput($this->render());
	}

	public function notify() {
		$this->log_info('notify', 'Get notify from server; REQUEST METHOD - ' . $_SERVER['REQUEST_METHOD']);

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$secret_word = $this->config->get('erip_secret_key_notify');
			$is_use_signature = ( $this->config->get('erip_sign_notify') == 'on' ) ? true : false;
			$data = ( isset($this->request->post['Data']) ) ? htmlspecialchars_decode($this->request->post['Data']) : '';
			$signature = ( isset($this->request->post['Signature']) ) ? $this->request->post['Signature'] : '';

		    if($is_use_signature) {
		    	if($signature == $this->compute_signature($data, $secret_word))
			        $this->notify_success($data);
			    else  
			    	$this->notify_fail($data);
		    } else 
		    	$this->notify_success($data);
		}

		$this->log_info('notify', 'End (Get notify from server); REQUEST METHOD - ' . $_SERVER['REQUEST_METHOD']);
	}

	private function notify_success($dataJSON) {
		try {
        	$data = json_decode($dataJSON);
    	} catch(Exception $e) {
    		$this->log_error('notify_fail', "Fail to parse the server response; RESPONSE - " . $dataJSON);
    		$this->notify_fail($dataJSON);
    	}

		$this->load->model('checkout/order');

        if(isset($data->CmdType)) {
        	switch ($data->CmdType) {
        		case '1':
        			$this->model_checkout_order->update($data->AccountNo, $this->config->get('erip_processing_status_id'));
        			$this->log_info('notify_success', 'Initialization to update status. STATUS ID - ' . $this->config->get('erip_processing_status_id') . "; RESPONSE - " . $dataJSON);

        			break;
        		case '2':
        			$this->model_checkout_order->update($data->AccountNo, $this->config->get('erip_cancel_status_id'));
					$this->log_info('notify_success', 'Initialization to update status. STATUS ID - ' . $this->config->get('erip_cancel_status_id') . "; RESPONSE - " . $dataJSON);

        			break;
        		default:
					$this->notify_fail($dataJSON);
					die();
        	}

	    	header("HTTP/1.0 200 OK");
	    	echo 'SUCCESS';

	    	$this->log_info('notify_success', 'Success to update status');
        } else
			$this->notify_fail($dataJSON);	

		$this->log_info('notify_success', 'End to update status');
	}

	private function notify_fail($dataJSON) {
		$this->log_error('notify_fail', "Fail to update status; RESPONSE - " . $dataJSON);

		header("HTTP/1.0 400 Bad Request");
		echo 'FAILED | Incorrect digital signature';
	}

	private function compute_signature($json, $secret_word) {
	    $hash = NULL;
	    $secret_word = trim($secret_word);
	    
	    if (empty($secret_word))
			$hash = strtoupper(hash_hmac('sha1', $json, ""));
	    else
	        $hash = strtoupper(hash_hmac('sha1', $json, $secret_word));

	    return $hash;
	}	

    private function compute_signature_add_invoice($request_params, $secret_word) {
    	$secret_word = trim($secret_word);
        $normalized_params = array_change_key_case($request_params, CASE_LOWER);
        $api_method = array(
                "accountno",
                "amount",
                "currency",
                // "expiration",
                // "info",
                "surname",
                "firstname",
                // "patronymic",
                "city",
                // "street",
                // "house",
                // "building",
                // "apartment",
                "isnameeditable",
                "isaddresseditable",
                "isamounteditable"
        );

        $result = $this->config->get('erip_token');

        foreach ($api_method as $item)
            $result .= ( isset($normalized_params[$item]) ) ? $normalized_params[$item] : '';

        $hash = strtoupper(hash_hmac('sha1', $result, $secret_word));

        return $hash;
    }

    private function log_error_exception($name, $message, $e) {
    	$this->log($name, "ERROR" , $message . '; EXCEPTION MESSAGE - ' . $e->getMessage() . '; EXCEPTION TRACE - ' . $e->getTraceAsString());
    }

    private function log_error($name, $message) {
    	$this->log($name, "ERROR" , $message);
    }

    private function log_info($name, $message) {
    	$this->log($name, "INFO" , $message);
    }

    private function log($name, $type, $message) {
    	$log = new Log('erip_expresspay/express-pay-' . date('Y.m.d') . '.log');
    	$log->write($type . " - IP - " . $_SERVER['REMOTE_ADDR'] . "; USER AGENT - " . $_SERVER['HTTP_USER_AGENT'] . "; FUNCTION - " . $name . "; MESSAGE - " . $message . ';');
    }

}

?>