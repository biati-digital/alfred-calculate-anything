<?php

/**
 * Clear
 * clears the workflow cache
 * and configuration
 */

require_once getcwd() . '/alfred/Alfred.php';

$translations  = \Alfred\getTranslation('config');

$clear = \Alfred\emptyCacheDirectory();
\Alfred\removeVariable('fixer_source_api');

echo $translations['cache_deleted'];
return true;
