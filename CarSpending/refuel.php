<?php

include 'translations.php';

date_default_timezone_set('Europe/Sofia');

$data = [
    ['km'=>165732, 'distance'=>487, 'liters'=>43, 'price'=>2.02, 'date'=>'2018-02-17'],
    ['km'=>165752, 'distance'=>507, 'liters'=>1.4, 'price'=>2.02, 'date'=>'2018-02-22'],
    ['km'=>165782, 'distance'=>537, 'liters'=>2.1, 'price'=>2.02, 'date'=>'2018-03-03'],
    ['km'=>165822, 'distance'=>577, 'liters'=>2.8, 'price'=>2.02, 'date'=>'2018-03-17'],
    ['km'=>165872, 'distance'=>627, 'liters'=>3.5, 'price'=>2.02, 'date'=>'2018-04-06'],
    ['km'=>165932, 'distance'=>687, 'liters'=>4.2, 'price'=>2.02, 'date'=>'2018-05-01'],
];

function spentLitersPer100km($liters_fuel, $distance) {
	return 100 * $liters_fuel / $distance; 
}

function spentMoneyPer100km($spentLitersPer100km, $LitrePrice) {
	return $spentLitersPer100km * $LitrePrice;
}

function spentMoneyPer1km($spentMoneyPer100km) {
 return $spentMoneyPer100km / 100;
}

function calcTotalFuelPrice($distance, $fuelPrice1km) {
	return $distance * $fuelPrice1km;
}

function calcRefuelDaysSum($currentDate, $prevDate) {
	return round((strtotime($currentDate) - strtotime($prevDate)) / 86400);
}

function calcAvgRefuelDays($refuelDaysSum, $refuelCount) {
	return $avgRefuelDays = round($refuelDaysSum/$refuelCount);
}

function calcSummarySpendings($data) {

	$totalFuelPrice = 0;
	$totalFuelLitres = 0;
	$refuelDaysSum= 0;
	$avgRefuelDays = 0;
	$refuelCount = 0;
	$spendingData['allRefuelStatuses'] = [];

	$prev = array_shift($data);
	foreach ($data as $value) {
		$distance = $value['distance'] - $prev['distance'];
		$totalFuelLitres += $value['liters'];
		$liters = $value['liters'];

		$spentLitersPer100km = spentLitersPer100km($value['liters'], $distance);
		$spentMoneyPer100km = spentMoneyPer100km($spentLitersPer100km, $prev['price']);
		$fuelPrice1km  = spentMoneyPer1km($spentMoneyPer100km);
		$totalFuelPrice += calcTotalFuelPrice($distance, $fuelPrice1km);

		$refuelDaysSum += calcRefuelDaysSum($value['date'], $prev['date']);

		$refuelStatus = [
			'spentLitersPer100km' => $spentLitersPer100km,
			'spentMoneyPer100km' => $spentMoneyPer100km,
			'fuelPrice1km' => $fuelPrice1km,
			'distance' => $distance,
			'totalFuelPrice' => calcTotalFuelPrice($distance, $fuelPrice1km),
			'refuelDaysSum' => $refuelDaysSum,
		];

		array_push($spendingData['allRefuelStatuses'],  $refuelStatus);
		
		$prev = $value;
		$refuelCount++;
	}
	
	$avgRefuelDays = calcAvgRefuelDays($refuelDaysSum, $refuelCount);

	$spendingData['spentLitersPer100km'] = $spentLitersPer100km;
	$spendingData['spentMoneyPer100km'] = $spentMoneyPer100km;
	$spendingData['fuelPrice1km'] = $fuelPrice1km;
	$spendingData['totalFuelPrice'] = $totalFuelPrice;
	$spendingData['totalFuelLitres'] = $totalFuelLitres;
	$spendingData['avgRefuelDays'] = $avgRefuelDays;

	return $spendingData;
}

function prepareOutput($calcData, $translation, $suffix, $lang='bg') {
	
	$lang = strtolower($lang);
	$outputData = array();

	if ($lang == 'en') {

		$refuelSpendingshtml = '';
		$html .= '<h3>Total Refuel Spendings</h3>
					<table border="1">';
		foreach ($calcData as $key => $value) {

			if (is_array($value)) {

				$refuelSpendingshtml = '
				<h3>Refuel Spendings</h3>
					<table border="1">
						<th>Spent Liters Per 100km</th>
						<th>Spent Money Per 100km</th>
						<th>Fuel Price 1 km</th>
						<th>Distance</th>
						<th>Fuel Price</th>
						<th>Refuel Days Count</th>';

				foreach ($value as $arr) {
					$refuelSpendingshtml .= 
					'<tr><td>'. number_format((float)$arr['spentLitersPer100km'], 2) .' Liters</td>'
					.'<td>'. number_format((float)$arr['spentMoneyPer100km'], 1) .' BGN</td>'
					.'<td>'. number_format((float)$arr['fuelPrice1km'], 1) .' BGN</td>'
					.'<td>'. $arr['distance'] .' km</td>'
					.'<td>'. number_format((float)$arr['totalFuelPrice'], 2) .' BGN</td>'
					.'<td>'. $arr['refuelDaysSum'] .'</td></tr>';
				}

				$refuelSpendingshtml .= '</table>';

			} else {
				if ($key == 'spentLitersPer100km') {
					$html .= '<tr><td>Spent Liters per 100km</td><td>'. number_format((float)$value, 2) .' Liters</td></tr>';
				} elseif ($key == 'spentMoneyPer100km') {
					$html .= '<tr><td>Spent Money per 100km</td><td>'. number_format((float)$value, 1) .' BGN</td></tr>';
				} elseif ($key == 'fuelPrice1km') {
					$html .= '<tr><td>Price per 1km</td><td>'. number_format((float)$value, 1) .' BGN</td></tr>';
				} elseif ($key == 'totalFuelPrice') {
					$html .= '<tr><td>Total fuel price</td><td>'. number_format($value, 2) .' BGN</td></tr>';
				} elseif ($key == 'totalFuelLitres') {
					$html .= '<tr><td>Total consumed fuel</td><td>' . $value .' Liters</td></tr>';
				} elseif ($key == 'avgRefuelDays') {
					$html .= '<tr><td>AVG Refuel Days</td><td>'. $value .' Days</td></tr>';
				}
			}
		}		
		
		$html .= '</table>';
		$html .= $refuelSpendingshtml;

	} elseif ($lang == 'bg') {

		$refuelSpendingshtml = '';
		$html .= '<table border="1">
				 	<h3>Тотал разходи за гориво</h3>';
		foreach ($calcData as $key => $val) {

			if (is_array($val)) {
				$refuelSpendingshtml = '
				<h3>Зареждане с гориво</h3>
				<table border="1">
					<th>Разход на 100км</th>
					<th>Цена на 100км</th>
					<th>Цена на 1км</th>
					<th>Разстояние</th>
					<th>Разходи за гориво</th>
					<th>Период на зареждане</th>';

				foreach ($val as $arr) {
					$refuelSpendingshtml .= 
					'<tr><td>'. number_format((float)$arr['spentLitersPer100km'], 1) .' литри</td>'
					.'<td>'. number_format((float)$arr['spentMoneyPer100km'], 2) .' лв</td>'
					.'<td>'. number_format((float)$arr['fuelPrice1km'], 2) .' лв</td>'
					.'<td>'. $arr['distance'] .' км</td>'
					.'<td>'. number_format((float)$arr['totalFuelPrice'], 1) .' лв</td>'
					.'<td>'. $arr['refuelDaysSum'] .' дни</td></tr>';
				}

				$refuelSpendingshtml .= '</table>';
			} else {
				$html .= "<tr><td>{$translation[$key]}</td><td>{$val}$suffix[$key]</td></tr>";
			}
		}

		$html .= '</table>';
		$html .= $refuelSpendingshtml;
	} else {
		// die('Incorrect language input. Please reload the page and try again.');
	}

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

$outputData = prepareOutput($spendingData, $translation, $suffix, 'en');

saveToFile('staticHtml.php', $outputData);

echo displayData('staticHtml.php');