<?php

/**
 * List
 * Return a list of all the available
 * units, currencies, etc.
 * You can search by unit name, country
 * name, etc.
 */

require_once __DIR__ . '/workflow/lib/functions.php';
require_once __DIR__ . '/workflow/calculateanything.php';

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
		'title' => 'List Available Cryptocurrencies',
		'subtitle' => 'Display the list of cryptocurrencies',
		'valid' => true,
		'arg' => 'cryptocurrency',
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

$calculate = new Workflow\CalculateAnything();
$items = ($list == 'units' ? $calculate->getCalculator('units')->listAvailable() : []);
$items = ($list == 'currency' ? $calculate->getCalculator('currency')->listAvailable() : $items);
$items = ($list == 'cryptocurrency' ? $calculate->getCalculator('cryptocurrency')->listAvailable() : $items);

foreach ($items as $item) {
	$response[] = $item;
}

echo '{"items": ' . json_encode($response) . ' }';
