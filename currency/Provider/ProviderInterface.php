<?php
namespace CurrencyConverter\Provider;

interface ProviderInterface
{
    /**
     * Gets exchange rate from cache
     *
     * @param  string $fromCurrency
     * @param  string $toCurrency
     * @return float
     */
    public function getRate($fromCurrency, $toCurrency);
}
