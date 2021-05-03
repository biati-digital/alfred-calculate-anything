<?php

namespace Alfred;

/**
 * Plist path
 *
 * @return string plist file path
 */
function getPlistPath()
{
    return getenv('PWD') . '/info.plist';
}


/**
 * Get all workflow varibales
 * creating an array of all options
 * and processing the value
 *
 * @param array $options
 * @return array
 */
function getVariables()
{
    $data = [];
    foreach ($_ENV as $key => $val) {
        $data[$key] = processEnvVariable($val);
    }

    return $data;
}

/**
 * Get variable
 * Get workflow variable
 *
 * @param string $key variable name
 * @return string|array  variable value
 */
function getVariable($key, $default = null)
{
    $value = getenv($key);
    if (empty($value) && !is_null($default)) {
        $value = $default;
    }

    return processEnvVariable($value);
}


/**
 * Create a notification using
 * Alfred
 *
 * @param string $title
 * @param string $message
 * @param string $trigger trigger id
 * @return void
 */
function notify($message = '', $title = '', $trigger = '')
{
    $title = (!empty($title) ? $title : getenv('alfred_workflow_name'));
    $native_notifications = !empty($trigger);

    if ($native_notifications) {
        $trigger = is_string($native_notifications) ? $native_notifications : 'notifier';
        $title = htmlspecialchars($title, ENT_QUOTES);
        $message = htmlspecialchars($message, ENT_QUOTES);
        $bundleid = getWorkflowIdentifier();
        $output = $title . '|' . $message;
        $script = 'tell application id "com.runningwithcrayons.Alfred" to run trigger "' . $trigger . '" in workflow "' . $bundleid . '" with argument "' . $output . '"';
        $command = "osascript -e '{$script}'";
    }

    if (!$native_notifications) {
        $title = htmlspecialchars($title, ENT_QUOTES);
        $message = htmlspecialchars($message, ENT_QUOTES);
        $command = "osascript -e 'display notification \"{$message}\" with title \"{$title}\"'";
    }

    shell_exec($command);
}


/**
 * Set variables
 * define the workflow variables
 * tihs will replace all current
 * variables with the new ones
 *
 * @param array $data variables data
 * @return bool
 */
function setVariables(&$data)
{
    foreach ($data as $key => $item) {
        $exportable = (isset($item['exportable']) ? $item['exportable'] : null);
        setVariable($key, $item['value'], $exportable);
    }
}


/**
 * Set Variable
 * Update workflow variable updating the Plist file
 *
 * @param string $key varibale name
 * @param string $value variable value
 * @return bool
 */
function setVariable($key = '', $value = '', $exportable = null)
{
    if (empty($key)) {
        return 'Variable key is empty, unable to save';
    }

    if (is_array($value)) {
        $value = json_encode($value);
        $value = addslashes($value);
    }

    $trigger = 'preferences';
    $bundleid = getWorkflowIdentifier();
    $option = $key . '|' . $value;
    $is_exportable = 'false';

    if (!is_null($exportable) && $exportable) {
        $is_exportable = 'true';
    }

    $option .= '|' . $is_exportable;
    $script = 'tell application id "com.runningwithcrayons.Alfred" to run trigger "' . $trigger . '" in workflow "' . $bundleid . '" with argument "' . $option . '"';
    $command = "osascript -e '{$script}' &";
    return shell_exec($command);
}

/**
 * Remove workflow variable
 *
 * @param string $key
 * @return string  output
 */
function removeVariable($key)
{
    $trigger = 'preferences';
    $bundleid = getWorkflowIdentifier();
    $option = $key . '|null|null|true';

    $script = 'tell application id "com.runningwithcrayons.Alfred" to run trigger "' . $trigger . '" in workflow "' . $bundleid . '" with argument "' . $option . '"';
    $command = "osascript -e '{$script}' &";
    return shell_exec($command);
}


/**
 * Get variable as string
 * Get workflow variable
 *
 * @param string $key variable name
 * @return string  variable value
 */
function getVariableAsString($key, $default = null, $separator = ',')
{
    $val = getVariable($key, $default);
    $str = '';

    if (empty($val) || !is_array($val)) {
        return $str;
    }

    return implode($separator, $val);
}


/**
 * Get argument
 * get the value from argv
 *
 * @param array $args
 * @param string $key
 * @param mixed $default
 * @param integer $trim_whitespace
 * @return mixed
 */
function getArgument($args, $key, $default = null, $trim_whitespace = 1)
{
    if (is_array($args) && isset($args[$key]) && !empty($args[$key])) {
        return ($trim_whitespace ? trim($args[$key]) : $args[$key]);
    }
    if (!is_null($default)) {
        return $default;
    }

    return '';
}


/**
 * Process variable
 * Handle the varibale value depending
 * on it's value
 *
 * @param string $val env value
 * @return string|array|bool
 */
function processEnvVariable($val)
{
    if (empty($val)) {
        return $val;
    }

    $val = ($val === 'true' ? true : $val);
    $val = ($val === 'false' ? false : $val);

    if (strpos($val, '\\') !== false) {
        $val = stripslashes($val);
    }

    if (strpos($val, '~') !== false) {
        $val = str_replace('~', getHomePath(), $val);
    } elseif (strpos($val, '|') !== false) {
        $val = explode('|', $val);
    } elseif (substr($val, 0, 1) === '{' || substr($val, 0, 1) === '[') {
        try {
            $val = json_decode($val, true);
        } catch (Exception $e) {
            $val = $val;
        }
    }

    return $val;
}


/**
 * Sanitize variable
 * Handle the varibale value before save
 *
 * @param string|array|bool $val env value
 * @return string
 */
function sanitizeEnvVariable($val)
{
    $val = ($val === true ? 'true' : $val);
    $val = ($val === false ? 'false' : $val);

    if (is_array($val)) {
        $val = json_encode($val);
    }

    return $val;
}


/**
 * Clean query
 * remove unwanted characters from query
 *
 * @param string $query
 * @return string  cleaned $query
 */
function cleanQuery($query)
{
    if (empty($query)) {
        return $query;
    }
    // $clean = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $tsts);
    // Fucking letter ñ this was the only
    // way i found to remove it without removing
    // the rest of the special characters in the string
    $clean = $query;
    $clean = urlencode($clean);
    $clean = str_replace('n%CC%83', 'n', $clean);
    $clean = str_replace('N%CC%83', 'n', $clean);
    $clean = str_replace('%C3%B3', 'o', $clean); // accented o
    $clean = str_replace('%CC%81', '', $clean); // accented i

    $clean = urldecode($clean);
    $clean = preg_replace('!\s+!', ' ', $clean);
    $clean = mb_strtolower($clean, 'UTF-8');

    return $clean;
}

/**
 * Home path
 * user home directory path
 *
 * @return string  path
 */
function getHomePath()
{
    return getenv('HOME');
}

/**
 * Desktop path
 * user desktop directory path
 *
 * @return string  path
 */
function getDesktopPath()
{
    $home = getHomePath();
    return "$home/Desktop";
}

/**
 * Username
 * return the username
 *
 * @return string
 */
function getUsername()
{
    return getenv('USER');
}


/**
 * Workflow Dir
 *
 * @param string $path optional append path
 * @return string
 */
function workflowDir($path = '')
{
    $dir = getenv('PWD') ? getenv('PWD') : getcwd();
    if ($path) {
        $dir .= $path;
    }
    return $dir;
}

/**
 * Workflow Identifier
 *
 * @param string $path optional append path
 * @return string
 */
function getWorkflowIdentifier()
{
    return getenv('alfred_workflow_bundleid');
}


/**
 * Get data path
 *
 * @param string $ipath inner path
 * @return strung path
 */
function getDataPath($ipath = '')
{
    $path = getenv('alfred_workflow_data');

    createDir($path);

    if (!empty($ipath)) {
        $path = $path . '/' . $ipath;
    }

    return $path;
}

/**
 * Theme color
 * Alfred appearance color info
 * from  shawnrice/alphred
 *
 * @return string
 */
function getThemeStyle()
{
    $matches = [];
    $pattern = "/rgba\(([0-9]{1,3}),([0-9]{1,3}),([0-9]{1,3}),([0-9.]{4,})\)/";

    preg_match_all($pattern, getVariable('alfred_theme_background'), $matches);

    if (empty($matches)) {
        throw new Exception('Unable to parse Alfred Theme');
    }

    $rgb = [
        'r' => $matches[1][0],
        'g' => $matches[2][0],
        'b' => $matches[3][0]
    ];

    $luminance = (0.299 * $rgb[ 'r' ] + 0.587 * $rgb[ 'g' ] + 0.114 * $rgb[ 'b' ]) / 255;

    if (0.5 < $luminance) {
        return 'light';
    }

    return 'dark';
}


/**
 * Create dir
 *
 * @param string $path
 * @return bool
 */
function createDir($path)
{
    if (!file_exists($path)) {
        mkdir($path);
    }

    return true;
}


/**
 * Read file
 *
 * @param string $path file path
 * @param string $type file type to parse
 * @return string|array file content
 */
function readFile($path, $type = 'text')
{
    if (!file_exists($path)) {
        return false;
    }

    $content = file_get_contents($path);
    if (empty($content)) {
        return false;
    }

    if ($type == 'json') {
        return json_decode($content, true);
    }

    return $content;
}


/**
 * Write file
 *
 * @param string $path file path
 * @param string $type file type to parse
 * @param bool  $append
 * @return bool
 */
function writeFile($path, $content, $append = null)
{
    if (is_array($content)) {
        $content = json_encode($content);
    }

    try {
        if ($append) {
            file_put_contents($path, $content, FILE_APPEND);
        } elseif (!$append) {
            file_put_contents($path, $content);
        }
    } catch (Exception $e) {
        throw new Exception($e->getMessage());
    }

    return true;
}


/**
 * Translations path
 * path to the langs folder or
 * sopecific translation file
 *
 * @param string $lang_file
 * @return string  file path
 */
function getTranslationsPath($lang_file = '')
{
    //$path = getenv('PWD') . '/lang';
    $path = workflowDir('/lang');

    if ($lang_file) {
        $path .= '/' . $lang_file;
    }

    return $path;
}


/**
 * Get translations
 * load the translation and return
 * the lang array
 * Translation may be already saved as
 * an env variable
 *
 * @param string $key
 * @param string $lang
 * @return array
 */

function getTranslation($key = '', $lang = '')
{
    $default_lang = 'en_EN';
    $user_language = getVariable('language', $default_lang);

    if (is_array($lang)) {
        $user_language = $lang['code'];
        $translations = $lang;
    }
    if (is_string($lang)) {
        $cached_translation = getVariable('translation');
        $translations = $cached_translation ? $cached_translation : loadTranslationsFile($user_language);
    }

    // Return default lang if translation not found
    if (!is_array($translations) || empty($translations)) {
        return getTranslation($key, $default_lang);
    }

    if (!empty($key) && isset($translations[$key])) {
        return $translations[$key];
    }

    return $translations;
}


/**
 * Load translations file
 * load the required files
 *
 * @param string $lang language code
 * @return bool
 */
function loadTranslationsFile($lang)
{
    //$path = getenv('PWD') ? getenv('PWD') : getcwd();
    $file = workflowDir('/lang/' . $lang . '.php');
    if (file_exists($file)) {
        $data = include $file;
        return $data;
    }
    return false;
}

/**
 * Get registered translations
 * an array of available translations
 * found in the lang folder
 *
 * @return array
 */
function getRegisteredTranslations()
{
    $dir = getTranslationsPath();
    $fileList = glob($dir . '/*.php');
    $langs = [];
    foreach ($fileList as $file) {
        if (strpos($file, '-keys') !== false) {
            continue;
        }

        $lang = include $file;

        if (isset($lang['code']) && isset($lang['name'])) {
            $langs[$lang['code']] = $lang['name'];
        }
    }

    return $langs;
}


/**
 * Workflow updater
 *
 * @param array $config
 * @return bool
 */
function workflowUpdater($config)
{
    require_once workflowDir('/alfred/Updater.php');

    $default = [
        'plist_url' => '',
        'workflow_url' => '',
        'force_check' => false,
        'force_download' => false,
        'alfred_notifications' => false,
    ];
    $updater = new Updater(array_merge($default, $config));

    return $updater;
}


/**
 * Filter
 * filter Alfred results
 * by provided query
 *
 * @param array $results
 * @param string $query search query
 * @param array $filterIn
 * @param numeric $ignoreCase
 * @return array
 */
function filterRestults(&$results, $query, $filterIn = ['title', 'match'])
{
    $query = trim($query);
    if (empty($query)) {
        return $results;
    }

    $filtered = [];
    $query = strtolower($query);

    foreach ($results as $val) {
        foreach ($filterIn as $key) {
            $result_val = (isset($val[$key]) ? $val[$key] : '');
            $result_val = strtolower($result_val);

            if (!empty($result_val) && strpos($result_val, $query) !== false) {
                $filtered[] = $val;
                break;
            }
        }
    }

    /*$filtered = array_filter($results, function ($val, $key) use ($query) {
        $result_title = (isset($val['title']) ? $val['title'] : false);
        $result_match = (isset($val['match']) ? $val['match'] : false);
        if ($result_match && strpos($result_match, $query) !== false) {
            return true;
        }
        if ($result_title && strpos($result_title, $query) !== false) {
            return true;
        }
    }, ARRAY_FILTER_USE_BOTH);*/

    return $filtered;
}
