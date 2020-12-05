<?php

namespace Workflow\Tools;

use Workflow\CalculateAnything as CalculateAnything;

class Vat extends CalculateAnything implements CalculatorInterface
{
    private $query;
    private $lang;
    private $keywords;
    private $stop_words;
    private $parsed;
    private $percent;

    /**
     * Construct
     */
    public function __construct($query)
    {
        $this->query = $query;
        $this->lang = $this->getTranslation('vat');
        $this->keywords = $this->getKeywords('vat');
        $this->stop_words = $this->getStopWords('vat');
        $this->percent = $this->getSetting('vat_percentage', '16%');
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

        if ($strlenght < 4) {
            return false;
        }


        $stopwords = ['+', '-'];
        $stopwords = array_merge($stopwords, array_keys($this->keywords));
        $stopwords = implode('|', $stopwords);
        $stopwords = $this->escapeKeywords($stopwords);
        $stopwords = '(' . $stopwords . ')';
        $word = strtolower($this->lang['vat']);

        preg_match('/^(\d*\.?\d*) ?' . $stopwords . ' ?' . $word . '$/i', $query, $matches);

        if (empty($matches)) {
            return false;
        }

        $matches = array_filter($matches);

        if (count($matches) < 3) {
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
        $value = '';
        $action = $this->parsed[2];

        if ($action !== '+' || $action !== '-') {
            $action = $this->keywordTranslation($action, $this->keywords);
        }

        if (empty($action)) {
            return false;
        }

        $processed = $this->getVatOf($this->parsed[1]);
        if (empty($processed)) {
            return false;
        }

        if ($action == '+' && $processed && isset($processed['plusvat'])) {
            $value = $processed['plusvat'];
            $value['title'] = $value['value'];
        } elseif ($action == '-' && $processed && isset($processed['minusvat'])) {
            $value = $processed['minusvat'];
            $value['title'] = $value['value'];
        } else {
            return false;
        }

        return (!empty($value) ? $this->output([$value]) : false);
    }


    /**
     * Process vat
     *
     * @return bool|array
     */
    public function getVatOf($amount)
    {
        $query = $amount;
        $percent = $this->percent;
        $processed = false;

        if (empty($query)) {
            return $this->output($processed);
        }

        $percent = (int) $percent;
        $amount = $this->cleanupNumber($query);

        $result = ($percent / 100) * $amount;
        $result = (fmod($result, 1) !== 0.00 ? bcdiv($result, 1, 2) : $result);

        if ($result && $result > 0) {
            $processed = true;
            $plusvat = $amount + $result;
            $minusvat = $amount / ((float) "1.$percent");
            $lang = $this->lang;

            $result = $this->formatNumber($result);
            $plusvat = $this->formatNumber($plusvat, -1, true);
            $minusvat = $this->formatNumber($minusvat, -1, true);
            $amount = $this->formatNumber($amount);

            $processed = [
                'result' => [
                    'title' => sprintf($lang['result'], $amount, $result),
                    'subtitle' => sprintf($lang['subtitle'], "{$percent}%"),
                    'value' => $result
                ],
                'plusvat' => [
                    'title' => sprintf($lang['plus'], $amount, $plusvat),
                    'subtitle' => sprintf($lang['subtitle'], "{$percent}%"),
                    'value' => $plusvat
                ],
                'minusvat' => [
                    'title' => sprintf($lang['minus'], $amount, $minusvat),
                    'subtitle' => sprintf($lang['minus_subtitle'], $amount, "{$percent}%"),
                    'value' => $minusvat
                ],
            ];
        }

        return $processed;
    }


    /**
     * Output
     * build the output the way
     * it should be displayed by Alfred
     *
     * @param array $result
     * @return array
     */
    public function output($processed)
    {
        $items = [];

        if (!$processed) {
            $items[] = [
                'title' => '...',
                'subtitle' => $this->lang['empty'],
                'arg' => '',
                'valid' => false,
            ];

            return $items;
        }

        foreach ($processed as $value) {
            $items[] = [
                'title' => $value['title'],
                'subtitle' => (isset($value['subtitle']) ? $value['subtitle'] : ''),
                'arg' => $value['value'],
                'mods' => [
                    'cmd' => [
                        'valid' => true,
                        'arg' => $this->cleanupNumber($value['value']),
                        'subtitle' => $this->lang['cmd'],
                    ]
                ]
            ];
        }

        return $items;
    }
}
