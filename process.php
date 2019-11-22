<?php

/**
 * Process
 * init file when processing
 * units, currency and percentages conversion
 * time and tax has their own files
 *
 * Queries will only be processed it
 * it has at least 3 characters
 */

require_once(__DIR__ . '/units.php');
require_once(__DIR__ . '/currency.php');
require_once(__DIR__ . '/percentages.php');
require_once(__DIR__ . '/px-em-rem.php');
require_once(__DIR__ . '/functions.php');

$query = clean_query(get_var($argv, 1));

if (strlen($query) < 3) {
	echo '{"items": []}';
	exit(0);
}

$lang  = get_translation();
$keywords  = get_extra_keywords();

define('LANG_STRINGS', $lang);
define('LANG_KEYWORDS', $keywords);

$result = '...';
$icon = 'icon.png';
$matches = [];
$process = false;
$value = false;
$response = [];
$should_check = strlen($query) >= 3;

if ($should_check && strpos($query, '%') !== false) {
	$data = process_percentages($query);
	if ($data) {
		$value = $data['value'];
		$process = $data['process'];
	}
}

elseif ($should_check && is_unit($query)) {
	$value = process_unit_conversion($query);
	$process = true;
}

elseif (is_pxemrem($query)) {
	$value = process_pxemrem($query);
	$process = true;
}

elseif (strlen($query) >= 3 && is_currency($query)) {
	$value = process_currency_conversion($query);
	$process = true;

	if (is_array($value)) {
		$icon = 'flags/' . $value['currency'] . '.png';
		$value = $value['data'];
	}
}

$response[] = [
	'title' => ($value ? $value : $result),
	'subtitle' => get_text('action_copy'),
	'arg' => ($value ? $value : $result),
	'valid' => ($value ? true : false),
	'icon' => [
        'path' => $icon
	]
];

if ($value && is_array($value)) {
	$response = [];
	foreach ($value as $key => $val) {
		$response[] = [
			'title' => $val,
			'subtitle' => get_text('action_copy'),
			'arg' => $key,
			'icon' => [
				'path' => $icon
			]
		];
	}
}

if ($process) {
	echo '{"items": ' . json_encode($response).' }';
	exit(0);
}

echo '{"items": []}';
