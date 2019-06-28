<?php

function get_card_index($card_index=34) { // -> idx=34, return 6
	return ($card_index-2) % 13;
}
function get_card_color_index($card_index = 34) { // -> card_index=34, color_index=2
	return intval(($card_index - 2) / 13);
}
// return valid belote cards 7 .. A
function get_card_value_by_index($card_index=34) { // -> card_index=34, color_index=2, return 8♦
	$card_map = [9=>'J', 10=>'Q', 11=>'K', 12=>'A'];

	$card_idx = ($card_index - 2) % 13; // 0..12

	return isset($card_map[$card_idx]) 
				? $card_map[$card_idx] 
				: $card_idx + 2;
}
function get_card_color_by_index($card_index=24) { // '♠', '♥', '♦', '♣'
	// $card_suit = ['spades', 'hearts', 'diamonds', 'clubs'];
	$card_suit = ['♠', '♥', '♦', '♣'];
	// $card_color_idx = floor($card_index / 13);
	$card_color_idx = floor(($card_index-2) / 13);
	return isset($card_suit[$card_color_idx]) 
				? $card_suit[$card_color_idx]
				: '';
}
function get_card($card_index=51) { // 7, 9, 10, 11, 12, 13, 14, 20, 21
	$card = [
		get_card_value_by_index($card_index), // 8
		get_card_color_by_index($card_index) // '♠', '♥', '♦', '♣'
	];

	return implode('', $card);
}
function is_belote_card($card=32) {
	$card_index = get_card_index($card);
	return (bool) ($card_index >= 5);
}
function has_ordinals($cards_list, $how_many) {

	sort($cards_list);
	$ordinals = [];
	$prev_card_index = 0;
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
function has_slots($cards_list) { // ideqta e da proverqva za 4 ednakvi karti bez znachenie ot koq boq-> 8 8 8 8
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

		if (!isset($cards_list[$pos+1])) {
			continue;
		}

		$current_card_index = get_card_index($card_index);
		$next_card_index = get_card_index($cards_list[$pos+1]);
		if ($current_card_index==10 && $next_card_index==11) {
			$belote[] = get_card_color_index($card_index); // has belote from specific color
		}
	}

	if (count($belote)) {
		sort($belote); // sort possible belotes (♠=0, ♥=1, ♦=2, ♣=3)
		$higher_weight_belote = array_shift($belote); // get first possible as a higher_weight_belote
		return [$higher_weight_belote*13 + 12, $higher_weight_belote*13 + 13]; // index 10 vs 11 (0..12)
		// 10 -> Q, 11 -> K
	}
	return false;
}

// Pokazva kartite na igrachite
function prepareOutput($players) {
	$html = "<table border=\"1\">\n";
	foreach($players as $player => $cards) {
		sort($cards);
		$cards_str = array_map('get_card', $cards);
		$player = $player+1;
		$html .= "<tr><td colspan=\"3\">Player {$player}:</td></tr>\n";

		$html .='<tr><td>'. implode(', ', $cards_str) ."</td>";

		// pokazva ordinals: terca, kvarta, kvinta
		$announcement = [];
		foreach([3=>'terca', 4=>'kvarta', 5=>'kvinta'] as $how_many => $name) {
			if (($from_cards = has_ordinals($cards, $how_many)) !== false) {
				$announcement[$name] = $from_cards;
			}
		}
		if (!empty($announcement)) {
			$name = key(array_reverse($announcement));
			$from_cards_str = array_map('get_card', max($announcement));
			$html .= "<td>- $name: ". implode(', ', $from_cards_str) ."</td>\n";
		}
		// pokazva 4 ednakvi karti ot razlichni boi
		if (($slot_cards = has_slots($cards)) !== false) {
			$slot_cards_str = array_map('get_card', $slot_cards);
		}
		// pokazva belotite
		if (($belote_cards = has_belote($cards)) !== false) {
			$belote_cards_str = array_map('get_card', $belote_cards);
			$html .= "<td>- belote: ". implode(', ', $belote_cards_str) ."</td>\n";
		}
	}
	return $html .= "</tr></table>";
}

function give_cards_to_players($belote_cards) {
	$deal_stages = [3, 2, 3];
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
	return $players;
}

function displayOutput($data) {
	echo $data;
}
// Zadavane na teste
// $all_cards = range(0,51);
$all_cards = range(2,53);
// Wzemane na karti za belot (7, 9, 10, 11, 12, 13, 14, 20, 21)
$belote_cards = array_filter($all_cards, 'is_belote_card'); // works
// Razmesvane
shuffle($belote_cards);
shuffle($belote_cards);
shuffle($belote_cards);

$players = give_cards_to_players($belote_cards);
$data = prepareOutput($players);
echo displayOutput($data);
