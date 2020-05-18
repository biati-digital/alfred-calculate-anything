<?php

namespace CurrencyConverter;

use CurrencyConverter\Provider\ExchangeRatesIo;

class CurrencyConverter implements CurrencyConverterInterface
{
    /**
     * store cache or not
     *
     * @var bool
     */
    protected $cachable = false;

    /**
     * @var Provider\ProviderInterface
     */
    protected $rateProvider;

    /**
     * @var Cache\Adapter\CacheAdapterInterface
     */
    protected $cacheAdapter;

    /**
     * {@inheritDoc}
     */
    public function convert($from, $to, $amount = 1)
    {
        $fromCurrency = $this->parseCurrencyArgument($from);
        $toCurrency = $this->parseCurrencyArgument($to);

        if ($this->isCacheable()) {
            if ($this->getCacheAdapter()->cacheExists($fromCurrency, $toCurrency)) {
                return $this->getCacheAdapter()->getRate($fromCurrency, $toCurrency) * $amount;
            } elseif ($this->getCacheAdapter()->cacheExists($toCurrency, $fromCurrency)) {
                return (1 / $this->getCacheAdapter()->getRate($toCurrency, $fromCurrency)) * $amount;
            }
        }

        $rate = $this->getRateProvider()->getRate($fromCurrency, $toCurrency);

        if ($this->isCacheable()) {
            $this->getCacheAdapter()->createCache($fromCurrency, $toCurrency, $rate);
        }

        return $rate * $amount;
    }

    /**
     * Sets if caching is to be enables
     *
     * @param  boolean $cachable
     * @return self
     */
    public function setCachable($cachable = true)
    {
        $this->cachable = (bool) $cachable;

        return $this;
    }

    /**
     * Checks if caching is enabled
     *
     * @return bool
     */
    public function isCacheable()
    {
        return $this->cachable;
    }

    /**
     * Gets Rate Provider
     *
     * @return Provider\ProviderInterface
     */
    public function getRateProvider()
    {
        if (!$this->rateProvider) {
            $this->setRateProvider(new ExchangeRatesIo());
        }

        return $this->rateProvider;
    }

    /**
     * Sets rate provider
     *
     * @param  Provider\ProviderInterface $rateProvider
     * @return self
     *
     */
    public function setRateProvider(Provider\ProviderInterface $rateProvider)
    {
        $this->rateProvider = $rateProvider;

        return $this;
    }

    /**
     * Sets cache adapter
     *
     * @param  Cache\Adapter\CacheAdapterInterface $cacheAdapter
     * @return self
     */
    public function setCacheAdapter(Cache\Adapter\CacheAdapterInterface $cacheAdapter)
    {
        $this->setCachable(true);
        $this->cacheAdapter = $cacheAdapter;

        return $this;
    }

    /**
     * Gets cache adapter
     *
     * @return Cache\Adapter\CacheAdapterInterface
     */
    public function getCacheAdapter()
    {
        if (!$this->cacheAdapter) {
            $this->setCacheAdapter(new Cache\Adapter\FileSystem());
        }

        return $this->cacheAdapter;
    }

    /**
     * Parses the Currency Arguments
     *
     * @param string|array $data
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    protected function parseCurrencyArgument($data)
    {
        if (is_string($data)) {
            $currency = $data;
        } elseif (is_array($data)) {
            if (isset($data['country'])) {
                $currency = CountryToCurrency::getCurrency($data['country']);
            } elseif (isset($data['currency'])) {
                $currency = $data['currency'];
            } else {
                throw new Exception\InvalidArgumentException('Please provide country or currency!');
            }
        } else {
            throw new Exception\InvalidArgumentException('Invalid currency provided. String or array expected.');
        }

        return $currency;
    }
}
