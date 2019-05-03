<?php

$allCards = [
	'7' => ['Hearts', 'Spades', 'Diamonds', 'Clubs'],
	'8' => ['Hearts', 'Spades', 'Diamonds', 'Clubs'],
	'9' => ['Hearts', 'Spades', 'Diamonds', 'Clubs'],
	'10' => ['Hearts', 'Spades', 'Diamonds', 'Clubs'],
	'J' => ['Hearts', 'Spades', 'Diamonds', 'Clubs'],
	'Q' => ['Hearts', 'Spades', 'Diamonds', 'Clubs'],
	'K' => ['Hearts', 'Spades', 'Diamonds', 'Clubs'],
	'A' => ['Hearts', 'Spades', 'Diamonds', 'Clubs']
];

$players = ['player1', 'player2', 'player3', 'player4'];
// $dealtCards = [];

// $card = array_rand($allCards, 1); //echo 'cardName: ' . $card . ' <<<';
// $player = array_rand($players, 1); //echo 'player: ' . $player . ' <<<';

// // echo '<pre/>'; print_r(array('type'=>$allCards[$card])) . "<br/><br/><br/>";

// if (!isset($dealtCards[$players[$player]])) {

// 	$dealtCards[$players[$player]] = [];
// 	if (!isset($dealtCards[$players[$player]][$card])) {
// 		$dealtCards[$players[$player]][$card] = $allCards[$card][array_rand($allCards[$card])];
// 	}

// 	// echo '<pre/>'; print_r($dealtCards); die('tuk sam');
// } else {
// 	// echo '<pre/>'; print_r(array($player => $players[$player])); die('v elsaa');
// }

// echo '<pre/>'; print_r(
// 	array('cardName: '=>$card, 'type'=>$allCards[$card][array_rand($allCards[$card])])
// ); die(' kiyovete19');

// echo '<pre/>'; print_r(
// 	$dealtCards
// ); die(' kiyovete19');

$keys = array_keys($allCards);
$dealtCards = [];
for ($len=count($keys), $i=0; $i < $len; $i++) { 

	$card = array_rand($allCards, 1); //echo 'cardName: ' . $card . ' <<<';
	$player = array_rand($players, 1); //echo 'player: ' . $player . ' <<<';

	// echo '<pre/>'; print_r(array('type'=>$allCards[$card])) . "<br/><br/><br/>";

	if (!isset($dealtCards[$players[$player]])) {
		$dealtCards[$players[$player]] = [];

		if (!isset($dealtCards[$players[$player]][$card])) {
			$dealtCards[$players[$player]][$card] = $allCards[$card][array_rand($allCards[$card])];
		}
	} else {

	}
}

echo '<pre/>'; print_r($dealtCards); die('tuk sam');
