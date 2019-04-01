<?php

namespace View;

class Display
{

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
	
	public function prepareSpendingsHTMLOutput($spendingsData, $translation) {
		$output = "<table border=\"1\">\n"
				. "<tr>\n"
				. "\t<th>{$translation['date']}</th>\n";

		$firstSpendings = current($spendingsData);
		$decimals = $this->prepareDecimals($firstSpendings, $translation);
		foreach($firstSpendings as $key => $val) {
			$label = isset($translation[$key])
						? $translation[$key]
						: '';
			$output .= "\t<th>{$label}</th>\n";
		}
		$output .= "</tr>\n";

		foreach($spendingsData as $date => $spendings) {
			$output .= "<tr>\n";
			
			$dateStr = date('Y-m-d', strtotime($date));
			$output .= "\t<td>{$dateStr}</td>\n";

			foreach($spendings as $key => $val) {
				$fmt_val= number_format($val, $decimals[$key], '.', '');
				$suffix = $this->suffixForKey($key, $val, $translation);
				$value 	= $fmt_val . ' ' . $suffix;
				$output .= "\t<td align=\"right\">{$value}</td>\n";
			}
			$output .= "</tr>\n";
		}
		$output .= "</table>\n";
		return $output;	
	}

	public function prepareSummaryHTMLOutput($summaryData, $translation) {
		$decimals = $this->prepareDecimals($summaryData, $translation);
		
		$output = "<table border=\"1\">\n";
		foreach($summaryData as $key => $val) {
			$label 		= isset($translation[$key]) 
							? $translation[$key] 
							: '';

			$fmt_val 	= number_format($val, $decimals[$key], '.', '');
			$suffix 	= $this->suffixForKey($key, $val, $translation);
			$value 		= $fmt_val . ' ' . $suffix;

			$output .= "<tr>\n"
					.  "\t<td>{$label}</td>\n"
					.  "\t<td align=\"right\">{$value}</td>\n"
					. "</tr>\n";
		}
		$output .= "</table>\n";
		return $output;
	}

	public function prepareHTMLOutput($spendingsData, $summaryData, $translation) {
		$label = isset($translation['spendings'])
					? $translation['spendings']
					: '';
		$output = "<h3>{$label}</h3>\n";
		$output .= $this->prepareSpendingsHTMLOutput($spendingsData, $translation);
		
		$output .= "<br/><br/>\n";
		
		$label = isset($translation['summary'])
					? $translation['summary']
					: '';
		$output .= "<h3>{$label}</h3>\n";
		$output .= $this->prepareSummaryHTMLOutput($summaryData, $translation);
		
		return $output;
	}

	public function displayData($strData) {
		echo $strData;
	}
}