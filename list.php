<?php

/**
 * List
 * Return a list of all the available
 * units, currencies, etc.
 * You can search by unit name, country
 * name, etc.
 */

require_once(__DIR__ . '/units.php');
require_once(__DIR__ . '/currency.php');

$list = getenv('list');
$response = [];

if (empty($list)) {
	$response[] = [
		'title' => 'List Available Currencies',
		'subtitle' => 'Display the list of currencies',
		'valid' => true,
		'arg' => 'currency',
	];
	$response[] = [
		'title' => 'List Available Units',
		'subtitle' => 'Display the list of units',
		'valid' => true,
		'arg' => 'units',
	];
	echo '{"items": ' . json_encode($response) . ' }';
	exit(0);
}

$items = ($list == 'units' ? get_units_list() : []);
$items = ($list == 'currency' ? get_currencies_list() : $items);

foreach ($items as $item) {
	$response[] = $item;
}

echo '{"items": ' . json_encode($response) . ' }';
