<?php

/**
 * Configure
 * Return a list of all the available
 * Options to configure the workflow
 */

if (getenv('alfred_debug')) {
    error_reporting(E_ALL);
    error_reporting(-1);
    ini_set('error_reporting', E_ALL);
}

require_once getcwd() . '/autoload.php';
require_once getcwd() . '/alfred/Alfred.php';
require_once getcwd() . '/workflow/calculateanything.php';

use function Alfred\getVariable;
use function Alfred\getVariableAsString;
use function Alfred\getTranslation;
use function Alfred\getArgument;
use function Alfred\cleanQuery;
use function Alfred\filterRestults;
use function Alfred\getThemeStyle;

$response = [];
$translation = getTranslation();
$strings  = getTranslation('config', $translation);
$fromkeyword = getVariable('fromkeyword');
$submenu  = getVariable('submenu');
$input  = getVariable('input');
$id = getVariable('id');
$process = false;
$main_menu = true;
$query = getArgument($argv, 1, '', false);
$theme = getThemeStyle();

if ($submenu || $input) {
    $main_menu = false;
}

if (!empty($query) || $fromkeyword || $submenu || $input) {
    $process = true;
}

if (substr($query, 0, 1) === ' ') {
    $process = true;
} elseif (!$submenu && substr($query, 0, 2) === 'ca') {
    $process = true;
    $query = substr($query, 2);
}

if (!$fromkeyword && !$submenu && !$input && $query && substr($query, 0, 1) !== ' ') {
    die();
}

if (!$process) {
    $response[] = [
        'title' => 'Calculate Anything',
        'subtitle' => 'Calculate Anything...',
        'valid' => true,
        'uid' => 'ca',
        'arg' => 'ca ',
        'variables' => [
            'fromkeyword' => true,
            'action' => 'menu',
        ],
    ];
    $alfred = [
        'items' => &$response
    ];
    echo json_encode($alfred);
    die();
}

if ($process && $main_menu) {
    // Language
    $response[] = [
        'title' => $strings['lang_title'],
        'subtitle' => $strings['lang_subtitle'] . ': ' . getVariable('language', 'en_EN'),
        'valid' => true,
        'match' => 'lang ' . $strings['lang_title'],
        'autocomplete' => $strings['lang_title'],
        'arg' => '',
        'variables' => [
            'id' => 'language',
            'action' => 'menu',
            'submenu' => 'language',
        ],
    ];

    // Add currency
    $response[] = [
        'title' => $strings['currency_title'],
        'subtitle' => $strings['currency_subtitle'] . ': ' . getVariableAsString('base_currency'),
        'match' => 'add currency ' .$strings['currency_title'],
        'autocomplete' => $strings['currency_title'],
        'valid' => true,
        'arg' => '',
        'variables' => [
            'id' => 'base_currency',
            'action' => 'menu',
            'submenu' => 'currency',
        ],
    ];

    // Delete currency
    $response[] = [
        'title' => $strings['delete_currency_title'],
        'subtitle' => $strings['delete_currency_subtitle'],
        'match' => 'delete currency ' . $strings['delete_currency_title'],
        'autocomplete' => $strings['delete_currency_title'],
        'valid' => true,
        'arg' => '',
        'variables' => [
            'id' => 'base_currency',
            'action' => 'menu',
            'submenu' => 'delete_base_currency',
        ],
    ];

    // Currency Format
    $response[] = [
        'title' => $strings['currency_locale_title'],
        'subtitle' => $strings['currency_locale_subtitle'],
        'match' => 'currency format ' . $strings['currency_locale_title'],
        'autocomplete' => $strings['currency_locale_title'],
        'valid' => true,
        'arg' => '',
        'variables' => [
            'id' => 'locale_currency',
            'action' => 'menu',
            'submenu' => 'currency_format',
        ],
    ];

    // Currency Fixer API Key
    $response[] = [
        'title' => $strings['fixer_title'],
        'subtitle' => $strings['fixer_subtitle'] . ': ' . getVariable('fixer_apikey', ''),
        'match' => 'fixer ' . $strings['fixer_title'],
        'autocomplete' => $strings['fixer_title'],
        'valid' => true,
        'arg' => getVariable('fixer_apikey', ''),
        'variables' => [
            'id' => 'fixer_apikey',
            'action' => 'menu',
            'input' => true,
            'input_title' => 'API Key',
            'input_value' => getVariable('fixer_apikey', ''),
        ],
    ];

    // Coin Market API Key
    $response[] = [
        'title' => $strings['crypto_title'],
        'subtitle' => $strings['crypto_subtitle'] . ': ' . getVariable('coinmarket_apikey', $strings['value_not_set']),
        'match' => 'crypto ' . $strings['crypto_title'],
        'autocomplete' => $strings['crypto_title'],
        'valid' => true,
        'arg' => getVariable('coinmarket_apikey', ''),
        'variables' => [
            'id' => 'coinmarket_apikey',
            'action' => 'menu',
            'input' => true,
            'input_title' => 'API Key',
            'input_value' => getVariable('coinmarket_apikey', ''),
        ],
    ];

    // Vat percentage
    $response[] = [
        'title' => $strings['vat_title'],
        'subtitle' => $strings['vat_subtitle'] . ': ' . getVariable('vat_percentage', '16%'),
        'match' => 'vat ' . $strings['vat_title'],
        'autocomplete' => $strings['vat_title'],
        'valid' => true,
        'arg' => getVariable('vat_percentage', '16%'),
        'variables' => [
            'id' => 'vat_percentage',
            'action' => 'menu',
            'input' => true,
            'input_title' => $strings['vat_input'],
        ],
    ];

    // Time zone
    $response[] = [
        'title' => $strings['base_timezone_title'],
        'subtitle' => $strings['base_timezone_subtitle'] . ': ' . getVariable('time_zone', 'America/Los_Angeles'),
        'match' => $strings['base_timezone_title'],
        'autocomplete' => $strings['base_timezone_title'],
        'valid' => true,
        'arg' => '',
        'variables' => [
            'id' => 'time_zone',
            'action' => 'menu',
            'submenu' => 'time_zone',
        ],
    ];

    // Add Time format
    $response[] = [
        'title' => $strings['add_date_title'],
        'subtitle' => $strings['add_date_subtitle'],
        'match' => $strings['add_date_title'],
        'autocomplete' => $strings['add_date_title'],
        'valid' => true,
        'arg' => '',
        'variables' => [
            'id' => 'time_format',
            'action' => 'menu',
            'input' => true,
            'input_process' => 'add_time_format',
            'input_title' => $strings['enter_save'],
        ],
    ];

    // Delete Time format
    $response[] = [
        'title' => $strings['delete_date_title'],
        'subtitle' => $strings['delete_date_subtitle'],
        'match' => $strings['delete_date_title'],
        'autocomplete' => $strings['delete_date_title'],
        'valid' => true,
        'arg' => '',
        'variables' => [
            'id' => 'time_format',
            'action' => 'menu',
            'submenu' => 'delete_time_format',
        ],
    ];

    // Base Pixels
    $response[] = [
        'title' => $strings['base_pixels_title'],
        'subtitle' => $strings['base_pixels_subtitle'] . ': ' . getVariable('base_pixels', '16px'),
        'match' => 'pixels ' . $strings['base_pixels_title'],
        'autocomplete' => $strings['base_pixels_title'],
        'valid' => true,
        'arg' => getVariable('base_pixels', '16px'),
        'variables' => [
            'id' => 'base_pixels',
            'action' => 'menu',
            'input' => true,
            'input_title' => 'Pixels',
        ],
    ];

    if (strpos($query, 'list') === false) {
        // List available
        $response[] = [
            'title' => $strings['list_available_title'],
            'subtitle' => $strings['list_available_subtitle'],
            'match' => 'list',
            'valid' => true,
            'arg' => '',
            'variables' => [
                'action' => 'menu',
                'submenu' => 'list'
            ],
        ];
    }

    // Clear cache
    $response[] = [
        'title' => $strings['cache_clear_title'],
        'subtitle' => $strings['cache_clear_subtitle'],
        'match' => 'clear',
        'autocomplete' => $strings['cache_clear_title'],
        'valid' => true,
        'arg' => 'clear_cache',
        'variables' => [
            'action' => 'clear_cache'
        ],
    ];

    // Check for update
    $response[] = [
        'title' => $strings['updates_title'],
        'subtitle' => $strings['updates_subtitle'],
        'match' => 'update',
        'autocomplete' => $strings['updates_title'],
        'valid' => true,
        'arg' => 'update',
        'variables' => [
            'action' => 'update',
        ],
    ];

    if (!empty($query)) {
        $response = filterRestults($response, $query);
    }
}

$goback = [
    'title' => $strings['goback_title'],
    'subtitle' => $strings['goback_subtitle'],
    'match' => 'goback',
    'valid' => true,
    'arg' => '',
    'icon' => ['path' => 'assets/goback-for-' . $theme .'.png'],
    'variables' => [
        'fromkeyword' => true,
        'action' => 'menu',
        'submenu' => '',
    ],
];


if ($submenu == 'language') {
    $langs = \Alfred\getRegisteredTranslations();

    $response[] = $goback;

    foreach ($langs as $language_key => $language_val) {
        $response[] = [
            'variables' => [
                'action' => 'save_config',
                'configure_key' => $id,
                'configure_val' => $language_key
            ],
            'title' => $language_val,
            'match' => $language_key,
            'arg' => $language_key,
        ];
    }
}


if ($submenu == 'currency') {
    $calculate = new \Workflow\CalculateAnything();
    $items = $calculate->getCalculator('currency')->listAvailable();

    $response[] = $goback;
    foreach ($items as $item) {
        $item['variables'] = [
            'action' => 'save_config',
            'input_process' => 'add_base_currency',
            'configure_key' => $id,
            'configure_val' => $item['arg']
        ];
        $item['match'] = $item['match'] . ' ' . $item['subtitle'];
        $response[] = $item;
    }

    if (!empty($query)) {
        $response = filterRestults($response, $query);
    }
}

if ($submenu == 'currency_format') {
    $amount = 1234.56;
    $calculate = new \Workflow\CalculateAnything();
    $locales = $calculate->getCalculator('currency')->currencyLocales();
    $formatted = [];
    $response[] = $goback;

    foreach ($locales as $locale) {
        setlocale(LC_MONETARY, $locale);
        $amount_formatted = money_format('%i', $amount);
        $amount_formatted = preg_replace("/\w+[^0-9-., ]/", '$', $amount_formatted);
        $amount_formatted = trim($amount_formatted);

        if (in_array($amount_formatted, $formatted)) {
            continue;
        }
        $formatted[] = $amount_formatted;
        $response[] = [
            'variables' => [
                'action' => 'save_config',
                'configure_key' => $id,
                'configure_val' => $locale
            ],
            'title' => $amount_formatted,
            'subtitle' => $strings['currency_locale_enter'],
            'arg' => $locale,
        ];
    }
}

// Handle delete currency
if ($submenu == 'delete_base_currency') {
    $stored_currencies = getVariable('base_currency', []);
    $response[] = $goback;

    if (empty($stored_currencies)) {
        $response[] = [
            'title' => '...',
            'subtitle' => $strings['empty_currency_formats'],
            'valid' => false,
        ];
    }

    foreach ($stored_currencies as $key => $value) {
        $response[] = [
            'variables' => [
                'action' => 'save_config',
                'input_process' => 'delete_base_currency',
                'configure_key' => $id,
                'configure_val' => $key
            ],
            'title' => $value,
            'subtitle' => $strings['enter_delete'],
            'arg' => $value,
        ];
    }
}


if ($submenu == 'time_zone') {
    $zones = timezone_identifiers_list();
    $response[] = $goback;

    foreach ($zones as $key => $value) {
        $response[] = [
            'variables' => [
                'action' => 'save_config',
                'configure_key' => $id,
                'configure_val' => $value
            ],
            'title' => str_replace(['_'], ' ', $value),
            'subtitle' => $strings['enter_save'],
            'match' => str_replace(['/', '_'], ' ', $value),
            'arg' => $value,
        ];
    }

    if (!empty($query)) {
        $response = filterRestults($response, $query);
    }
}


// Handle delete time zones
if ($submenu == 'delete_time_format') {
    $response[] = $goback;
    $timeformats = getVariable('time_format', []);

    if (empty($timeformats)) {
        $response[] = [
            'title' => '...',
            'subtitle' => $strings['empty_date_formats'],
            'valid' => false,
        ];
        echo '{"items": ' . json_encode($response) . ' }';
        exit(0);
    }

    foreach ($timeformats as $key => $value) {
        $response[] = [
            'variables' => [
                'action' => 'save_config',
                'input_process' => 'delete_time_format',
                'configure_key' => $id,
                'configure_val' => $key
            ],
            'title' => $value,
            'subtitle' => $strings['enter_delete_date'],
            'arg' => $value,
        ];
    }
}

// List available
if ($submenu == 'list' || strpos($query, 'list') !== false) {
    $list_type = getVariable('list_type');
    $list_type = empty($list_type) ? 'select' : $list_type;

    if ($list_type == 'select') {
        $response[] = [
            'title' => $strings['list_currencies_title'],
            'subtitle' => $strings['list_subtitle'],
            'valid' => true,
            'arg' => '',
            'variables' => [
                'fromkeyword' => true,
                'action' => 'menu',
                'submenu' => 'list',
                'list_type' => 'currency'
            ],
        ];
        $response[] = [
            'title' => $strings['list_cryptocurrencies_title'],
            'subtitle' => $strings['list_subtitle'],
            'valid' => true,
            'arg' => '',
            'variables' => [
                'fromkeyword' => true,
                'action' => 'menu',
                'submenu' => 'list',
                'list_type' => 'cryptocurrency'
            ],
        ];
        $response[] = [
            'title' => $strings['list_units_title'],
            'subtitle' => $strings['list_subtitle'],
            'valid' => true,
            'arg' => '',
            'variables' => [
                'fromkeyword' => true,
                'action' => 'menu',
                'submenu' => 'list',
                'list_type' => 'units'
            ],
        ];
    }

    if ($list_type !== 'select') {
        $calculate = new \Workflow\CalculateAnything();
        $list = [];

        switch ($list_type) {
            case 'units':
                $list = $calculate->getCalculator('units')->listAvailable();
                break;
            case 'currency':
                $list = $calculate->getCalculator('currency')->listAvailable();
                break;
            case 'cryptocurrency':
                $list = $calculate->getCalculator('cryptocurrency')->listAvailable();
                break;
            default:
                $list = [];
        }

        $response = array_merge($response, $list);

        if (!empty($query)) {
            $response = filterRestults($response, $query);
        }
    }
}


if ($input) {
    $input_title = getVariable('input_title');
    $response[] = [
        'variables' => [
            'action' => 'save_config',
            'configure_key' => $id,
            'configure_val' => trim($query),
        ],
        'title' => "{$input_title}: {$query}",
        'subtitle' => $strings['enter_save'],
        'uid' => $query,
        'arg' => $query,
        'valid' => true,
    ];
}


$alfred = [
    'items' => &$response
];

echo json_encode($alfred);
exit(0);
