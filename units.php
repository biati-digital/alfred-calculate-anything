<?php

/**
 * Units
 * Handle units conversions
 * for example 10km to cm
 * 100 years to days
 * 100year s
 */

require_once(__DIR__ . '/units/Convertor.php');

use Olifolkerd\Convertor\Convertor;


/**
 * List of available units
 *
 * @return array
 */
function available_units()
{
    return [
        'length' => [
            'm',
            'km',
            'dm',
            'cm',
            'mm',
            'μm',
            'nm',
            'pm',
            'in',
            'ft',
            'yd',
            'mi',
            'h',
            'ly',
            'au',
            'pc',
        ],
        'area' => [
            'm2',
            'km2',
            'cm2',
            'mm2',
            'ft2',
            'mi2',
            'ac',
            'ha',
            'ha',
        ],
        'volume' => [
            'l',
            'ml',
            'm3',
            'pt',
            'gal',
        ],
        'weight' => [
            'kg',
            'g',
            'mg',
            'N',
            'st',
            'lb',
            'oz',
            't',
            'ukt',
            'ust',
        ],
        'speed' => [
            'mps',
            'kph',
            'mph',
        ],
        'rotation' => [
            'deg',
            'rad',
        ],
        'temperature' => [
            'k',
            'c',
            'f',
        ],
        'pressure' => [
            'pa',
            'kpa',
            'mpa',
            'bar',
            'mbar',
            'psi',
        ],
        'time' => [
            's',
            'year',
            'month',
            'week',
            'day',
            'hr',
            'min',
            'ms',
            'μs',
            'ns',
        ],
        'energy' => [
            'j',
            'kj',
            'mj',
            'cal',
            'Nm',
            'ftlb',
            'whr',
            'kwhr',
            'mwhr',
            'mev',
        ],
    ];
}


/**
 * Get units list
 * get a readable units list
 * to display to the user
 *
 * @return array
 */
function get_units_list()
{
    $translation = get_translation('units');
    $units = available_units();
    $list = [];
    foreach ($units as $key => $value) {
        $type = $key;
        $type_name = (isset($translation[$type]) ? $translation[$type] : $type);

        foreach ($value as $val) {
            $unit = $val;
            $unit_name = (isset($translation[$val]) ? $translation[$val] : $val);

            $list[] = [
                'title' => "$unit_name = $val",
                'subtitle' => $key,
                'subtitle' => sprintf($translation['belongs_to'], $val, $type_name),
                'match' => $val . '  ' . $unit_name,
                'autocomplete' => $unit_name,
                'arg' => $val,
                'valid' => true,
                'mods' => [
                    'cmd' => [
                        'valid' => true,
                        'arg' => $val,
                        'subtitle' => get_text('action_copy'),
                    ]
                ],
            ];
        }
    }

    return $list;
}


/**
 * Regex
 * get a scaped regex based on
 * the available units
 *
 * @return string
 */
function available_units_regex()
{
    $units = available_units();
    $params = [];
    foreach ($units as $key => $value) {
        $params[] = implode(' |', $value);
    }

    $translated_units = translated_units();
    $params[] = implode(' |', array_keys($translated_units));

    $params = implode('|', $params);
    $params = str_replace('$', '\$', $params);
    $params = str_replace('/', '\/', $params);
    $params = str_replace('.', '\.', $params);

    return '(' . $params . ')';
}


/**
 * is unit
 * check if given string is unit
 *
 * @param string $query
 * @return boolean
 */
function is_unit($query)
{
    $units = available_units_regex();
    // $query = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $query);
    // $units = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $units);
    // $units = str_replace('~', '', $units);
    $stopwords = unit_stopwords();
    return preg_match('/^\d*\.?\d+ ?' . $units . ' ?' . $stopwords . '?/i', $query, $matches);
}


/**
 * Unit stop words
 * words that can be used in the query
 * when using natural languge like
 * 100km to cm - here the word "to" is a stop word
 *
 * @param mixed $sep
 * @return string
 */
function unit_stopwords($sep = false)
{
    $keys = get_extra_keywords('units');
    $stop_words = get_stopwords_string($keys['stop_words'], $sep);

    return $stop_words;
}


/**
 * Unit type
 * return the type of the unit
 * for example km = length, kph = speed, etc.
 *
 * @param string $unit
 * @return mixed string if found
 */
function get_unit_type($unit)
{
    $units = available_units();
    $found = false;
    foreach ($units as $key => $value) {
        if (in_array($unit, $value)) {
            $found = $key;
            break;
        }
    }

    return $found;
}


/**
 * Process
 * process conversion
 *
 * @param string $query
 * @return mixed
 */
function process_unit_conversion($query)
{
    $stopwords = unit_stopwords(' %s ');
    $query = preg_replace('!\s+!', ' ', $query);
    $query = preg_replace("/ ?" . $stopwords . " ?/i", ' ', $query);
    $data = explode(' ', $query);

    if (count($data) < 2) {
        return false;
    }

    if (count($data) == 2) {
        $from = trim($data[0]);
        $to = trim($data[1]);
    }

    if (count($data) == 3) {
        $from = trim($data[0]) . trim($data[1]);
        $to = trim($data[2]);
    }
    if (empty($from) || empty($to)) {
        return false;
    }

    $from_amount = preg_replace('/[^0-9.]/', '', $from);
    $from_unit = cleanup_unit(preg_replace('/[0-9.]+/', '', $from));
    $from_unit_type = get_unit_type($from_unit);

    $to = cleanup_unit($to);
    $to_unit_type = get_unit_type($to);

    if (!$from_unit_type || !$to_unit_type) {
        return false;
    }

    if ($to_unit_type !== $from_unit_type) {
        $units_str = get_translation('units');
        return sprintf($units_str['error'], $from_unit, $to);
    }

    $from_amount = floatval($from_amount);

    if ($from_unit == 'year' && $to == 'month') {
        $converted = $from_amount * 12;
    }
    else {
        $convert = new Convertor($from_amount, $from_unit);
        $converted = $convert->to($to);
    }

    $decimals = 2;
    if ($from_unit_type == 'temperature') {
        $decimals = 1;
    }
    if ($from_unit_type == 'time') {
        if ($converted > 1) {
            $to  = str_replace(
                ['s', 'year', 'month', 'week', 'day', 'hr', 'min', 'ms'],
                ['seconds', 'years', 'months', 'weeks', 'days', 'hours', 'minutes', 'milliseconds'],
                $to
            );
        }

        $strings = get_translation('time');

        $to  = str_replace(
            array_keys($strings),
            array_values($strings),
            $to
        );

        $to = ' ' . $to;
    }

    return (fmod($converted, 1) !== 0.00 ? bcdiv($converted, 1, $decimals) : number_format($converted)) . $to;
}


/**
 * Clean unit
 * clean up the unit
 *
 * @param string $val
 * @return string
 */
function cleanup_unit($val)
{
    $unit = mb_strtolower($val, 'UTF-8');
    $unit = trim($unit);
    $unit = translated_units($unit);

    return $unit;
}


/**
 * Translated units
 * Convert some keywords to
 * the actual unit so you can use
 * natual language to make conversions
 *
 * For example:
 * 100 kilometers to meters
 * Will be converted to
 * 100km to m
 *
 * Still the user can be able
 * to write 100hr to s
 *
 * The keywords list can be found in
 * /lang/{lang}-keys.php
 *
 * @param boolean $unit
 * @return array
 */
function translated_units($unit = false)
{
    $unit = ($unit ? iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $unit) : $unit);
    $units = [];
    $keys = get_extra_keywords('units');

    if (!empty($keys)) {
        $units = array_merge($units, $keys);
    }

    if (!$unit) {
        return $units;
    }

    if (isset($units[$unit])) {
        return $units[$unit];
    }

    return $unit;
}
