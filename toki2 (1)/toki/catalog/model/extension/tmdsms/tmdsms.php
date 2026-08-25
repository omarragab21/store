<?php
class ModelExtensionTmdsmsTmdSms extends Model {

	public function sendSms($data){
		$tmdsmsurl = $this->config->get('tmdsms_url');
		$tmdsmsstatus =  $this->config->get('tmdsms_status');
		$method =  $this->config->get('tmdsms_method');

     	if(!empty($data['message']) && $tmdsmsstatus == 1 && $method == 1){
     		$find = array(
				'{mobileno}',
				'{message}',
				'{dltid}',
				'&amp;'
			);

			$replace = array(
				'mobileno'  => $data['telephone'],
				'message' 	=> urlencode($data['message']),
				'dltid'		=> $data['dltid'],
				'&amp;' 	=>'&'
			);
			
			$format = str_replace(array("\r\n", "\r", "\n"), '', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '', trim(str_replace($find, $replace, $tmdsmsurl)))); 
			$url = $format;
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$ssl=$this->config->get('tmdsms_ssl'); 
			if($ssl == 1){
				curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, 3);
			}
			$curl_scraped_page = curl_exec($ch);
			curl_close($ch);
			$debug=$this->config->get('tmdsms_debug'); 
			if($debug == 1){
				$this->log->write('Mobile No:-'.$data['telephone'].' Message:-'.$data['message'].' dlt:-'.$data['dltid']);
			}
		}

	}
}
