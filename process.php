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
 * @modify date 13-05-2020
 */

require_once __DIR__ . '/workflow/lib/functions.php';
require_once __DIR__ . '/workflow/calculateanything.php';

$query = cleanQuery(getVar($argv, 1));
$action = getVar($argv, 2);
$calculate = new Workflow\CalculateAnything($query);

if (!empty($action)) {
    if ($action == 'vat') {
        $processed = $calculate->processVat();
    }
    if ($action == 'time') {
        $processed = $calculate->processTime();
    }
} else {
    $processed = $calculate->processQuery();
}

if ($processed) {
    echo '{"items": ' . json_encode($processed) . ' }';
    exit(0);
}

echo '{"items": []}';
exit(0);
