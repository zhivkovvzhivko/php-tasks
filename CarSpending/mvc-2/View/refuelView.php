<?php

namespace View;

class RefuelView implements ViewInterface
{

	public function displayData($strData) {

		foreach ($strData as $strData) {
			echo $strData;
		}
	}

	public function render() {
		include('View/refuelView.php');
	}
}
