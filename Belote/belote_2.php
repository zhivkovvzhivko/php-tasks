<?php

function get_card_index($card_index) {
	return $card_index % 13;
}
function get_card_color_index($card_index) {
	return floor($card_index / 13);
}
function get_card_value_by_index($card_index) {
	$card_map = [9=>'J', 10=>'Q', 11=>'K', 12=>'A'];
	$card_idx = $card_index % 13;
	return isset($card_map[$card_idx]) 
				? $card_map[$card_idx] 
				: $card_idx + 2;
}
function get_card_color_by_index($card_index) {
	// $card_suit = ['spades', 'hearts', 'diamonds', 'clubs'];
	$card_suit = ['♠', '♥', '♦', '♣'];
	$card_color_idx = floor($card_index / 13);
	return isset($card_suit[$card_color_idx]) 
				? $card_suit[$card_color_idx]
				: '';
}
function get_card($card_index) {
	$card = [
		get_card_value_by_index($card_index),
		get_card_color_by_index($card_index)
	];
	return implode('', $card);
}
function is_belote_card($card_index) {
	return (bool) ($card_index % 13 >= 5);
}
function has_ordinals($cards_list, $how_many) {
	sort($cards_list);
	$ordinals = [];
	foreach($cards_list as $card_index) {
		if ($card_index == $prev_card_index + 1) {
			$cnt++;
			if ($cnt == $how_many) {
				$ordinals[] = $card_index - $how_many + 1;
			}
		} else {
			$cnt = 1;
		}
		$prev_card_index = $card_index;
	}
	if (count($ordinals)) {
		sort($ordinals);
		$idx = array_pop($ordinals);
		return range($idx, $idx + $how_many - 1);
	}
	return false;
}
function has_slots($cards_list) {
	$cards_count = array_count_values(array_map('get_card_index', $cards_list));
	$slots = [];
	foreach($cards_count as $card_index => $number) {
		if ($number == 4) {
			$slots[] = $card_index; // has slots with starting index = $card_index
		}
	}
	if (count($slots) > 0) { // could have as maximum of two sequences of slots
		$card_index = max($slots); // try to find maximum weight slots
		return [$card_index, $card_index+13, $card_index+26, $card_index+39];
	}
	return false;
}
function has_belote($cards_list) {
	sort($cards_list);
	$belote = [];
	foreach($cards_list as $pos => $card_index) {
		if ($card_index%13==10 && $cards_list[$pos+1]%13==11) {
			$belote[] = get_card_color_index($card_index); // has belote from specific color
		}
	}
	if (count($belote)) {
		sort($belote); // sort possible belotes (♠=0, ♥=1, ♦=2, ♣=3)
		$higher_weight_belote = array_shift($belote); // get first possible as a higher_weight_belote
		return [$higher_weight_belote*13 + 10, $higher_weight_belote*13 + 11];
		// 10 -> Q, 11 -> K
	}
	return false;
}

// Zadavane na teste
$all_cards = range(0,51);
// Wzemane na karti za belot
$belote_cards = array_filter($all_cards, 'is_belote_card');
// Razmesvane
shuffle($belote_cards);
shuffle($belote_cards);
shuffle($belote_cards);
// Razdavane
$deal_stages = array(3, 2, 3);
$max_cards_per_player = array_sum($deal_stages); // 8
$belote_cards_number = count($belote_cards); // 32
$players_number = $belote_cards_number / $max_cards_per_player; // 4
$players = array_fill(0, $players_number, []); // [ [], [], [], [] ]
foreach($deal_stages as $stage_cards) {
	foreach($players as $player => $cards) {
		for ($c=0; $c<$stage_cards; $c++) {
			array_push($players[$player], array_shift($belote_cards));
		}
	}
}
// Pokazva kartite na igrachite
echo '<pre>', "\n";
foreach($players as $player => $cards) {
	sort($cards);
	$cards_str = array_map('get_card', $cards);
	echo "Player ", $player+1, ": ", implode(', ', $cards_str), "\n";
	// pokazva ordinals: terca, kvarta, kvinta
	foreach(array(3=>'terca', 4=>'kvarta', 5=>'kvinta') as $how_many => $name) {
		if (($from_cards = has_ordinals($cards, $how_many)) !== false) {
			$from_cards_str = array_map('get_card', $from_cards);
			echo "  - ", $name, ": ", implode(', ', $from_cards_str), "\n";
		}
	}
	// pokazva 4 ednakvi karti ot razlichni boi
	if (($slot_cards = has_slots($cards)) !== false) {
		$slot_cards_str = array_map('get_card', $slot_cards);
		echo "  - slots: ", implode(', ', $slot_cards_str), "\n";
	}
	// pokazva belotite
	if (($belote_cards = has_belote($cards)) !== false) {
		$belote_cards_str = array_map('get_card', $belote_cards);
		echo "  - belote: ", implode(', ', $belote_cards_str), "\n";
	}
}