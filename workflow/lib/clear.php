<?php

/**
 * Clear
 * clears the workflow cache
 * and configuration
 */

require_once 'functions.php';

$type = (isset($argv[1]) && !empty($argv[1]) ? $argv[1] : false);
$cache_dir = getDataPath('cache');
$message = '';

if (!$type || !$cache_dir) {
    return false;
}

if ($type == 'cache' || $type == 'all') {
    if (!file_exists($cache_dir)) {
        return true;
    }

    // Delete all children.
    $files = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($cache_dir, \RecursiveDirectoryIterator::SKIP_DOTS),
        \RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $fileinfo) {
        $action = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
        if (!@$action($fileinfo->getRealPath())) {
            return false;
        }
    }
}

if ($type == 'settings' || $type == 'all') {
    $settings = getSettingsPath();

    if (!empty($settings) && (is_file($settings) || is_link($settings))) {
        unlink($settings);
    }
}

return true;
