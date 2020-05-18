<?php

namespace Workflow\Tools;

use Workflow\CalculateAnything as CalculateAnything;

class Percentage extends CalculateAnything implements CalculatorInterface
{
    private $query;
    private $stop_words;
    private $keywords;
    private $lang;

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
        if ($strlenght >= 3 && strpos($this->query, ' ') !== false && strpos($this->query, '%') !== false) {
            return true;
        }
        return false;
    }


    /**
     * Process query
     *
     * @return string|array
     */
    public function processQuery()
    {
        $query = $this->query;
        $keys = $this->keywords;
        $stop_words = $this->getStopWordsString($this->stop_words);
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

        $this->query = $query;
        $processed = '';

        // Calculate Percentage of value
        // 30% de 100 = 30
        if (preg_match('/^\d*\.?\d*% ?' . $stop_words . '? ?(\d+)?/', $query, $matches)) {
            $processed = $this->percentageOf();
        }

        // Total plus percentage
        // 100 + 16% = 116
        elseif (preg_match('/^\d*\.?\d+ ?\+ ?\d*\.?\d*%$/', $query, $matches)) {
            $processed = $this->totalPlusPercentage();
        }

        // Total minus percentage
        // 116 - 16% = 100
        elseif (preg_match('/^\d*\.?\d+ ?- ?\d*\.?\d*%$/', $query, $matches)) {
            $processed = $this->totalMinusPercentage();
        }

        // Calculates `a` percent of `b` is what percent?
        // 30 % 40 = 75%
        // So 30 is 75% of 40.
        elseif (preg_match('/^\d+ +?\%.+?\d+/', $query, $matches)) {
            $processed = $this->percentOfTwoNumbers();
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
                    'alt' => [
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
        $query = preg_replace("/[^0-9.%]/", ' ', $query);
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
        $query = $this->query;
        $query = preg_replace('/\s+/', '', $query);
        $query = preg_replace("/ +?\+ +?/", ' ', $query);
        $data = explode('+', $query);

        if (count($data) < 2) {
            return false;
        }

        $amount = $this->cleanupNumber($data[0]);
        $percent = $this->cleanupNumber($data[1]);

        return $this->formatNumber($amount + (($percent / 100) * $amount));
    }

    /**
     * Total minus percetage
     * 116 - 16% = 100
     *
     * @return array
     */
    private function totalMinusPercentage()
    {
        $query = $this->query;
        $query = preg_replace('/\s+/', '', $query);
        $data = explode('-', $query);

        if (count($data) < 2) {
            return false;
        }

        $amount = $this->cleanupNumber($data[0]);
        $percent = $this->cleanupNumber($data[1]);
        $percent_min = ($percent / 100) * $amount;

        $result = $percent == 100 ? '0.00' : $this->formatNumber($amount - $percent_min);
        $saved = $amount - $this->cleanupNumber($result);
        $famount = $this->formatNumber($amount);
        $saved = $this->formatNumber($saved);

        $values = [];
        $values[$result] = $famount . ' - ' . $data[1] . ' = ' . $result;
        $values[$saved] = $famount . ' - ' . $result . ' = ' . $saved;
        return $values;
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
        $query = $this->query;
        $query = preg_replace("/ +?\% +?/", ' ', $query);
        $query = preg_replace('!\s+!', ' ', $query);
        $data = explode(' ', $query);

        if (count($data) < 2) {
            return false;
        }

        $val1 = $this->cleanupNumber($data[0]);
        $val2 = $this->cleanupNumber($data[1]);
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
