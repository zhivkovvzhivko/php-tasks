<?php

namespace Controller;

use Model\RefuelModel;
use Translations\Translations;

class RefuelController
{

	private $view;

	public function __controller($view) {
		$this->view = $view;
		// print '<pre/>'; print_r($this->view); exit('in controller');
	}
	
	public function refuelData(){
		$translations = new Translations();
		$refuelModel = new RefuelModel($translations);
		$data = $refuelModel->getOutputData;

		$this->view->displayData($data);
	}

	public function test() {

		$model = new RefuelModel();
		print '<pre/>'; print_r($model); die('in controller');
	}
}
