<?php
class ModelPaymentEripExpressPay extends Model {
	public function getMethod($address, $total) {
		$this->load->language('payment/erip_expresspay');
		
		$status = true;
				
		$method_data = array();
		
		if ($status) {
			$method_data = array(
				'code'       => 'erip_expresspay',
				'title'      => $this->language->get('text_title'),
				'sort_order' => $this->config->get('erip_sort_order')
			);
		}
		return $method_data;
	}
}
?>