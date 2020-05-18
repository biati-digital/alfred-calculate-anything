<?php
namespace CurrencyConverter;

interface CurrencyConverterInterface
{
    /**
     * Converts currency from one to another
     *
     * @param array|string   $from
     * @param array|string   $to
     * @param float optional $amount
     *
     * @return float
     */
    public function convert($from, $to, $amount = 1);  
}
