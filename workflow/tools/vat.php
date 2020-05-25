<?php

namespace Workflow\Tools;

use Workflow\CalculateAnything as CalculateAnything;

class Vat extends CalculateAnything implements CalculatorInterface
{
    private $query;
    private $lang;

    /**
     * Construct
     */
    public function __construct($query)
    {
        $this->query = preg_replace('/[^\\d.]+/', '', $query);
        $this->lang = $this->getTranslation('vat');
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
        if ($strlenght >= 1) {
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
        $percent = $this->getSetting('vat_percentage', '16%');
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
            $plustaxt = $amount + $result;
            $minustax = $amount / ((float) "1.$percent");

            $processed = [
                'amount' => $this->formatNumber($amount),
                'result' => $this->formatNumber($result),
                'plustaxt' => $this->formatNumber($plustaxt, -1, true),
                'minustax' => $this->formatNumber($minustax, -1, true),
                'defined_percentage' => $percent
            ];
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

        $amount = $processed['amount'];
        $result = $processed['result'];
        $plustaxt = $processed['plustaxt'];
        $minustax = $processed['minustax'];
        $percent = $processed['defined_percentage'];
        $lang = $this->lang;

        $items[] = [
            'title' => sprintf($lang['result'], $amount, $result),
            'subtitle' => sprintf($lang['subtitle'], "{$percent}%"),
            'arg' => $result,
            'mods' => [
                'cmd' => [
                    'valid' => true,
                    'arg' => $amount,
                    'subtitle' => $this->lang['cmd'],
                ],
                'alt' => [
                    'valid' => true,
                    'arg' => $this->cleanupNumber($amount),
                    'subtitle' => $this->lang['alt'],
                ],
            ]
        ];

        $items[] = [
            'title' => sprintf($lang['plus'], $amount, $plustaxt),
            'subtitle' => sprintf($lang['plus_subtitle'], $amount, "{$percent}%"),
            'arg' => $plustaxt,
            'mods' => [
                'cmd' => [
                    'valid' => true,
                    'arg' => $plustaxt,
                    'subtitle' => $this->lang['cmd'],
                ],
                'alt' => [
                    'valid' => true,
                    'arg' => $this->cleanupNumber($plustaxt),
                    'subtitle' => $this->lang['alt'],
                ],
            ]
        ];

        $items[] = [
            'title' => sprintf($lang['minus'], $amount, $minustax),
            'subtitle' => sprintf($lang['minus_subtitle'], $amount, "{$percent}%"),
            'arg' => $minustax,
            'mods' => [
                'cmd' => [
                    'valid' => true,
                    'arg' => $minustax,
                    'subtitle' => $this->lang['cmd'],
                ],
                'alt' => [
                    'valid' => true,
                    'arg' => $this->cleanupNumber($minustax),
                    'subtitle' => $this->lang['alt'],
                ],
            ]
        ];

        return $items;
    }
}
