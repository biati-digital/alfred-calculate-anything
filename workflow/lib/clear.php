<?php

/**
 * Clear
 * clears the workflow cache
 * and configuration
 */
 
require_once getcwd() . '/alfred/Alfred.php';

$translations  = \Alfred\getTranslation('config');
$cache_dir = \Alfred\getDataPath('cache');

if (!$cache_dir || !file_exists($cache_dir)) {
    echo $translations['cache_deleted'];
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

echo $translations['cache_deleted'];
return true;
