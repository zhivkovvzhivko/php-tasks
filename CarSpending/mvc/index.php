<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Sofia');

require_once('csvReader.php');
require_once('display.php');
require_once('translations.php');

$reader = new CsvReader();
$display = new Display();
$translations = new Translations();

$refuelData = $reader->readCsv('refuelData.csv');
$spendingsData = $reader->calcSpendings($refuelData);
$summaryData = $reader->calcSummary($refuelData);

$outputData = [];
foreach(['bg', 'en'] as $lang) {
	$outputData[$lang] = $display->prepareHTMLOutput($spendingsData, $summaryData, $translations::translations[$lang]);
}

// saveToFile('en.html', $outputData['en']);
$display->displayData($outputData['bg']);
$display->displayData($outputData['en']);
