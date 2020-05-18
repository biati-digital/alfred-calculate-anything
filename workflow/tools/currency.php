<?php

namespace Workflow\Tools;

use Workflow\CalculateAnything as CalculateAnything;

/**
 * Currency
 * Handle currency conversions
 * for example 100usd to mxn
 * 100 usd mxn
 * 100usd mxn
 * 100eur
 */

class Currency extends CalculateAnything implements CalculatorInterface
{
    private $query;
    private $stop_words;
    private $keywords;
    private $currencyList;
    private $lang;

    /**
     * Construct
     */
    public function __construct($query)
    {
        $this->query = str_replace(',', '', $query);
        $this->lang = $this->getTranslation('currency');
        $this->keywords = $this->getKeywords('currency');
        $this->stop_words = $this->getStopWords('currency');
        $this->currencyList = $this->currencies();
    }


    /**
     * currencies
     * Cryptocurrency currencies
     *
     * @return array
     */
    private function currencies()
    {
        return [
            'AED' => 'United Arab Emirates dirham',
            'AFN' => 'Afghan afghani',
            'ALL' => 'Albanian lek',
            'AMD' => 'Armenian dram',
            'ANG' => 'Netherlands Antillean guilder',
            'AOA' => 'Angolan kwanza',
            'ARS' => 'Argentine peso',
            'AUD' => 'Australian dollar',
            'AWG' => 'Aruban florin',
            'AZN' => 'Azerbaijani manat',
            'BAM' => 'Bosnia and Herzegovina convertible mark',
            'BBD' => 'Barbados dollar',
            'BDT' => 'Bangladeshi taka',
            'BGN' => 'Bulgarian lev',
            'BHD' => 'Bahraini dinar',
            'BIF' => 'Burundian franc',
            'BMD' => 'Bermudian dollar',
            'BND' => 'Brunei dollar',
            'BOB' => 'Boliviano',
            'BRL' => 'Brazilian real',
            'BSD' => 'Bahamian dollar',
            'BTN' => 'Bhutanese ngultrum',
            'BWP' => 'Botswana pula',
            'BYN' => 'New Belarusian ruble',
            'BYR' => 'Belarusian ruble',
            'BZD' => 'Belize dollar',
            'CAD' => 'Canadian dollar',
            'CDF' => 'Congolese franc',
            'CHF' => 'Swiss franc',
            'CLF' => 'Unidad de Fomento',
            'CLP' => 'Chilean peso',
            'CNY' => 'Renminbi|Chinese yuan',
            'COP' => 'Colombian peso',
            'CRC' => 'Costa Rican colon',
            'CUC' => 'Cuban convertible peso',
            'CUP' => 'Cuban peso',
            'CVE' => 'Cape Verde escudo',
            'CZK' => 'Czech koruna',
            'DJF' => 'Djiboutian franc',
            'DKK' => 'Danish krone',
            'DOP' => 'Dominican peso',
            'DZD' => 'Algerian dinar',
            'EGP' => 'Egyptian pound',
            'ERN' => 'Eritrean nakfa',
            'ETB' => 'Ethiopian birr',
            'EUR' => 'Euro',
            'FJD' => 'Fiji dollar',
            'FKP' => 'Falkland Islands pound',
            'GBP' => 'Pound sterling',
            'GEL' => 'Georgian lari',
            'GHS' => 'Ghanaian cedi',
            'GIP' => 'Gibraltar pound',
            'GMD' => 'Gambian dalasi',
            'GNF' => 'Guinean franc',
            'GTQ' => 'Guatemalan quetzal',
            'GYD' => 'Guyanese dollar',
            'HKD' => 'Hong Kong dollar',
            'HNL' => 'Honduran lempira',
            'HRK' => 'Croatian kuna',
            'HTG' => 'Haitian gourde',
            'HUF' => 'Hungarian forint',
            'IDR' => 'Indonesian rupiah',
            'ILS' => 'Israeli new shekel',
            'INR' => 'Indian rupee',
            'IQD' => 'Iraqi dinar',
            'IRR' => 'Iranian rial',
            'ISK' => 'Icelandic króna',
            'JMD' => 'Jamaican dollar',
            'JOD' => 'Jordanian dinar',
            'JPY' => 'Japanese yen',
            'KES' => 'Kenyan shilling',
            'KGS' => 'Kyrgyzstani som',
            'KHR' => 'Cambodian riel',
            'KMF' => 'Comoro franc',
            'KPW' => 'North Korean won',
            'KRW' => 'South Korean won',
            'KWD' => 'Kuwaiti dinar',
            'KYD' => 'Cayman Islands dollar',
            'KZT' => 'Kazakhstani tenge',
            'LAK' => 'Lao kip',
            'LBP' => 'Lebanese pound',
            'LKR' => 'Sri Lankan rupee',
            'LRD' => 'Liberian dollar',
            'LSL' => 'Lesotho loti',
            'LYD' => 'Libyan dinar',
            'MAD' => 'Moroccan dirham',
            'MDL' => 'Moldovan leu',
            'MGA' => 'Malagasy ariary',
            'MKD' => 'Macedonian denar',
            'MMK' => 'Myanmar kyat',
            'MNT' => 'Mongolian tögrög',
            'MOP' => 'Macanese pataca',
            'MRO' => 'Mauritanian ouguiya',
            'MUR' => 'Mauritian rupee',
            'MVR' => 'Maldivian rufiyaa',
            'MWK' => 'Malawian kwacha',
            'MXN' => 'Mexican peso',
            'MXV' => 'Mexican Unidad de Inversion',
            'MYR' => 'Malaysian ringgit',
            'MZN' => 'Mozambican metical',
            'NAD' => 'Namibian dollar',
            'NGN' => 'Nigerian naira',
            'NIO' => 'Nicaraguan córdoba',
            'NOK' => 'Norwegian krone',
            'NPR' => 'Nepalese rupee',
            'NZD' => 'New Zealand dollar',
            'OMR' => 'Omani rial',
            'PAB' => 'Panamanian balboa',
            'PEN' => 'Peruvian Sol',
            'PGK' => 'Papua New Guinean kina',
            'PHP' => 'Philippine peso',
            'PKR' => 'Pakistani rupee',
            'PLN' => 'Polish złoty',
            'PYG' => 'Paraguayan guaraní',
            'QAR' => 'Qatari riyal',
            'RON' => 'Romanian leu',
            'RSD' => 'Serbian dinar',
            'RUB' => 'Russian ruble',
            'RWF' => 'Rwandan franc',
            'SAR' => 'Saudi riyal',
            'SBD' => 'Solomon Islands dollar',
            'SCR' => 'Seychelles rupee',
            'SDG' => 'Sudanese pound',
            'SEK' => 'Swedish krona',
            'SGD' => 'Singapore dollar',
            'SHP' => 'Saint Helena pound',
            'SLL' => 'Sierra Leonean leone',
            'SOS' => 'Somali shilling',
            'SRD' => 'Surinamese dollar',
            'SSP' => 'South Sudanese pound',
            'STD' => 'São Tomé and Príncipe dobra',
            'SVC' => 'Salvadoran colón',
            'SYP' => 'Syrian pound',
            'SZL' => 'Swazi lilangeni',
            'THB' => 'Thai baht',
            'TJS' => 'Tajikistani somoni',
            'TMT' => 'Turkmenistani manat',
            'TND' => 'Tunisian dinar',
            'TOP' => 'Tongan paʻanga',
            'TRY' => 'Turkish lira',
            'TTD' => 'Trinidad and Tobago dollar',
            'TWD' => 'New Taiwan dollar',
            'TZS' => 'Tanzanian shilling',
            'UAH' => 'Ukrainian hryvnia',
            'UGX' => 'Ugandan shilling',
            'USD' => 'United States dollar',
            'UYI' => 'Uruguay Peso en Unidades Indexadas',
            'UYU' => 'Uruguayan peso',
            'UZS' => 'Uzbekistan som',
            'VEF' => 'Venezuelan bolívar',
            'VND' => 'Vietnamese đồng',
            'VUV' => 'Vanuatu vatu',
            'WST' => 'Samoan tala',
            'XAF' => 'Central African CFA franc',
            'XCD' => 'East Caribbean dollar',
            'XOF' => 'West African CFA franc',
            'XPF' => 'CFP franc',
            'YER' => 'Yemeni rial',
            'ZAR' => 'South African rand',
            'ZMW' => 'Zambian kwacha',
            'ZWL' => 'Zimbabwean dollar'
        ];
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
        if ($strlenght <= 3) {
            return false;
        }

        $query = $this->query;
        $currencies = $this->matchRegex();
        $stopwords = $this->getStopWordsString($this->stop_words);

        return preg_match('/^\d*\.?\d+ ?' . $currencies . ' ?' . $stopwords . '?/i', $query, $matches);
    }


    /**
     * Process query
     *
     * @return string|array
     */
    public function processQuery()
    {
        $query = $this->query;
        $data = $this->extractQueryData($query);

        if (!$data) {
            return false;
        }

        if ($data['amount'] <= 0 || $data['from'] == $data['to']) {
            return $data['amount'] . $data['to'];
        }

        $locale = $this->getSetting('locale_currency', 'en_US');
        setlocale(LC_MONETARY, $locale);

        $converted = $this->convert($data);

        if ($converted['error']) {
            return $converted['error'];
        }

        $data['converted'] = [];
        foreach ($converted as $key => $value) {
            $data['converted'][$key] = [];

            $total = money_format('%i', $value['total']);
            $total = preg_replace("/[^0-9.,]/", '', $total);
            $single = $value['single'];
            $single = $this->formatNumber($value['single']);

            $data['converted'][$key]['total'] = ['value' => $total, 'formatted' => "{$total}{$key}"];
            $data['converted'][$key]['single'] = ['value' => $single, 'formatted' => "1{$data['from']} = {$single}{$key}"];
        }

        return $this->output($data);
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
        $converted = $result['converted'];

        foreach ($converted as $key => $value) {
            $total = $value['total'];
            $single = $value['single'];
            $icon = 'flags/' . $key . '.png';

            $items[] = [
                'title' => $total['formatted'],
                'subtitle' => $single['formatted'],
                'arg' => $total['value'],
                'icon' => ['path' => $icon],
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
     * Handle conversion
     *
     * @param array $data
     * @return array
     */
    public function convert($data)
    {
        $converted = [];
        $amount = $data['amount'];
        $from = $data['from'];
        $to = $data['to'];
        $use_cache = (isset($data['use_cache']) ? $data['use_cache'] : true);
        $cache_seconds = ($use_cache ? 14400 : 0);
        $fixer_apikey = $this->getSetting('fixer_apikey');

        if (is_string($to)) {
            $to = [$to];
        }

        foreach ($to as $currency) {
            // Use Fixer io
            if (!empty($fixer_apikey)) {
                $cache_seconds = ($use_cache ? 7200 : 0);
                $conversion = $this->fixerConversion($amount, $from, $currency, $cache_seconds);
            } else {
                $conversion = $this->exchangeRatesConversion($amount, $from, $currency, $cache_seconds);
            }

            if (isset($conversion['error']) && !empty($conversion['error'])) {
                $converted['error'] = $conversion['error'];
            }
            $converted[$currency] = $conversion;
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
     * @return array
     */
    private function fixerConversion($amount, $from, $to, $cache_seconds)
    {
        $cached = $this->getCachedConversion('fixer', $from, $to, $cache_seconds);
        if ($cached) {
            $cached = (float) $cached;
            $value = $cached;
            $total = $amount * $value;
        }

        if (!$cached) {
            $exchange = $this->getRates($cache_seconds);

            if (is_string($exchange)) {
                return ['total' => '', 'single' => '', 'error' => $exchange];
            }

            $base = $exchange['base'];
            $rates = $exchange['rates'];
            $default_base_currency = $rates[$base];

            $new_base_currency = $rates[$from]; //from currency
            $base_exchange = $default_base_currency / $new_base_currency;
            $value = ($rates[$to] * $base_exchange);
            $total = $amount * $value;

            $this->cacheConversion('fixer', $from, $to, $value);
        }

        return ['total' => $total, 'single' => $value, 'error' => false];
    }


    /**
     * exchangeratesapi.io
     *
     * @param int $amount
     * @param string $from
     * @param string $to
     * @param int $cache_seconds
     * @return array
     */
    public function exchangeRatesConversion($amount, $from, $to, $cache_seconds)
    {
        $cached = $this->getCachedConversion('exchangerates', $from, $to, $cache_seconds);
        if ($cached) {
            $cached = (float) $cached;
            $value = $cached;
        }

        if (!$cached) {
            $this->required();

            $converter = new \CurrencyConverter\CurrencyConverter;
            $value = '';
            $error = false;

            try {
                $value = $converter->convert($from, $to);
                $this->cacheConversion('exchangerates', $from, $to, $value);
            } catch (\Throwable $th) {
                $error = true;
                $message = $th->getMessage();
                preg_match('/{(.*)}/', $message, $matches);

                if ($matches && !empty($matches)) {
                    $value = $matches[1];
                }
            }

            if ($error) {
                return ['error' => $value];
            }
        }

        return ['total' => $amount * $value, 'single' => $value, 'error' => false];
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
        $default_currency = $this->getBaseCurrency();
        $stopwords = $this->getStopWordsString($this->stop_words, ' %s ');

        preg_match('/^(\d*\.?\d+)[^\d]/i', $query, $amount_match);
        if (empty($amount_match)) {
            return false;
        }

        $amount = getVar($amount_match, 1);
        $amount = trim($amount);
        $string = str_replace($amount, '', $query);
        $string = trim($string);

        preg_match('/(.*).*' . $stopwords . '(.*)/i', $string, $matches);

        // Matches strings like 100 usd to mxn
        if (!empty($matches)) {
            $matches = array_values(array_filter($matches));
            $from = getVar($matches, 1);
            $to = getVar($matches, 3);
        }
        // String is like 100 usd or 100 usd mxn
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
            $from = getVar($data, 0);
            $to = getVar($data, 1);
        }

        $from = strtoupper($from);
        $to = (!empty($to) ? strtoupper($to) : $default_currency);

        $from = $this->getCorrectCurrency($from);
        $to = (is_string($to) ? $this->getCorrectCurrency($to) : $to);

        if (!$from || !$to) {
            return false;
        }

        return [
            'from' => $from,
            'to' => $to,
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
     * @return mixed array if sucess or string with error message
     */
    private function getRates($cache_seconds)
    {
        $dir = getDataPath('cache/fixer');
        createDir($dir);

        $file = $dir . '/rates.json';
        if (file_exists($file)) {
            $c = file_get_contents($file);

            if (!empty($c)) {
                $c = json_decode($c, true);
                $updated = $c['timestamp'];
                $time = time() - $updated;

                // Only return cached rates if cache
                // has not expired otherwise continue
                // to fetch the new rates
                if ($time < $cache_seconds) {
                    return $c;
                }
            }
        }

        $apikey = $this->getSetting('fixer_apikey');
        if (empty($apikey)) {
            throw new Exception('No API Key provided');
        }

        $c = file_get_contents("http://data.fixer.io/api/latest?access_key={$apikey}&format=1");
        if (empty($c)) {
            return $this->lang['fetch_error'];
        }

        file_put_contents($file, $c);

        return json_decode($c, true);
    }




    /**
     * Get correct currency
     * the user can enter for example ¥
     * and this function should return JPY
     * so it will search if the key exists in the
     * currencies list and translation keywords
     *
     * @param string $val
     * @return string|bool
     */
    public function getCorrectCurrency($val)
    {
        if ($this->isValidCurrency($val)) {
            return $val;
        }

        // $val = strtolower($val);
        $val = mb_strtolower($val);
        $val = $this->keywordTranslation($val, $this->keywords);
        $val = strtoupper($val);
        if ($this->isValidCurrency($val)) {
            return $val;
        }

        return false;
    }


    /**
     * Regex
     * create a regex from the
     * available currencies array
     *
     * @return string
     */
    private function matchRegex()
    {
        $currencies = $this->currencyList;
        $params = implode('|', array_keys($currencies));
        $params .= '|' . implode('|', array_values($currencies));
        $translation_keywords = $this->keywords;

        if (!empty($translation_keywords)) {
            $params .= '|' . implode('|', array_keys($translation_keywords));
        }
        $params = $this->escapeKeywords($params);

        return '(' . $params . ')';
    }


    /**
     * Is valid
     * check if given srting
     * is valid and exists in the
     * available array
     *
     * @param string $val
     * @return bool
     */
    public function isValidCurrency($val)
    {
        return isset($this->currencyList[$val]);
    }


    /**
     * Get the base currency
     * defined in the workflow configuration
     *
     * @return array
     */
    public function getBaseCurrency()
    {
        $currency = $this->getSetting('base_currency', 'USD');
        if (is_string($currency)) { // convert old setting to array
            $currency = [$currency];
        }

        return $currency;
    }


    /**
     * Get list
     * get a readable units list
     * to display to the user
     *
     * @return array
     */
    function listAvailable()
    {
        $translation = $this->getTranslation('currency');
        $units = $this->currencyList;
        $list = [];
        foreach ($units as $key => $value) {
            $curr = $key;
            $curr_name = (isset($translation[$curr]) ? $translation[$curr] : $curr);

            $list[] = [
                'title' => $curr,
                'subtitle' => $curr_name,
                'arg' => $curr,
                'match' => $curr . '  ' . $curr_name,
                'autocomplete' => $curr_name,
                'valid' => true,
                'mods' => [
                    'cmd' => [
                        'valid' => true,
                        'arg' => $curr,
                        'subtitle' => $this->getText('action_copy'),
                    ]
                ],
                'icon' => [
                    'path' => "flags/{$curr}.png"
                ]
            ];
        }

        return $list;
    }

    /**
     * Requred
     * includes requred files
     * only if conversion can be processed
     *
     * @return void
     */
    private function required()
    {

        // $dir = __DIR__ . DIRECTORY_SEPARATOR . 'currency' . DIRECTORY_SEPARATOR;
        $dir = dirname(__DIR__, 1) . '/lib/currency';

        include $dir . '/Cache/Adapter/CacheAdapterInterface.php';
        include $dir . '/Cache/Adapter/AbstractAdapter.php';
        include $dir . '/Cache/Adapter/FileSystem.php';
        include $dir . '/Cache/Adapter/ZendAdapter.php';

        include $dir . '/Provider/ProviderInterface.php';
        include $dir . '/Provider/ExchangeRatesIo.php';
        include $dir . '/Provider/FixerApi.php';

        include $dir . '/Exception/ExceptionInterface.php';
        include $dir . '/Exception/InvalidArgumentException.php';
        include $dir . '/Exception/RunTimeException.php';
        include $dir . '/Exception/UnsupportedCurrencyException.php';

        include $dir . '/guzzle/Exception/GuzzleException.php';
        include $dir . '/guzzle/Exception/TransferException.php';
        include $dir . '/guzzle/Exception/RequestException.php';
        include $dir . '/guzzle/Exception/BadResponseException.php';
        include $dir . '/guzzle/Exception/ConnectException.php';
        include $dir . '/guzzle/Exception/ClientException.php';
        include $dir . '/guzzle/Exception/InvalidArgumentException.php';
        include $dir . '/guzzle/Exception/SeekException.php';
        include $dir . '/guzzle/Exception/ServerException.php';
        include $dir . '/guzzle/Exception/TooManyRedirectsException.php';
        include $dir . '/guzzle/Handler/Proxy.php';

        include $dir . '/guzzle/Handler/EasyHandle.php';
        include $dir . '/guzzle/Handler/CurlMultiHandler.php';
        include $dir . '/guzzle/Handler/CurlHandler.php';
        include $dir . '/guzzle/Handler/CurlFactoryInterface.php';
        include $dir . '/guzzle/Handler/CurlFactory.php';
        include $dir . '/guzzle/Handler/StreamHandler.php';
        include $dir . '/guzzle/functions.php';
        include $dir . '/guzzle/PrepareBodyMiddleware.php';
        include $dir . '/guzzle/Middleware.php';
        include $dir . '/guzzle/RedirectMiddleware.php';
        include $dir . '/guzzle/RequestOptions.php';
        include $dir . '/guzzle/HandlerStack.php';
        include $dir . '/PSR7/functions.php';
        include $dir . '/PSR7/UriInterface.php';
        include $dir . '/PSR7/MessageInterface.php';
        include $dir . '/PSR7/Uri.php';
        include $dir . '/PSR7/MessageTrait.php';
        include $dir . '/PSR7/ResponseInterface.php';
        include $dir . '/PSR7/Response.php';
        include $dir . '/PSR7/RequestInterface.php';
        include $dir . '/PSR7/Request.php';
        include $dir . '/PSR7/StreamInterface.php';
        include $dir . '/PSR7/Stream.php';

        include $dir . '/guzzle/Promise/functions.php';
        include $dir . '/guzzle/Promise/TaskQueueInterface.php';
        include $dir . '/guzzle/Promise/TaskQueue.php';
        include $dir . '/guzzle/Promise/PromiseInterface.php';
        include $dir . '/guzzle/Promise/Promise.php';
        include $dir . '/guzzle/Promise/FulfilledPromise.php';

        include $dir . '/guzzle/ClientInterface.php';
        include $dir . '/guzzle/Client.php';

        include $dir . '/CurrencyConverterInterface.php';
        include $dir . '/CurrencyConverter.php';
        include $dir . '/CountryToCurrency.php';
    }
}
