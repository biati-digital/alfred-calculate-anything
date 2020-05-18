<?php

require dirname(__DIR__, 2) . '/vendor/autoload.php';

function getSettings()
{
    $file = getSettingsPath();

    if (!file_exists($file)) {
        return [];
    }

    $content = file_get_contents($file);
    if (empty($content)) {
        return [];
    }

    return json_decode($content, true);
}

function getSetting($name, $default = false, $settings = false)
{
    if (!$settings || !is_array($settings)) {
        $settings = getSettings();
    }

    if (isset($settings[$name])) {
        return $settings[$name];
    }

    if ($default) {
        return $default;
    }

    return null;
}

function saveSettings($settings)
{
    $file = getSettingsPath();
    file_put_contents($file, json_encode($settings));
}

function getSettingsPath()
{
    return getDataPath('settings.json');
}

function getDataPath($ipath = '')
{
    $path = getenv('alfred_workflow_data');

    if (empty($path) && defined('DOING_TESTS')) {
        $username = exec('whoami');
        $path = "/Users/{$username}/Library/Application Support/Alfred/Workflow Data/com.alfred.calculateanything";
    }

    createDir($path);

    if (!empty($ipath)) {
        return $path . '/' . $ipath;
    }
    return $path;
}

function createDir($path)
{
    if (!file_exists($path)) {
        mkdir($path);
    }

    return true;
}

function getVar($array, $key, $default = null)
{
    if (is_array($array) && isset($array[$key]) && !empty($array[$key])) {
        return trim($array[$key]);
    }
    if (!is_null($default)) {
        return $default;
    }

    return '';
}

function getTranslation($key = '', $lang = '')
{
    $default_lang = defaultLang();
    $lang = (empty($lang) ? getSetting('language', $default_lang) : $lang);
    $translations = loadTranslationsFile($lang);
    if ($translations) {
        // Return default lang if translation error
        if (!is_array($translations) || empty($translations)) {
            return getTranslation($key, $default_lang);
        }

        if (empty($key)) {
            return $translations;
        }

        if (isset($translations[$key])) {
            return $translations[$key];
        }

        return false;
    }

    return getTranslation($key, $default_lang);
}

function loadTranslationsFile($lang)
{
    $file = dirname(__DIR__, 2) . '/lang/' . $lang . '.php';
    if (file_exists($file)) {
        $data = include $file;
        return $data;
    }
    return false;
}

function getText($key)
{
    $strings = getTranslation('general');
    if (!is_array($strings) || !isset($strings[$key])) {
        return '';
    }
    return $strings[$key];
}

function defaultLang()
{
    return 'en_EN';
}

function getExtraKeywords($key = '', $lang = '')
{
    if (defined('LANG_KEYWORDS')) {
        $strings = LANG_KEYWORDS;
        if (empty($key)) {
            return $strings;
        }

        if (isset($strings[$key])) {
            return $strings[$key];
        }

        return false;
    }

    $default_lang = defaultLang();
    $lang = (empty($lang) ? getSetting('language', $default_lang) : $lang);
    // $file = __DIR__ . '/lang/' . $lang . '-keys.php';
    $file = dirname(__DIR__, 2) . '/lang/' . $lang . '-keys.php';

    if (file_exists($file)) {
        $translations = include $file;

        // Return default lang if translation error
        if (!is_array($translations) || empty($translations)) {
            return getExtraKeywords($key, $default_lang);
        }

        // If language is different
        // from english, also load the en keys
        // so they are global
        if ($lang !== $default_lang) {
            $translations = mergeWithBaseKeywords($translations);
        }

        if (empty($key)) {
            return $translations;
        }

        if (isset($translations[$key])) {
            return $translations[$key];
        }

        return false;
    }

    return getExtraKeywords($key, $default_lang);
}


function mergeWithBaseKeywords($keywords)
{
    $en_keys = getEnglishKeywords();
    foreach ($en_keys as $key => $value) {
        if (!isset($keywords[$key])) {
            $keywords[$key] = $value;
            continue;
        }

        foreach ($value as $k => $v) {
            if (isset($keywords[$key][$k]) && is_array($keywords[$key][$k])) {
                $mul = array_merge($keywords[$key][$k], $v);
                $keywords[$key][$k] = array_unique($mul);
            } elseif (!isset($keywords[$key][$k])) {
                $keywords[$key][$k] = $v;
            }
        }
    }

    return $keywords;
}

function getEnglishKeywords()
{
    return include dirname(__DIR__, 2) . '/lang/en_EN-keys.php';
}

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


function startsWith(string $haystack, string $needle, bool $case = true): bool
{
    if ($case) {
        return strpos($haystack, $needle, 0) === 0;
    }

    return stripos($haystack, $needle, 0) === 0;
}

function endsWith(string $haystack, string $needle, bool $case = true): bool
{
    $expectedPosition = strlen($haystack) - strlen($needle);
    if ($case) {
        return strrpos($haystack, $needle, 0) === $expectedPosition;
    }

    return strripos($haystack, $needle, 0) === $expectedPosition;
}


function workflowUpdater($config = [])
{
    require_once dirname(__DIR__, 2) . '/alfred/updater.php';

    $default = [
        'plist_url' => 'https://raw.githubusercontent.com/biati-digital/alfred-calculate-anything/master/info.plist',
        'workflow_url' => 'https://github.com/biati-digital/alfred-calculate-anything/releases/latest/download/Calculate.Anything.alfredworkflow',
        'force_check' => false,
        'force_download' => false,
        'download_type' => 'async',
    ];
    $updater = new Alfred\Updater(array_merge($default, $config));

    return $updater->checkUpdates();
}


spl_autoload_register(function ($class) {
    $class = str_replace('_', '-', $class);
    $class = str_replace('\\CalculateAnything\\', '\\tools\\', $class);
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $class = strtolower($class) . '.php';

    $dir = dirname(__DIR__, 2);
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
