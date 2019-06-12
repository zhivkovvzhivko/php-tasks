<?php

function get_card_value_by_index($card_value=34) { // -> card_index=34, color_index=2, return 8
	return (($card_value - 2) % 13) + 2;
}

function get_card_color_by_index($card_value=34) {
	return intval(($card_value - 2) / 13);
}

function get_card_text($card_value=34) { //  -> idx=34, return ‘8 diamonds’
	$card_value = get_card_value_by_index($card_value);
	$card_color_index = get_card_color_by_index($card_value);

	$card_color_text = '';
	switch ($card_color_index) {
		case 0:
			$card_color_text = 'spades';
			break;
		case 1:
			$card_color_text = 'clubs';
			break;
		case 2:
			$card_color_text = 'diamonds';
			break;
		case 3:
			$card_color_text = 'clubs';
			break;
	}

	return $card_value .' '. $card_color_text;
}

function get_card_index($card_value=34, $color_index=null) { // -> card_value=8, color_index=2, return 34
	return ($card_value - 2) % 13;
}

function get_valid_belote_cards($deck){
	$belote_cards = [];
	foreach ($deck as $card) {
		$card_index = get_card_index($card);
		if ($card_index >= 5) { // Checks if the card is valid belote card at least value 7
			array_push($belote_cards, $card);
		}
	}

	// shuffle cards/elements in array 3 times
	shuffle($belote_cards);
	shuffle($belote_cards);
	shuffle($belote_cards);
	return $belote_cards;
}

// Give per 3, 2 and 3 cards to every player
function give_cards_to_players($players, $belote_cards) {
	
	$len = 3;
	for ($i=0; $i < $len; $i++) { 
		foreach ($players as $player => $playerCards) {
			$players[$player][] = array_shift($belote_cards);
		}
	}
	$len = 2;
	for ($i=0; $i < $len; $i++) { 
		foreach ($players as $player => $playerCards) {
			$players[$player][] = array_shift($belote_cards);
		}
	}
	$len = 3;
	for ($i=0; $i < $len; $i++) { 
		foreach ($players as $player => $playerCards) {
			$players[$player][] = array_shift($belote_cards);
		}
	}

// echo '<pre>'; print_r($players); die(' 75 ');
	return check_player_announcement($players);
}


function check_player_announcement($player_cards) {
	$checked_cards = [];

	// sort players cards
	foreach ($player_cards as $player => $playerCards) {
		$cards = $player_cards[$player];
		sort($cards);

		$ordinal = 0;
		$prev = array_shift($cards);
		foreach ($cards as $card_index) { // checks players announcement

			if ($card_index == $prev+1) {
				$ordinal++;
			} else {
				$ordinal = 0;
			}		
		}
		// checks announcment
		if ($ordinal == 3) {
			$announcement = 'Tierce';
		} elseif ($ordinal = 4) {
			$announcement = 'Fifty';
		} elseif ($ordinal = 5) {
			$announcement = 'Hundred';
		} elseif (false) {
			// check for belot Q and K
			$announcement = 'Belote';
		}
		$checked_cards[$player]['cards'] = $cards;
		$checked_cards[$player]['announcement'] = $announcement ?? '';
	}
	return $checked_cards;
}

function prepareOutput($player_cards){
	$output = "<table border=\"1\">";
	$output .= "<tr><th>Player name</th><th>Player cards</th><th>Player announcement</th></tr>\n";

	foreach ($player_cards as $player => $cards) {
		$output .= "<tr><td>$player</td><td>";

		foreach ($cards as $v) {
			if (is_array($v)) {
				foreach ($v as $card) {
					$output .= $card . ', ';
				}
			} else {
				$output .= "</td><td>$v</td>";
				$output .= "<tr>\n";
			}
		}
	}
	
	$output .= '</table>';
	return $output;
}

function displayOutput($data) {
	echo $data;
}

function throw_cards($player_cards, $start_from_player=2) {
	echo '<pre>'; print_r($player_cards); die(' in throw');
}

$deck = range(2, 53);
$cards_numbers = [7, 8, 9, 10, 11, 12, 13, 14];
$type = ['spades', 'hearts', 'diamonds', 'clubs'];

$players = [
	'player1' => [], 
	'player2' => [], 
	'player3' => [], 
	'player4' => []
];

$belote_cards = get_valid_belote_cards($deck);
$player_cards = give_cards_to_players($players, $belote_cards);
$data = prepareOutput($player_cards);
displayOutput($data);

// echo '<pre/>'; print_r($player_cards); echo '<br/><br/>'; //die(' tuk ');
// echo 'get_card_value_by_index: ' . get_card_value_by_index() . '<br/>';
// echo 'get_card_index: ' . get_card_index() . '<br/>';
// echo 'get_card_color_by_index: ' . get_card_color_by_index() . '<br/>';
// echo 'get_card_text: ' . get_card_text() . '<br/>';