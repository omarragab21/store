<?php
require_once(dirname(__FILE__) . "/tabby_paylater.php");
class ControllerExtensionPaymentTabbyInstallments extends ControllerExtensionPaymentTabbyPayLater {
	private $error = array();
	
    protected $code = 'tabby_installments';
    const DEFAULT_TITLE = "4 interest-free payments";

}
