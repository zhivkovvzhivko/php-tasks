<?php

	$data = array(
        array('km'=>165732, 'distance'=>487, 'liters'=>43, 'price'=>2.02, 'date'=>'2018-02-17'),
        array('km'=>165752, 'distance'=>507, 'liters'=>59.4, 'price'=>2.02, 'date'=>'2018-02-27'),
        array('km'=>165882, 'distance'=>537, 'liters'=>89.1, 'price'=>2.02, 'date'=>'2018-02-29'),
        array('km'=>165903, 'distance'=>558, 'liters'=>65, 'price'=>2.02, 'date'=>'2018-03-15'),
        array('km'=>165946, 'distance'=>601, 'liters'=>76, 'price'=>2.02, 'date'=>'2018-03-24'),
        array('km'=>165988, 'distance'=>643, 'liters'=>50, 'price'=>2.02, 'date'=>'2018-04-12'),
	);

	function fuelCostPerHundredKilometers($liters_fuel, $old_distance)
	{
		return 100 * $liters_fuel / $old_distance;
	}

	function fuelPriceForHundredKilometers($fuelCostPerHundredKilometers, $LitrePrice)
	{
		return $fuelCostPerHundredKilometers * $LitrePrice;
	}

	print '<table border="1">
			<th>Среден разход на 100км</th>
			<th>Цена на 1 км</th>
			<th>Цена на 100 км</th>
			<th>Период на зареждане</th>
			<th>Тотал изразходвано гориво</th>
			<th>Тотал разходи за гориво</th>';

	$total_liters = 0;
	$totalFuelCost = 0;
	for ($i=0; $i < count($data); $i++) {
		if ($i+1 < count($data)) {
			$distance = $data[$i+1]['distance'];
			$liters = $data[$i+1]['liters'];
			$total_liters += $data[$i+1]['km'] - $data[$i]['km'];
			$totalFuelCost += ($data[$i+1]['km'] - $data[$i]['km']) * $data[$i]['price'];

			$fuelCostPerHundredKilometers = fuelCostPerHundredKilometers($liters, $data[$i]['distance']);
			$fuelPriceForHundredKilometers = fuelPriceForHundredKilometers($fuelCostPerHundredKilometers, $data[$i+1]['liters']);
			$pricePerKm = $fuelPriceForHundredKilometers / 100;

			$datedefferenceInSeconds = strtotime($data[$i+1]['date']) - strtotime($data[$i]['date']);
			$loadingperiod = $datedefferenceInSeconds / 86400;

			print '<tr>';
			print '<td>'. $fuelCostPerHundredKilometers .'</td>';
			print '<td>'. $pricePerKm .'</td>';
			print '<td>'. $fuelPriceForHundredKilometers .'</td>';
			print '<td>'. round($loadingperiod) .'</td>';
		}
	}
	
	print '<td rowspan="5">'. $total_liters .' литри</td>';
	print '<td rowspan="5">'. $totalFuelCost .'</td>';
	print '</tr></table>';
