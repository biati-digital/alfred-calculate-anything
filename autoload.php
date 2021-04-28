<?php

require __DIR__ . '/vendor/autoload.php';

spl_autoload_register(function ($class) {
    $class = str_replace('_', '-', $class);
    $class = str_replace('\\CalculateAnything\\', '\\tools\\', $class);
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $class = strtolower($class) . '.php';

    $dir = __DIR__;
    $file = $dir . '/' . $class;

    if (is_readable($file)) {
        include_once $file;
    } else {
        $libs = [
            'olifolkerd/convertor/convertor.php' => $dir . '/workflow/lib/units/Convertor.php',
            'olifolkerd/convertor/exceptions/convertorinvalidunitexception.php' => $dir . '/workflow/lib/units/Exceptions/ConvertorInvalidUnitException.php',
        ];

        if (isset($libs[$class]) && is_readable($libs[$class])) {
            include_once $libs[$class];
        }
    }
});
