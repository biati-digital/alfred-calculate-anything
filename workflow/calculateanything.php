<?php

namespace Workflow;

use Workflow\Tools\Percentage;
use Workflow\Tools\Cryptocurrency;
use Workflow\Tools\Currency;
use Workflow\Tools\PXEmRem;
use Workflow\Tools\Units;
use Workflow\Tools\Vat;
use Workflow\Tools\Time;
use Workflow\Tools\DataStorage;
use Workflow\Tools\Color;

class CalculateAnything
{
    protected static $translations;
    protected static $langKeywords;
    protected static $percentageCalculator;
    protected static $cryptocurrencyCalculator;
    protected static $currencyCalculator;
    protected static $pxemremCalculator;
    protected static $unitsCalculator;
    protected static $vatCalculator;
    protected static $dataStorageCalculator;
    protected static $colorCalculator;
    protected static $settings;
    protected static $updater;
    protected static $_query;
    protected static $settingsArgs = [
        'language',
        'base_currency',
        'coinmarket_apikey',
        'fixer_apikey',
        'fixer_apisource',
        'time_format',
        'time_zone',
        'vat_percentage',
        'locale_currency',
        'decimal_separator',
        'currency_cache_hours',
        'currency_decimals',
        'base_pixels',
        'cryptocurrency_cache_hours',
        'custom_cryptocurrencies',
        'datastorage_force_binary',
        'decimals',
    ];
    // new settings for Alfred 5
    protected static $settingsArgsV5 = [
        'language',
        'decimals',
        'decimal_separator',
        'number_output_format',
        'timezone',
        'base_currencies',
        'apikey_fixer',
        'apikey_coinmarket',
        'currency_decimals',
        'crypto_decimals',
        'currency_cache_hours',
        'cryptocurrency_cache_hours',
        'datastorage_force_binary',
        'vat_value',
        'date_format',
        'pixels_base',
    ];

    /**
     * Construct
     */
    public function __construct($query = '')
    {
        self::$settings = $this->loadSettings();
        self::$translations = \Alfred\getTranslation('', $this->getSetting('language'));
        self::$langKeywords = $this->getExtraKeywords();
        self::$_query = $query;
    }


    /**
     * Process initial query
     *
     * @return boolean|array
     */
    public function processQuery()
    {
        $query = $this->getQuery();
        if (empty($query)) {
            $query = 'now';
        }

        $lenght = strlen($query);
        // For all calculators that do not require a keyword
        // the passed query must have at least 3 characters
        // being the first one a number
        if ($lenght < 3 || ($query[0] !== '-' && !is_numeric($query[0])) || ($query[0] === '-' && !is_numeric($query[1]))) {
            return false;
        }

        self::$percentageCalculator = new Percentage($query);
        self::$currencyCalculator = new Currency($query);
        self::$cryptocurrencyCalculator = new Cryptocurrency($query);
        self::$pxemremCalculator = new PXEmRem($query);
        self::$unitsCalculator = new Units($query);
        self::$vatCalculator = new Vat($query);
        self::$dataStorageCalculator = new DataStorage($query);

        // Process query
        $processed = $this->processByType();

        return $processed;
    }


    /**
     * Process the query
     * checking before if query type
     * it's supported
     *
     * @return mixed
     */
    private function processByType()
    {
        $query = $this->getQuery();
        $lenght = strlen($query);
        $percentages = self::$percentageCalculator;
        $cryptocurrency = self::$cryptocurrencyCalculator;
        $currency = self::$currencyCalculator;
        $pxemrem = self::$pxemremCalculator;
        $units = self::$unitsCalculator;
        $vat = self::$vatCalculator;
        $datastorage = self::$dataStorageCalculator;
        $processed = [];

        if ($units->shouldProcess($lenght)) {
            return $units->processQuery();
        }

        if ($percentages->shouldProcess($lenght)) {
            return $percentages->processQuery();
        }

        if ($pxemrem->shouldProcess($lenght)) {
            return $pxemrem->processQuery();
        }

        if ($vat->shouldProcess($lenght)) {
            return $vat->processQuery();
        }

        if ($datastorage->shouldProcess($lenght)) {
            return $datastorage->processQuery();
        }

        if ($currency->shouldProcess($lenght)) {
            return $currency->processQuery();
        }

        if ($cryptocurrency->shouldProcess($lenght)) {
            return $cryptocurrency->processQuery();
        }

        return $processed;
    }


    /**
     * Process Vat
     * handle vat calculations
     *
     * @return array|bool
     */
    public function processVat()
    {
        $query = preg_replace('/[^\\d.,]+/', '', self::$_query);
        $vatCalculator = new Vat($query);
        $data = $vatCalculator->getVatOf($query);
        return $vatCalculator->output($data);
    }

    /**
     * Process Time
     * handle time calculations
     *
     * @return array|bool
     */
    public function processTime()
    {
        $timeCalculator = new Time(self::$_query);
        $data = $timeCalculator->processQuery();

        return $data;
    }


    /**
     * Process Color
     * handle color conversions
     *
     * @return array|bool
     */
    public function processColor()
    {
        $colorCalculator = new Color(self::$_query);
        $data = $colorCalculator->processQuery();

        return $data;
    }


    /**
     * Return a new instance
     * of a calculator
     *
     * @param string $id
     * @param string $query
     */
    public function getCalculator($id, $query = '')
    {
        $calculator = false;
        switch ($id) {
            case 'percentage':
                $calculator = new Percentage($query);
                break;
            case 'currency':
                $calculator = new Currency($query);
                break;
            case 'cryptocurrency':
                $calculator = new Cryptocurrency($query);
                break;
            case 'units':
                $calculator = new Units($query);
                break;
            case 'pxemrem':
                $calculator = new PXEmRem($query);
                break;
            default:
                # code...
                break;
        }

        return $calculator;
    }



    /**
     * Translations
     * get workflow translations
     *
     * @param string $key
     * @return array
     */
    public function getTranslation($key = '')
    {
        return self::$translations[$key];
    }

    /**
     * Translations get text
     * get workflow translation text
     *
     * @param string $key
     * @return string
     */
    public function getText($key)
    {
        $strings = $this->getTranslation('general');
        if (!is_array($strings) || !isset($strings[$key])) {
            return '';
        }
        return $strings[$key];
    }

    /**
     * Keywords
     * returns an array of keywords
     * that are used for natural language queries
     * this keywords is an array of key value pairs
     * the key is the keyword and the value is
     * the end result for example
     * 'Bitcoins' => 'BTC',
     * If the user types Bitcoins the code will
     * convert that word to BTC
     * so the user can write thinks like
     * minute, minutes, years, year, Kilometers, etc.
     * and the code will convert those words in the query
     *
     * @param string $key
     * @return array
     */
    protected function getKeywords($key = '')
    {
        return self::$langKeywords[$key];
    }

    /**
     * Get stop words
     * returns an array with all the stop
     * words of the current calculator,
     * this words are used to identify the composition
     * of the string and  then removed
     * for example in the query
     * 100 km to meters
     * to is a stop word, they can be safety removed
     *
     * @param string $key
     * @return array
     */
    protected function getStopWords($key = '')
    {
        return self::$langKeywords[$key]['stop_words'];
    }

    /**
     * Stop words string
     * a string formed by the stop
     * words, used in regex
     *
     * @param array $words
     * @param string|boolean $spaced
     * @return string
     */
    protected function getStopWordsString($words, $spaced = false)
    {
        if (is_string($words)) {
            $words = $this->getStopWords($words);
        }

        $sep = ($spaced ? ' | ' : '|');
        if (is_bool($spaced)) {
            $str = implode($sep, $words);
        }
        if (is_string($spaced)) {
            $w = [];
            foreach ($words as $word) {
                $w[] = sprintf($spaced, $word);
            }
            $str = implode('|', $w);
        }
        return '(' . $str . ')';
    }

    /**
     * Get query
     * return the query the user provided in
     *
     * @return string
     */
    protected function getQuery()
    {
        return self::$_query;
    }

    /**
     * Get settings
     * returns the workflow stored settings
     *
     * @return array
     */
    public function getSettings()
    {
        return self::$settings;
    }

    /**
     * Get setting
     * return a specific workflow setting
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getSetting($name, $default = null)
    {
        $settings = $this->getSettings();
        if (isset($settings[$name])) {
            if (empty($settings[$name]) && !is_null($default)) {
                return $default;
            }

            return $settings[$name];
        }

        if (!is_null($default)) {
            return $default;
        }

        return null;
    }


    /**
     * Load Settings
     * works for Alfred 4 settings
     * and the new settings for Alfred 5
     *
     * @return array
     */
    public function loadSettings()
    {
        // old Alfred v4 settings
        $settings = \Alfred\getVariables(self::$settingsArgs);


        if (\Alfred\getAlfredVersion() >= 5) {
            $new_settings = \Alfred\getVariables(self::$settingsArgsV5);

            if (!empty($settings['language']) && $settings['language'] !== 'en_EN' && $new_settings['language'] !== 'en_EN') {
                $settings['language'] = $new_settings['language'];
            }
            if (empty($settings['language'])) {
                $settings['language'] = $new_settings['language'];
            }
            if (!empty($new_settings['timezone']) && $new_settings['timezone'] !== 'none') {
                $settings['time_zone'] = $new_settings['timezone'];
            }
            if (!empty($new_settings['base_currencies'])) {
                $settings['base_currency'] = str_replace(' ', '', $new_settings['base_currencies']);
                $settings['base_currency'] = explode(',', $settings['base_currency']);
            }
            if (!empty($new_settings['apikey_fixer'])) {
                $settings['fixer_apikey'] = $new_settings['apikey_fixer'];
            }
            if (!empty($new_settings['apikey_coinmarket'])) {
                $settings['coinmarket_apikey'] = $new_settings['apikey_coinmarket'];
            }
            if (!empty($new_settings['crypto_decimals'])) {
                $settings['crypto_decimals'] = $new_settings['crypto_decimals'];
            }
            if (!empty($new_settings['vat_value'])) {
                $settings['vat_percentage'] = $new_settings['vat_value'];
            }
            if (!empty($new_settings['pixels_base'])) {
                $settings['base_pixels'] = $new_settings['pixels_base'];
            }
            if (
                !empty($new_settings['date_format']) && $new_settings['date_format'] !== 'j F, Y, g:i:s a' ||
                empty($settings['time_format'])
            ) {
                $settings['time_format'] = $new_settings['date_format'];
                if (is_string($settings['time_format'])) {
                    $settings['time_format'] = explode('|', $new_settings['date_format']);
                }
            }
            if (!empty($new_settings['number_output_format'])) {
                $settings['number_output_format'] = $new_settings['number_output_format'];
            }
            if (!empty($new_settings['currency_decimals'])) {
                $settings['currency_decimals'] = $new_settings['currency_decimals'];
            }
        }

        return $settings;
    }



    /**
     * Escape words
     * dos some escape for regex match
     *
     * @param string $key
     * @return string
     */
    public function escapeKeywords($key)
    {
        $key = str_replace('+', '\+', $key);
        $key = str_replace('$', '\$', $key);
        $key = str_replace('/', '\/', $key);
        $key = str_replace('.', '\.', $key);
        return $key;
    }


    /**
     * Get extra keywords
     * keywords are a list of words in natural language
     * that teh user can use in the workflow, for example
     * a keywords can be bitcoins and it will be converted to BTC
     * or the keywords kilograms will be converted to kg
     * that way natural language can be used on queries
     *
     * @param string $key
     * @param string $lang
     * @return array
     */
    public function getExtraKeywords($key = '', $lang = '')
    {
        $default_lang = 'en_EN';
        $lang = (empty($lang) ? \Alfred\getVariable('language', $default_lang) : $lang);
        $file = \Alfred\getTranslationsPath($lang . '-keys.php');

        if (file_exists($file)) {
            $translations = include $file;

            // Return default lang if translation error
            if (!is_array($translations) || empty($translations)) {
                return $this->getExtraKeywords($key, $default_lang);
            }

            // If language is different
            // from english, also load the en keys
            // so they are global
            if ($lang !== $default_lang) {
                $translations = $this->mergeWithBaseKeywords($translations);
            }

            if (empty($key)) {
                return $translations;
            }

            if (isset($translations[$key])) {
                return $translations[$key];
            }

            return false;
        }

        return $this->getExtraKeywords($key, $default_lang);
    }


    public function mergeWithBaseKeywords($keywords)
    {
        $en_keys = include \Alfred\getTranslationsPath('en_EN-keys.php');
        foreach ($en_keys as $key => $value) {
            if (!isset($keywords[$key])) {
                $keywords[$key] = $value;
                continue;
            }

            foreach ($value as $k => $v) {
                if (isset($keywords[$key][$k]) && is_array($keywords[$key][$k])) {
                    $mul = array_merge($keywords[$key][$k], $v);
                    $keywords[$key][$k] = array_unique($mul);
                } elseif (!isset($keywords[$key][$k])) {
                    $keywords[$key][$k] = $v;
                }
            }
        }

        return $keywords;
    }

    /**
     * Translated keywords
     * Convert some keywords to
     * the actual value so you can use
     * natural language to make conversions
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
     * @return array|string
     */
    public function keywordTranslation($word = false, $keywordsArray = [])
    {
        $val = mb_strtolower($word, 'UTF-8');
        $keywords = $keywordsArray;

        if (!$val) {
            return $keywords;
        }

        // IF there's an exact match
        if (isset($keywordsArray[$val])) {
            return $keywordsArray[$val];
        }

        foreach ($keywords as $key => $value) {
            if (is_array($value)) {
                continue;
            }
            $key = $this->escapeKeywords($key);
            $val = preg_replace('/(^|\W)' . $key . '\b(\W|$)/i', ' ' . $value . ' ', $val);
        }

        $val = trim($val);
        $val = preg_replace('!\s+!', ' ', $val);

        return $val;
    }


    /**
     * Cached conversion
     *
     * @param string $id
     * @param string $from
     * @param string $to
     * @param string $value
     * @return void
     */
    public function cacheConversion($id, $from, $to, $value)
    {
        $dir = \Alfred\getDataPath('cache/' . $id);
        \Alfred\createDir($dir);

        $file = $dir . '/' . $from . '-' . $to . '.txt';
        $file = str_replace(' ', '\ ', $file);
        $command = "echo \"{$value}\" >> {$file}";
        shell_exec("{$command}");

        return true;
    }


    /**
     * Get cached conversion
     * return the cached rate
     *
     * @param string $from
     * @param string $to
     * @param int $cache_seconds
     * @return mixed
     */
    public function getCachedConversion($id, $from, $to, $cache_seconds)
    {
        $cache_dir = \Alfred\getDataPath('cache');

        \Alfred\createDir($cache_dir);

        $dir = \Alfred\getDataPath('cache/' . $id);
        $file = $dir . '/' . $from . '-' . $to . '.txt';

        \Alfred\createDir($dir);

        if (!file_exists($file)) {
            return false;
        }

        $updated = filemtime($file);
        $time = time() - $updated;

        if ($time > $cache_seconds) { // cache already expired
            return false;
        }

        $val = file_get_contents($file);
        if (empty($val)) {
            return false;
        }

        return $val;
    }


    /**
     * Do request
     * make a curl request
     *
     * @param string $to target url
     * @param array  $headers
     * @param string $method
     *
     * @return array|bool|string
     */
    public function doRequest($to, $headers = [], $method = 'GET')
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $to);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        $req = curl_exec($curl);

        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            // print_r('CURL FAILED: request to ' . $to . ' failed, error was ' . $error_msg);
            $req = ['error' => $error_msg];
            return $req;
        }

        curl_close($curl);

        if (empty($req)) {
            // print_r('CURL EMPTY RESPONSE: request to ' . $to . ' returned empty ');
            $req = ['error' => 'CURL EMPTY RESPONSE'];
            return $req;
        }

        try {
            $req = json_decode($req, true);
        } catch (\Throwable $th) {
        }

        return $req;
    }


    /**
     * Cleanup number
     *
     * @param string $number
     * @return int
     */
    public function cleanupNumber(string $number)
    {
        $number = str_replace(' ', '', $number);

        if ($this->getSetting('decimal_separator', 'dot') === 'comma') {
            $number = str_replace('.', '', $number);
            $number = str_replace(',', '.', $number);
            return floatval($number);
        }

        return floatval(str_replace(',', '', $number));
    }

    /**
     * Format number
     * handle number format for the multiple
     * converters and their specific rules
     * TODO: Remove single decimal when is 0 for example 100c f = 212.0 f
     *
     * @param int $number
     * @param int $custom_decimals
     * @param bool $round
     * @return int|float
     */
    public function formatNumber($number, $decimals = -1, $round = false)
    {
        if (!is_numeric($number)) {
            return $number;
        }

        // decimals from Alfred config are returned as strings
        if (gettype($decimals) === 'string') {
            $decimals = floatval($decimals);
        }

        // Check if number is then parse as float value of that
        preg_match('/([-+]?[0-9]*\.?[0-9]+)([eE][-+]?[0-9]+)/s', $number, $matches);
        if (!empty($matches) && isset($matches[2])) {
            $number = floatval($number);
        }

        $output_format = $this->getSetting('number_output_format', 'comma_dot');

        switch ($output_format) {
            case 'dot_comma':
                $thousands_sep = '.';
                $decimal_point = ',';
                break;
            case 'space_comma':
                $thousands_sep = ' ';
                $decimal_point = ',';
                break;
            default:
                $thousands_sep = ',';
                $decimal_point = '.';
                break;
        }

        //if decimals are .00 remove them
        if (fmod($number, 1) === 0.00) {
            $decimals = 0;
        }

        // handle numbers with a lot of decimals like
        // 0.00035486302682 to bypass the decimals configured by the user
        $data = explode('.', $number . '');
        $decimals_in_number = end($data);
        $total_decimals_in_number = strlen($decimals_in_number);

        if (str_starts_with($decimals_in_number, '0')) {
            preg_match('/^(0+)([1-9]{1,})/', $decimals_in_number, $matches);
            if (!empty($matches) && !empty($matches[1]) && !empty($matches[2])) {
                if (strlen($matches[2]) > $decimals) {
                    $decimals = strlen($matches[1]) + $decimals;
                } else {
                    $decimals = strlen($matches[1]) + strlen($matches[2]);
                }
            }
        }

        if ($decimals == -1) {
            $decimals = $total_decimals_in_number;
        }

        $negation = ($number < 0) ? (-1) : 1;
        $coefficient = 10 ** $decimals;
        $number = $negation * floor((string)(abs($number) * $coefficient)) / $coefficient;

        if ($number > 1 && $decimals > $total_decimals_in_number) {
            $decimals = $total_decimals_in_number;
        }
        if ($decimals > 8) {
            $decimals = 8;
        }

        return number_format($number, $decimals, $decimal_point, $thousands_sep);
    }
}
