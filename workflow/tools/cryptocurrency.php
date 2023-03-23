<?php

namespace Workflow\Tools;

use Workflow\CalculateAnything as CalculateAnything;

/**
 * Cryptocurrency
 * Handle cryptocurrency conversions
 * for example 100 bitcoins in usd
 * 100 bitcoin mxn
 * 100btc mxn
 * 100eth
 */
class Cryptocurrency extends CalculateAnything implements CalculatorInterface
{
    private $query;
    private $stop_words;
    private $keywords;
    private $lang;
    private $symbolsList;
    private $rates_cache_seconds;
    private static $rates;
    private static $custom_rates;
    private static $display_updating_message;

    /**
     * Construct
     */
    public function __construct($query)
    {
        $this->query = str_replace(',', '', $query);
        $this->lang = $this->getTranslation('crypto_currency');
        $this->keywords = $this->getKeywords('crypto_currency');
        $this->stop_words = $this->getStopWords('crypto_currency');
        $this->symbolsList = $this->symbols();
        $this->rates_cache_seconds = $this->getCacheDuration();
        $this->setUpdatingMessageDisplay(true);
    }


    /**
     * Symbols
     * Cryptocurrency Symbols
     *
     * @return array
     */
    private function symbols(): array
    {
        $symbols = [];
        $file = \Alfred\getWorkflowPath() . '/data/crypto-currencies.json';

        if (file_exists($file)) {
            $symbols = file_get_contents($file);
            if (!empty($symbols)) {
                $symbols = json_decode($symbols, true);
                $currencies = self::$currencyCalculator->currencies();
                foreach ($currencies as $key => $item) {
                    if (isset($symbols[$key])) {
                        $symbols[] = $key;
                    }
                }
            }
        }

        $custom = $this->getSetting('custom_cryptocurrencies', '');
        if (!empty($custom) && is_array($custom)) {
            foreach ($custom as $symbol) {
                if (!isset($symbols[$symbol])) {
                    self::$custom_rates[$symbol] = $symbol;
                    $symbols[$symbol] = $symbol;
                }
            }
        }

        return $symbols;
    }


    /**
     * Duration of the cache
     * before calling the API again
     *
     * @return int
     */
    private function getCacheDuration(): int
    {
        $duration = 86400;
        $customCacheExpire = $this->getSetting('cryptocurrency_cache_hours', '');
        if (!empty($customCacheExpire) && is_numeric($customCacheExpire)) {
            $customCacheExpire = (int)$customCacheExpire;
            $duration = $customCacheExpire * 3600;
        }

        return $duration;
    }


    /**
     * shouldProcess
     *
     * @param integer $strlenght
     *
     * @return bool
     */
    public function shouldProcess(int $strlenght = 0)
    {
        if ($strlenght <= 3) {
            return false;
        }

        $checks = array_chunk($this->symbolsList, 100, true);
        $match = false;
        $query = $this->query;

        foreach ($checks as $list) {
            $currencies = $this->matchRegex($list);
            $stopwords = $this->getStopWordsString($this->stop_words);
            preg_match('/^([-\d+\.,\s]*) ?' . $currencies . ' ?' . $stopwords . '?/i', $query, $matches);

            if (!empty($matches)) {
                $match = true;
                break;
            }
        }

        return $match;
    }


    /**
     * Process query
     *
     * @return bool|array
     */
    public function processQuery()
    {
        $query = $this->query;
        $data = $this->extractQueryData($query);
        if (!$data) {
            return false;
        }

        $data['converted'] = [];
        if (
            $data['amount'] <= 0 ||
            (!empty($data['to']['crypto']) && count($data['to']['crypto']) === 1 && $data['from'] === $data['to']['crypto'][0])
        ) {
            $data['converted'][$data['to']] = [
                'total' => ['value' => $data['amount'], 'formatted' => "{$data['amount']} {$data['to']}"],
                'single' => ['value' => 1, 'formatted' => "1 {$data['from']} = 1 {$data['to']}"],
            ];
            return $this->output($data);
        }

        $decimals = $this->getSetting('crypto_decimals', 2);
        $converted = $this->convert($data);

        if (!empty($converted['error'])) {
            return $this->outputError($converted);
        }

        if (!empty($converted['currency'])) {
            foreach ($converted['currency'] as $key => $value) {
                $currency_key = $key . '_currency';
                $data['converted'][$currency_key] = [];
                $total = $this->formatNumber($value['total'], $decimals);
                $single = $this->formatNumber($value['single'], -1);

                $data['converted'][$currency_key]['total'] = ['value' => $total, 'formatted' => "{$total} {$key}"];
                $data['converted'][$currency_key]['single'] = ['value' => $single, 'formatted' => "1 {$data['from']} = {$single} {$key}"];
                $data['converted'][$currency_key]['symbol'] = $key;
                $data['converted'][$currency_key]['icon'] = 'flags/' . $key . '.png';;
            }
        }

        if (!empty($converted['crypto'])) {
            foreach ($converted['crypto'] as $key => $value) {
                $data['converted'][$key] = [];
                $total = $this->formatNumber($value['total'], $decimals);
                $single = $this->formatNumber($value['single'], -1);

                $formatted_text = "{$total} {$key}";
                $formatted_single_text = "1 {$data['from']} = {$single} {$key}";

                $name = $this->getCryptoFullName($key);
                if (!empty($name)) {
                    $formatted_single_text .= " - {$name}";
                }

                $data['converted'][$key]['total'] = ['value' => $total, 'formatted' => $formatted_text];
                $data['converted'][$key]['single'] = ['value' => $single, 'formatted' => $formatted_single_text];
                $data['converted'][$key]['symbol'] = $key;
                $data['converted'][$key]['icon'] = false;
            }
        }

        return $this->output($data);
    }


    /**
     * Output
     * build the output the way
     * it should be displayed by Alfred
     *
     * @param array $result
     *
     * @return array
     */
    public function output($result)
    {
        $items = [];

        if (isset($result['noapi']) && $result['noapi']) {
            $items[] = [
                'title' => $this->lang['noapikey_title'],
                'subtitle' => $this->lang['noapikey_subtitle'],
                'valid' => false,
            ];
            return $items;
        }

        $converted = $result['converted'];

        foreach ($converted as $key => $value) {
            $total = $value['total'];
            $single = $value['single'];
            $icon = $value['icon'];

            $items[] = [
                'title' => $total['formatted'],
                'subtitle' => $single['formatted'],
                'arg' => $total['value'],
                'icon' =>  $icon ? ['path' => $icon] : false,
                'mods' => [
                    'cmd' => [
                        'valid' => true,
                        'arg' => $this->cleanupNumber($total['value']),
                        'subtitle' => $this->lang['cmd'],
                    ],
                    'alt' => [
                        'valid' => true,
                        'arg' => $this->cleanupNumber($single['value']),
                        'subtitle' => $this->lang['alt'],
                    ],
                ]
            ];
        }

        return $items;
    }


    /**
     * Output error notification
     *
     * @param array $error
     *
     * @return array workflow response
     */
    public function outputError($error)
    {
        $items = [];
        $items[] = [
            'title' => $error['error'],
            'valid' => false,
            'arg' => '',
        ];
        if (isset($error['reload'])) {
            $items['rerun'] = $error['reload'];
        }
        return $items;
    }


    /**
     * Handle conversion
     *
     * @param array $data
     *
     * @return array
     */
    public function convert(array $data): array
    {
        $converted = ['currency' => false, 'crypto' => false, 'error' => false, 'reload' => false];
        $amount = $data['amount'];
        $from = $data['from'];
        $to_currency = isset($data['to']['currency']) ? $data['to']['currency'] : false;
        $to_crypto = isset($data['to']['crypto']) ? $data['to']['crypto'] : false;

        if (!empty($to_crypto)) {
            $converted['crypto'] = [];
            foreach ($to_crypto as $crypto) {
                $conversion = $this->cryptoConversion($amount, $from, $crypto);
                if (!empty($conversion['error'])) {
                    $converted['error'] = $conversion['error'];
                }
                if (!empty($conversion['reload'])) {
                    $converted['reload'] = $conversion['reload'];
                }
                $converted['crypto'][$crypto] = $conversion;
            }
        }

        if (empty($converted['reload']) && empty($converted['error']) && !empty($to_currency)) {
            $converted['currency'] = [];

            foreach ($to_currency as $currency) {
                $crypto_data = $this->getCryptoData($from);

                if (!empty($crypto_data)) {
                    // rates are already in USD, if the target currency
                    // is USD just return the stored value
                    if ($currency === 'USD') {
                        $converted['currency'][$currency] = [
                            'total' => $crypto_data['price'] * $amount,
                            'single' => $crypto_data['price'],
                            'error' => '',
                            'reload' => false,
                        ];
                    } else {
                        self::$currencyCalculator->setUpdatingMessageDisplay(false);
                        $currency_conversion = self::$currencyCalculator->exchangeConversion($crypto_data['price'] * $amount, 'USD', $currency);

                        if (!empty($currency_conversion['error'])) {
                            $converted['error'] = $currency_conversion['error'];
                        } else {
                            $currency_conversion['single'] = $currency_conversion['total'] / $amount;
                            $converted['currency'][$currency] = $currency_conversion;
                        }
                    }
                }
            }
        }

        return $converted;
    }


    /**
     * Fixer conversion
     *
     * @param int $amount
     * @param string $from
     * @param string $to
     * @param int $cache_seconds
     *
     * @return array
     */
    private function cryptoConversion($amount, $from, $to)
    {
        $exchange = self::$rates;
        if (empty($exchange)) {
            $exchange = $this->getRates();
        }

        if (isset($exchange['reload'])) {
            return [
                'total' => '',
                'single' => '',
                'error' => $exchange['message'],
                'reload' => $exchange['reload'],
            ];
        }

        if (isset($exchange['error_message'])) {
            return [
                'total' => '',
                'single' => '',
                'error' => $exchange['error_message'],
                'reload' => isset($exchange['reload']) ? $exchange['reload'] : false,
            ];
        }
        if (is_string($exchange)) {
            return ['total' => '', 'single' => '', 'error' => $exchange];
        }

        $exchange = $exchange['data'];
        $crypto_data = $this->getRate($from, $exchange);

        if (empty($crypto_data)) {
            return [
                'total' => '',
                'single' => '',
                'error' => $this->lang['nosymbol_title'],
                'reload' => false,
            ];
        }

        $total = 0;
        $value = 0;
        $crypto_value = $crypto_data['price'];
        $crypto_to_data = $this->getRate($to, $exchange);
        if (!$crypto_to_data) {
            return [];
        }

        $to_value = $crypto_to_data['price'];
        $to_value = str_replace(',', '', $to_value);

        $convert = (float)$crypto_value / (float)$to_value;
        $value = $convert;
        $total = $convert * $amount;

        return [
            'total' => $total,
            'single' => $value,
            'error' => false
        ];
    }



    /**
     * Extract query data
     * extract the values from and to
     * from the query typed by the user
     * it returns from, to and amount
     */
    private function extractQueryData($query)
    {
        $amount = '';
        $from = '';
        $to = '';
        $default_currency = self::$currencyCalculator->getBaseCurrency();
        $stopwords = $this->getStopWordsString($this->stop_words, ' %s ');

        preg_match('/^(\d*\.?\d+)[^\d]/i', $query, $amount_match);
        if (empty($amount_match)) {
            return false;
        }

        $amount = \Alfred\getArgument($amount_match, 1);
        $amount = trim($amount);
        $string = str_replace($amount, '', $query);
        $string = trim($string);

        preg_match('/(.*).*' . $stopwords . '(.*)/i', $string, $matches);

        // Matches strings like 100 usd to mxn
        if (!empty($matches)) {
            $matches = array_values(array_filter($matches));
            $from = \Alfred\getArgument($matches, 1);
            $to = \Alfred\getArgument($matches, 3);
        } // String is like 100 usd or 100 usd mxn
        elseif (empty($matches)) {
            $keywords = $this->keywords;
            foreach ($keywords as $key => $value) {
                if (is_array($value)) {
                    continue;
                }
                $key = $this->escapeKeywords($key);
                $string = preg_replace('/(^|\W)' . $key . '(\W|$)/i', ' ' . $value . ' ', $string);
            }

            $string = preg_replace('!\s+!', ' ', $string);
            $string = trim($string);

            $data = explode(' ', $string);
            $from = \Alfred\getArgument($data, 0);
            $to = \Alfred\getArgument($data, 1);
        }

        $from = strtoupper($from);
        $to = (!empty($to) ? strtoupper($to) : '');
        $_to = $to;
        $from = $this->getCorrectSymbol($from);
        $to = (!empty($to) ? $this->getCorrectSymbol($to) : '');
        $convert_to = ['currency' => false, 'crypto' => false];

        // there's 4 possible cases
        // 1 - $to is provided and $to is crypto and also regular currency (like pln)
        // 2 - $to is provided and $to is crypto
        // 3 - $to is provided and $to is regular currency
        // 4 - no $to is provided so we default to $default_currency if they are defined

        if (!empty($to)) {
            $to_currency = $this->isNormalCurrency($_to);

            if ($to_currency) {
                // Handle case 1
                $convert_to['crypto'] = [$to];
                $convert_to['currency'] = [$to_currency];
            } else {
                // Handle case 2
                $convert_to['crypto'] = [$to];
            }
        } elseif (empty($to) && !empty($_to)) {
            // Handle case 3
            $to_currency = $this->isNormalCurrency($_to);
            if ($to_currency) {
                $convert_to['currency'] = [$to_currency];
            }
        } elseif (empty($to) && !empty($default_currency)) {
            // Handle case 4
            $convert_to['currency'] = $default_currency;
        }

        if (!$from || (empty($convert_to['crypto']) && empty($convert_to['currency']))) {
            return false;
        }

        return [
            'from' => $from,
            'to' => $convert_to,
            'amount' => $this->cleanupNumber($amount),
        ];
    }


    /**
     * Get rates
     * if already cached and cache
     * has not expired returns
     * the cached rates otherwise
     * it fetches the rates again
     *
     * @param int $cache_seconds number of seconds before the cache expires
     *
     * @return mixed array if success or string with error message
     */
    private function getRates($skip_rerun = false)
    {
        $id = 'coinmarketcap';
        $cache_seconds = $this->rates_cache_seconds;
        $apikey = $this->getSetting('coinmarket_apikey', '');
        $from = "https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest?CMC_PRO_API_KEY={$apikey}&limit=5000";
        $cache_path = \Alfred\getDataPath('cache');
        $dir = $cache_path . '/' . $id;

        if (empty($apikey)) {
            return [
                'total' => '',
                'single' => '',
                'error_message' => $this->lang['noapikey_title'],
            ];
        }

        // Make sure the cache folder is created
        \Alfred\createDir($cache_path);
        \Alfred\createDir($dir);

        $rates_file = $dir . '/rates.json';

        if (file_exists($rates_file)) {
            $rates = file_get_contents($rates_file);

            if (!empty($rates)) {
                $rates = json_decode($rates, true);
                $updated = isset($rates['last_updated']) ? $rates['last_updated'] : null;

                if (is_null($updated)) {
                    $updated = isset($rates['timestamp']) ? $rates['timestamp'] : date(strtotime('today - 3 days'));
                }

                $time = time() - $updated;

                // Only return cached rates if cache
                // has not expired otherwise continue
                // to fetch the new rates
                if ($time < $cache_seconds) {
                    self::$rates = $rates;
                    return $rates;
                }
            }
        }


        // Before we update the rates, as it takes a few seconds
        // depending on the API and internet connection, we tell
        // Alfred to display a loading message and rerun the query
        // if the variable "rerun" exists it means that this is the second
        // run and we should not display the loading message and
        // call the API to update the rates
        if (!\Alfred\getVariable('rerun') && $this->shouldDisplayUpdatingMessage()) {
            return [
                'message' => $this->lang['updating_rates'],
                'reload' => 0.2,
            ];
        }

        $rates = $this->doRequest($from, ['Accepts: application/json']);

        if (empty($rates)) {
            return [
                'error' => $this->lang['fetch_error'],
                'reload' => 0.1,
            ];
        }

        $ratesData = ['data' => []];
        $ratesData['last_updated'] = time();

        if (is_array($rates) && !empty($rates['data'])) {

            // get specific rates
            if (!empty(self::$custom_rates)) {
                $custom_rates = $this->getSpecificRates(self::$custom_rates);
                $rates['data'] = array_merge($rates['data'], $custom_rates);
            }

            foreach ($rates['data'] as $rate) {
                $symbol = $rate['symbol'];
                $slug = $rate['slug'];
                $name = $rate['name'];
                $quote = $rate['quote'];

                $ratesData['data'][$symbol] = [
                    'slug' => $slug,
                    'name' => $name,
                    'price' => $quote['USD']['price'],
                ];
            }

            file_put_contents($rates_file, json_encode($ratesData));

            self::$rates = $ratesData;
            return $ratesData;
        }

        if (isset($rates['status']) && isset($rates['status']['error_message'])) {
            return $rates['status'];
        }

        return [];
    }


    /**
     * Get specific rates
     *
     * @param array $rates
     *
     * @return array
     */
    public function getSpecificRates($rates = []): array
    {
        $rates_data = [];
        $apikey = $this->getSetting('coinmarket_apikey', '');
        $url = "https://pro-api.coinmarketcap.com/v2/cryptocurrency/quotes/latest?CMC_PRO_API_KEY={$apikey}";
        $url .= '&symbol=' . implode(',', array_keys($rates));

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Accepts: application/json']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $rates = curl_exec($curl);

        curl_close($curl);

        $rates = json_decode($rates, true);

        if (is_array($rates) && !empty($rates['data'])) {
            foreach ($rates['data'] as $crypto) {
                if (is_array($crypto)) {
                    $rates_data[] = $crypto[0];
                    continue;
                }
                $rates_data[] = $crypto;
            }
        }

        return $rates_data;
    }




    /**
     * Get single rate
     * from the $exchange_rates list
     *
     * @param string $symbol
     * @param array $exchange_rates
     *
     * @return array
     */
    public function getRate($symbol, $exchange_rates)
    {
        if (isset($exchange_rates[$symbol])) {
            return $exchange_rates[$symbol];
        }

        return [];
    }


    /**
     * Get cached rates information
     *
     * @param $id
     *
     * @return bool|array
     */
    public function getCachedRates($id)
    {
        $cache_path = \Alfred\getDataPath('cache');
        $dir = $cache_path . '/' . $id;
        $rates_file = $dir . '/rates.json';

        if (file_exists($rates_file)) {
            $rates = file_get_contents($rates_file);
            if (!empty($rates)) {
                return json_decode($rates, true);
            }
        }

        return false;
    }


    /**
     * Get correct symbol
     * searching in translations and symbols array
     *
     * @param string $val
     * @return string|bool
     */
    public function getCorrectSymbol($val)
    {
        if ($this->isValidSymbol($val)) {
            return $val;
        }

        $val = mb_strtolower($val);
        $val = $this->keywordTranslation($val, $this->keywords);

        if ($this->isValidSymbol($val)) {
            return $val;
        }

        if (($key = array_search($val, $this->symbolsList)) !== false) {
            return $key;
        }

        return false;
    }


    public function isNormalCurrency($val)
    {
        // Check if instead of a cryptocurrency is a regular currency
        $is_currency = self::$currencyCalculator->getCorrectCurrency($val);
        if ($is_currency) {
            return $is_currency;
        }

        return false;
    }


    public function getCryptoData($val)
    {
        $rates = self::$rates;
        if (empty($rates)) {
            self::$display_updating_message = false;
            $rates = $this->getRates();
        }
        $symbol = $this->getCorrectSymbol($val);

        if (
            empty($symbol) ||
            empty($rates) ||
            empty($rates['data']) ||
            empty($rates['data'][$symbol])
        ) {
            return false;
        }

        $rate = $rates['data'][$symbol];
        $rate['code'] = $symbol;

        return $rate;
    }


    public function getCryptoFullName($val)
    {
        $data = $this->getCryptoData($val);
        if (empty($data)) {
            return false;
        }

        return $data['name'];
    }


    /**
     * Regex
     * create a regex from the
     * available currencies array
     *
     * @return string
     */
    private function matchRegex($currencies = [])
    {
        $currencies = !empty($currencies) ? $currencies : $this->symbolsList;
        $params = implode('\b|', array_keys($currencies));
        $params .= '\b|' . implode('\b|', array_values($currencies));
        $translation_keywords = $this->keywords;

        if (!empty($translation_keywords)) {
            $params .= '\b|' . implode('\b|', array_keys($translation_keywords));
        }
        $params = $this->escapeKeywords($params);

        return '(' . $params . '\b)';
    }


    /**
     * Is valid
     * check if given symbols
     * is valid and exists in the
     * symbols array
     *
     * @param string $val
     * @return bool
     */
    private function isValidSymbol($val)
    {
        return isset($this->symbolsList[$val]);
    }


    /**
     * Enable or disable
     * showing the updating rates message
     * @param $display
     *
     * @return void
     */
    public function setUpdatingMessageDisplay($display)
    {
        self::$display_updating_message = $display;
    }


    /**
     * Should display updating rates message

     * @return boolean
     */
    public function shouldDisplayUpdatingMessage()
    {
        return self::$display_updating_message;
    }


    /**
     * Get list
     * get a readable units list
     * to display to the user
     *
     * @return array
     */
    public function listAvailable()
    {
        $units = $this->symbolsList;
        $list = [];
        foreach ($units as $key => $value) {
            $list[] = [
                'title' => $key,
                'subtitle' => ucwords($value),
                'arg' => $key,
                'match' => $key . '  ' . $value,
                'autocomplete' => $value,
                'valid' => true,
                'mods' => [
                    'cmd' => [
                        'valid' => true,
                        'arg' => $key,
                        'subtitle' => $this->getText('action_copy'),
                    ]
                ],
                'variables' => ['action' => 'clipboard'],
                //'icon' => ['path' => "flags/{$key}.png"]
            ];
        }

        return $list;
    }
}
