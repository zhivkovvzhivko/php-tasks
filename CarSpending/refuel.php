<?php

$data = array(
    array('km'=>165732, 'distance'=>487, 'liters'=>43, 'price'=>2.02, 'date'=>'2018-02-17'),
    array('km'=>165752, 'distance'=>507, 'liters'=>1.4, 'price'=>2.02, 'date'=>'2018-02-22'),
    array('km'=>165782, 'distance'=>537, 'liters'=>2.1, 'price'=>2.02, 'date'=>'2018-03-03'),
    array('km'=>165822, 'distance'=>577, 'liters'=>2.8, 'price'=>2.02, 'date'=>'2018-03-17'),
    array('km'=>165872, 'distance'=>627, 'liters'=>3.5, 'price'=>2.02, 'date'=>'2018-04-06'),
    array('km'=>165932, 'distance'=>687, 'liters'=>4.2, 'price'=>2.02, 'date'=>'2018-05-01'),
);

function fuelCost100km($liters_fuel, $distance)
{
	return 100 * $liters_fuel / $distance; 
}

function fuelPrice100km($fuelCost100km, $LitrePrice)
{
	return $fuelCost100km * $LitrePrice;
}

function calcCarSpanding($data) {

	$totalFuelCost = 0;
	$totalFuelLitres = 0;
	$datesDefferenceCount = 0;
	$loadingAVGPeriod = 0;
	$prev = array_shift($data);

	foreach ($data as $value) {
		$distance = $value['distance'] - $prev['distance'];
		$totalFuelLitres += $value['liters'];

		$liters = $value['liters'];
		$fuelCost100km = number_format(fuelCost100km($value['liters'], $distance), 1);
		$fuelPrice100km = number_format(fuelPrice100km($fuelCost100km, $prev['price']), 2);
		$fuelPrice1km  = number_format($fuelPrice100km / 100, 2);
		$totalFuelCost += $distance * $fuelPrice1km;

		$dateDefferenceInSeconds = strtotime($prev['date']) - strtotime($value['date']);
		$datesDefferenceCount += round(($dateDefferenceInSeconds / 86400) * -1);

		$prev = $value;
	}

		$loadingAVGPeriod = round($datesDefferenceCount / count($data));

		$spendingData['Разход на 100км'] = $fuelCost100km . ' литра';
		$spendingData['Цена на 100км'] = $fuelPrice100km . ' лева';
		$spendingData['Цена на 1км'] = $fuelPrice1km . ' лева';
		$spendingData['Тотал разходи за гориво'] = $totalFuelCost . ' лева';
		$spendingData['Тотал изразходвано гориво'] = $totalFuelLitres . ' литри';
		$spendingData['Среден период на зареждане'] = $loadingAVGPeriod . ' дни';

		// return $spendingData;
		printSpendingData($spendingData);
}

echo calcCarSpanding($data);

function printSpendingData($data) {

	$html = '<table border="1">';
	foreach ($data as $key => $value) {
		$html .= '<tr><td>' . $key . '</td><td>' . $value .'</td></tr>';
	}
	$html .= '</table>';

	echo $html;
}
