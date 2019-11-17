<?php
/**
 * Actions
 * This file handles
 * saving the workflow
 * configuration
 */

require __DIR__ . '/functions.php';
$settings = get_settings();

$param = getenv('configure_key');
$value = getenv('configure_val');

if (empty($param)) {
    die();
}

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
save_settings($settings);
echo "Value saved: $param";
