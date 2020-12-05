<?php

namespace Workflow\Tools;

use Workflow\CalculateAnything as CalculateAnything;

class Percentage extends CalculateAnything implements CalculatorInterface
{
    private $query;
    private $stop_words;
    private $keywords;
    private $lang;
    private $parsed;

    /**
     * Construct
     */
    public function __construct($query)
    {
        $this->query = $query;
        $this->keywords = $this->getKeywords('percentage');
        $this->stop_words = $this->getStopWords('percentage');
        $this->lang = $this->getTranslation('percentage');
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
        $query = trim($this->query);
        $query = str_replace(',', '', $query);

        if ($strlenght < 3 || !strpos($query, '%')) {
            return false;
        }

        $stopwords = ['+', '-', '%'];
        $stopwords = array_merge($stopwords, $this->stop_words);
        $stopwords = implode('|', $stopwords);
        $stopwords = $this->escapeKeywords($stopwords);
        $stopwords = '(' . $stopwords . ')';

        $keys = $this->keywords;
        foreach ($keys as $k => $value) {
            if (is_array($value)) {
                continue;
            }
            $query = str_replace($k, $value, trim($query));
        }

        preg_match('/^(\d*\.?\d*%?)\s?' . $stopwords . '\s?(\d*\.?\d*%?)/i', $query, $matches);

        if (empty($matches)) {
            return false;
        }

        $matches = array_filter($matches);
        if (count($matches) < 4) {
            return false;
        }
        $this->parsed = $matches;
        return true;
    }


    /**
     * Process query
     *
     * @return string|array
     */
    public function processQuery()
    {
        $action = trim($this->parsed[2]);
        $processed = '';

        // Total plus percentage
        // 100 + 16% = 116
        if ($action == '+') {
            $processed = $this->totalPlusPercentage();
        }

        // Total minus percentage
        // 116 - 16% = 100
        elseif ($action == '-') {
            $processed = $this->totalMinusPercentage();
        }

        // Calculates `a` percent of `b` is what percent?
        // 30 % 40 = 75%
        // So 30 is 75% of 40.
        elseif ($action == '%') {
            $processed = $this->percentOfTwoNumbers();
        }

        // Calculate Percentage of value
        // 30% de 100 = 30
        elseif (in_array($action, $this->stop_words)) {
            $processed = $this->percentageOf();
        }

        return $this->output($processed);
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
                'arg' => $val,
                'subtitle' => $this->getText('action_copy'),
                'mods' => [
                    'cmd' => [
                        'valid' => true,
                        'arg' => $this->cleanupNumber($val),
                        'subtitle' => $this->lang['alt'],
                    ],
                ]
            ];
        }

        return $items;
    }


    /**
     * Percentage of
     * 30% of 100 = 30
     *
     * @return string
     */
    private function percentageOf()
    {
        $query = $this->query;
        $query = preg_replace('/[^0-9.%]/', ' ', $query);
        $query = preg_replace('!\s+!', ' ', $query);
        $data = explode(' ', $query);

        if (count($data) < 2) {
            return false;
        }
        $percent = $this->cleanupNumber($data[0]);
        $amount = $this->cleanupNumber($data[1]);

        return $this->formatNumber(($percent / 100) * $amount);
    }


    /**
     * Total plus percetage
     * 100 + 16% = 116
     *
     * @return string
     */
    private function totalPlusPercentage()
    {
        $total = $this->parsed[1];
        $percentage = $this->parsed[3];

        $amount = $this->cleanupNumber($total);
        $percent = $this->cleanupNumber($percentage);
        $result = $this->formatNumber($amount + (($percent / 100) * $amount));

        return $result;

        /*
        // This is a more advanced output with more information
        // not sure if necessary but i'll keep it for now
        // in case i add a setting to configure the output

        $saved = $amount - $this->cleanupNumber($result);
        $famount = $this->formatNumber($amount);
        $saved = $this->formatNumber($saved);
        $saved = abs($saved);

        $values = [];
        $values[$result] = $famount . ' + ' . $percentage . ' = ' . $result;
        $values[$saved] = sprintf($this->lang['percentage_of'], $percentage, $famount, $saved);

        return $values; */
    }

    /**
     * Total minus percetage
     * 116 - 16% = 100
     *
     * @return array
     */
    private function totalMinusPercentage()
    {
        $total = $this->parsed[1];
        $percentage = $this->parsed[3];

        $amount = $this->cleanupNumber($total);
        $percent = $this->cleanupNumber($percentage);
        $percent_min = ($percent / 100) * $amount;

        $result = $percent == 100 ? '0.00' : $this->formatNumber($amount - $percent_min);
        return $result;

        /*
        // This is a more advanced output with more information
        // not sure if necessary but i'll keep it for now
        // in case i add a setting to configure the output

        $saved = $amount - $this->cleanupNumber($result);
        $famount = $this->formatNumber($amount);
        $saved = $this->formatNumber($saved);

        $values = [];
        $values[$result] = $famount . ' - ' . $percentage . ' = ' . $result;
        $values[$saved] = sprintf($this->lang['percentage_of'], $percentage, $famount, $saved);
        return $values; */
    }


    /**
     * Percent of two numbers
     * 30 % 40 = 75%
     * So 30 is 75% of 40.
     *
     * @return array
     */
    private function percentOfTwoNumbers()
    {
        $val1 = $this->cleanupNumber($this->parsed[1]);
        $val2 = $this->cleanupNumber($this->parsed[3]);
        $percentage = ($val1 / $val2) * 100;
        $percentage = $this->formatNumber($percentage);

        $pincrease = ($val2 - $val1) / $val1 * 100;
        $pincrease = $this->formatNumber($pincrease);

        $pdecrease = ($val2 - $val1) / $val2 * 100;
        $pdecrease = $this->formatNumber($pdecrease);
        $lang = $this->lang;

        $values = [];
        $values["{$percentage}%"] = sprintf($lang['result'], $val1, "{$percentage}%", $val2);
        $values["{$pincrease}%"] = sprintf($lang['increase'], $val1, $val2, "{$pincrease}%");
        $values["{$pdecrease}%"] = sprintf($lang['decrease'], $val2, $val1, "{$pdecrease}%");

        return $values;
    }
}
