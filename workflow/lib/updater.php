<?php

/**
 * Check for updates
 * check for avaiñable updates
 */

require_once 'functions.php';

$updates = workflowUpdater([
    'force_check' => true,
    'download_type' => 'sync',
]);

// No title if there are updates as the updater
// will display the notifications
$notification = (!$updates ? 'You have the latest version.' : '');

echo $notification;
