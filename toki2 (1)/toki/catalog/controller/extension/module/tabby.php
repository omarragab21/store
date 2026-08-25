<?php
class ControllerExtensionModuleTabby extends Controller {
	public function index() {
		$this->document->addScript('https://checkout.tabby.ai/integration.js');
	}
}
