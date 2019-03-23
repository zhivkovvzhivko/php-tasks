<?php
// ini_set('error_reporting', E_ALL ^ E_DEPRECATED ^ E_STRICT);
// ini_set('display_errors', 0);

//  display_errors(0);
// ini_set('display_errors', 1);
date_default_timezone_set('Europe/Sofia');

$data = array(
    array('km'=>165732, 'distance'=>487, 'liters'=>43, 'price'=>2.02, 'date'=>'2018-02-17'),
    array('km'=>165752, 'distance'=>507, 'liters'=>1.4, 'price'=>2.02, 'date'=>'2018-02-22'),
    array('km'=>165782, 'distance'=>537, 'liters'=>2.1, 'price'=>2.02, 'date'=>'2018-03-03'),
    array('km'=>165822, 'distance'=>577, 'liters'=>2.8, 'price'=>2.02, 'date'=>'2018-03-17'),
    array('km'=>165872, 'distance'=>627, 'liters'=>3.5, 'price'=>2.02, 'date'=>'2018-04-06'),
    array('km'=>165932, 'distance'=>687, 'liters'=>4.2, 'price'=>2.02, 'date'=>'2018-05-01'),
);

function fuelCost100km($liters_fuel, $distance) {
	return 100 * $liters_fuel / $distance; 
}

function fuelPrice100km($fuelCost100km, $LitrePrice) {
	return $fuelCost100km * $LitrePrice;
}

function calcSummarySpendings($data) {

	$totalFuelCost = 0;
	$totalFuelLitres = 0;
	$refuelDaysSum= 0;
	$avgRefuelDays = 0;
	$refuelCount = 0;

	$prev = array_shift($data);
	foreach ($data as $value) {
		$distance = $value['distance'] - $prev['distance'];
		$totalFuelLitres += $value['liters'];
		$liters = $value['liters'];

		$fuelCost100km = fuelCost100km($value['liters'], $distance);
		$fuelPrice100km = fuelPrice100km($fuelCost100km, $prev['price']);
		$fuelPrice1km  = $fuelPrice100km / 100;
		$totalFuelCost += $distance * number_format($fuelPrice1km, 2);

		$refuelDaysSum+= round((strtotime($value['date']) - strtotime($prev['date'])) / 86400);

		$prev = $value;
		$refuelCount++;
	}

	$avgRefuelDays = round($refuelDaysSum/$refuelCount);

	$spendingData['fuelCost100km'] = $fuelCost100km;
	$spendingData['fuelPrice100km'] = $fuelPrice100km;
	$spendingData['fuelPrice1km'] = $fuelPrice1km;
	$spendingData['totalFuelCost'] = $totalFuelCost;
	$spendingData['totalFuelLitres'] = $totalFuelLitres;
	$spendingData['avgRefuelDays'] = $avgRefuelDays;

	return $spendingData;
}

function prepareOutput($calcData, $lang='bg') {
	
	$lang = strtolower($lang);
	$outputData = array();

	if ($lang === 'en') {
		foreach ($calcData as $key => $value) {
			if ($key == 'fuelCost100km') {
				$outputData['Liters cost per 100km'] = number_format((float)$value, 2) . ' Liters';
			} elseif ($key == 'fuelPrice100km') {
				$outputData['Price per 100km'] = number_format((float)$value, 1) . ' BGN';
			} elseif ($key == 'fuelPrice1km') {
				$outputData['Price per 1km'] = number_format((float)$value, 1) . ' BGN';
			} elseif ($key == 'totalFuelCost') {
				$outputData['Total fuel cost'] = $value . ' BGN';
			} elseif ($key == 'totalFuelLitres') {
				$outputData['Total consumed fuel'] = $value . ' Liters';
			} elseif ($key == 'avgRefuelDays') {
				$outputData[$key] = $value . ' Days';
			}
		}		
	} elseif ($lang === 'bg') {
		foreach ($calcData as $key => $value) {
			if ($key == 'fuelCost100km') {
				$outputData['Разход на 100км'] = number_format((float)$value, 1) . ' литра';
			} elseif ($key == 'fuelPrice100km') {
				$outputData['Цена на 100км'] = number_format((float)$value, 2) . ' лева';
			} elseif ($key == 'fuelPrice1km') {
				$outputData['Цена на 1км'] = number_format((float)$value, 2) . ' лева';
			} elseif ($key == 'totalFuelCost') {
				$outputData['Тотал разходи за гориво'] = $value . ' лева';
			} elseif ($key == 'totalFuelLitres') {
				$outputData['Тотал изразходвано гориво'] = $value . ' литри';
			} elseif ($key == 'avgRefuelDays') {
				$outputData['Среден период на зареждане'] = $value . ' дни';
			}
		}
	} else {
		// die('Incorrect language input. Please reload the page and try again.');
	}

	return $outputData;
}

function createHtmlTable($data) {
	$html = '<table border="1">';
	foreach ($data as $key => $value) {
		$html .= '<tr><td>' . $key . '</td><td>' . $value .'</td></tr>';
	}
	$html .= '</table>';

	return $html;
}

function saveToFile($filename, $strData) {

	$handle = fopen($filename, 'a+');
	fwrite($handle, $strData . "\r");
	fclose($handle);
}

function displayData($strData) {
	return file_get_contents($strData);
}

$spendingData = calcSummarySpendings($data);
$outputData = prepareOutput($spendingData, 'bg');
$htmlOutput = createHtmlTable($outputData);

saveToFile('data.php', $htmlOutput);

echo displayData('data.php');


// TODO

/*
function calcRefuelSpendings($data) {}

*/