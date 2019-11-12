<?php
namespace CurrencyConverter\Cache\Adapter;

interface CacheAdapterInterface
{
    /**
     * Checks if cache exists
     *
     * @param  string $fromCurrency
     * @param  string $toCurrency
     * @return bool
     */
    public function cacheExists($fromCurrency, $toCurrency);

    /**
     * Gets exchange rate from cache
     *
     * @param  string $fromCurrency
     * @param  string $toCurrency
     * @return float
     */
    public function getRate($fromCurrency, $toCurrency);

    /**
     * Creates new cache
     *
     * @param  string $fromCurrency
     * @param  string $toCurrency
     *                              @param float rate
     * @return void
     */
    public function createCache($fromCurrency, $toCurrency, $rate);
}
