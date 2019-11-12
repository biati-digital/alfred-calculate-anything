<?php
/**
 * Actions
 * This file handles
 * saving the workflow
 * configuration
 */

require __DIR__ . '/functions.php';

$settings = get_settings();
$action = get_var($argv, 1);
$value = get_var($argv, 2);

if ($action == 'add' || $action == 'delete') {
    if (empty($value)) {
        return false;
    }
    if (!isset($settings['timezones'])) {
        $settings['timezones'] = [];
    }

    if ($action == 'add' && !in_array($value, $settings['timezones'])) {
        $settings['timezones'][] = $value;
    }

    if ($action == 'delete' && ($key = array_search($value, $settings['timezones'])) !== false) {
        unset($settings['timezones'][$key]);
    }
    if ($action == 'delete' && $value == 'all') {
        $settings['timezones'] = [];
    }
    save_settings($settings);
    echo "Added {$value}";
    exit(0);
}


// List
$response = [];
$timezones = get_setting('timezones', [], $settings);

if (empty($timezones)) {
    $response[] = [
        'title' => '...',
        'subtitle' => 'There are no stored time zones',
        'valid' => false,
    ];
    echo '{"items": ' . json_encode($response) . ' }';
    exit(0);
}

foreach ($timezones as $key => $value) {
    $response[] = [
        'title' => $value,
        'subtitle' => 'Press enter to delete time zone',
        'arg' => $value,
    ];
}
echo '{"items": ' . json_encode($response) . ' }';
