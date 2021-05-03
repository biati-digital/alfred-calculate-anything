<?php

/**
 * Check for updates
 * check for available updates
 */

require_once getcwd() . '/alfred/Alfred.php';
require_once getcwd() . '/workflow/calculateanything.php';

$translations  = \Alfred\getTranslation('general');
$calculate = new \Workflow\CalculateAnything();
$updater = $calculate->getUpdater();
$update_check = $updater->checkForUpdates(true);

if (!$update_check || !isset($update_check['update_available']) || !$update_check['update_available']) {
    echo $translations['no_updates'];
    die();
}

$updater->notify($translations['update_downloading']);
$updater->downloadUpdate();

\Alfred\removeVariable('update_available');

echo $translations['update_downloaded'];
