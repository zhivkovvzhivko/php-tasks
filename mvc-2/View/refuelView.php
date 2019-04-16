<?php

namespace View;

class RefuelView implements ViewInterface
{

	public function displayData($strData) {
		echo $strData;
	}

	public function render() {
		include('View/refuelView.php');
	}
}