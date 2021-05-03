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
    private static $fixer_rates;
    private static $basic_rates;

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
        $this->rates_cache_seconds = 86400;
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

        if (!empty($converted['error'])) {
            return $this->outputError($converted);
        }

        $data['converted'] = [];
        foreach ($converted as $key => $value) {
            $data['converted'][$key] = [];

            $total = money_format('%i', $value['total']);
            $total = preg_replace('/[^0-9.,]/', '', $total);
            $single = $value['single'];
            $single = money_format('%i', $single);
            $single = preg_replace("/\w+[^0-9-., ]/", '', $single);
            //$single = $this->formatNumber($value['single']);

            $data['converted'][$key]['total'] = ['value' => $total, 'formatted' => "{$total} {$key}"];
            $data['converted'][$key]['single'] = ['value' => $single, 'formatted' => "1 {$data['from']} = {$single} {$key}"];
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
            $icon = 'assets/flags/' . $key . '.png';

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
        $converted = [];
        $amount = $data['amount'];
        $from = $data['from'];
        $to = $data['to'];
        $use_cache = (isset($data['use_cache']) ? $data['use_cache'] : true);
        $fixer_apikey = $this->getSetting('fixer_apikey');
        $method = !empty($fixer_apikey) ? 'fixer' : 'exchangeratehost';

        if (is_string($to)) {
            $to = [$to];
        }

        foreach ($to as $currency) {
            // Use Fixer io
            if ($method == 'fixer') {
                $cache_seconds = ($use_cache ? $this->rates_cache_seconds : 0);
                $conversion = $this->fixerConversion($amount, $from, $currency);
            } elseif ($method == 'exchangeratehost') {
                $conversion = $this->exchangeRateHostConversion($amount, $from, $currency);
            }

            if (isset($conversion['error']) && !empty($conversion['error'])) {
                $converted['error'] = $conversion['error'];
            }
            if (isset($conversion['reload']) && !empty($conversion['reload'])) {
                $converted['reload'] = $conversion['reload'];
            }
            $converted[$currency] = $conversion;
        }

        return $converted;
    }


    /**
     * Fixer.io conversion
     *
     * @param int $amount
     * @param string $from
     * @param string $to
     * @param int $cache_seconds
     * @return array
     */
    private function fixerConversion($amount, $from, $to)
    {
        $apikey = $this->getSetting('fixer_apikey');
        if (empty($apikey)) {
            return [
                'total' => '',
                'single' => '',
                'error' => $this->lang['nofixerapikey_title'],
            ];
        }

        $exchange = self::$fixer_rates;
        if (!$exchange) {
            $cache_seconds = $this->rates_cache_seconds;
            $ratesURL = "http://data.fixer.io/api/latest?access_key={$apikey}&format=1";
            $exchange = $this->getRates('fixer', $ratesURL, $cache_seconds);

            if (isset($exchange['error'])) {
                return [
                    'total' => '',
                    'single' => '',
                    'error' => $exchange['error'],
                    'reload' => isset($exchange['reload']) ? $exchange['reload'] : false,
                ];
            }

            self::$fixer_rates = $exchange;
        }

        $base = $exchange['base'];
        $rates = $exchange['rates'];
        $default_base_currency = $rates[$base];

        $new_base_currency = $rates[$from]; //from currency
        $base_exchange = $default_base_currency / $new_base_currency;
        $value = ($rates[$to] * $base_exchange);
        $total = $amount * $value;

        return ['total' => $total, 'single' => $value, 'error' => false];
    }


    /**
     * exchangerate.host
     *
     * @param int $amount
     * @param string $from
     * @param string $to
     * @param int $cache_seconds
     * @return array
     */
    public function exchangeRateHostConversion($amount, $from, $to)
    {
        //$basic_rates
        $exchange = self::$basic_rates;
        if (!$exchange) {
            $cache_seconds = $this->rates_cache_seconds;
            $ratesURL = "https://api.exchangerate.host/latest";
            $exchange = $this->getRates('exchangeratehost', $ratesURL, $cache_seconds);

            if (!isset($exchange['success']) || !$exchange['success']) {
                return [
                    'total' => '',
                    'single' => '',
                    'error' => $exchange['error'],
                    'reload' => isset($exchange['reload']) ? $exchange['reload'] : false,
                ];
            }

            self::$basic_rates = $exchange;
        }

        $base = $exchange['base'];
        $rates = $exchange['rates'];
        $default_base_currency = $rates[$base];

        $new_base_currency = $rates[$from]; //from currency
        $base_exchange = $default_base_currency / $new_base_currency;
        $value = ($rates[$to] * $base_exchange);
        $total = $amount * $value;

        return ['total' => $total, 'single' => $value, 'error' => false];
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
    private function getRates($id, $from, $cache_seconds)
    {
        $ratesURL = $from;
        $cache_path = \Alfred\getDataPath('cache');
        $dir = $cache_path .'/' . $id;

        // Make sure the cache folder is created
        \Alfred\createDir($cache_path);
        \Alfred\createDir($dir);

        $rates_file = $dir . '/rates.json';
        //$ratesURL = str_replace('?', '\?', $ratesURL);
        //$ratesURL = str_replace('=', '\=', $ratesURL);
        //$ratesURL = str_replace('&', '\&', $ratesURL);
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
                    return $rates;
                }
            }
        }

        $rates = file_get_contents($ratesURL);
        if (empty($rates)) {
            return [
                'error' => $this->lang['fetch_error'],
                'reload' => 0.1,
            ];
        }

        $rates = json_decode($rates, true);
        $rates['last_updated'] = time();

        file_put_contents($rates_file, json_encode($rates));

        return $rates;
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
     * Curerncy locales
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
