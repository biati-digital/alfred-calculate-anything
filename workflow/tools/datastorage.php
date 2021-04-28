<?php

namespace Workflow\Tools;

use Workflow\CalculateAnything as CalculateAnything;

/**
 * DataStorage
 * Convert between storage size
 * https://en.wikipedia.org/wiki/Gigabyte
 *
 * Examples:
 * 100 megas to kB
 * 100 MB to kB
 * 100 GB to mb
 * 1 GB mib
 * 100tb in mb
 * ...
 */

class DataStorage extends CalculateAnything implements CalculatorInterface
{
    private $query;
    private $lang;
    private $stop_words;
    private $keywords;
    private $size_types;
    private $match_regex;
    private $stop_words_regex;

    /**
     * Construct
     */
    public function __construct($query)
    {
        $this->query = str_replace(',', '', $query);
        $this->lang = $this->getTranslation('datastorage');
        $this->keywords = $this->getKeywords('datastorage');
        $this->stop_words = $this->getStopWords('datastorage');
        $this->size_types = $this->sizeTypes();
        $this->match_regex = $this->matchRegex();
        $this->stop_words_regex = $this->getStopWordsString($this->stop_words, ' %s ');
    }

    public function sizeTypes()
    {
        return [
            'b' => ['title' => 'Byte', 'unit' => 'B', 'base' => 1000, 'exponent' => 0],
            'kb' => ['title' => 'Kilobyte', 'unit' => 'kB', 'base' => 1000, 'exponent' => 0],
            'mb' => ['title' => 'Megabyte', 'unit' => 'MB', 'base' => 1000, 'exponent' => 2],
            'gb' => ['title' => 'Gigabyte', 'unit' => 'GB', 'base' => 1000, 'exponent' => 3],
            'tb' => ['title' => 'Terabyte', 'unit' => 'TB', 'base' => 1000, 'exponent' => 4],
            'pb' => ['title' => 'Petabyte', 'unit' => 'PB', 'base' => 1000, 'exponent' => 5],
            'eb' => ['title' => 'Exabyte', 'unit' => 'EB', 'base' => 1000, 'exponent' => 6],
            'zb' => ['title' => 'Zettabyte', 'unit' => 'ZB', 'base' => 1000, 'exponent' => 7],
            'yb' => ['title' => 'Yottabyte', 'unit' => 'YB', 'base' => 1000, 'exponent' => 8],
            'bit' => ['title' => 'bit', 'unit' => 'bit', 'base' => 1024, 'exponent' => 0],
            'kib' => ['title' => 'Kibibyte', 'unit' => 'KiB', 'base' => 1024, 'exponent' => 0],
            'mib' => ['title' => 'Mebibyte', 'unit' => 'MiB', 'base' => 1024, 'exponent' => 2],
            'gib' => ['title' => 'Gibibyte', 'unit' => 'GiB', 'base' => 1024, 'exponent' => 3],
            'tib' => ['title' => 'Tebibyte', 'unit' => 'TiB', 'base' => 1024, 'exponent' => 4],
            'pib' => ['title' => 'Pebibyte', 'unit' => 'PiB', 'base' => 1024, 'exponent' => 5],
            'eib' => ['title' => 'Exbibyte', 'unit' => 'EiB', 'base' => 1024, 'exponent' => 6],
            'zib' => ['title' => 'Zebibyte', 'unit' => 'ZiB', 'base' => 1024, 'exponent' => 7],
            'yib' => ['title' => 'Yobibyte', 'unit' => 'YiB', 'base' => 1024, 'exponent' => 8],
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
        if ($strlenght < 3) {
            return false;
        }

        $query = $this->query;
        $stop_words = $this->stop_words_regex;
        return preg_match('/^\d*\.?\d+ ?'. $this->match_regex . '\b ?' . $stop_words . '? ?\b' . $this->match_regex . '\b/i', $query, $matches);
    }


    /**
     * Process query
     *
     * @return string|array
     */
    public function processQuery()
    {
        $query = $this->query;
        $stop_words = $this->stop_words_regex;
        $query = preg_replace("/ ?" . $stop_words . " ?/i", ' ', $query);
        $query = preg_replace('!\s+!', ' ', $query);
        preg_match('/^(\d*\.?\d+) ?' . $this->match_regex . '\b ?' . $stop_words . '? ?\b' . $this->match_regex . '\b/i', $query, $matches);
        $matches = array_values(array_filter($matches));

        $total_inputs = count($matches);
        $from = $this->cleanupNumber($matches[1]);
        $from_unit = trim($matches[2]);
        $target = trim($matches[3]);

        if (isset($this->keywords[$from_unit])) {
            $from_unit = $this->keywords[$from_unit];
        }
        if (isset($this->keywords[$target])) {
            $target = $this->keywords[$target];
        }

        $result = [];
        $data = [
            'from' => $from,
            'from_unit' => $from_unit,
            'to' => $target,
        ];

        $val = $this->convertSize($data);
        $result[] = $val;

        return $this->output($result);
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
        $items = [];
        $result = (is_array($result) ? $result : [$result]);

        foreach ($result as $key => $val) {
            $title = $val['error'];
            $value = 0;

            if (empty($val['error'])) {
                $title = $val['value'] . ' ' . $val['to'];
                $value = $val['value'];
            }

            $items[] = [
                'title' => $title,
                'arg' => $value,
                'subtitle' => $this->getText('action_copy'),
                'mods' => [
                    'cmd' => [
                        'valid' => true,
                        'arg' => $this->cleanupNumber($value),
                        'subtitle' => $this->lang['cmd'],
                    ]
                ],
            ];
        }

        return $items;
    }



    /**
     * Do font calculations
     *
     * @param array $data
     * @return string
     */
    private function convertSize(&$data)
    {
        $result = ['value' => '', 'error' => ''];
        $units = $this->sizeTypes();
        $from = $data['from'];
        $from_unit = $data['from_unit'];
        $from_base = (isset($units[$from_unit]) ? $units[$from_unit]['base'] : 0);
        $to = $data['to'];
        $to_base = 0;
        $to_exponent = 0;
        $unit = (isset($units[$to]) ? $units[$to]['unit'] : $to);

        if ($from_unit == 'bit' && $to == 'b') {
            $result['value'] = $from * 0.125;
            $result['to'] = $unit;
            return $result;
        }
        if ($from_unit == 'b' && $to == 'bit') {
            $result['value'] = $from * 8;
            $result['to'] = $unit;
            return $result;
        }

        // Convert to bytes
        if ($from_unit !== 'b') {
            $from_exponent = (isset($units[$from_unit]) ? $units[$from_unit]['exponent'] : 0);
            $from = ($from_exponent > 0 ? $from * pow($from_base, $from_exponent) : $from * $from_base);
        }

        $bytes = $from;

        if ($to == 'b') {
            $result['value'] = $this->formatNumber($bytes, 8);
            $result['to'] = $unit;

            return $result;
        }

        if (empty($bytes)) {
            return $result;
        }

        if (!isset($units[$to])) {
            $result['error'] = sprintf($this->lang['error'], $to);
            return $result;
        }

        $to_base = $units[$to]['base'];
        $to_exponent = $units[$to]['exponent'];
        $force_binary = \Alfred\getVariable('datastorage_force_binary');

        if ($force_binary === true) {
            $to_base = 1024;
        }

        $converted = ($to_exponent > 0 ? $bytes / pow($to_base, $to_exponent) : $bytes / $to_base);
        $result['value'] = $this->formatNumber($converted);
        $result['to'] = $unit;

        return $result;
    }


    /**
     * Regex
     * create a regex from the
     * available currencies array
     *
     * @return string
     */
    private function matchRegex()
    {
        $types = $this->size_types;
        $params = implode('|', array_keys($types));
        $types_names = [];

        foreach ($types as $type) {
            $types_names[$type['title']] = $type['unit'];
        }
        if (!empty($types_names)) {
            $params .= '|' . implode('|', array_keys($types_names));
        }
        if (!empty($this->keywords)) {
            $params .= '|' . implode('|', array_keys($this->keywords));
        }

        $params = $this->escapeKeywords($params);

        return '(' . $params . ')';
    }
}
