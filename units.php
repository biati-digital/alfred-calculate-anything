<?php

/**
 * Units
 * Handle units conversions
 * for example 10km to cm
 * 100 years to days
 * 100year s
 */

require_once(__DIR__ . '/units/Exceptions/ConvertorInvalidUnitException.php');
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
            'dm3',
            'l',
            'ml',
            'cm3',
            'hl',
            'kl',
            'm3',
            'pt',
            'gal',
            'qt',
            'ft3',
            'in3',
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
            'fps',
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
    $stopwords = unit_stopwords();
    $query = str_replace(',', '', $query);
    return preg_match('/^\d*\.?\d+ ?' . $units . ' ?' . $stopwords . '?/i', $query, $matches);
}


/**
 * Check if unit is valid
 *
 * @param string $unit
 * @return boolean
 */
function is_valid_unit($unit)
{
    $units = available_units();
    $found = false;
    foreach ($units as $key => $value) {
        if (in_array($unit, $value)) {
            $found = true;
            break;
        }
    }
    return $found;
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
    $unit = str_replace('**', '', $unit);
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
    // $regex = available_units_regex();
    $query = str_replace(',', '', $query);

    preg_match('/^(\d*\.?\d+)[^\d]/i', $query, $amount_match);
    if (empty($amount_match)) {
        return false;
    }

    $amount = get_var($amount_match, 1);
    $amount = trim($amount);
    $string = str_replace($amount, '', $query);
    $string = trim($string);

    preg_match('/(.*).*' . $stopwords . '(.*)/i', $string, $matches);

    // Matches strings like 100 kilograms to ounces
    if (!empty($matches)) {
        $matches = array_values(array_filter($matches));
        $from = get_var($matches, 1);
        $to = get_var($matches, 3);
    }

    elseif (empty($matches)) {
        $keywords = get_extra_keywords('units');

        foreach ($keywords as $key => $value) {
            if (is_array($value)) {
                continue;
            }
            $key = escape_units_keywords($key);
            $string = preg_replace('/(^|\W)' . $key . '(\W|$)/i', ' ' . $value . ' ', $string);
        }

        $string = preg_replace('!\s+!', ' ', $string);
        $string = trim($string);
        $data = explode(' ', $string);
        $from = get_var($data, 0);
        $to = get_var($data, 1);
    }

    if (empty($from) || empty($to)) {
        return false;
    }

    if (empty($from) || empty($to)) {
        return false;
    }

    return make_unit_conversion([
        'from_amount' => cleanup_number($amount),
        'from_unit' => cleanup_unit($from),
        'to' => cleanup_unit($to),
    ]);
}


/**
 * Make actual conversion
 *
 * @param array $data
 * @return mixed
 */
function make_unit_conversion($data)
{
    $from_amount = $data['from_amount'];
    $from_unit = $data['from_unit'];
    $to = $data['to'];
    $from_unit_type = get_unit_type($from_unit);
    $to_unit_type = get_unit_type($to);

    if (empty($from_unit_type) || empty($to_unit_type)) {
        return false;
    }

    if ($to_unit_type !== $from_unit_type) {
        $units_str = get_translation('units');
        return sprintf($units_str['error'], standard_unit($from_unit), standard_unit($to));
    }

    if ($from_unit == 'year' && $to == 'month') {
        $converted = $from_amount * 12;
    } else {
        $conversion_error = false;
        try {
            $convert = new Convertor($from_amount, $from_unit);
            $converted = $convert->to($to);
        } catch (\Throwable $th) {
            $conversion_error = $th->getMessage();
        }

        if ($conversion_error) {
            return $conversion_error;
        }
    }

    $decimals = -1;
    if ($from_unit_type == 'temperature') {
        $decimals = 1;
    }

    // Before displaying the result
    // Convert some units to readable human form
    if ($from_unit_type == 'time') {
        $time_human_units = ['seconds', 'years', 'months', 'weeks', 'days', 'hours', 'minutes', 'milliseconds'];
        if ($converted > 1) {
            $to  = str_replace(
                ['s', 'year', 'month', 'week', 'day', 'hr', 'min', 'ms'],
                $time_human_units,
                $to
            );
        }
        $strings = get_translation('time');
        if (is_array($strings) && isset($strings[$to])) {
            $to = $strings[$to];
        }
        $to = ' ' . $to;
    }

    return format_number($converted, $decimals) . standard_unit($to);
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
    $unit = trim($val);
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
    $keywords = get_extra_keywords('units');
    if (!$unit) {
        return $keywords;
    }

    if (!is_valid_unit($unit)) {
        foreach ($keywords as $key => $value) {
            if (is_array($value)) {
                continue;
            }
            $key = escape_currency_keywords($key);
            $unit = preg_replace('/(^|\W)' . $key . '(\W|$)/i', ' ' . $value . ' ', $unit);
        }
    }

    $unit = trim($unit);
    $unit = preg_replace('!\s+!', ' ', $unit);
    if (ends_with($unit, '2')) {
        $unit = str_replace('2', '**2', $unit);
    }

    return $unit;
}


function standard_unit($unit)
{
    return str_replace('**', '', $unit);
}


function escape_units_keywords($key)
{
    $key = str_replace('$', '\$', $key);
    $key = str_replace('/', '\/', $key);
    $key = str_replace('.', '\.', $key);
    return $key;
}
