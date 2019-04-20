<?php

namespace Controller;

use Model\RefuelModel;
use View\RefuelView;
use Translations\Translations;

class RefuelController
{

	private $view;

	public function __controller($view) {
		$this->view = $view;
	}
	
	public function refuelData(){
		$translations = new Translations();
		$refuelModel = new RefuelModel($translations);
		

		// $refuelData = $refuelModel->readCsv($testArr);
		// print '<pre/>'; print_r($refuelData); exit(' 166');
		// $spendingsData = $this->calcSpendings($refuelData);
		// $summaryData = $this->calcSummary($refuelData);

		$data = $refuelModel->getOutputData();

		$this->view->displayData($data);
	}

	public function test() {

		$model = new RefuelModel();
		print '<pre/>'; print_r($model); die('in controller');
	}
}
