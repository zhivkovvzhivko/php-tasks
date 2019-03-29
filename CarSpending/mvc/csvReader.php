<?php

class CsvReader
{

	public function readCsv($strFileName) {
		$data = fopen($strFileName, 'r');

		$refuelData = [];
		while (($line = fgetcsv($data, 1000, ' ')) !== false) {
			$lineParts = [];

			// splits row and takes every part from it
			$lineParts = explode(',', $line[0]);
			$array_keys = [];
			$array_values = [];
			foreach ($lineParts as $value) {
				list($key, $value) = array_map('static::trimElements', explode('=>', $value));
				$array_keys[] = $key;
				$array_values[] = $value;
			}

			array_push($refuelData, array_combine($array_keys, $array_values));
		}

		return $refuelData;
	}

	public static function trimElements($singleElement) {
	    return trim($singleElement);
	}

	public function calcFuelUsage($usedLiters, $distance) {
		return $usedLiters / $distance; 
	}

	public function calcFuelUsagePer100($usedLiters, $distance) {
		return 100 * $this->calcFuelUsage($usedLiters, $distance);
	}

	public function calcPrice($liters, $singlePrice) {
		return $liters * $singlePrice;
	}

	public function calcDaysElapsed($fromDate, $toDate) {
		return round((strtotime($toDate) - strtotime($fromDate)) / 86400);
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function calcSpendings($data) {
		$spendings = [];
		$prev = array_shift($data);
		// print '<pre/>'; print_r($data); die(' spendings');
		foreach ($data as $current) {
			$fuelUsagePer100 = $this->calcFuelUsagePer100($prev['liters'], $current['distance']);
			
			$spendings[$current['date']] = [
				'liters' 			=> $prev['liters'],
				'distance' 			=> $current['distance'],
				'km'				=> $current['km'],
				'refuelDays'		=> $this->calcDaysElapsed($prev['date'], $current['date']),
				'fuelUsagePer100'	=> $fuelUsagePer100,
				'fuelPricePer100'	=> $this->calcPrice($fuelUsagePer100, $prev['price']),
				'totalPrice'		=> $this->calcPrice($prev['liters'], $prev['price']),
			];
			$prev = $current;
		}
		return $spendings;
	}

	public function calcSummary($data) {
		$prev = array_shift($data);

		$sumOfDistances = 0;
		$fuelUsagePer100 = 0;
		$fuelPricesPer100 = 0;
		$fuelPricesSum = 0;
		$totalFuelUsage = 0;
		$refuelDaysSum = 0;
		$totalExpenses = $this->calcPrice($prev['liters'], $prev['price']);

		foreach ($data as $current) {
			$sumOfDistances 	+= $current['distance'];
			$fuelUsagePer100 	+= $this->calcFuelUsagePer100($prev['liters'], $current['distance']);
			$fuelPricesPer100 	+= $this->calcPrice($fuelUsagePer100, $prev['price']);
			$fuelPricesSum 		+= $prev['price'];
			$totalFuelUsage 	+= $prev['liters']; 
			$totalExpenses 		+= $this->calcPrice($current['liters'], $current['price']);
			$refuelDaysSum 		+= $this->calcDaysElapsed($prev['date'], $current['date']);
			$prev = $current;
		}
		return [
			'avgDistancePerRefuel'	=> $sumOfDistances / count($data),
			'avgFuelUsagePer100'	=> $fuelUsagePer100 / count($data),
			'avgFuelPricePer100'	=> $fuelPricesPer100 / count($data),
			'avgRefuelLiters'		=> $totalFuelUsage / count($data),
			'avgFuelPrice'			=> $fuelPricesSum / count($data),
			'totalFuelUsage'		=> $totalFuelUsage,
			'totalExpenses'			=> $totalExpenses,
			'avgRefuelDays'			=> $refuelDaysSum / count($data)
		];
	}


	public function getSuffixKey($key) {
		$suffix_key = '';
		if (in_array($key, ['avgFuelUsagePer100', 'avgRefuelLiters', 'totalFuelUsage', 'liters', 'fuelUsagePer100'])) {
			$suffix_key = 'unit_liter';
		}
		if (in_array($key, ['avgFuelPricePer100', 'avgFuelPrice', 'totalExpenses', 'totalPrice', 'fuelPricePer100'])) {
			$suffix_key = 'unit_currency';
		}
		if (in_array($key, ['avgRefuelDays', 'refuelDays'])) {
			$suffix_key = 'unit_day';
		}
		if (in_array($key, ['avgDistancePerRefuel', 'distance', 'km'])) {
			$suffix_key = 'unit_distance';
		}
		return $suffix_key;
	}

	public function getSingularOrPluralSuffix($val, $suffix) {
		if ($val == 0 || abs($val) == 1) {
			$s = $suffix . '_' . abs($val);
		} else {
			$s = $suffix . '_*';
		}
		return $s;
	}

	public function prepareDecimals($data, $lang) {
		if ($lang == 'en') {
			$decimals = array_combine(array_keys($data), array_fill(0, count($data), 1));
			$decimals['avgFuelUsagePer100'] = 2;
			$decimals['fuelUsagePer100'] = 2;
		} else {
			$decimals = array_combine(array_keys($data), array_fill(0, count($data), 2));
			$decimals['avgFuelUsagePer100'] = 1;
			$decimals['fuelUsagePer100'] = 1;
		}
		$decimals['refuelDays'] = 0;
		$decimals['avgRefuelDays'] = 0;

		return $decimals;
	}

	public function suffixForKey($key, $val, $translation) {
		$suffix_key = $this->getSuffixKey($key);
		$suffix = '';
		if (isset($translation[$suffix_key])) {
			$suffix = $translation[$suffix_key];
		} else {
			$sk = $this->getSingularOrPluralSuffix($val, $suffix_key);
			if (isset($translation[$sk])) {
				$suffix = $translation[$sk];
			}
		}
		return $suffix;
	}


	public function saveToFile($filename, $strData) {
		file_put_contents($filename, $strData);
	}

	// $spendingsData = calcSpendings($data);
	// $summaryData = calcSummary($data);
	// $outputData = [];
	// foreach(['bg', 'en'] as $lang) {
	// 	$outputData[$lang] = prepareHTMLOutput($spendingsData, $summaryData, $translations[$lang]);
	// }
}
