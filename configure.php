<?php

/**
 * Configure
 * Return a list of all the available
 * Options to configure the workflow
 */

require_once __DIR__ . '/workflow/lib/functions.php';

$param = getenv('param');
$response = [];
$settings = getSettings();
$strings  = getTranslation('config');
$currencies = getSetting('base_currency', 'USD', $settings);

if (is_string($currencies)) { // convert old setting to array
    $currencies = [$currencies];
}

if (empty($param)) {
    // Language
    $response[] = [
        'title' => $strings['lang_title'],
        'subtitle' => $strings['lang_subtitle'] . ': ' . getSetting('language', 'en_EN', $settings),
        'valid' => true,
        'match' => $strings['lang_title'],
        'autocomplete' => $strings['lang_title'],
        'arg' => 'language',
    ];

    // Add currency
    $response[] = [
        'title' => $strings['currency_title'],
        'subtitle' => $strings['currency_subtitle'] . ': ' . implode(', ', $currencies),
        'match' => $strings['currency_title'],
        'autocomplete' => $strings['currency_title'],
        'valid' => true,
        'variables' => [
            'searchable_results' => 'enable'
        ],
        'arg' => 'add_base_currency',
    ];

    // Delete currency
    $response[] = [
        'title' => $strings['delete_currency_title'],
        'subtitle' => $strings['delete_currency_subtitle'],
        'match' => $strings['delete_currency_title'],
        'autocomplete' => $strings['delete_currency_title'],
        'valid' => true,
        'arg' => 'delete_base_currency',
    ];
    $response[] = [
        'title' => $strings['currency_locale_title'],
        'subtitle' => $strings['currency_locale_subtitle'] . ': ' . getSetting('locale_currency', 'en_US', $settings),
        'match' => $strings['currency_locale_title'],
        'autocomplete' => $strings['currency_locale_title'],
        'valid' => true,
        'arg' => 'locale_currency',
    ];

    // Coin Market API Key
    $response[] = [
        'title' => $strings['crypto_title'],
        'subtitle' => $strings['crypto_subtitle'] . ': ' . getSetting('coinmarket_apikey', '', $settings),
        'match' => $strings['crypto_title'],
        'autocomplete' => $strings['crypto_title'],
        'valid' => true,
        'arg' => 'coinmarket_apikey',
    ];

    // Currency Fixer API Key
    $response[] = [
        'title' => $strings['fixer_title'],
        'subtitle' => $strings['fixer_subtitle'] . ': ' . getSetting('fixer_apikey', '', $settings),
        'match' => $strings['fixer_title'],
        'autocomplete' => $strings['fixer_title'],
        'valid' => true,
        'arg' => 'fixer_apikey',
    ];

    // Measurement system
    $response[] = [
        'title' => $strings['measurement_title'],
        'subtitle' => $strings['measurement_subtitle'] . ': ' . getSetting('measurement_system', 'metric', $settings),
        'match' => $strings['measurement_title'],
        'autocomplete' => $strings['measurement_title'],
        'valid' => true,
        'arg' => 'measurement_system',
    ];

    // Vat percentage
    $response[] = [
        'title' => $strings['vat_title'],
        'subtitle' => $strings['vat_subtitle'] . ': ' . getSetting('vat_percentage', '16%', $settings),
        'match' => $strings['vat_title'],
        'autocomplete' => $strings['vat_title'],
        'valid' => true,
        'arg' => 'vat_percentage',
    ];

    // Time zone
    $response[] = [
        'title' => $strings['base_timezone_title'],
        'subtitle' => $strings['base_timezone_subtitle'] . ': ' . getSetting('time_zone', 'America/Los_Angeles', $settings),
        'match' => $strings['base_timezone_title'],
        'autocomplete' => $strings['base_timezone_title'],
        'valid' => true,
        'variables' => [
            'searchable_results' => 'enable'
        ],
        'arg' => 'time_zone',
    ];

    // Add Time format
    $response[] = [
        'title' => $strings['add_date_title'],
        'subtitle' => $strings['add_date_subtitle'],
        'match' => $strings['add_date_title'],
        'autocomplete' => $strings['add_date_title'],
        'valid' => true,
        'arg' => 'add_time_zone',
    ];

    // Delete Time format
    $response[] = [
        'title' => $strings['delete_date_title'],
        'subtitle' => $strings['delete_date_subtitle'],
        'match' => $strings['delete_date_title'],
        'autocomplete' => $strings['delete_date_title'],
        'valid' => true,
        'arg' => 'delete_time_zone',
    ];

    // Base Pixels
    $response[] = [
        'title' => $strings['base_pixels_title'],
        'subtitle' => $strings['base_pixels_subtitle'] . ': ' . getSetting('base_pixels', '16px', $settings),
        'match' => $strings['base_pixels_title'],
        'autocomplete' => $strings['base_pixels_title'],
        'valid' => true,
        'arg' => 'base_pixels',
    ];
    echo '{"items": ' . json_encode($response) . ' }';
    exit(0);
}



if ($param == 'language') {
    $response = [];
    $langs = getRegisteredTranslations();

    foreach ($langs as $key => $lang) {
        $response[] = [
            'variables' => [
                'configure_key' => $param,
                'configure_val' => $key
            ],
            'title' => $lang,
            'match' => $key,
            'arg' => $value,
        ];
    }

    echo '{"items": ' . json_encode($response) . ' }';
    exit(0);
}


if ($param == 'add_base_currency') {
    $response = [];
    $calculate = new Workflow\CalculateAnything();
    $items = $calculate->getCalculator('currency')->listAvailable();

    foreach ($items as $item) {
        $item['variables'] = [
            'configure_key' => $param,
            'configure_val' => $item['arg']
        ];
        $response[] = $item;
    }

    echo '{"items": ' . json_encode($response) . ' }';
    exit(0);
}



if ($param == 'time_zone') {
    $response = [];
    $zones = timezone_identifiers_list();

    foreach ($zones as $key => $value) {
        $response[] = [
            'variables' => [
                'configure_key' => $param,
                'configure_val' => $value
            ],
            'title' => str_replace(['_'], ' ', $value),
            'subtitle' => $strings['enter_delete_base_currency'],
            'match' => str_replace(['/', '_'], ' ', $value),
            'arg' => $value,
        ];
    }

    echo '{"items": ' . json_encode($response) . ' }';
    exit(0);
}

// Handle delete time zones
if ($param == 'delete_time_zone') {
    $response = [];
    $timezones = getSetting('timezones', [], $settings);

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

// Handle delete currency
if ($param == 'delete_base_currency') {
    $response = [];
    $stored_currencies = $currencies;

    if (empty($stored_currencies)) {
        $response[] = [
            'title' => '...',
            'subtitle' => $strings['empty_currency_formats'],
            'valid' => false,
        ];
        echo '{"items": ' . json_encode($response) . ' }';
        exit(0);
    }

    foreach ($stored_currencies as $key => $value) {
        $response[] = [
            'variables' => [
                'configure_key' => $param,
                'configure_val' => $key
            ],
            'title' => $value,
            'subtitle' => $strings['enter_delete_base_currency'],
            'arg' => $value,
        ];
    }

    //print_r($response);
    echo '{"items": ' . json_encode($response) . ' }';
    exit(0);
}




$value = getVar($argv, 2, '');
$query = trim(getVar($argv, 1));
$param_name = ucfirst(str_replace('_', ' ', $param));
$config_value = $query;

echo '{"items": [
    {
        "variables": {
            "configure_key": "' . $param . '",
            "configure_val": "' . $config_value . '"
        },
        "title": "' . $param_name . ': ' . $query . '",
        "subtitle": "' . $strings['enter_save'] . '",
        "uid": "' . $query . '",
        "arg": "' . $query . '",
        "valid": true,
    }
]}';
