<?php

/**
 * Actions
 * This file handles
 * saving the workflow
 * configuration
 */

require __DIR__ . '/workflow/lib/functions.php';
$settings = getSettings();

$param = getenv('configure_key');
$value = getenv('configure_val');

if (empty($param)) {
    die();
}

// Handle currencies
if (!isset($settings['base_currency'])) {
    $settings['base_currency'] = [];
} elseif (is_string($settings['base_currency'])) {
    // convert all setting to array
    $settings['base_currency'] = [$settings['base_currency']];
}
if ($param == 'add_base_currency' || $param == 'delete_base_currency') {
    if ($param == 'add_base_currency') {
        $value = str_replace(' ', '', $value);
        $value = strtoupper($value);
        $value = trim($value);
        $value = explode(',', $value);
        $newcurrencies = array_merge($settings['base_currency'], $value);
        $settings['base_currency'] = array_unique($newcurrencies);
    }
    if ($param == 'delete_base_currency') {
        if (isset($settings['base_currency'][$value])) {
            unset($settings['base_currency'][$value]);
        }
    }

    $value = $settings['base_currency'];
    $param = 'base_currency';
}

// Handle timezones
if (!isset($settings['timezones'])) {
    $settings['timezones'] = [];
}
if ($param == 'add_time_zone' || $param == 'delete_time_zone') {
    if ($param == 'add_time_zone') {
        $settings['timezones'][] = $value;
    }
    if ($param == 'delete_time_zone') {
        if (isset($settings['timezones'][$value])) {
            unset($settings['timezones'][$value]);
        }
    }

    $value = $settings['timezones'];
    $param = 'timezones';
}

$settings[$param] = $value;
saveSettings($settings);
echo "Value saved: $param";
