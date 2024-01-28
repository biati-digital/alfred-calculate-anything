<?php

/**
 * Process
 *
 * Copyright (c) 2020 biati digital
 * This software is released under the MIT License.
 * https://opensource.org/licenses/MIT
 *
 * @author biati digital <https://www.biati.digital>
 * @version 2.0.1
 * @create date 12-11-2019
 * @modify date 06-09-2020
 */

if (getenv('alfred_debug')) {
    error_reporting(E_ALL);
    error_reporting(-1);
    ini_set('error_reporting', E_ALL);
}

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/alfred/Alfred.php';
require_once __DIR__ . '/workflow/lib/backcompat.php';
require_once __DIR__ . '/workflow/calculateanything.php';

use function Alfred\cleanQuery;

$query = cleanQuery($query);
$action = isset($action) ? $action : '';
$calculate = new Workflow\CalculateAnything($query);
$processed = [];
$alfred = ['items' => []];

switch ($action) {
    case 'vat':
        $processed = $calculate->processVat();
        break;
    case 'time':
        $processed = $calculate->processTime();
        break;
    case 'color':
        $processed = $calculate->processColor();
        break;
    default:
        $processed = $calculate->processQuery();
}

if (!empty($processed['rerun'])) {
    $alfred['rerun'] = $processed['rerun'];
    unset($processed['rerun']);

    if (!isset($processed['variables'])) {
        $processed['variables'] = [];
    }

    $processed['variables']['rerun'] = true;
}

if (isset($processed['variables'])) {
    $alfred['variables'] = $processed['variables'];
    unset($processed['variables']);
}

if ($processed) {
    $alfred['items'] = $processed;
    echo json_encode($alfred);
    exit(0);
}

echo json_encode($alfred);
exit(0);
