<?php

namespace Workflow;

use \Workflow\Tools\Percentage;
use \Workflow\Tools\Cryptocurrency;
use \Workflow\Tools\Currency;
use \Workflow\Tools\PXEmRem;
use \Workflow\Tools\Units;
use \Workflow\Tools\Vat;
use \Workflow\Tools\Time;

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
    protected static $settings;
    protected static $_query;

    /**
     * Construct
     */
    public function __construct($query = '')
    {
        self::$translations = getTranslation();
        self::$langKeywords = getExtraKeywords();
        self::$settings = getSettings();
        self::$_query = $query;
    }

    /**
     * Process initial query
     *
     * @return array
     */
    public function processQuery()
    {
        $query = $this->getQuery();
        $lenght = strlen($query);

        // For all calculators that do not require a keyword
        // the passed query must have at leats 3 characters
        // being the first one a number
        if ($lenght < 3 || !is_numeric($query[0])) {
            return false;
        }

        self::$percentageCalculator = new Percentage($query);
        self::$currencyCalculator = new Currency($query);
        self::$cryptocurrencyCalculator = new Cryptocurrency($query);
        self::$pxemremCalculator = new PXEmRem($query);
        self::$unitsCalculator = new Units($query);

        // Process query
        $processed = $this->processByType();

        if (!empty($processed)) {
            workflowUpdater();
        }

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

        if ($cryptocurrency->shouldProcess($lenght)) {
            return $cryptocurrency->processQuery();
        }

        if ($currency->shouldProcess($lenght)) {
            return $currency->processQuery();
        }

        return $processed;
    }


    /**
     * Process VAt
     * handle vat calculations
     *
     * @return array|bool
     */
    public function processVat()
    {
        $vatCalculator = new Vat(self::$_query);
        $data = $vatCalculator->processQuery();

        return $data;
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
     * Return a new instance
     * of a calclulator
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
     * returns an array of jeywords
     * that are used for natual language queries
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
     * to is a stop word, they can be safetly removed
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
    public function getSetting($name, $default = '')
    {
        return getSetting($name, $default, $this->getSettings());
    }

    /**
     * Esaape words
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
     * Translated keywords
     * Convert some keywords to
     * the actual value so you can use
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
    public function keywordTranslation($word = false, $keywordsArray)
    {
        $val = mb_strtolower($word, 'UTF-8');
        $keywords = $keywordsArray;

        if (!$val) {
            return $keywords;
        }

        foreach ($keywords as $key => $value) {
            if (is_array($value)) {
                continue;
            }
            $key = $this->escapeKeywords($key);
            $val = preg_replace('/(^|\W)' . $key . '(\W|$)/i', ' ' . $value . ' ', $val);
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
        $dir = getDataPath('cache/' . $id);
        createDir($dir);

        $file = $dir . '/' . $from . '-' . $to . '.txt';

        file_put_contents($file, $value);
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
        $cache_dir = getDataPath('cache');

        createDir($cache_dir);

        $dir = getDataPath('cache/' . $id);
        $file = $dir . '/' . $from . '-' . $to . '.txt';

        createDir($dir);

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
     * Cleanup number
     *
     * @param string $number
     * @return int
     */
    public function cleanupNumber($number)
    {
        return floatval(str_replace(',', '', $number));
    }

    /**
     * Format number
     * handle number format for the multiple
     * converters and their specific rules
     *
     * @param int $number
     * @param int $decimals
     * @param bool $round
     * @return int
     */
    public function formatNumber($number, $decimals = -1, $round = false)
    {
        if (!is_numeric($number)) {
            return $number;
        }

        // Check if number is exponent and simply return it as is
        preg_match('/[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)/s', $number, $matches);
        if (!empty($matches) && isset($matches[1])) {
            return sprintf('%f', $number);
            // return $number;
        }

        if (fmod($number, 1) !== 0.00) {
            if ($decimals >= 0) {
                if ($round) {
                    return number_format($number, $decimals);
                }
                $number = bcdiv($number, 1, $decimals);
                return number_format($number, $decimals);
            }

            $decimals = 1;
            $string = '' . $number;
            $string = explode('.', $string);
            $string = str_split(end($string));
            $count = 1;

            // If string has 2 or more decimals make some cleanup
            if (count($string) >= 2) {
                $decimals = 2;

                foreach ($string as $order => $value) {
                    $prev = (isset($string[$order - 1]) ? $string[$order - 1] : '');
                    if ($value == '0') {
                        $count += 1;
                        continue;
                    }
                    // if ($value !== '0' && $prev !== '0') {
                    if ($value !== '0' && $prev !== '0') {
                        $count += 1;
                        $end_digit = $value;
                        break;
                    }
                }
                $decimals = $count;
            }

            if ($round) {
                return number_format($number, $decimals);
            }

            $number = bcdiv($number, 1, $decimals);
            return number_format($number, $decimals);
        } else {
            return number_format($number);
        }
    }
}
