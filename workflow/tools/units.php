<?php

namespace Workflow\Tools;

use Workflow\CalculateAnything as CalculateAnything;
use Olifolkerd\Convertor\Convertor;

/**
 * Units conversion
 * 100 ounces to kilograms
 * 100oz to kg
 * 100oz = kg
 * 100oz kg
 * 10 years to months
 * 10years to seconds
 * 1 year to sec
 * 1hr s
 * 10 días a horas (use your own language)
 * ...
 */

class Units extends CalculateAnything implements CalculatorInterface
{
    private $query;
    private $stop_words;
    private $keywords;
    private $unitsList;
    private $lang;

    /**
     * Construct
     */
    public function __construct($query)
    {
        $this->query = str_replace(',', '', $query);
        $this->lang = $this->getTranslation('units');
        $this->keywords = $this->getKeywords('units');
        $this->stop_words = $this->getStopWords('units');
        $this->unitsList = $this->availableUnits();
    }

    /**
     * List of available units
     *
     * @return array
     */
    private function availableUnits()
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
     * shouldProcess
     *
     * @param string $query
     * @param integer $strlenght
     * @return bool
     */
    public function shouldProcess(int $strlenght = 0)
    {
        if ($strlenght <= 3) {
            return false;
        }

        $query = $this->query;
        $units = $this->matchRegex();
        $stopwords = $this->getStopWordsString($this->stop_words);

        return preg_match('/^\d*\.?\d+ ?' . $units . ' ?' . $stopwords . '?/i', $query, $matches);
    }


    /**
     * Process query
     *
     * @return string|array
     */
    public function processQuery()
    {
        $query = $this->query;
        $data = $this->extractQueryData($query);

        return $this->output($this->convert($data));
    }

    /**
     * Output
     * build the output the way
     * it should be displayed by Alfred
     *
     * @param array $result
     * @return array
     */
    public function output($result)
    {
        $items = [
            'title' => $result,
            'arg' => $result,
            'subtitle' => $this->getText('action_copy'),
            'mods' => [
                'cmd' => [
                    'valid' => true,
                    'arg' => $result,
                    'subtitle' => $this->lang['cmd'],
                ],
                'alt' => [
                    'valid' => true,
                    'arg' => $this->cleanupNumber($result),
                    'subtitle' => $this->lang['alt'],
                ],
            ]
        ];
        return $items;
    }

    /**
     * Make actual conversion
     *
     * @param array $data
     * @return mixed
     */
    public function convert($data)
    {
        $from = $data['from'];
        $to = $data['to'];
        $amount = $data['amount'];
        $from_type = $this->getUnitType($from);
        $to_type = $this->getUnitType($to);

        if (empty($from_type) || empty($to_type)) {
            return false;
        }

        if ($to_type !== $from_type) {
            $units_str = $this->getTranslation('units');
            return sprintf($units_str['error'], $this->standardUnit($from), $this->standardUnit($to));
        }

        if ($from == 'year' && $to == 'month') {
            $converted = $amount * 12;
        } else {
            $conversion_error = false;
            try {
                $convert = new Convertor($amount, $from);
                $converted = $convert->to($to);
            } catch (\Throwable $th) {
                $conversion_error = $th->getMessage();
            }

            if ($conversion_error) {
                return $conversion_error;
            }
        }

        $decimals = -1;
        if ($from_type == 'temperature') {
            $decimals = 1;
        }

        // Before displaying the result
        // Convert some units to readable human form
        if ($from_type == 'time') {
            $time_human_units = ['seconds', 'years', 'months', 'weeks', 'days', 'hours', 'minutes', 'milliseconds'];
            if ($converted > 1) {
                $to  = str_replace(
                    ['s', 'year', 'month', 'week', 'day', 'hr', 'min', 'ms'],
                    $time_human_units,
                    $to
                );
            }
            $strings = $this->getTranslation('time');
            if (is_array($strings) && isset($strings[$to])) {
                $to = $strings[$to];
            }
            $to = ' ' . $to;
        }

        return $this->formatNumber($converted, $decimals) . $this->standardUnit($to);
    }



    /**
     * Extract query data
     * extract the values from and to
     * from the query typed by the user
     * it returns from, to and amount
     */
    private function extractQueryData($query)
    {
        preg_match('/^(\d*\.?\d+)[^\d]/i', $query, $amount_match);
        if (empty($amount_match)) {
            return false;
        }

        $stopwords = $this->getStopWordsString($this->stop_words, ' %s ');
        $amount = getVar($amount_match, 1);
        $amount = trim($amount);
        $string = str_replace($amount, '', $query);
        $string = trim($string);

        preg_match('/(.*).*' . $stopwords . '(.*)/i', $string, $matches);

        // Matches strings like 100 kilograms to ounces
        if (!empty($matches)) {
            $matches = array_values(array_filter($matches));
            $from = getVar($matches, 1);
            $to = getVar($matches, 3);
        } elseif (empty($matches)) {
            $keywords = $this->keywords;

            foreach ($keywords as $key => $value) {
                if (is_array($value)) {
                    continue;
                }
                $key = $this->escapeKeywords($key);
                $string = preg_replace('/(^|\W)' . $key . '(\W|$)/i', ' ' . $value . ' ', $string);
            }

            $string = preg_replace('!\s+!', ' ', $string);
            $string = trim($string);
            $data = explode(' ', $string);
            $from = getVar($data, 0);
            $to = getVar($data, 1);
        }

        if (empty($from) || empty($to)) {
            return false;
        }

        return [
            'amount' => $this->cleanupNumber($amount),
            'from' => $this->cleanupUnit($from),
            'to' => $this->cleanupUnit($to),
        ];
    }


    /**
     * Unit type
     * return the type of the unit
     * for example km = length, kph = speed, etc.
     *
     * @param string $unit
     * @return mixed string if found
     */
    private function getUnitType($unit)
    {
        $unit = str_replace('**', '', $unit);
        $units = $this->unitsList;
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
     * Check if unit is valid
     *
     * @param string $unit
     * @return boolean
     */
    private function isValidUnit($unit)
    {
        $units = $this->unitsList;
        $found = false;
        foreach ($units as $value) {
            if (in_array($unit, $value)) {
                $found = true;
                break;
            }
        }
        return $found;
    }


    /**
     * Regex
     * create a regex from the
     * available units array
     *
     * @return string
     */
    private function matchRegex()
    {
        $units = $this->unitsList;
        $params = [];
        foreach ($units as $value) {
            $params[] = implode(' |', $value);
        }

        $translation_keywords = $this->keywords;
        if (!empty($translation_keywords)) {
            $params[] = implode(' |', array_keys($translation_keywords));
        }

        $params = implode('|', $params);
        $params = $this->escapeKeywords($params);

        return '(' . $params . ')';
    }

    /**
     * Clean unit
     * clean up the unit
     *
     * @param string $val
     * @return string
     */
    private function cleanupUnit($val)
    {
        $unit = trim($val);
        $unit = $this->keywordTranslation($unit, $this->keywords);

        $unit = trim($unit);
        $unit = preg_replace('!\s+!', ' ', $unit);
        if (endsWith($unit, '2')) {
            $unit = str_replace('2', '**2', $unit);
        }

        return $unit;
    }

    private function standardUnit($unit)
    {
        return str_replace('**', '', $unit);
    }


    /**
     * Get units list
     * get a readable units list
     * to display to the user
     *
     * @return array
     */
    public function listAvailable()
    {
        $translation = $this->getTranslation('units');
        $units = $this->unitsList;
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
                            'subtitle' => $this->getText('action_copy'),
                        ]
                    ],
                ];
            }
        }

        return $list;
    }
}
