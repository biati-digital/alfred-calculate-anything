<?php

namespace Workflow\Tools;

use Workflow\CalculateAnything as CalculateAnything;

/**
 * Cryptourrency
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
    private $apikey;

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
        $this->apikey = $this->getSetting('coinmarket_apikey', '');
    }


    /**
     * Symbols
     * Cryptocurrency Symbols
     *
     * @return array
     */
    private function symbols()
    {
        return [
            'BTC'   => 'bitcoin',
            'ETH'   => 'ethereum',
            'XRP'   => 'xrp',
            'USDT'  => 'tether',
            'BCH'   => 'bitcoin cash',
            'BSV'   => 'bitcoin sv',
            'LTC'   => 'litecoin',
            'BNB'   => 'binance coin',
            'EOS'   => 'eos',
            'XTZ'   => 'tezos',
            'XLM'   => 'stellar',
            'ADA'   => 'cardano',
            'LINK'  => 'chainlink',
            'LEO'   => 'unus sed leo',
            'CRO'   => 'crypto.com coin',
            'XMR'   => 'monero',
            'TRX'   => 'tron',
            'HT'    => 'huobi token',
            'USDC'  => 'usd coin',
            'ETC'   => 'ethereum classic',
            'NEO'   => 'neo',
            'DASH'  => 'dash',
            'HEDG'  => 'hedgetrade',
            'MIOTA' => 'iota',
            'ATOM'  => 'cosmos',
            'ZEC'   => 'zcash',
            'XEM'   => 'nem',
            'MKR'   => 'maker',
            'DOGE'  => 'dogecoin',
            'ONT'   => 'ontology',
            'OKB'   => 'okb',
            'BAT'   => 'basic attention token',
            'FTT'   => 'ftx token',
            'PAX'   => 'paxos standard',
            'DGB'   => 'digibyte',
            'ZRX'   => '0x',
            'VET'   => 'vechain',
            'BUSD'  => 'binance usd',
            'BTG'   => 'bitcoin gold',
            'REP'   => 'augur',
            'DCR'   => 'decred',
            'SNX'   => 'synthetix network token',
            'HBAR'  => 'hedera hashgraph',
            'TUSD'  => 'trueusd',
            'ICX'   => 'icon',
            'HYN'   => 'hyperion',
            'QTUM'  => 'qtum',
            'ALGO'  => 'algorand',
            'THETA' => 'theta',
            'LSK'   => 'lisk',
            'ENJ'   => 'enjin coin',
            'SNT'   => 'status',
            'RVN'   => 'ravencoin',
            'DAI'   => 'multi-collateral dai',
            'KNC'   => 'kyber network',
            'ZB'    => 'zb token',
            'BCD'   => 'bitcoin diamond',
            'WAVES' => 'waves',
            'OMG'   => 'omisego',
            'HIVE'  => 'hive',
            'ABBC'  => 'abbc coin',
            'NRG'   => 'energi',
            'MCO'   => 'mco',
            'FXC'   => 'flexacoin',
            'LEND'  => 'aave',
            'MONA'  => 'monacoin',
            'DX'    => 'dxchain token',
            'HOT'   => 'holo',
            'NANO'  => 'nano',
            'SC'    => 'siacoin',
            'DGD'   => 'digixdao',
            'ZIL'   => 'zilliqa',
            'NMR'   => 'numeraire',
            'KCS'   => 'kucoin shares',
            'CKB'   => 'nervos network',
            'BTM'   => 'bytom',
            'RDD'   => 'reddcoin',
            'KMD'   => 'komodo',
            'STEEM' => 'steem',
            'REN'   => 'ren',
            'CRPT'  => 'crypterium',
            'NEXO'  => 'nexo',
            'BTT'   => 'bittorrent',
            'LUNA'  => 'terra',
            'MATIC' => 'matic network',
            'QNT'   => 'quant',
            'XVG'   => 'verge',
            'SEELE' => 'seele-n',
            'ZEN'   => 'horizen',
            'BTS'   => 'bitshares',
            'DATA'  => 'streamr',
            'RCN'   => 'ripio credit network',
            'MANA'  => 'decentraland',
            'BHT'   => 'bhex token',
            'BCN'   => 'bytecoin',
            'HC'    => 'hypercash',
            'VSYS'  => 'v.systems',
            'MAID'  => 'maidsafecoin',
            'PAXG'  => 'pax gold',
            'UBT'   => 'unibright',
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

        $currencies = $this->matchRegex();
        $stopwords = $this->getStopWordsString($this->stop_words);
        $query = $this->query;
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

        if (empty($this->apikey)) {
            return $this->output(['noapi' => true]);
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

        if ($result['noapi']) {
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

        if (is_string($to)) {
            $to = [$to];
        }

        foreach ($to as $currency) {
            $conversion = $this->coinmarketConversion($amount, $from, $currency, $cache_seconds);

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
    private function coinmarketConversion($amount, $from, $to, $cache_seconds)
    {
        $cached = $this->getCachedConversion('coinmarketcap', $from, $to, $cache_seconds);
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

            $exchange = $exchange['data'];
            $crypto_data = $this->getRate($from, $exchange);

            if (!$crypto_data) {
                return false;
            }

            $crypto_value = $crypto_data['quote']['USD']['price'];
            $to_type = $this->isValidSymbol($to) ? 'cryptocurrency' : 'currency';

            // Check if doing a converstion from crypto currency to crypto currency
            if ($to_type == 'cryptocurrency') {
                $crypto_to_data = $this->getRate($to, $exchange);
                if (!$crypto_to_data) {
                    return false;
                }

                $to_value = $crypto_to_data['quote']['USD']['price'];
                $to_value = str_replace(',', '', $to_value);

                $convert = floatval($crypto_value) / floatval($to_value);
                $value = $convert;
                $total = $convert * $amount;
            }

            if ($to_type == 'currency') {
                if ($to == 'USD') {
                    // Rates are already in USD
                    $crypto_value = str_replace(',', '', $crypto_value);
                    $total = floatval($crypto_value) * $amount;
                } elseif (self::$currencyCalculator->isValidCurrency($to)) {
                    $currency_conversion = self::$currencyCalculator->convert([
                        'from' => 'USD',
                        'to' => $to,
                        'amount' => $crypto_value,
                    ]);

                    if ($currency_conversion && isset($currency_conversion[$to])) {
                        $total = str_replace(',', '', $currency_conversion[$to]['total']);
                        $total = floatval($total) * $crypto_value;
                        $value = floatval($total) / $crypto_value;
                    }
                }
            }

            $this->cacheConversion('coinmarketcap', $from, $to, $value);
        }

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
        $default_currency = self::$currencyCalculator->getBaseCurrency();
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

        $from = $this->getCorrectSymbol($from);
        $to = (is_string($to) ? $this->getCorrectSymbol($to) : $to);

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
        $dir = getDataPath('cache/coinmarketcap');
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

        $apikey = $this->apikey;
        if (empty($apikey)) {
            throw new Exception('No API Key provided');
        }

        $c = file_get_contents("https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest?CMC_PRO_API_KEY={$apikey}");
        if (empty($c)) {
            return $this->lang['fetch_error'];
        }

        file_put_contents($file, $c);

        return json_decode($c, true);
    }


    /**
     * Get single rate
     * from the $exchange_rates list
     *
     * @param string $symbol
     * @param array $exchange_rates
     * @return array
     */
    public function getRate($symbol, $exchange_rates)
    {
        $crypto_data = false;

        foreach ($exchange_rates as $value) {
            if ($value['symbol'] == $symbol) {
                $crypto_data = $value;
                break;
            }
        }

        return $crypto_data;
    }


    /**
     * Get correct symbol
     * searching in translations and symbols array
     *
     * @param string $val
     * @return string|bool
     */
    private function getCorrectSymbol($val)
    {
        if ($this->isValidSymbol($val)) {
            return $val;
        }

        // $val = strtolower($val);
        $val = mb_strtolower($val);
        $val = $this->keywordTranslation($val, $this->keywords);

        if (($key = array_search($val, $this->symbolsList)) !== false) {
            return $key;
        }

        // Check if instead of a cryptocurrency is a regular currency
        $is_currency = self::$currencyCalculator->getCorrectCurrency($val);
        if ($is_currency) {
            return $is_currency;
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
        $currencies = $this->symbolsList;
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
     * Get list
     * get a readable units list
     * to display to the user
     *
     * @return array
     */
    function listAvailable()
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
                'icon' => [
                    'path' => "flags/{$key}.png"
                ]
            ];
        }

        return $list;
    }
}
