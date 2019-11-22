<?php

/**
 * Time
 * handle time conversions
 * congigure by calling:
 *
 * calculate set base timezone America/Los_Angeles
 * calculate add timezone F jS, Y, g:i:s a
 * calculate delete timezone
 *
 * It uses the global language configured with
 * calculate set language en
 */

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/functions.php';

use Jenssegers\Date\Date;

$settings = get_settings();
$response = [];
$timezone = get_var($argv, 2, get_setting('time_zone', 'America/Los_Angeles', $settings));
$query    = get_var($argv, 1, 'now');
$lang     = get_setting('language', default_lang(), $settings);
$strings  = get_translation('time');

define('TIME_ZONE', $timezone);
define('LANGUAGE', $lang);
define('STRINGS', $strings);


/**
 * Is timestamp
 * check if passed query is timestamp
 *
 * @param mixed $timestamp
 * @return boolean
 */
function is_timestamp($timestamp)
{
    if (!is_numeric($timestamp)) {
        return false;
    }
    return ((string) (int) $timestamp === $timestamp)
        && ($timestamp <= PHP_INT_MAX)
        && ($timestamp >= ~PHP_INT_MAX);
}

if (!empty($query) && is_timestamp($query)) {
    $query = (int) $query;
}


/**
 * Get date instance
 * return a date instance with
 * the specified time
 *
 * @param string $time
 * @return object
 */
function get_date($time = 'now')
{
    $d = false;
    try {
        $d = new Date($time, new DateTimeZone(TIME_ZONE));
    } catch (\Throwable $th) {
        throw $th;
    }
    return $d;
}


/**
 * Time diffrence
 * get the time difference between
 * to dates in the specified format
 *
 * @param string $time1
 * @param string $time2
 * @param string $format
 * @return string
 */
function times_difference($time1, $time2, $format = 'hours')
{
    $time1 = new Date($time1, new DateTimeZone(TIME_ZONE));
    $time2 = new Date($time2, new DateTimeZone(TIME_ZONE));

    if ($format == 'days') {
        $diff_hours = $time1->diffInHours($time2);
        if ($diff_hours < 24) {
            return $diff_hours;
        }

        return round($diff_hours / 24);
    }
    if ($format == 'hours') {
        return $time1->diffInHours($time2);
    }
    if ($format == 'minutes') {
        return $time1->diffInMinutes($time2);
    }
    if ($format == 'seconds') {
        return $time1->diffInSeconds($time2);
    }
    if ($format == 'milliseconds') {
        return $time1->diffInMilliseconds($time2);
    }
    if ($format == 'microseconds') {
        return $time1->diffInMicroseconds($time2);
    }
}

/**
 * Translate on begin
 * given a query like
 * +30 días
 * it need to be converted to
 * +30 days so the code can understand it
 *
 * @param string $query
 * @return string
 */
function translate_date($query)
{
    $query = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $query);
    $strs = STRINGS;

    if (is_numeric($query) || empty($strs)) {
        return $query;
    }

    $query = mb_strtolower($query, 'UTF-8');
    $keys = get_extra_keywords('time');
    foreach ($keys as $k => $value) {
        if (is_array($value)) {
            continue;
        }
        $query = str_replace($k, $value, $query);
    }

    $query = str_replace(
        array_values($strs),
        array_keys($strs),
        $query
    );

    return $query;
}


/**
 * Time keywords
 * some keywords used to
 * trigger diferent actions
 *
 * @param string $check
 * @return mixed
 */
function time_keywords($check = '')
{
    $data = [
        'until' => [
            'hasta'
        ],
        'between' => [
            'entre'
        ],
        'start of' => [
            'inicio de'
        ],
        'end of' => [
            'fin de'
        ],
    ];

    if (isset($data[$check])) {
        return $data[$check];
    }

    return $data;
}


/**
 * Regex
 * create a regex based on the keywords
 *
 * @param string $check
 * @return string
 */
function available_time_keywords_regex($check)
{
    $keys = time_keywords($check);
    $params = implode('|', array_values($keys));
    return '('. $check.'|' . $params . ')';
}

/**
 * Time Until
 * check if query ia a until query
 *
 * @param string $time
 * @return mixed
 */
function date_is_untill($time)
{
    $keys = available_time_keywords_regex('until');
    preg_match('/\w+ ' . $keys . ' .*/i', $time, $matches);

    if (!$matches || empty($matches)) {
        return false;
    }

    $time = preg_replace('/ ' . $keys . ' /i', '|', $time);
    $time = explode('|', $time);
    $time = array_filter($time);

    $k = (isset($time[0]) && !empty($time[0]) ? $time[0] : false);
    $date = (isset($time[1]) && !empty($time[1]) ? $time[1] : false);
    $valid_k = ['days', 'day', 'hours', 'hour', 'minutes', 'minutes', 'seconds', 'second'];

    if (!in_array($k, $valid_k) || !$date) {
        return false;
    }

    return ['get' => $k, 'time' => $date];
}

/**
 * Time end of
 * check if query ia a until query end
 *
 * @param string $time
 * @return mixed
 */
function date_is_endof($time)
{
    $keys = available_time_keywords_regex('end of');
    preg_match('/^' . $keys . ' .*/i', $time, $matches);

    if (!$matches || empty($matches)) {
        return false;
    }

    $time = preg_replace('/^' . $keys . '/i', '', $time);
    $time = trim($time);
    if ($time == 'year') {
        $year = get_date()->format('Y');
        $time = $year . '-12-31 23:59:59';
    }
    elseif (is_numeric($time) && strlen($time) == 4) {
        $time = $time . '-12-31 23:59:59';
    }
    return $time;
}

/**
 * Time start of
 * check if query ia a until query start
 *
 * @param string $time
 * @return mixed
 */
function date_is_startof($time)
{
    $keys = available_time_keywords_regex('start of');
    preg_match('/^' . $keys . ' .*/i', $time, $matches);

    if (!$matches || empty($matches)) {
        return false;
    }

    $time = preg_replace('/^' . $keys . '/i', '', $time);
    $time = trim($time);
    if ($time == 'year') {
        $time = 'first day of January this year';
    }
    else {
        if (is_numeric($time) && strlen($time) == 4) {
            $time = $time.'-01-01';
        }
    }

    return $time;
}


/**
 * Process
 * Process the query and perform
 * the correct action
 *
 * @param string $time
 * @return mixed array if success or false if not processed
 */
function process_time($query)
{
    Date::setLocale(LANGUAGE);

    // From user lang to en_us so time conversion
    // is able to understand some words
    $query = translate_date($query);
    $strings = STRINGS;

    // handle two dates like: 25 December, 2019 - 31 December, 2019
    if (strpos($query, ' - ') !== false) {
        $data = str_replace(' - ', '|', $query);
        $data = explode('|', $data);
        $data = array_filter($data);

        if (count($data) == 2) {
            $time1 = get_date(trim($data[0]));
            $time2 = get_date(trim($data[1]));
            $subtitle = sprintf($strings['difference_subtitle'], $time1, $time2);

            if ($time1 && $time2) {
                return [
                    'title' => $time1->timespan($time2),
                    'val' => $time1->timespan($time2),
                    'subtitle' => $subtitle,
                ];
            }

            return false;
        }
    }

    // Handle Until
    elseif ($until = date_is_untill($query)) {
        $utime = $until['time'];
        $get_tr = $until['get'];
        $check = times_difference('now', $utime, $until['get']);
        $title = $check . ' ' . $get_tr;
        $subtitle = sprintf($strings['until_subtitle'], $get_tr, $utime);

        $title = str_replace(
            array_keys($strings),
            array_values($strings),
            $title
        );

        $subtitle = str_replace(
            array_keys($strings),
            array_values($strings),
            $subtitle
        );

        if ($check) {
            return [
                'title' => $title,
                'val' => $check,
                'subtitle' => $subtitle,
            ];
        }

        return false;
    }

    // Handle End of
    elseif ($endof = date_is_endof($query)) {
        $end = get_date($endof);
        if ($end) {
            return [
                'instance' => $end
            ];
        }

        return false;
    }

    // Handle Start of
    elseif ($startof = date_is_startof($query)) {
        $start = get_date($startof);
        if ($start) {
            return [
                'instance' => $start
            ];
        }

        return false;
    }

    elseif (is_timestamp($query)) {
        $query = (int) $query;
    }

    $processed = get_date($query);
    if ($processed) {
        return [
            'instance' => $processed
        ];
    }

    return false;
}



$processed_date = process_time($query);
if (!$processed_date) {
    $response[] = [
        "title" => '...',
        "subtitle" => $strings['notvalid_subtitle'],
        "valid" => false,
    ];
    echo '{"items": ' . json_encode($response) . ' }';
    exit(0);
}

$instance = (isset($processed_date['instance']) ? $processed_date['instance'] : false);

if (!$instance) {
    $response[] = [
        "title" => $processed_date['title'],
        "subtitle" => $processed_date['subtitle'],
        "arg" => $processed_date['val'],
        "mods" => [
            "cmd" => [
                "valid" => true,
                "arg" => $processed_date['val'],
                "subtitle" => "Action this item to copy this value to the clipboard",
            ]
        ]
    ];
    echo '{"items": ' . json_encode($response) . ' }';
    exit(0);
}


$formats = get_setting('timezones', '', $settings);
if (empty($formats)) {
    $formats = ['F jS, Y, g:i:s a'];
}

foreach ($formats as $key => $format) {
    $date = $instance->format($format);
    $response[] = [
        "title" => $date,
        "subtitle" => sprintf($strings['format_subtitle'], $format),
        "arg" => $date,
        "mods" => [
            "cmd" => [
                "valid" => true,
                "arg" => $date,
                "subtitle" => "Action this item to copy this value to the clipboard",
            ]
        ]
    ];
}

if ($query !== 'now') {
    $now = Date::now();
    if ($now > $instance) {
        $count = $instance->ago();
    } else {
        $count = $instance->timespan($now);
    }

    $response[] = [
        "title" => $count,
        "subtitle" => "Timespan",
        "arg" => $count,
        "mods" => [
            "cmd" => [
                "valid" => true,
                "arg" => $count,
                "subtitle" => "Action this item to copy this value to the clipboard",
            ]
        ]
    ];
}


$response[] = [
    "title" => $instance->getTimestamp(),
    "subtitle" => "Timestamp",
    "arg" => $instance->getTimestamp(),
    "mods" => [
        "cmd" => [
            "valid" => true,
            "arg" => $instance->getTimestamp(),
            "subtitle" => "Action this item to copy this value to the clipboard",
        ]
    ]
];

echo '{"items": ' . json_encode($response) . ' }';
