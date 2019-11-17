<?php
function get_settings()
{
    $file = get_settings_path();

    if (!file_exists($file)) {
        return [];
    }

    $content = file_get_contents($file);
    if (empty($content)) {
        return [];
    }

    return json_decode($content, true);
}

function get_setting($name, $default = false, $settings = false)
{
    if (!$settings) {
        $settings = get_settings();
    }

    if (isset($settings[$name])) {
        return $settings[$name];
    }

    if ($default) {
        return $default;
    }

    return null;
}

function save_setting($name, $value, $settings = false)
{
    if (!$settings) {
        $settings = get_settings();
    }

    $settings[$name] = $value;
    save_settings($settings);
    return true;
}

function save_settings($settings)
{
    $file = get_settings_path();
    file_put_contents($file, json_encode($settings));
}

function format_number($number, $round = false)
{
    if (fmod($number, 1) !== 0.00) {
        $decimals = 1;
        $string = ''. $number;
        $string = explode('.', $string);
        $string = str_split(end($string));
        $count = 1;

        // If string has 2 or more decimals make some cleanup
        if (count($string) >= 2) {
            $decimals = 2;

            foreach ($string as $order => $value) {
                $prev = (isset($string[$order - 1]) ? $string[$order - 1] : '');
                if ($value == '0') {
                    $count += 1;
                    continue;
                }
                if ($value !== '0' && $prev !== '0') {
                    $count += 1;
                    $end_digit = $value;
                    break;
                }
            }
            $decimals = $count;
        }

        if ($round) {
            return number_format($number, $decimals);
        }
        $number = bcdiv($number, 1, $decimals);
        return number_format($number, $decimals);
    } else {
        return number_format($number);
    }
}

function cleanup_number($number)
{
    return floatval(str_replace(',', '', $number));
}

function get_settings_path()
{
    return get_data_path('settings.json');
}

function get_data_path($ipath = '')
{
    $path = getenv('alfred_workflow_data');
    create_dir($path);

    if (!empty($ipath)) {
        return $path .'/'. $ipath;
    }
    return $path;
}

function create_dir($path)
{
    if (!file_exists($path)) {
        mkdir($path);
    }

    return true;
}

function get_var($array, $key, $default = null)
{
    if (is_array($array) && isset($array[$key]) && !empty($array[$key])) {
        return trim($array[$key]);
    }
    if (!is_null($default)) {
        return $default;
    }

    return '';
}


function get_translation($key = '', $lang = '')
{
    if (defined('LANG_STRINGS')) {
        $strings = LANG_STRINGS;
        if (empty($key)) {
            return $strings;
        }

        if (isset($strings[$key])) {
            return $strings[$key];
        }

        return false;
    }

    $default_lang = default_lang();
    $lang = (empty($lang) ? get_setting('language', $default_lang) : $lang);
    $translations = load_translations_file($lang);
    if ($translations) {
        // Return default lang if translation error
        if (!is_array($translations) || empty($translations)) {
            return get_translation($key, $default_lang);
        }

        if (empty($key)) {
            return $translations;
        }

        if (isset($translations[$key])) {
            return $translations[$key];
        }

        return false;
    }

    return get_translation($key, $default_lang);
}

function load_translations_file($lang)
{
    $file = __DIR__ . '/lang/' . $lang . '.php';
    if (file_exists($file)) {
        $data = include $file;
        return $data;
    }
    return false;
}

function get_text($key)
{
    $strings = get_translation('general');
    if (!is_array($strings) || !isset($strings[$key])) {
        return '';
    }
    return $strings[$key];
}

function default_lang()
{
    return 'en_EN';
}

function get_extra_keywords($key = '', $lang = '')
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

    $default_lang = default_lang();
    $lang = (empty($lang) ? get_setting('language', $default_lang) : $lang);
    $file = __DIR__ . '/lang/' . $lang . '-keys.php';

    if (file_exists($file)) {
        $translations = include $file;

        // Return default lang if translation error
        if (!is_array($translations) || empty($translations)) {
            return get_extra_keywords($key, $default_lang);
        }

        // If language is different
        // from english, also load the en keys
        // so they are global
        if ($lang !== $default_lang) {
            $translations = merge_with_base_keywords($translations);
        }

        if (empty($key)) {
            return $translations;
        }

        if (isset($translations[$key])) {
            return $translations[$key];
        }

        return false;
    }

    return get_extra_keywords($key, $default_lang);
}


function merge_with_base_keywords($keywords)
{
    $en_keys = get_en_keywords();
    foreach ($en_keys as $key => $value) {
        if (!isset($keywords[$key])) {
            $keywords[$key] = $value;
            continue;
        }

        foreach ($value as $k => $v) {
            if (isset($keywords[$key][$k]) && is_array($keywords[$key][$k])) {
                $mul = array_merge($keywords[$key][$k], $v);
                $keywords[$key][$k] = array_unique($mul);
            }
            elseif (!isset($keywords[$key][$k])) {
                $keywords[$key][$k] = $v;
            }
        }
    }

    return $keywords;
}


function get_en_keywords()
{
    return include __DIR__ . '/lang/en_EN-keys.php';
}


function get_stopwords_string($words, $spaced = false)
{
    $sep = ($spaced ? ' | ' : '|');
    if (is_bool($spaced)) {
        $str = implode($sep, $words);
    }
    if (is_string($spaced)) {
        $w = [];
        foreach ($words as $word) {
            $w[] = sprintf($spaced, $word);
        }
        $str = implode('|', $w);
    }
    return '(' . $str . ')';
}


function clean_query($query)
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
    $clean = urldecode($clean);
    $clean = preg_replace('!\s+!', ' ', $clean);

    return $clean;
}
