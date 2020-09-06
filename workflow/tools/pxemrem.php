<?php

namespace Workflow\Tools;

use Workflow\CalculateAnything as CalculateAnything;

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
 * 2rem to px base 18px
 * 10px
 * 10em
 * ...
 */

class PXEmRem extends CalculateAnything implements CalculatorInterface
{
    private $query;
    private $stop_words;
    private $keywords;

    /**
     * Construct
     */
    public function __construct($query)
    {
        $this->query = str_replace(',', '', $query);
        $this->keywords = $this->getKeywords('units');
        $this->stop_words = $this->getStopWords('units');
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
        return preg_match('/^\d*\.?\d+ ?(px|em|rem|pt) ?/', $query, $matches);
    }


    /**
     * Process query
     *
     * @return string|array
     */
    public function processQuery()
    {
        $query = $this->query;
        $stop_words = $this->getStopWordsString($this->stop_words, ' %s ');
        $query = preg_replace("/ ?" . $stop_words . " ?/i", ' ', $query);
        $query = preg_replace('!\s+!', ' ', $query);

        preg_match('/^(\d*\.?\d+) ?(px|em|rem|pt) ?' . $stop_words . '? ?(px|em|rem|pt)? ?(base.*px$)?/', $query, $matches);
        $matches = array_values(array_filter($matches));

        $from = 0;
        $from_unit = '';
        $total_inputs = count($matches);
        $target = '';
        $base = $this->getSetting('base_pixels', '16px');
        $base = $this->cleanupNumber($base);

        if ($total_inputs >= 3) {
            $from = $this->cleanupNumber($matches[1]);
            $from_unit = trim($matches[2]);
        }

        if ($total_inputs == 4) {
            if (strpos($matches[3], 'base') !== false) {
                $base = preg_replace('/[^0-9]/', '', $matches[3]);
                $base = $this->cleanupNumber($base);
            } else {
                $target = trim($matches[3]);
            }
        } elseif ($total_inputs == 5) {
            $target = trim($matches[3]);
            $base = preg_replace('/[^0-9]/', '', $matches[4]);
            $base = $this->cleanupNumber($base);
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
                $val = $this->calculateFontSize($data);
                $result[$val] = $val;
            }
        } else {
            $val = $this->calculateFontSize($data);
            $result[$val] = $val;
        }

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
            $items[] = [
                'title' => $val,
                'arg' => $key,
                'subtitle' => $this->getText('action_copy'),
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
    private function calculateFontSize($data)
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
}
