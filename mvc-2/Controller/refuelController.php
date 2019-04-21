<?php

namespace Controller;

use Model\RefuelModel;
use View\RefuelView;
use Translations\Translations;

class RefuelController
{

	private $view;

	public function __construct($view) {
		$this->view = $view;
	}
	
	public function refuelData(){
		$translations = new Translations();
		$refuelModel = new RefuelModel($translations);

		$data = $refuelModel->getOutputData();
		
		$this->view->displayData($data);
	}

	public function test() {

		$model = new RefuelModel();
		print '<pre/>'; print_r($model->getOutputData()); die('in TESTcontroller');
	}
}
