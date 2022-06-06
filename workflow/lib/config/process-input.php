<?php

/**
 * Process input
 * this file is in charge of saving the
 * workflow configuration as workflow variables
 */

require_once getcwd() . '/autoload.php';
require_once getcwd() . '/alfred/Alfred.php';
require_once getcwd() . '/workflow/lib/backcompat.php';
require_once getcwd() . '/workflow/calculateanything.php';

use function Alfred\getVariable;
use function Alfred\getTranslation;
use function Alfred\getArgument;
use function Alfred\cleanQuery;
use function Alfred\filterRestults;

$query = getArgument($argv, 1);
$id = getVariable('id');
$input_process = getVariable('input_process');
$param = getVariable('configure_key');
$value = getVariable('configure_val');
$strings  = getTranslation('config');
$response = [];
$config_value = $value;

$response = [
    'alfredworkflow' => [
        'arg' => $strings['option_saved'] . ': ' . $id,
    ]
];

/*
 * If input does not need to be processed
 * simply exit and continue with the workflow process
 * input_process will be empty if we are simply saving a string
 * a value that does not need to be processed.
 */
if (empty($input_process)) {
    echo json_encode($response);
    die();
}

if ($input_process === 'add_base_currency') {
    $currencies = getVariable('base_currency', []);
    $value = str_replace(' ', '', $value);
    $value = strtoupper($value);
    $value = trim($value);
    $value = explode(',', $value);

    $newcurrencies = $value;

    if (is_array($currencies) && !empty($currencies)) {
        $newcurrencies = array_merge($currencies, $value);
    }

    $config_value = array_unique($newcurrencies);
}

if ($input_process === 'delete_base_currency') {
    $currencies = getVariable($param, []);
    if (empty($currencies)) {
        $currencies = [];
    }
    if (isset($currencies[$value])) {
        unset($currencies[$value]);
    }
    $config_value = $currencies;
}


if ($input_process === 'add_cryptocurrency') {
    $cryptourrencies = getVariable('custom_cryptocurrencies', []);
    $value = str_replace(' ', '', $value);
    $value = strtoupper($value);
    $value = trim($value);
    $value = explode(',', $value);

    $newryptocurrencies = $value;

    if (is_array($cryptourrencies) && !empty($cryptourrencies)) {
        $newryptocurrencies = array_merge($cryptourrencies, $value);
    }

    $config_value = array_unique($newryptocurrencies);

    $cache_path = \Alfred\getDataPath('cache');
    \Alfred\emptyDirectory($cache_path . '/coinmarketcap');
}


if ($input_process === 'delete_cryptocurrency') {
    $cryptocurrencies = getVariable($param, []);
    if (empty($cryptocurrencies)) {
        $cryptocurrencies = [];
    }
    if (isset($cryptocurrencies[$value])) {
        unset($cryptocurrencies[$value]);
    }
    $config_value = $cryptocurrencies;
}


if ($input_process === 'add_time_format') {
    $timezones = getVariable($param, []);
    if (empty($timezones)) {
        $timezones = [];
    }
    $timezones[] = $value;
    $config_value = $timezones;
}

if ($input_process === 'delete_time_format') {
    $timezones = getVariable($param, []);
    if (empty($timezones)) {
        $timezones = [];
    }
    if (isset($timezones[$value])) {
        unset($timezones[$value]);
    }
    $config_value = $timezones;
}

// Sanitize value
if (is_array($config_value)) {
    if (!empty($config_value)) {
        $config_value = json_encode($config_value);
        $config_value = addslashes($config_value);
    }
    if (empty($config_value)) {
        $config_value = '';
    }
}

$response['alfredworkflow']['variables'] = [
    'configure_key' => $id,
    'configure_val' => $config_value,
];

echo json_encode($response);
