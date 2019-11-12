<?php
/**
 * Actions
 * This file handles
 * saving the workflow
 * configuration
 */

require __DIR__ . '/functions.php';
$settings = get_settings();

$type = get_var($argv, 1);
$value = get_var($argv, 2);

if (empty($type)) {
    die();
}

$settings[$type] = $value;
save_settings($settings);
echo "Value saved: $value";
