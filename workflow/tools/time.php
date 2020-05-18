<?php

namespace Workflow\Tools;

use DateTimeZone;
use Jenssegers\Date\Date;
use Workflow\CalculateAnything as CalculateAnything;

class Time extends CalculateAnything implements CalculatorInterface
{
    private $query;
    private $lang;
    private $keywords;
    private $timezone;
    private $display_formats;
    private $display_language;

    /**
     * Construct
     */
    public function __construct($query)
    {
        $this->query = (!empty($query) ? $query : 'now');
        if ($this->isTimestamp($this->query)) {
            $this->query = (int) $this->query;
        }

        $this->lang = $this->getTranslation('time');
        $this->keywords = $this->getKeywords('time');
        $this->timezone = getVar($argv, 3, $this->getSetting('time_zone', 'America/Los_Angeles'));
        $this->display_formats = $this->getSetting('timezones', ['F jS, Y, g:i:s a']);
        $this->display_language = $this->getSetting('language', defaultLang());
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
        if ($strlenght >= 3) {
            return true;
        }
        return false;
    }


    /**
     * Process query
     *
     * @return string|array
     */
    public function processQuery()
    {
        Date::setLocale($this->display_language);

        if (!$this->shouldProcess(strlen($this->query))) {
            return false;
        }

        // From user lang to en_us so time conversion
        // is able to understand some words
        $query = $this->translateDate($this->query);
        $data = $this->extractQueryData($query);

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
    public function output($processed)
    {
        $strings = $this->lang;
        $items = [];

        if (!$processed) {
            $items[] = [
                'title' => '...',
                'subtitle' => $strings['notvalid_subtitle'],
                'valid' => false,
            ];
            return $items;
        }

        // $instance = getVar($processed, 'instance', false);
        $instance = (isset($processed['instance']) ? $processed['instance'] : false);

        if (!$instance) {
            $items[] = [
                'title' => $processed['title'],
                'subtitle' => $processed['subtitle'],
                'arg' => $processed['val'],
            ];
            return $items;
        }


        foreach ($this->display_formats as $format) {
            $date = $instance->format($format);
            $items[] = [
                'title' => $date,
                'subtitle' => sprintf($strings['format_subtitle'], $format),
                'arg' => $date
            ];
        }

        if ($this->query !== 'now') {
            $now = Date::now();
            if ($now > $instance) {
                $count = $instance->ago();
            } else {
                $count = $instance->timespan($now);
            }

            $items[] = [
                'title' => $count,
                'subtitle' => "Timespan",
                'arg' => $count,
            ];
        }

        $items[] = [
            'title' => $instance->getTimestamp(),
            'subtitle' => "Timestamp",
            'arg' => $instance->getTimestamp()
        ];

        return $items;
    }



    /**
     * Extract query data
     * extract the values from and to
     * from the query typed by the user
     * it returns from, to and amount
     */
    private function extractQueryData($query)
    {
        $data = [];
        $strings = $this->lang;

        // handle two dates like: 25 December, 2019 - 31 December, 2019
        if (strpos($query, ' - ') !== false) {
            $data = str_replace(' - ', '|', $query);
            $data = explode('|', $data);
            $data = array_filter($data);

            if (count($data) == 2) {
                $time1 = $this->getDate(trim($data[0]));
                $time2 = $this->getDate(trim($data[1]));
                $subtitle = sprintf($strings['difference_subtitle'], $time1, $time2);

                if ($time1 && $time2) {
                    return [
                        'title' => $time1->timespan($time2),
                        'val' => $time1->timespan($time2),
                        'subtitle' => $subtitle,
                    ];
                }

                return false;
            }
        }

        // Handle Until
        if ($until = $this->dateIsUntill($query)) {
            $utime = $until['time'];
            $get_tr = $until['get'];
            $check = $this->timesDifference('now', $utime, $until['get']);
            $title = $check . ' ' . $get_tr;
            $subtitle = sprintf($strings['until_subtitle'], $get_tr, $utime);

            $title = str_replace(
                array_keys($strings),
                array_values($strings),
                $title
            );

            $subtitle = str_replace(
                array_keys($strings),
                array_values($strings),
                $subtitle
            );

            if ($check) {
                return [
                    'title' => $title,
                    'val' => $check,
                    'subtitle' => $subtitle,
                ];
            }

            return false;
        }

        // Handle End of
        if ($endof = $this->dateIsEndOf($query)) {
            $end = $this->getDate($endof);
            if ($end) {
                return [
                    'instance' => $end
                ];
            }

            return false;
        }

        // Handle Start of
        if ($startof = $this->dateIsStartOf($query)) {
            $start = $this->getDate($startof);
            if ($start) {
                return [
                    'instance' => $start
                ];
            }

            return false;
        } elseif ($this->isTimestamp($query)) {
            $query = (int) $query;
        }

        $processed = $this->getDate($query);
        if ($processed) {
            return [
                'instance' => $processed
            ];
        }

        return false;
    }


    /**
     * Translate on begin
     * given a query like
     * +30 días
     * it need to be converted to
     * +30 days so the code can understand it
     *
     * @param string $query
     * @return string
     */
    function translateDate($query)
    {
        $query = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $query);
        $strs = $this->lang;

        if ($query == 'time') {
            return 'now';
        }

        if (is_numeric($query) || empty($strs)) {
            return $query;
        }

        $query = mb_strtolower($query, 'UTF-8');
        $keys = $this->keywords;
        foreach ($keys as $k => $value) {
            if (is_array($value)) {
                continue;
            }
            $query = str_replace($k, $value, $query);
        }

        $query = str_replace(
            array_values($strs),
            array_keys($strs),
            $query
        );

        return $query;
    }


    /**
     * Get date instance
     * return a date instance with
     * the specified time
     *
     * @param string $time
     * @return object
     */
    private function getDate($time = 'now')
    {
        $d = false;
        try {
            $d = new Date($time, new DateTimeZone($this->timezone));
        } catch (\Throwable $th) {
            throw $th;
        }
        return $d;
    }

    /**
     * Time diffrence
     * get the time difference between
     * to dates in the specified format
     *
     * @param string $time1
     * @param string $time2
     * @param string $format
     * @return string
     */
    function timesDifference($time1, $time2, $format = 'hours')
    {
        $time1 = new Date($time1, new DateTimeZone($this->timezone));
        $time2 = new Date($time2, new DateTimeZone($this->timezone));

        if ($format == 'days') {
            $diff_hours = $time1->diffInHours($time2);
            if ($diff_hours < 24) {
                return $diff_hours;
            }

            return round($diff_hours / 24);
        }
        if ($format == 'hours') {
            return $time1->diffInHours($time2);
        }
        if ($format == 'minutes') {
            return $time1->diffInMinutes($time2);
        }
        if ($format == 'seconds') {
            return $time1->diffInSeconds($time2);
        }
        if ($format == 'milliseconds') {
            return $time1->diffInMilliseconds($time2);
        }
        if ($format == 'microseconds') {
            return $time1->diffInMicroseconds($time2);
        }
    }


    /**
     * Is timestamp
     * check if passed query is timestamp
     *
     * @param mixed $timestamp
     * @return boolean
     */
    public function isTimestamp($timestamp)
    {
        if (!is_numeric($timestamp)) {
            return false;
        }
        return ((string) (int) $timestamp === $timestamp)
            && ($timestamp <= PHP_INT_MAX)
            && ($timestamp >= ~PHP_INT_MAX);
    }


    /**
     * Time Until
     * check if query ia a until query
     *
     * @param string $time
     * @return mixed
     */
    private function dateIsUntill($time)
    {
        $keys = $this->timeKeywordsRegex('until');
        preg_match('/\w+ ' . $keys . ' .*/i', $time, $matches);

        if (!$matches || empty($matches)) {
            return false;
        }

        $time = preg_replace('/ ' . $keys . ' /i', '|', $time);
        $time = explode('|', $time);
        $time = array_filter($time);

        $k = (isset($time[0]) && !empty($time[0]) ? $time[0] : false);
        $date = (isset($time[1]) && !empty($time[1]) ? $time[1] : false);
        $valid_k = ['days', 'day', 'hours', 'hour', 'minutes', 'minutes', 'seconds', 'second'];

        if (!in_array($k, $valid_k) || !$date) {
            return false;
        }

        return ['get' => $k, 'time' => $date];
    }


    /**
     * Time end of
     * check if query ia a until query end
     *
     * @param string $time
     * @return mixed
     */
    private function dateIsEndOf($time)
    {
        $keys = $this->timeKeywordsRegex('end of');
        preg_match('/^' . $keys . ' .*/i', $time, $matches);

        if (!$matches || empty($matches)) {
            return false;
        }

        $time = preg_replace('/^' . $keys . '/i', '', $time);
        $time = trim($time);
        if ($time == 'year') {
            $year = $this->getDate()->format('Y');
            $time = $year . '-12-31 23:59:59';
        } elseif (is_numeric($time) && strlen($time) == 4) {
            $time = $time . '-12-31 23:59:59';
        }
        return $time;
    }


    /**
     * Time start of
     * check if query ia a until query start
     *
     * @param string $time
     * @return mixed
     */
    private function dateIsStartOf($time)
    {
        $keys = $this->timeKeywordsRegex('start of');
        preg_match('/^' . $keys . ' .*/i', $time, $matches);

        if (!$matches || empty($matches)) {
            return false;
        }

        $time = preg_replace('/^' . $keys . '/i', '', $time);
        $time = trim($time);
        if ($time == 'year') {
            $time = 'first day of January this year';
        } else {
            if (is_numeric($time) && strlen($time) == 4) {
                $time = $time . '-01-01';
            }
        }

        return $time;
    }


    /**
     * Time keywords
     * some keywords used to
     * trigger diferent actions
     *
     * @param string $check
     * @return mixed
     */
    private function timeKeywords($check = '')
    {
        $data = [
            'until' => [
                'hasta'
            ],
            'between' => [
                'entre'
            ],
            'start of' => [
                'inicio de'
            ],
            'end of' => [
                'fin de'
            ],
        ];

        if (isset($data[$check])) {
            return $data[$check];
        }

        return $data;
    }


    /**
     * Regex
     * create a regex based on the keywords
     *
     * @param string $check
     * @return string
     */
    private function timeKeywordsRegex($check)
    {
        $keys = $this->timeKeywords($check);
        $params = implode('|', array_values($keys));
        return '(' . $check . '|' . $params . ')';
    }
}
