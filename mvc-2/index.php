<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
spl_autoload_register();
date_default_timezone_set('Europe/Sofia');

use Controller\RefuelController;
use View\RefuelView;
use Model\RefuelModel;

$view = new RefuelView();
// print '<pre/>'; print_r($view); exit('in index');

$refuelController = new RefuelController($view);	
print $refuelController->refuelData();
print $refuelController->test();
// print '<pre/>'; print_r($refuelController); die('in index');
