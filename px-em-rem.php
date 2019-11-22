<?php

/**
 * Pixels Rem Em Pt
 * Handle pixels em and rem conversions
 * for example 1px to rem
 * or simply 1px
 *
 * Examples:
 * 18px to em base 20px
 * 18px base 20px
 * 18px to em
 * 18px to rem
 * 10px to pt
 * 10px
 * 10em
 * ...
 */

function is_pxemrem($query)
{
    return preg_match('/^\d*\.?\d+ ?(px|em|rem|pt) ?/', $query, $matches);
}


/**
 * Process
 * handle process
 *
 * @param string $query
 * @return mixed
 */
function process_pxemrem($query)
{
    $keys = get_extra_keywords('units');
    $stop_words = get_stopwords_string($keys['stop_words'], ' %s ');
    $query = preg_replace("/ ?" . $stop_words . " ?/i", ' ', $query);
    $query = preg_replace('!\s+!', ' ', $query);

    preg_match('/^(\d*\.?\d+) ?(px|em|rem|pt) ?'. $stop_words .'? ?(px|em|rem|pt)? ?(base.*px$)?/', $query, $matches);
    $matches = array_values(array_filter($matches));

    $from = 0;
    $from_unit = '';
    $total_inputs = count($matches);
    $target = '';
    $base = get_setting('base_pixels', '16px');
    $base = cleanup_number($base);

    if ($total_inputs >= 3) {
        $from = cleanup_number($matches[1]);
        $from_unit = trim($matches[2]);
    }

    if ($total_inputs == 4) {
        if (strpos($matches[3], 'base') !== false) {
            $base = preg_replace('/[^0-9]/', '', $matches[3]);
            $base = cleanup_number($base);
        }
        else {
            $target = trim($matches[3]);
        }
    }
    elseif ($total_inputs == 5) {
        $target = trim($matches[3]);
        $base = preg_replace('/[^0-9]/', '', $matches[4]);
        $base = cleanup_number($base);
    }

    $result = [];
    $units = ['px', 'em', 'rem', 'pt'];
    $data = [
        'from' => $from,
        'from_unit' => $from_unit,
        'to' => $target,
        'base' => $base,
    ];

    if (empty($target)) {
        if (($key = array_search($from_unit, $units)) !== false) {
            unset($units[$key]);
        }
        foreach ($units as $key => $value) {
            $data['to'] = $value;
            $val = calculate_font_size($data);
            $result[$val] = $val;
        }
    } else {
        $val = calculate_font_size($data);
        $result[$val] = $val;
    }

    return $result;
}


/**
 * Do font calculations
 *
 * @param array $data
 * @return string
 */
function calculate_font_size($data)
{
    $result = 0;
    $from = $data['from'];
    $from_unit = $data['from_unit'];
    $to = $data['to'];
    $base = $data['base'];
    $emrem = ['em', 'rem'];
    $pt = 0.75;

    // exit if no required action
    if ($from_unit == $to || (in_array($from_unit, $emrem) && in_array($to, $emrem))) {
        return $from . $to;
    }

    // from px
    if ($from_unit == 'px') {
        if ($to == 'pt') {
            return ($from * $pt) . $to;
        }
        if (in_array($to, $emrem)) {
            return ($from / $base) . $to;
        }
    }

    // from pt
    if ($from_unit == 'pt') {
        if ($to == 'px') {
            return ($from / $pt) . $to;
        } elseif (in_array($to, $emrem)) {
            return (($from / $pt) / $base) . $to;
        }
    }

    // from em/rem
    if (in_array($from_unit, $emrem)) {
        if ($to == 'px') {
            return ($from * $base) . $to;
        }
        if ($to == 'pt') {
            return (($from * $base) * $pt) . $to;
        }
    }

    return $result;
}
