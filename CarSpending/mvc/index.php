<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
spl_autoload_register();
date_default_timezone_set('Europe/Sofia');

use Controller\CsvReader;
use View\Display;
use Translations\Translations;

$csvReader = new CsvReader();	
$display = new Display();
$translations = new Translations();

$refuelData = $csvReader->readCsv('refuelData.csv');
$spendingsData = $csvReader->calcSpendings($refuelData);
$summaryData = $csvReader->calcSummary($refuelData);

$outputData = [];
foreach(['bg', 'en'] as $lang) {
	$outputData[$lang] = $display->prepareHTMLOutput($spendingsData, $summaryData, $translations->translations[$lang]);
}

$display->displayData($outputData['bg']);
$display->displayData($outputData['en']);
$csvReader->saveToFile('en.html', $outputData['en']);
