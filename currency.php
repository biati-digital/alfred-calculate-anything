<?php

/**
 * Currency
 * Handle currency conversions
 * for example 100usd to mxn
 * 100 usd mxn
 * 100usd mxn
 * 100eur
 */

require_once(__DIR__ . '/functions.php');

/**
 * List of currencies
 * avaulable currencies
 *
 * @return array
 */
function get_available_currencies()
{
    return [
        'AED' => "United Arab Emirates dirham",
        'AFN' => "Afghan afghani",
        'ALL' => "Albanian lek",
        'AMD' => "Armenian dram",
        'ANG' => "Netherlands Antillean guilder",
        'AOA' => "Angolan kwanza",
        'ARS' => "Argentine peso",
        'AUD' => "Australian dollar",
        'AWG' => "Aruban florin",
        'AZN' => "Azerbaijani manat",
        'BAM' => "Bosnia and Herzegovina convertible mark",
        'BBD' => "Barbados dollar",
        'BDT' => "Bangladeshi taka",
        'BGN' => "Bulgarian lev",
        'BHD' => "Bahraini dinar",
        'BIF' => "Burundian franc",
        'BMD' => "Bermudian dollar",
        'BND' => "Brunei dollar",
        'BOB' => "Boliviano",
        'BRL' => "Brazilian real",
        'BSD' => "Bahamian dollar",
        'BTN' => "Bhutanese ngultrum",
        'BWP' => "Botswana pula",
        'BYN' => "New Belarusian ruble",
        'BYR' => "Belarusian ruble",
        'BZD' => "Belize dollar",
        'CAD' => "Canadian dollar",
        'CDF' => "Congolese franc",
        'CHF' => "Swiss franc",
        'CLF' => "Unidad de Fomento",
        'CLP' => "Chilean peso",
        'CNY' => "Renminbi|Chinese yuan",
        'COP' => "Colombian peso",
        'CRC' => "Costa Rican colon",
        'CUC' => "Cuban convertible peso",
        'CUP' => "Cuban peso",
        'CVE' => "Cape Verde escudo",
        'CZK' => "Czech koruna",
        'DJF' => "Djiboutian franc",
        'DKK' => "Danish krone",
        'DOP' => "Dominican peso",
        'DZD' => "Algerian dinar",
        'EGP' => "Egyptian pound",
        'ERN' => "Eritrean nakfa",
        'ETB' => "Ethiopian birr",
        'EUR' => "Euro",
        'FJD' => "Fiji dollar",
        'FKP' => "Falkland Islands pound",
        'GBP' => "Pound sterling",
        'GEL' => "Georgian lari",
        'GHS' => "Ghanaian cedi",
        'GIP' => "Gibraltar pound",
        'GMD' => "Gambian dalasi",
        'GNF' => "Guinean franc",
        'GTQ' => "Guatemalan quetzal",
        'GYD' => "Guyanese dollar",
        'HKD' => "Hong Kong dollar",
        'HNL' => "Honduran lempira",
        'HRK' => "Croatian kuna",
        'HTG' => "Haitian gourde",
        'HUF' => "Hungarian forint",
        'IDR' => "Indonesian rupiah",
        'ILS' => "Israeli new shekel",
        'INR' => "Indian rupee",
        'IQD' => "Iraqi dinar",
        'IRR' => "Iranian rial",
        'ISK' => "Icelandic króna",
        'JMD' => "Jamaican dollar",
        'JOD' => "Jordanian dinar",
        'JPY' => "Japanese yen",
        'KES' => "Kenyan shilling",
        'KGS' => "Kyrgyzstani som",
        'KHR' => "Cambodian riel",
        'KMF' => "Comoro franc",
        'KPW' => "North Korean won",
        'KRW' => "South Korean won",
        'KWD' => "Kuwaiti dinar",
        'KYD' => "Cayman Islands dollar",
        'KZT' => "Kazakhstani tenge",
        'LAK' => "Lao kip",
        'LBP' => "Lebanese pound",
        'LKR' => "Sri Lankan rupee",
        'LRD' => "Liberian dollar",
        'LSL' => "Lesotho loti",
        'LYD' => "Libyan dinar",
        'MAD' => "Moroccan dirham",
        'MDL' => "Moldovan leu",
        'MGA' => "Malagasy ariary",
        'MKD' => "Macedonian denar",
        'MMK' => "Myanmar kyat",
        'MNT' => "Mongolian tögrög",
        'MOP' => "Macanese pataca",
        'MRO' => "Mauritanian ouguiya",
        'MUR' => "Mauritian rupee",
        'MVR' => "Maldivian rufiyaa",
        'MWK' => "Malawian kwacha",
        'MXN' => "Mexican peso",
        'MXV' => "Mexican Unidad de Inversion",
        'MYR' => "Malaysian ringgit",
        'MZN' => "Mozambican metical",
        'NAD' => "Namibian dollar",
        'NGN' => "Nigerian naira",
        'NIO' => "Nicaraguan córdoba",
        'NOK' => "Norwegian krone",
        'NPR' => "Nepalese rupee",
        'NZD' => "New Zealand dollar",
        'OMR' => "Omani rial",
        'PAB' => "Panamanian balboa",
        'PEN' => "Peruvian Sol",
        'PGK' => "Papua New Guinean kina",
        'PHP' => "Philippine peso",
        'PKR' => "Pakistani rupee",
        'PLN' => "Polish złoty",
        'PYG' => "Paraguayan guaraní",
        'QAR' => "Qatari riyal",
        'RON' => "Romanian leu",
        'RSD' => "Serbian dinar",
        'RUB' => "Russian ruble",
        'RWF' => "Rwandan franc",
        'SAR' => "Saudi riyal",
        'SBD' => "Solomon Islands dollar",
        'SCR' => "Seychelles rupee",
        'SDG' => "Sudanese pound",
        'SEK' => "Swedish krona",
        'SGD' => "Singapore dollar",
        'SHP' => "Saint Helena pound",
        'SLL' => "Sierra Leonean leone",
        'SOS' => "Somali shilling",
        'SRD' => "Surinamese dollar",
        'SSP' => "South Sudanese pound",
        'STD' => "São Tomé and Príncipe dobra",
        'SVC' => "Salvadoran colón",
        'SYP' => "Syrian pound",
        'SZL' => "Swazi lilangeni",
        'THB' => "Thai baht",
        'TJS' => "Tajikistani somoni",
        'TMT' => "Turkmenistani manat",
        'TND' => "Tunisian dinar",
        'TOP' => "Tongan paʻanga",
        'TRY' => "Turkish lira",
        'TTD' => "Trinidad and Tobago dollar",
        'TWD' => "New Taiwan dollar",
        'TZS' => "Tanzanian shilling",
        'UAH' => "Ukrainian hryvnia",
        'UGX' => "Ugandan shilling",
        'USD' => "United States dollar",
        'UYI' => "Uruguay Peso en Unidades Indexadas",
        'UYU' => "Uruguayan peso",
        'UZS' => "Uzbekistan som",
        'VEF' => "Venezuelan bolívar",
        'VND' => "Vietnamese đồng",
        'VUV' => "Vanuatu vatu",
        'WST' => "Samoan tala",
        'XAF' => "Central African CFA franc",
        'XCD' => "East Caribbean dollar",
        'XOF' => "West African CFA franc",
        'XPF' => "CFP franc",
        'YER' => "Yemeni rial",
        'ZAR' => "South African rand",
        'ZMW' => "Zambian kwacha",
        'ZWL' => "Zimbabwean dollar"
    ];
}


/**
 * Get units list
 * get a readable units list
 * to display to the user
 *
 * @return array
 */
function get_currencies_list()
{
    $translation = get_translation('currency');
    $units = get_available_currencies();
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
                    'subtitle' => get_text('action_copy'),
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
 * Regex
 * create a regex from the
 * available currencies array
 *
 * @return string
 */
function available_currencies_regex()
{
    $currencies = get_available_currencies();
    $params = implode('|', array_keys($currencies));
    $translated_currencies = translated_currencies();
    $params .= '|'. implode('|', array_keys($translated_currencies));
    $params = str_replace('$', '\$', $params);
    $params = str_replace('/', '\/', $params);
    $params = str_replace('.', '\.', $params);

    return '(' . $params . ')';
}

/**
 * Is valid
 * check if given currency
 * is valid and exists in the
 * currencies array
 *
 * @param string $val
 * @return boolean
 */
function is_valid_currency($val)
{
    $currencies = get_available_currencies();
    return isset($currencies[$val]);
}

/**
 * Is currency
 * validate if guven query
 * is a currency conversion string
 *
 * @param string $query
 * @return boolean
 */
function is_currency($query)
{
    $currencies = available_currencies_regex();
    $stopwords = currency_stopwords();
    return preg_match('/^\d+ ?' . $currencies . ' ?'. $stopwords .'?/i', $query, $matches);
}

/**
 * Currency stop words
 * words that can be used in the query
 * when using natural languge like
 * 100usd to mxn - here the word "to" is a stop word
 *
 * @param mixed $sep
 * @return string
 */
function currency_stopwords($sep = false)
{
    $keys = get_extra_keywords('currency');
    $stop_words = get_stopwords_string($keys['stop_words'], $sep);

    return $stop_words;
}


/**
 * Process
 * process conversion
 *
 * @param string $query
 * @return mixed
 */
function process_currency_conversion($query)
{
    $settigs = get_settings();
    $regex = available_currencies_regex();
    $stopwords = currency_stopwords(' %s ?');
    $query = preg_replace('!\s+!', ' ', $query);
    $query = preg_replace("/" . $stopwords . " ?/i", ' ', $query);
    $query = preg_replace('/(\d) ' . $regex . '/i', '$1$2', $query); //remove spaces between 100 USD = 100USD

    $data = explode(' ', $query);
    $data = array_filter($data);

    if (count($data) == 1) {
        $from = trim($data[0]);
        $to = get_setting('base_currency', 'USD', $settigs);
    }

    if (count($data) == 2) {
        $from = trim($data[0]);
        $to = trim($data[1]);
    }

    if (count($data) == 3) {
        $from = trim($data[0]) . trim($data[1]);
        $to = trim($data[2]);
    }

    if (empty($from) || empty($to)) {
        return false;
    }

    $to_currency = translated_currencies($to);
    $from_amount = preg_replace('/[^0-9.]/', '', $from);
    $from_currency = translated_currencies(preg_replace('/[0-9]+/', '', $from));
    $from_currency = trim(strtoupper($from_currency));
    $to_currency = trim(strtoupper($to_currency));

    if (!is_valid_currency($from_currency) || !is_valid_currency($to_currency)) {
        return false;
    }

    if ($from_amount <= 0) {
        return $from_amount . $to_currency;
    }

    $from_amount = floatval($from_amount);

    $locale = get_setting('locale_currency', 'en_US', $settigs);
    setlocale(LC_MONETARY, $locale);

    $converted = convert_currency($from_amount, $from_currency, $to_currency);

    if ($converted['error']) {
        return $converted['error'];
    }

    $total = money_format('%.2n', $converted['total']);
    $single = $converted['single'];
    $processed = [];
    $processed[$total] = $total;
    $processed[$single] = "1{$from_currency} = {$single}{$to_currency}";

    return [
        'data' => $processed,
        'currency' => $to_currency,
    ];
}


/**
 * Actual conversion
 * use the api to convert
 * the currencies
 *
 * @param int $amount
 * @param string $from
 * @param string $to
 * @return array
 */
function convert_currency($amount, $from, $to)
{
    include_required_files();

    $converter = new CurrencyConverter\CurrencyConverter;
    $value = '';
    $error = false;

    try {
        $value = $converter->convert($from, $to);
        $value = $value;
        $value = (fmod($value, 1) !== 0.00 ? bcdiv($value, 1, 2) : $value);
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

    return ['total' => $amount * $value, 'single' => $value, 'error' => false];
}


/**
 * Requred
 * includes requred files
 * only if conversion can be processed
 *
 * @return void
 */
function include_required_files()
{
    $dir = __DIR__ . DIRECTORY_SEPARATOR . 'currency' . DIRECTORY_SEPARATOR;
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


/**
 * Translated currencies
 * Convert some keywords to
 * the actual currency so you can use
 * natual language to make conversions
 *
 * For example:
 * 100¥ to $
 * Will be converted to
 * 100JPY to usd
 *
 * Still the user can be able
 * to write 100jpy to usd
 *
 * The keywords list can be found in
 * /lang/{lang}-keys.php
 *
 * @param boolean $unit
 * @return array
 */
function translated_currencies($val = false)
{
    $tc = [];
    $keys = get_extra_keywords('currency');
    if (!empty($keys)) {
        $tc = array_merge($tc, $keys);
    }

    if (!$val) {
        return $tc;
    }

    if (isset($tc[$val])) {
        return $tc[$val];
    }

    return $val;
}
