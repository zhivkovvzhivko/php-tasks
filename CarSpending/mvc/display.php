<?php

class Display
{
	
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
				$suffix = suffixForKey($key, $val, $translation);
				$value 	= $fmt_val . ' ' . $suffix;
				$output .= "\t<td align=\"right\">{$value}</td>\n";
			}
			$output .= "</tr>\n";
		}
		$output .= "</table>\n";
		return $output;	
	}

	public function prepareSummaryHTMLOutput($summaryData, $translation) {
		$decimals = prepareDecimals($summaryData, $translation);
		
		$output = "<table border=\"1\">\n";
		foreach($summaryData as $key => $val) {
			$label 		= isset($translation[$key]) 
							? $translation[$key] 
							: '';

			$fmt_val 	= number_format($val, $decimals[$key], '.', '');
			$suffix 	= suffixForKey($key, $val, $translation);
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
		$output .= prepareSpendingsHTMLOutput($spendingsData, $translation);
		
		$output .= "<br/><br/>\n";
		
		$label = isset($translation['summary'])
					? $translation['summary']
					: '';
		$output .= "<h3>{$label}</h3>\n";
		$output .= prepareSummaryHTMLOutput($summaryData, $translation);
		
		return $output;
	}

	public function displayData($strData) {
		echo $strData;
	}
}