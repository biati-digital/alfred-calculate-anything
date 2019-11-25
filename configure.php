<?php

/**
 * Configure
 * Return a list of all the available
 * Options to configure the workflow
 */

require_once(__DIR__ . '/functions.php');

$param = getenv('param');
$response = [];
$strings  = get_translation('config');

if (empty($param)) {
	$response[] = [
		'title' => $strings['lang_title'],
		'subtitle' => $strings['lang_subtitle'],
		'valid' => true,
		'arg' => 'language',
		'match' => $strings['lang_title'],
		'autocomplete' => $strings['lang_title'],
		'autocomplete' => $curr_name,
	];
	$response[] = [
		'title' => $strings['currency_title'],
		'subtitle' => $strings['currency_subtitle'],
		'match' => $strings['currency_title'],
		'autocomplete' => $strings['currency_title'],
		'valid' => true,
		'arg' => 'base_currency',
	];
	$response[] = [
		'title' => $strings['currency_locale_title'],
		'subtitle' => $strings['currency_locale_subtitle'],
		'match' => $strings['currency_locale_title'],
		'autocomplete' => $strings['currency_locale_title'],
		'valid' => true,
		'arg' => 'locale_currency',
	];
	$response[] = [
		'title' => $strings['fixer_title'],
		'subtitle' => $strings['fixer_subtitle'],
		'match' => $strings['fixer_title'],
		'autocomplete' => $strings['fixer_title'],
		'valid' => true,
		'arg' => 'fixer_apikey',
	];
	$response[] = [
		'title' => $strings['measurement_title'],
		'subtitle' => $strings['measurement_subtitle'],
		'match' => $strings['measurement_title'],
		'autocomplete' => $strings['measurement_title'],
		'valid' => true,
		'arg' => 'measurement_system',
	];
	$response[] = [
		'title' => $strings['vat_title'],
		'subtitle' => $strings['vat_subtitle'],
		'match' => $strings['vat_title'],
		'autocomplete' => $strings['vat_title'],
		'valid' => true,
		'arg' => 'vat_percentage',
	];
	$response[] = [
		'title' => $strings['base_timezone_title'],
		'subtitle' => $strings['base_timezone_subtitle'],
		'match' => $strings['base_timezone_title'],
		'autocomplete' => $strings['base_timezone_title'],
		'valid' => true,
		'arg' => 'time_zone',
	];
	$response[] = [
		'title' => $strings['add_date_title'],
		'subtitle' => $strings['add_date_subtitle'],
		'match' => $strings['add_date_title'],
		'autocomplete' => $strings['add_date_title'],
		'valid' => true,
		'arg' => 'add_time_zone',
	];
	$response[] = [
		'title' => $strings['delete_date_title'],
		'subtitle' => $strings['delete_date_subtitle'],
		'match' => $strings['delete_date_title'],
		'autocomplete' => $strings['delete_date_title'],
		'valid' => true,
		'arg' => 'delete_time_zone',
	];
	$response[] = [
		'title' => $strings['base_pixels_title'],
		'subtitle' => $strings['base_pixels_subtitle'],
		'match' => $strings['base_pixels_title'],
		'autocomplete' => $strings['base_pixels_title'],
		'valid' => true,
		'arg' => 'base_pixels',
	];
	echo '{"items": ' . json_encode($response) . ' }';
	exit(0);
}

// Handle delete time zones
if ($param == 'delete_time_zone') {
	$response = [];
	$timezones = get_setting('timezones', [], $settings);

	if (empty($timezones)) {
		$response[] = [
			'title' => '...',
			'subtitle' => $strings['empty_date_formats'],
			'valid' => false,
		];
		echo '{"items": ' . json_encode($response) . ' }';
		exit(0);
	}

	foreach ($timezones as $key => $value) {
		$response[] = [
			'variables' => [
        		'configure_key' => $param,
				'configure_val' => $key
			],
			'title' => $value,
			'subtitle' => $strings['enter_delete_date'],
			'arg' => $value,
		];
	}
	echo '{"items": ' . json_encode($response) . ' }';
	exit(0);
}


$value = get_var($argv, 2, '');
$query = trim(get_var($argv, 1));
$param_name = ucfirst(str_replace('_', ' ', $param));
$config_value = $query;

echo '{"items": [
    {
		"variables": {
        	"configure_key": "'. $param . '",
        	"configure_val": "' . $config_value . '"
		},
		"title": "'.$param_name.': ' . $query . '",
		"subtitle": "' . $strings['enter_save'] . '",
        "uid": "' . $query . '",
		"arg": "' . $query . '",
		"valid": true,
    }
]}';
