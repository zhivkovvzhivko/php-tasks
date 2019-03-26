<?php

date_default_timezone_set('Europe/Sofia');

$data = [
    ['km'=>165732, 'distance'=>487, 'liters'=>43, 'price'=>2.02, 'date'=>'2018-02-17'],
    ['km'=>165752, 'distance'=>507, 'liters'=>1.4, 'price'=>2.02, 'date'=>'2018-02-22'],
    ['km'=>165782, 'distance'=>537, 'liters'=>2.1, 'price'=>2.02, 'date'=>'2018-03-03'],
    ['km'=>165822, 'distance'=>577, 'liters'=>2.8, 'price'=>2.02, 'date'=>'2018-03-17'],
    ['km'=>165872, 'distance'=>627, 'liters'=>3.5, 'price'=>2.02, 'date'=>'2018-04-06'],
    ['km'=>165932, 'distance'=>687, 'liters'=>4.2, 'price'=>2.02, 'date'=>'2018-05-01'],
];

$translations = [
	"bg" => [
		'spendings'				=> 'Разходи за гориво',
		'summary'				=> 'Обобщени',
		'avgDistancePerRefuel' 	=> 'Средно изминато разстояние с едно зареждане',
		'avgFuelUsagePer100'	=> 'Среден разход на 100 км',
		'avgFuelPricePer100'	=> 'Средна цена на 100 км',
		'avgRefuelLiters'		=> 'Средно количество при зареждане',
		'avgFuelPrice'			=> 'Средна цена на гориво',
		'totalFuelUsage'		=> 'Всичко потребено гориво',
		'totalExpenses'			=> 'Всичко разходи',
		'avgRefuelDays'			=> 'Среден период на зареждане',
		'unit_distance'			=> 'км.', 
		'unit_liter'			=> 'л.',
		'unit_currency_0'		=> 'лева', 
		'unit_currency_1' 		=> 'лев',
		'unit_currency_*' 		=> 'лева',
		'unit_day_0'			=> 'дни', 
		'unit_day_1'			=> 'ден',
		'unit_day_*' 			=> 'дни',
		'liters'				=> 'Заредени литри',
		'distance'				=> 'Изминати',
		'km'					=> 'Километраж',
		'refuelDays'			=> 'Дни от последно зареждане',
		'fuelUsagePer100'		=> 'Разход на 100 км',
		'fuelPricePer100'		=> 'Цена на 100 км',
		'totalPrice'			=> 'Всичко цена'
	],
	"en" => [
		'spendings'				=> 'Fuel spendings',
		'summary'				=> 'Summary',
		'avgDistancePerRefuel'	=> 'Average distance per refuel',
		'avgFuelUsagePer100'	=> 'Average usage per 100 km',
		'avgFuelPricePer100'	=> 'Average price per 100 km',
		'avgRefuelLiters'		=> 'Total used liters',
		'avgFuelPrice'			=> 'Average fuel price',
		'totalFuelUsage'		=> 'Total fuel usage',
		'totalExpenses'			=> 'Total expenses',
		'avgRefuelDays'			=> 'Average refuel period',
		'unit_distance'			=> 'km', 
		'unit_liter'			=> 'l',
		'unit_currency'			=> 'BGN',
		'unit_day_0'			=> 'days', 
		'unit_day_1'			=> 'day',
		'unit_day_*' 			=> 'days',
		'liters'				=> 'Refuel liters',
		'distance'				=> 'Distance',
		'km'					=> 'Board km',
		'refuelDays'			=> 'Days from last refuel',
		'fuelUsagePer100'		=> 'Fuel usage per 100 km',
		'fuelPricePer100'		=> 'Fuel price per 100 km',
		'totalPrice'			=> 'Total price'
	],
];

function calcFuelUsage($usedLiters, $distance) {
	return $usedLiters / $distance; 
}
function calcFuelUsagePer100($usedLiters, $distance) {
	return 100 * calcFuelUsage($usedLiters, $distance);
}
function calcPrice($liters, $singlePrice) {
	return $liters * $singlePrice;
}
function calcDaysElapsed($fromDate, $toDate) {
	return round((strtotime($toDate) - strtotime($fromDate)) / 86400);
}
function calcSpendings($data) {
	$spendings = [];
	$prev = array_shift($data);
	foreach ($data as $current) {
		$fuelUsagePer100 = calcFuelUsagePer100($prev['liters'], $current['distance']);
		
		$spendings[$current['date']] = [
			'liters' 			=> $prev['liters'],
			'distance' 			=> $current['distance'],
			'km'				=> $current['km'] - $prev['km'],
			'refuelDays'		=> calcDaysElapsed($prev['date'], $current['date']),
			'fuelUsagePer100'	=> $fuelUsagePer100,
			'fuelPricePer100'	=> calcPrice($fuelUsagePer100, $prev['price']),
			'totalPrice'		=> calcPrice($prev['liters'], $prev['price']),
		];
	}
	return $spendings;
}

function calcSummary($data) {
	$prev = array_shift($data);

	$sumOfDistances = 0;
	$fuelUsagePer100 = 0;
	$fuelPricesPer100 = 0;
	$fuelPricesSum = 0;
	$totalFuelUsage = 0;
	$refuelDaysSum = 0;
	$totalExpenses = calcPrice($prev['liters'], $prev['price']);

	foreach ($data as $current) {
		$sumOfDistances 	+= $current['distance'];
		$fuelUsagePer100 	+= calcFuelUsagePer100($prev['liters'], $current['distance']);
		$fuelPricesPer100 	+= calcPrice($fuelUsagePer100, $prev['price']);
		$fuelPricesSum 		+= $current['price'];
		$totalFuelUsage 	+= $prev['liters']; 
		$totalExpenses 		+= calcPrice($current['liters'], $current['price']);
		$refuelDaysSum 		+= calcDaysElapsed($prev['date'], $current['date']);
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


function getSuffixKey($key) {
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


function getSingularOrPluralSuffix($val, $suffix) {
	if (abs($val) <= 1) {
		$s = $suffix . '_' . abs($val);
	} else {
		$s = $suffix . '_*';
	}
	return $s;
}

function prepareDecimals($data, $lang) {
	if ($lang == 'en') {
		$decimals = array_combine(array_keys($data), array_fill(0, count($data), 1));
		$decimals['avgFuelUsagePer100'] = 2;
		$decimals['fuelUsagePer100'] = 2;
	} else {
		$decimals = array_combine(array_keys($data), array_fill(0, count($data), 2));
		$decimals['avgFuelUsagePer100'] = 1;
		$decimals['fuelUsagePer100'] = 1;
	}
}

function prepareSpendingsHTMLOutput($spendingsData, $translation) {
	$output = "<table border=\"1\">\n"
			. "<th>\n"
			. "\t<td>{$translation['date']}</td>\n";

	$firstSpendings = current($spendingsData);
	$decimals = prepareDecimals($firstSpendings, $translation);
	foreach($firstSpendings as $key => $val) {
		$label = isset($translation[$key])
					? $translation[$key]
					: '';
		$output .= "\t<td>{label}</td>\n";
	}
	$output .= "</th>\n";
	foreach($spendingsData as $date => $spendings) {
		$output .= "<tr>\n";
		
		$dateStr = date('Y-m-d', strtotime($date));
		$output .= "\t<td>{$dateStr}</td>\n";
		
		foreach($spendings as $key => $val) {
			$value = number_format($val, 2);
			$output .= "\t<td>{$value}</td>\n";
		}
		$output .= "</tr>\n";
	}
	$output .= "</table>\n";
	return $output;	
}

function prepareSummaryHTMLOutput($summaryData, $translation) {
	$decimals = prepareDecimals($summaryData, $translation);
	
	$output = "<table border=\"1\">\n";
	foreach($summaryData as $key => $val) {
		$suffix_key = getSuffixKey($key);
		$suffix = '';
		if (isset($translation[$suffix_key])) {
			$suffix = $translation[$suffix_key];
		} else {
			$sk = getSingularOrPluralSuffix($val, $suffix_key);
			if (isset($translation[$sk])) {
				$suffix = $translation[$sk];
			}
		}				

		$label 		= isset($translation[$key]) 
						? $translation[$key] 
						: '';

		$fmt_val 	= number_format($val, $decimals[$key]);
		$value 		= $fmt_val . ' ' . $suffix;

		$output .= "<tr>\n"
				.  "\t<td>{$label}</td>\n"
				.  "\t<td>{$value}</td>\n"
				. "</tr>\n";
	}
	$output .= "</table>\n";
	return $output;
}

function prepareHTMLOutput($spendingsData, $summaryData, $translation) {
	$label = isset($translation['spendings'])
				? $translation['spendings']
				: '';
	$output = "<h3>{$label}</h3>\n";
	$output .= prepareSpendingsHTMLOutput($spendingsData, $translation);
	
	$output .= "<br/><br/>\n";
	
	$label = isset($translation['summary'])
				? $translation['summary']
				: '';
	$output = "<h3>{$label}</h3>\n";
	$output .= prepareSummaryHTMLOutput($summaryData, $translation);
	
	return $output;
}

function saveToFile($filename, $strData) {
	file_put_contents($filename, $strData);
}

function displayData($strData) {
	echo $strData;
}

$calcData = calcSummary($data);
$outputData = [];
foreach(['bg', 'en'] as $lang) {
	$outputData[$lang] = prepareHTMLOutput($spendingsData, $summaryData, $translations[$lang]);
}
saveToFile('en.html', $outputData['en']);
displayData($outputData['bg']);
displayData($outputData['en']);