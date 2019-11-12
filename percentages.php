<?php

/**
 * Percentages
 * handle percentage calculations
 * getting the values from the query
 *
 * for example:
 * 45% of 900 = 405
 * 100 + 16% = 116
 * 100 plus 16% = 116
 */


function process_percentages($query)
{
    $keys = get_extra_keywords('percentage');
    $stop_words = get_stopwords_string($keys['stop_words']);
    $from = array_keys($keys);
    $to = array_values($keys);

    $from[] = 'plus';
    $to[] = '+';
    $from[] = 'minus';
    $to[] = '-';

    $query = str_replace(
        $from,
        $to,
        $query
    );

    // Calculate Percentage of value
    // 30% de 100 = 30
    // if (preg_match('/^\d+% ?'. $stop_words .'? ?(\d+)?/', $query, $matches)) {
    if (preg_match('/^\d*\.?\d*% ?'. $stop_words .'? ?(\d+)?/', $query, $matches)) {
        // $value = percentage_of($query);
        // $process = true;
        return ['value' => percentage_of($query), 'process' => true];
    }

    // Total plus percentage
    // 100 + 16% = 116
    elseif (preg_match('/\d+ +?\+.+?\d+%$/', $query, $matches)) {
        // $value = total_plus_percentage($query);
        // $process = true;
        return ['value' => total_plus_percentage($query), 'process' => true];
    }

    // Total minus percentage
    // 116 - 16% = 100
    elseif (preg_match('/\d+ +?\- +?\d+%$/', $query, $matches)) {
        // $value = total_minus_percentage($query);
        // $process = true;
        return ['value' => total_minus_percentage($query), 'process' => true];
    }

    // Calculates `a` percent of `b` is what percent?
    // 30 % 40 = 75%
    // So 30 is 75% of 40.
    elseif (preg_match('/\d+ +?\%.+?\d+/', $query, $matches)) {
        // $value = percent_of_two_numbers($query);
        // $process = true;
        return ['value' => percent_of_two_numbers($query), 'process' => true];
    }

    return false;
}


function percentage_of($query)
{
    $query = preg_replace("/[^0-9.%]/", ' ', $query);
    $query = preg_replace('!\s+!', ' ', $query);
    $data = explode(' ', $query);

    if (count($data) < 2) {
        return false;
    }
    $percent = cleanup_number($data[0]);
    $amount = cleanup_number($data[1]);

    return format_number(($percent / 100) * $amount);
}


function total_plus_percentage($query)
{
    $query = preg_replace("/ +?\+ +?/", ' ', $query);
    $data = explode(' ', $query);

    if (count($data) < 2) {
        return false;
    }

    $amount = cleanup_number($data[0]);
    $percent = cleanup_number($data[1]);

    return format_number($amount + (($percent / 100) * $amount));
}


function total_minus_percentage($query)
{
    $query = preg_replace("/ +?\- +?/", ' ', $query);
    $data = explode(' ', $query);

    if (count($data) < 2) {
        return false;
    }

    $amount = cleanup_number($data[0]);
    $percent = cleanup_number($data[1]);

    $result = $percent == 100 ? '0.00' : format_number($amount - $amount * ((float) "0.$percent"));
    $saved = $amount - cleanup_number($result);
    $famount = format_number($amount);
    $saved = format_number($saved);

    $values = [];
    $values[$result] = $famount .' - '. $data[1] .' = '. $result;
    $values[$saved] = $famount .' - '. $result .' = '. $saved;
    return $values;
}


function percent_of_two_numbers($query)
{
    $query = preg_replace("/ +?\% +?/", ' ', $query);
    $query = preg_replace('!\s+!', ' ', $query);
    $data = explode(' ', $query);

    if (count($data) < 2) {
        return false;
    }

    $val1 = cleanup_number($data[0]);
    $val2 = cleanup_number($data[1]);
    $percentage = ($val1 / $val2) * 100;
    $percentage = format_number($percentage);

    $pincrease = ($val2 - $val1) / $val1 * 100;
    $pincrease = format_number($pincrease);

    $pdecrease = ($val2 - $val1) / $val2 * 100;
    $pdecrease = format_number($pdecrease);
    $lang_strings = LANG_STRINGS['percentage'];

    $values = [];
    $values["{$percentage}%"] = sprintf($lang_strings['result'], $val1, "{$percentage}%", $val2);
    $values["{$pincrease}%"] = sprintf($lang_strings['increase'], $val1, $val2, "{$pincrease}%");
    $values["{$pdecrease}%"] = sprintf($lang_strings['decrease'], $val2, $val1, "{$pdecrease}%");

    return $values;
}
