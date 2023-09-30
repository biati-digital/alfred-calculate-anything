<?php

namespace Workflow\Tools;

use Workflow\CalculateAnything as CalculateAnything;

//use Olifolkerd\Convertor\Convertor;

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
    private $unitsInfo;
    private $fractionUnits;
    private $lang;
    private $match_units;


    /**
     * Construct
     */
    public function __construct($query)
    {
        $this->query = $query;
        $this->lang = $this->getTranslation('units');
        $this->keywords = $this->getKeywords('units');
        $this->stop_words = $this->getStopWords('units');
        $this->fractionUnits = $this->fractionUnits();

        $this->buildUnitsData();
    }


    /**
     * Define all available units
     */
    private function units()
    {
        return [
            'length' => [
                'base' => 'm',
                'units' => [
                    'm' => 1, //meter
                    'km' => 1000, //kilometer
                    'dm' => 0.1, //decimeter
                    'cm' => 0.01, //centimeter
                    'mm' => 0.001, //milimeter
                    'μm' => 0.000001, //micrometer
                    'nm' => 0.000000001, //nanometer
                    'pm' => 0.000000000001, //picometer
                    'in' => 0.0254, //inch
                    'ft' => 0.3048, //foot
                    'yd' => 0.9144, //yard
                    'mi' => 1609.344, //mile
                    'nmi' => 1852, //nautical miles
                    'h' => 0.1016, //hand
                    'ly' => 9460730472580800, //lightyear
                    'au' => 149597870700, //astronomical unit
                    'pc' => 30856775814913672.789139379577965, //parsec
                ]
            ],
            'area' => [
                'base' => 'm2',
                'units' => [
                    'm2' => 1, //meter square - base unit for area
                    'km2' => 1000000, //kilometer square
                    'cm2' => 0.0001, //centimeter square
                    'mm2' => 0.000001, //milimeter square
                    'ft2' => 0.092903, //foot square
                    'mi2' => 2589988.11, //mile square
                    'ac' => 4046.86, //acre
                    'ha' => 10000, //hectare
                ]
            ],
            'volume' => [
                'base' => 'l',
                'units' => [
                    'l' => 1, //litre - base unit for volume
                    'dm3' => 1, //cubic decimeter - litre
                    'ml' => 0.001, //mililitre
                    'cm3' => 0.001, //cubic centimeter - mililitre
                    'hl' => 100, //hectolitre
                    'kl' => 1000, //kilolitre
                    'm3' => 1000, //meters cubed - kilolitre
                    'pt' => 0.473176, //pint
                    'uspt' => 0.473176, //us pint
                    'ukpt' => 0.56826125, //pint
                    'gal' => 3.78541, //gallon
                    'usgal' => 3.78541, //us gallon
                    'ukgal' => 4.405, //uk gallon
                    'qt' => 0.946353, //quart
                    'usqt' => 0.946353, //us quart
                    'ukqt' => 1.1365225, //uk imperial quart
                    'yd3' => 764.55485798, //cubic yard
                    'ft3' => 28.316846592, //cubic feet
                    'in3' => 0.016387064, //cubic inches
                    'floz' => 0.0295735, //Fluid ounces
                ]
            ],
            'weight' => [
                'base' => 'kg',
                'units' => [
                    'kg' => 1, //kilogram - base unit for weight
                    'g' => 0.001, //gram
                    'mg' => 0.000001, //milligram
                    'N' => 9.80665002863885, //Newton (based on earth gravity)
                    'st' => 6.35029, //stone
                    'lb' => 0.453592, //pound
                    'oz' => 0.028349523125, //ounce
                    't' => 1000, //metric tonne
                    'ukt' => 1016.047, //UK Long Ton
                    'ust' => 907.1847, //US short Ton
                ]
            ],
            'speed' => [
                'base' => 'mps',
                'units' => [
                    'mps' => 1, //meter per second - base unit for speed
                    'kph' => 0.277778, //kilometer per hour
                    'mph' => 0.44704, //miles per hour
                    'fps' => 0.3048, //feet per second
                    'knot' => 0.514444, //knot
                ]
            ],
            'rotation' => [
                'base' => 'deg',
                'units' => [
                    'deg' => 1, //degrees - base unit for rotation
                    'rad' => 57.2958, //radian
                ]
            ],
            'temperature' => [
                'base' => 'k',
                'units' => [
                    'k' => 1, //kelvin - base unit for temperature
                    'c' => function ($val, $to_from) {
                        return $to_from ? $val - 273.15 : $val + 273.15;
                    },
                    'f' => function ($val, $to_from) {
                        return $to_from ? ($val * 9 / 5 - 459.67) : (($val + 459.67) * 5 / 9);
                    },
                ]
            ],
            'pressure' => [
                'base' => 'pa',
                'units' => [
                    'pa' => 1, //Pascal - base unit for Pressure
                    'hpa' => 100, //hpa
                    'kpa' => 1000, //kilopascal
                    'mpa' => 1000000, //megapascal
                    'bar' => 100000, //bar
                    'mbar' => 100, //milibar
                    'psi' => 6894.76, //pound-force per square inch
                ]
            ],
            'time' => [
                'base' => 's',
                'units' => [
                    's' => 1, //second - base unit for time
                    'year' => 31536000, //year - standard year
                    'month' => 2628000, //month - 31 days
                    'week' => 604800, //week
                    'day' => 86400, //day
                    'hr' => 3600, //hour
                    'min' => 60, //minute
                    'ms' => 0.001, //millisecond
                    'μs' => 0.000001, //microsecond
                    'ns' => 0.000000001, //nanosecond
                ]
            ],
            'energy' => [
                'base' => 'j',
                'units' => [
                    'j' => 1, //joule - base unit for energy
                    'kj' => 1000, //kilojoule
                    'mj' => 1000000, //megajoule
                    'cal' => 4184, //calorie
                    'Nm' => 1, //newton meter
                    'ftlb' => 1.35582, //foot pound
                    'whr' => 3600, //watt hour
                    'kwhr' => 3600000, //kilowatt hour
                    'mwhr' => 3600000000, //megawatt hour
                    'mev' => 0.00000000000000016, //mega electron volt
                ]

            ],
            'power' => [
                'base' => 'w',
                'units' => [
                    'w' => 1, //watt - base unit for power
                    'kw' => 1000, // kilowatt
                    'ps' => 735.5, //metric horsepower
                    'hp' => 745.7, // mechanical horsepower
                ]
            ]
        ];
    }


    /**
     * List of available units
     *
     * @return array
     */
    private function buildUnitsData()
    {

        $registered_units = $this->units();
        $units = [];
        $units_cats = [];

        foreach ($registered_units as $category => $data) {
            $units[$category] = array_keys($data['units']);

            foreach ($units[$category] as $key => $value) {
                $units_cats[$value] = $category;
            }
        }

        $this->unitsList = $units;
        $this->unitsInfo = $units_cats;

        return $units;
    }


    private function fractionUnits()
    {
        return [
            'm' => 'cm',
            'km' => 'm',
            'dm' => 'cm',
            'cm' => 'mm',
            'mm' => 'μm',
            'μm' => 'nm',
            'nm' => 'pm',
            'ft' => 'in',
            'yd' => 'in',
            'mi' => 'ft',
            'l' => 'ml',
            'pt' => 'floz',
            'uspt' => 'floz',
            'ukpt' => 'floz',
            'gal' => 'qt',
            'qt' => 'pt',
            'usqt' => 'uspt',
            'ukqt' => 'ukpt',
            'usgal' => 'usqt',
            'ukgal' => 'ukqt',
            'g' => 'mg',
            'st' => 'lb',
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

        return preg_match('/^([-\d+\.,\s]*) ?' . $units . ' ?' . $stopwords . '? ' . $units . '$/i', $query, $matches);
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
                'subtitle' => '',
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
                    'arg' => $this->cleanupNumber($result['value']),
                    'subtitle' => $this->lang['cmd'],
                ],
                'alt' => [
                    'valid' => true,
                    'arg' => $result['formatted'],
                    'subtitle' => $this->lang['alt'],
                ],
            ]
        ];
        if (isset($result['fraction']) and $result['fraction']) {
            $items[] = [
                'title' => $result['fraction']['formatted'],
                'arg' => $result['fraction']['value'],
                'subtitle' => $this->getText('action_copy'),
                'valid' => true,
                'mods' => [
                    'cmd' => [
                        'valid' => true,
                        'arg' => $this->cleanupNumber($result['fraction']['value']),
                        'subtitle' => $this->lang['cmd'],
                    ],
                    'alt' => [
                        'valid' => true,
                        'arg' => $result['fraction']['formatted'],
                        'subtitle' => $this->lang['alt'],
                    ],
                ]
            ];
        }

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
        $amount = $data['amount'];
        $can_convert = $this->getConversionCategory($data['from'], $data['to']);

        if (empty($can_convert)) {
            return sprintf($this->lang['error'], $this->standardUnit($data['from']), $this->standardUnit($data['to']));
        }

        $category = isset($can_convert['category']) ? $can_convert['category'] : '';
        $from = isset($can_convert['from']) ? $can_convert['from'] : '';
        $to = isset($can_convert['to']) ? $can_convert['to'] : '';

        $converted = $this->convertTo($amount, $from, $to);

        if (in_array($to, $this->fractionUnits) && fmod($converted, 1) > 0) {
            if (isset($this->fractionUnits[$to])) {
                $fraction = $this->convertTo(fmod($converted, 1), $to, $this->fractionUnits[$to]);
                $fraction_unit = $this->fractionUnits[$to];
            }
        }

        $decimals = -1;
        if ($category == 'temperature') {
            $decimals = 1;
        }

        // Before displaying the result
        // Convert some units to readable human form
        if ($category == 'time') {
            if ($converted > 1) {
                $human_readable = [
                    'ms' => 'milliseconds',
                    's' => 'seconds',
                    'min' => 'minutes',
                    'hr' => 'hours',
                    'day' => 'days',
                    'week' => 'weeks',
                    'month' => 'months',
                    'year' => 'years',
                ];

                if (isset($human_readable[$to])) {
                    $to = $human_readable[$to];
                }
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
            'formatted' => $resultValue . ' ' . $resultUnit,
            'value' => $resultValue,
            'fraction' => (isset($fraction) ? [
                'formatted' => bcdiv($converted, 1, 0) . ' ' . $resultUnit . ', ' . $fraction . ' ' . $fraction_unit,
                'value' => $resultValue
            ] : false)
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
        $matches = [];
        $stopwords = $this->getStopWordsString($this->stop_words);

        preg_match('/^([-\d+\.,\s]*) ?' . $this->match_units . ' ?' . $stopwords . '? ' . $this->match_units . '$/i', $query, $matches);

        if (empty($matches)) {
            return false;
        }

        $total_match = count($matches);
        $amount = \Alfred\getArgument($matches, 1, '');
        $from = $this->getCorrectunit(\Alfred\getArgument($matches, 2));
        $to = $this->getCorrectunit(\Alfred\getArgument($matches, $total_match - 1));

        if (empty($from) || empty($to)) {
            return false;
        }

        return [
            'amount' => $this->cleanupNumber(str_replace(' ', '', $amount)),
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

        if (isset($this->unitsInfo[$unit])) {
            return $this->unitsInfo[$unit];
        }

        return false;
    }

    /**
     * Check if a category
     * has a specific unit
     *
     * @param $unit     unit to search
     * @param $category category to check if has the unit
     * @return boolean
     */
    private function getConversionCategory($from_unit, $to_unit)
    {
        $all_units = $this->units();
        $found_from = [];
        foreach ($all_units as $category => $value) {
            $units = $value['units'];

            foreach ($units as $key => $data) {
                if ($from_unit === $key || $from_unit === strtolower($key)) {
                    $found_from[$key] = $category;
                }
            }
        }

        if (empty($found_from)) {
            return false;
        }

        $result = [];
        foreach ($found_from as $unit => $unit_category) {
            $set = $all_units[$unit_category];
            foreach ($set['units'] as $key => $data) {
                if ($to_unit === $key || $to_unit === strtolower($key)) {
                    $result['from'] = $unit;
                    $result['to'] = $key;
                    $result['category'] = $unit_category;
                    break;
                }
            }

            if (!empty($result)) {
                break;
            }
        }

        return $result;
    }


    /**
     * Check if a category
     * has a specific unit
     *
     * @param $unit     unit to search
     * @param $category category to check if has the unit
     * @return boolean
     */
    private function categoryHasUnit($unit, $category)
    {
        $units = $this->units();
        $set = $units[$category];
        $cat_has_unit = array_filter(array_keys($set['units']), function ($item) use ($unit) {
            return strtolower($item) === $unit;
        });

        return $cat_has_unit;
    }


    /**
     * Check if unit is valid
     *
     * @param string $unit
     * @return boolean
     */
    private function isValidUnit($unit)
    {
        return isset($this->unitsInfo[$unit]);
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
            $params_start[] = implode('\b|', $value) . '\b';
        }

        $translation_keywords = $this->keywords;
        if (!empty($translation_keywords)) {
            // $params_start[] = implode(' |', array_keys($translation_keywords));
            $params_start[] = implode('\b|', array_keys($translation_keywords)) . '\b';
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
        if ($this->unitEndsWith($unit, '2')) {
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
        if ($this->isValidUnit($val)) {
            return $this->cleanupUnit($val);
        }

        //$val = mb_strtolower($val);
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


    public function unitEndsWith(string $haystack, string $needle, bool $case = true): bool
    {
        $expectedPosition = strlen($haystack) - strlen($needle);
        if ($case) {
            return strrpos($haystack, $needle, 0) === $expectedPosition;
        }

        return strripos($haystack, $needle, 0) === $expectedPosition;
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
                $unit_name = (isset($translation[$val]) ? $translation[$val] : $val);

                $list[] = [
                    'title' => "$unit_name = $val",
                    'subtitle' => sprintf($translation['belongs_to'], $val, $type_name),
                    'arg' => $val,
                    'match' => $val . '  ' . $unit_name,
                    'autocomplete' => $unit_name,
                    'valid' => true,
                    'variables' => ['action' => 'clipboard'],
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


    /**
     * Make the actual conversion
     */
    private function convertTo($amount, $from_unit, $to_unit)
    {
        $from_type = $this->getUnitType($from_unit);
        $base = 0;
        $units = $this->units();
        $set = $units[$from_type];

        if (is_callable($set['units'][$from_unit])) {
            $base = $set['units'][$from_unit]($amount, false);
        } else {
            $base = $amount * $set['units'][$from_unit];
        }

        //calculate converted value
        if (is_callable($set['units'][$to_unit])) {
            $result = $set['units'][$to_unit]($base, true);
        } else {
            $result = $base / $set['units'][$to_unit];
        }

        return $result;
    }
}
