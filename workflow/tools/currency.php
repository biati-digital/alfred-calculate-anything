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
    private $rates_cache_seconds;
    private static $rates;
    private static $display_updating_message;

    /**
     * Construct
     */
    public function __construct($query)
    {
        $this->query = $query;
        $this->lang = $this->getTranslation('currency');
        $this->keywords = $this->getKeywords('currency');
        $this->stop_words = $this->getStopWords('currency');
        $this->currencyList = $this->currencies();
        $this->rates_cache_seconds = $this->getCacheDuration();
        $this->setUpdatingMessageDisplay(true);
    }


    /**
     * currencies
     * Cryptocurrency currencies
     *
     * @return array
     */
    public function currencies()
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
     * Duration of the cache
     * before calling the API again
     * @return int
     */
    private function getCacheDuration(): int
    {
        $duration = 86400;
        $customCacheExpire = $this->getSetting('currency_cache_hours', '');
        if (!empty($customCacheExpire) && is_numeric($customCacheExpire)) {
            $customCacheExpire = (int)$customCacheExpire;
            $duration = $customCacheExpire * 3600;
        }

        return $duration;
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

        return preg_match('/^([-\d+\.,\s]*) ?' . $currencies . ' ?' . $stopwords . '?/i', $query, $matches);
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

        $data['converted'] = [];
        if (
            $data['amount'] <= 0 ||
            (!empty($data['to']['currency']) && count($data['to']['currency']) === 1 && $data['from'] == $data['to']['currency'][0])
        ) {
            $_to = $data['to']['currency'][0];
            $data['converted'][$_to . '_currency'] = [
                'total' => ['value' => $data['amount'], 'formatted' => "{$data['amount']} $_to"],
                'single' => ['value' => 1, 'formatted' => "1 {$data['from']} = 1 $_to"],
                'symbol' => $_to,
                'icon' => 'flags/' . $_to . '.png',
            ];
            return $this->output($data);
        }

        $converted = $this->convert($data);
        $decimals = $this->getSetting('currency_decimals', 2);

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
                $data['converted'][$currency_key]['icon'] = 'flags/' . $key . '.png';
            }
        }

        if (!empty($converted['crypto'])) {
            foreach ($converted['crypto'] as $key => $value) {
                $currency_key = $key . '_crypto';
                $data['converted'][$currency_key] = [];
                $total = $this->formatNumber($value['total'], $decimals);
                $single = $this->formatNumber($value['single'], -1);

                $formatted_text = "{$total} {$key}";
                $formatted_single_text = "1 {$data['from']} = {$single} {$key}";

                $name = self::$cryptocurrencyCalculator->getCryptoFullName($key);
                if (!empty($name)) {
                    $formatted_single_text .= " - {$name}";
                }

                $data['converted'][$currency_key]['total'] = ['value' => $total, 'formatted' => $formatted_text];
                $data['converted'][$currency_key]['single'] = ['value' => $single, 'formatted' => $formatted_single_text];
                $data['converted'][$currency_key]['symbol'] = $key;
                $data['converted'][$currency_key]['icon'] = '';
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
     * @return array
     */
    public function output($result)
    {
        $items = [];
        $converted = $result['converted'];

        foreach ($converted as $value) {
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
     * @return array
     */
    public function convert($data)
    {
        $converted = ['currency' => false, 'crypto' => false, 'error' => false, 'reload' => false];
        $amount = $data['amount'];
        $from = $data['from'];

        $to_currency = isset($data['to']['currency']) ? $data['to']['currency'] : false;
        $to_crypto = isset($data['to']['crypto']) ? $data['to']['crypto'] : false;

        if (!empty($to_currency)) {
            $converted['currency'] = [];
            foreach ($to_currency as $currency) {
                $conversion = $this->exchangeConversion($amount, $from, $currency);

                if (isset($conversion['error']) && !empty($conversion['error'])) {
                    $converted['error'] = $conversion['error'];
                }
                if (isset($conversion['reload']) && !empty($conversion['reload'])) {
                    $converted['reload'] = $conversion['reload'];
                }
                $converted['currency'][$currency] = $conversion;
            }
        }

        if (empty($converted['reload']) && empty($converted['error']) && !empty($to_crypto)) {
            $converted['crypto'] = [];
            foreach ($to_crypto as $crypto) {

                // Get the crypto value in USD
                // and make the conversion
                $crypto_conversion = self::$cryptocurrencyCalculator->getCryptoData($crypto);
                if (!empty($crypto_conversion)) {
                    $total = 0;
                    $single = 0;
                    $_amount = $amount;

                    // crypto prices are always returned in USD
                    $crypto_price = $crypto_conversion['price'];

                    if ($from === 'USD') {
                        $total = $amount / $crypto_price;
                        $single = 1000 / $crypto_price;
                    } else {
                        // convert to USD first
                        self::$display_updating_message = false;
                        $conversion = $this->exchangeConversion($amount, $from, 'USD');
                        if (!empty($conversion)) {
                            $amount = $conversion['total'];
                            $total = $amount / $crypto_price;
                            $single = $total / $_amount;
                        }
                    }

                    $converted['crypto'][$crypto] = [
                        'total' => $total,
                        'single' => $single,
                        'error' => '',
                        'reload' => false,
                    ];
                }
            }
        }

        return $converted;
    }


    /**
     * Exchange conversion
     *
     * @param int $amount
     * @param string $from
     * @param string $to
     * @return array
     */
    public function exchangeConversion($amount, $from, $to)
    {
        $exchange = self::$rates;
        if (empty($exchange)) {
            $exchange = $this->getRates();
        }

        if (isset($exchange['error'])) {
            $error_message = !empty($exchange['error']['info']) ? $exchange['error']['info'] : $exchange['error'];
            return [
                'total' => '',
                'single' => '',
                'error' => $error_message,
                'reload' => isset($exchange['reload']) ? $exchange['reload'] : false,
            ];
        }

        if (
            isset($exchange['message']) &&
            (str_contains($exchange['message'], 'Invalid') || str_contains($exchange['message'], 'API key'))
        ) {
            return [
                'total' => '',
                'single' => '',
                'error' => $exchange['message'],
                'reload' => false
            ];
        }

        if (isset($exchange['reload'])) {
            return [
                'total' => '',
                'single' => '',
                'error' => $exchange['message'],
                'reload' => $exchange['reload'],
            ];
        }

        $base = $exchange['base'];
        $rates = $exchange['rates'];
        $default_base_currency = $rates[$base];

        $new_base_currency = $rates[$from]; //from currency
        $base_exchange = $default_base_currency / $new_base_currency;
        $value = ($rates[$to] * $base_exchange);
        $total = $amount * $value;

        return [
            'total' => $total,
            'single' => $value,
            'name' => $this->currencyList[$to],
            'slug' => $to,
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
        $default_currency = $this->getBaseCurrency();
        $stopwords = $this->getStopWordsString($this->stop_words, ' %s ');

        preg_match('/^([0-9,.\s]+)[^\d]/i', $query, $amount_match);
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
        }
        // String is like 100 usd or 100 usd mxn
        elseif (empty($matches)) {
            $keywords = $this->keywords;

            uksort($keywords, function ($keya, $keyb) {
                return strlen($keyb) - strlen($keya);
            });

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

        $from = $this->getCorrectCurrency(strtoupper($from));
        $_to = strtoupper($to);
        $to = !empty($to) ? $this->getCorrectCurrency($_to) : '';
        $convert_to = ['currency' => false, 'crypto' => false];

        // there's 4 possible cases
        // 1 - $to is provided and $to is a regular currency (like pln) but also a crypto
        // 2 - $to is provided and $to is regular currency
        // 3 - $to is provided and $to is crypto
        // 4 - no $to is provided so we default to $default_currency if they are defined

        if (!empty($to) && is_string($to)) {
            $to_crypto = self::$cryptocurrencyCalculator->getCorrectSymbol($_to);

            if ($to_crypto) {
                // Handle case 1
                $convert_to['currency'] = [$to];
                $convert_to['crypto'] = [$to_crypto];
            } else {
                // Handle case 2
                $convert_to['currency'] = [$to];
            }
        } elseif (empty($to) && !empty($_to)) {
            // // Handle case 3
            $to_crypto = self::$cryptocurrencyCalculator->getCorrectSymbol($_to);
            if ($to_crypto) {
                $convert_to['crypto'] = [$to_crypto];
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
     * it should return an array in the following format
     * [
     *     [success] => 1
     *     [base] => EUR
     *     [rates] => [
     *        [AED] => 3.94891
     *        ...
     *     ]
     * ]
     *
     * @param int $cache_seconds number of seconds before the cache expires
     * @return mixed array if success or string with error message
     */
    private function getRates()
    {
        $configured_exchange = $this->getConfiguredExchangeData();
        $cache_seconds = $this->rates_cache_seconds;
        $id = $configured_exchange['id'];
        $from = $configured_exchange['url'];
        $api_key = $configured_exchange['apiKey'];
        $http_headers = $configured_exchange['headers'];
        $cache_path = \Alfred\getDataPath('cache');
        $dir = $cache_path . '/' . $id;

        if (empty($api_key)) {
            return [
                'message' => $this->lang['nofixerapikey_title']
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
                if ($rates['success'] && $time < $cache_seconds) {
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

        if (empty($http_headers)) {
            $http_headers = ['Accepts: application/json'];
        }

        $rates = $this->doRequest($from, $http_headers);

        if (!empty($rates['error'])) {
            return [
                'error' => $rates['error']
            ];
        }

        if (empty($rates) || !is_array($rates)) {
            return [
                'error' => $this->lang['fetch_error'],
                'reload' => 0.1,
            ];
        }

        $rates['last_updated'] = time();

        if (isset($rates['success']) && $rates['success']) {
            file_put_contents($rates_file, json_encode($rates));
        }

        self::$rates = $rates;

        return $rates;
    }


    /**
     * The entire pourpose of this method
     * is to get the correct API endpoint
     * it requires a big ass method
     * because fixer changed it's endpoint a few months ago
     * we need to verify if the user API is from
     * the previous endpoint or the new one
     * and store it in a workflow variable
     * once the old endpoint stops working completely
     * we can remove this method and just add
     * the urls directly in the getRates method
     */
    private function getConfiguredExchangeData()
    {
        $id = 'fixer';
        $api_url = "https://api.apilayer.com/fixer/latest";
        $headers = [];
        $fixer_apikey = $this->getSetting('fixer_apikey');

        if (!empty($fixer_apikey)) {
            $id = 'fixer';
            $apiSource = \Alfred\getVariable('fixer_source_api', '');
            $old_api = "http://data.fixer.io/api/latest?access_key={$fixer_apikey}&format=1";
            $new_url = 'https://api.apilayer.com/fixer/latest';
            $new_headers = [
                "Content-Type: text/plain",
                "apikey: {$fixer_apikey}",
            ];

            // Fixer moved it's API to API layer
            // old API keys will not work with API Layer and new API Keys
            // will not work with the old API URL we need to validate
            // the API key and save the correct source to avoid duplicated
            // API calls, eventually the old API will stop working
            // make request to check what's the correct endpoint
            if (empty($apiSource)) {
                $req = $this->doRequest($old_api);

                if ($req && !empty($req['success'])) {
                    $apiSource = 'fixer_io';
                } else {
                    // If the API key does not work with the deprecated url, try with the new one
                    $req = $this->doRequest($new_url, $new_headers);
                    if ($req && !empty($req['success'])) {
                        $apiSource = 'fixer_apilayer';
                    }
                }

                if (!empty($apiSource)) {
                    \Alfred\setVariable('fixer_source_api', $apiSource, false);
                }
            }

            if ($apiSource === 'fixer_io') {
                $api_url = $old_api;
            }
            if ($apiSource === 'fixer_apilayer') {
                $api_url = $new_url;
                $headers = $new_headers;
            }
        }

        return [
            'id' => $id,
            'url' => $api_url,
            'headers' => $headers,
            'apiKey' => $fixer_apikey
        ];
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
     * check if given string
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
    public function listAvailable()
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
                'variables' => ['action' => 'clipboard'],
                'icon' => [
                    'path' => "assets/flags/{$curr}.png"
                ]
            ];
        }

        return $list;
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
     * Currency locales
     *
     * @return array
     */
    public function currencyLocales()
    {
        return [
            'en_US',
            'af_ZA',
            'am_ET',
            'ar_AE',
            'ar_BH',
            'ar_DZ',
            'ar_EG',
            'ar_IQ',
            'ar_JO',
            'ar_KW',
            'ar_LB',
            'ar_LY',
            'ar_MA',
            'ar_OM',
            'ar_QA',
            'ar_SA',
            'ar_SY',
            'ar_TN',
            'ar_YE',
            'az_Cyrl_AZ',
            'az_Latn_AZ',
            'be_BY',
            'bg_BG',
            'bn_BD',
            'bs_Cyrl_BA',
            'bs_Latn_BA',
            'cs_CZ',
            'da_DK',
            'de_AT',
            'de_CH',
            'de_DE',
            'de_LI',
            'de_LU',
            'dv_MV',
            'el_GR',
            'en_AU',
            'en_BZ',
            'en_CA',
            'en_GB',
            'en_IE',
            'en_JM',
            'en_MY',
            'en_NZ',
            'en_SG',
            'en_TT',
            'en_ZA',
            'en_ZW',
            'es_AR',
            'es_BO',
            'es_CL',
            'es_CO',
            'es_CR',
            'es_DO',
            'es_EC',
            'es_ES',
            'es_GT',
            'es_HN',
            'es_MX',
            'es_NI',
            'es_PA',
            'es_PE',
            'es_PR',
            'es_PY',
            'es_SV',
            'es_US',
            'es_UY',
            'es_VE',
            'et_EE',
            'fa_IR',
            'fi_FI',
            'fil_PH',
            'fo_FO',
            'fr_BE',
            'fr_CA',
            'fr_CH',
            'fr_FR',
            'fr_LU',
            'fr_MC',
            'he_IL',
            'hi_IN',
            'hr_BA',
            'hr_HR',
            'hu_HU',
            'hy_AM',
            'id_ID',
            'ig_NG',
            'is_IS',
            'it_CH',
            'it_IT',
            'ja_JP',
            'ka_GE',
            'kk_KZ',
            'kl_GL',
            'km_KH',
            'ko_KR',
            'ky_KG',
            'lb_LU',
            'lo_LA',
            'lt_LT',
            'lv_LV',
            'mi_NZ',
            'mk_MK',
            'mn_MN',
            'ms_BN',
            'ms_MY',
            'mt_MT',
            'nb_NO',
            'ne_NP',
            'nl_BE',
            'nl_NL',
            'pl_PL',
            'prs_AF',
            'ps_AF',
            'pt_BR',
            'pt_PT',
            'ro_RO',
            'ru_RU',
            'rw_RW',
            'sv_SE',
            'si_LK',
            'sk_SK',
            'sl_SI',
            'sq_AL',
            'sr_Cyrl_BA',
            'sr_Cyrl_CS',
            'sr_Cyrl_ME',
            'sr_Cyrl_RS',
            'sr_Latn_BA',
            'sr_Latn_CS',
            'sr_Latn_ME',
            'sr_Latn_RS',
            'sw_KE',
            'tg_Cyrl_TJ',
            'th_TH',
            'tk_TM',
            'tr_TR',
            'uk_UA',
            'ur_PK',
            'uz_Cyrl_UZ',
            'uz_Latn_UZ',
            'vi_VN',
            'wo_SN',
            'yo_NG',
            'zh_CN',
            'zh_HK',
            'zh_MO',
            'zh_SG',
            'zh_TW'
        ];
    }
}
