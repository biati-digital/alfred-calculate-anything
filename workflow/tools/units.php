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
    private $match_units;

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
        $this->match_units = $units;

        return preg_match('/^\d*\.?\d+ ?' . $units . ' ?' . $stopwords . '? ' . $units . '$/i', $query, $matches);
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
        $conversion = $this->convert($data);

        return $this->output($conversion);
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
        if (empty($result)) {
            return false;
        }

        $items = [];
        if (is_string($result) && !empty($result)) {
            $items[] = [
                'title' => $result,
                'arg' => false,
                'subtitle' => $this->getText('action_copy'),
                'valid' => false,
            ];
            return $items;
        }

        $items[] = [
            'title' => $result['formatted'],
            'arg' => $result['value'],
            'subtitle' => $this->getText('action_copy'),
            'valid' => true,
            'mods' => [
                'cmd' => [
                    'valid' => true,
                    'arg' => $result['value'],
                    'subtitle' => $this->lang['cmd'],
                ],
                'alt' => [
                    'valid' => true,
                    'arg' => $result['formatted'],
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
            return sprintf($this->lang['error'], $this->standardUnit($from), $this->standardUnit($to));
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

        $resultValue = $this->formatNumber($converted, $decimals);
        $resultUnit = $this->standardUnit($to);

        return [
            'formatted' => $resultValue . $resultUnit,
            'value' => $resultValue
        ];
    }



    /**
     * Extract query data
     * extract the values from and to
     * from the query typed by the user
     * it returns from, to and amount
     */
    private function extractQueryData($query)
    {
        $query = str_replace(',', '', $query);
        $stopwords = $this->getStopWordsString($this->stop_words);

        preg_match('/^(\d*\.?\d+) ?' . $this->match_units . ' ?' . $stopwords . '? ' . $this->match_units . '$/i', $query, $matches);

        if (empty($matches)) {
            return false;
        }

        $total_match = count($matches);
        $amount = getVar($matches, 1, '');
        $from = $this->getCorrectunit(getVar($matches, 2));
        $to = $this->getCorrectunit(getVar($matches, $total_match - 1));

        if (empty($from) || empty($to)) {
            return false;
        }

        return [
            'amount' => $this->cleanupNumber($amount),
            'from' => $from,
            'to' => $to,
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
        $params_start = [];
        foreach ($units as $value) {
            // $params_start[] = implode(' |', $value);
            $params_start[] = implode('|', $value);
        }

        $translation_keywords = $this->keywords;
        if (!empty($translation_keywords)) {
            // $params_start[] = implode(' |', array_keys($translation_keywords));
            $params_start[] = implode('|', array_keys($translation_keywords));
        }

        $params_start = implode('|', $params_start);
        $params_start = $this->escapeKeywords($params_start);

        return '(' . $params_start . ')';
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
        $unit = preg_replace('!\s+!', ' ', $unit);
        if (endsWith($unit, '2')) {
            $unit = str_replace('2', '**2', $unit);
        }

        return $unit;
    }

    /**
     * Get correct unit
     * the user can enter for example liter
     * and this function should return l
     * so it will search if the key exists in the
     * units list and translation keywords
     *
     * @param string $val
     * @return string|bool
     */
    private function getCorrectunit($val)
    {
        if (empty($val)) {
            return false;
        }

        $val = mb_strtolower($val);
        $val = $this->keywordTranslation($val, $this->keywords);

        if (!$this->isValidUnit($val)) {
            return false;
        }

        return $this->cleanupUnit($val);
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
