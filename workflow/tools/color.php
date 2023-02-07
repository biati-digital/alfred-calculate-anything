<?php

namespace Workflow\Tools;

use DateTimeZone;
use Jenssegers\Date\Date;
use Workflow\CalculateAnything as CalculateAnything;

class Color extends CalculateAnything implements CalculatorInterface
{
    private $query;
    private $lang;
    private $keywords;
    private $stop_words;

    /**
     * Construct
     */
    public function __construct($query)
    {
        $this->query = $query;
        $this->lang = $this->getTranslation('color');
        $this->keywords = $this->getKeywords('color');
        $this->stop_words = $this->getStopWords('color');
    }


    /**
     * shouldProcess
     *
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
     * @return mixed
     */
    public function processQuery()
    {
        if (!$this->shouldProcess(strlen($this->query))) {
            return false;
        }

        $stop_words = array_map(function ($a) {
            return ' ' . $a;
            }, $this->stop_words);

        $query = $this->query;
        $query = preg_replace('/\s+/', ' ', $query);
        $query = str_replace($stop_words, '', $query);
        $stop_words = $this->getStopWordsString($this->stop_words, ' %s ');
        $query = preg_replace("/ ?" . $stop_words . "$/i", '', $query);
        $query = preg_replace("/ ?" . $stop_words . " ?/i", ' ', $query);
        $query = preg_replace('!\s+!', ' ', $query);
        $parts = explode(' ', $query);
        $last = array_pop($parts);
        $parts = array(implode(' ', $parts), $last);
        $clean = array_filter($parts);

        if (count($clean) < 2) {
            return $this->output([
                'Enter a color space to convet to...'
            ]);
        }

        $fromType = $this->getColorType($parts[0]);
        $fromData = [];
        $toType = trim($parts[1]);
        $fromMethod = 'from' . strtoupper($fromType);
        $toMethod = 'to' . strtoupper($toType);


        // from and to are the same type
        if ($fromType === $toType) {
            return $this->output([$parts[0]]);
        }

        if(is_callable(array($this, $fromMethod))){
            $fromData['from'] = $fromType;
            $fromData['to'] = $toType;
            $fromData['value'] = $this->$fromMethod($parts[0]);
        }

        // Convert To
        if(is_callable(array($this, $toMethod))){
            $value = [];
            $processed = $this->$toMethod($fromData);
            if (is_string($processed)) {
                $value[] = $processed;
            }

            return $this->output($value);
        }

        return $this->output([
            'There is no support for ' . $toType . ' color space yet.'
        ]);
    }


    /**
     * Detect the type of color from a string
     * @param $color_string
     *
     * @return string
     */
    public function getColorType($color_string)
    {
        $type = '';
        if(
            preg_match('/^#[a-f0-9]{3}$/i', $color_string) ||
            preg_match('/^#[a-f0-9]{6}$/i', $color_string) ||
            preg_match('/^#[a-f0-9]{8}$/i', $color_string)
        ) {
            $type = 'hex';
        } elseif (str_starts_with($color_string, 'rgba')) {
            $type = 'rgba';
        } elseif (str_starts_with($color_string, 'rgb')) {
            $type = 'rgb';
        } elseif (str_starts_with($color_string, 'hsl')) {
            $type = 'hsl';
        } elseif (str_starts_with($color_string, 'hwb')) {
            $type = 'hwb';
        } elseif (str_starts_with($color_string, 'cmyk')) {
            $type = 'cmyk';
        } elseif (str_starts_with($color_string, 'hsv')) {
            $type = 'hsv';
        } elseif (str_starts_with($color_string, 'hsb')) {
            $type = 'hsv';
        } elseif (str_starts_with($color_string, 'yuv')) {
            $type = 'yuv';
        } elseif (str_starts_with($color_string, 'xyz')) {
            $type = 'xyz';
        } elseif (str_contains($color_string, 'lab(')) {
            $type = 'lab';
        }

        return $type;
    }




    /**
     * Parse HEX color
     * @param $color
     *
     * @return array
     */
    public function fromHEX($color)
    {
        $color = str_replace('#', '', $color);
        $color = preg_replace("/[^0-9A-Fa-f]/", '', $color);
        $rgb = ['red' => 0, 'green' => 0, 'blue' => 0, 'alpha' => ''];
        $characters = strlen($color);

        if ($characters === 8) {
            $rgb['red'] = hexdec(substr($color, 0, 2));
            $rgb['green'] = hexdec(substr($color, 2, 2));
            $rgb['blue'] = hexdec(substr($color, 4, 2));
            $rgb['alpha'] = hexdec(substr($color, 6, 2)) / 255;
        } elseif ($characters === 6) {
            $rgb['red'] = hexdec(substr($color, 0, 2));
            $rgb['green'] = hexdec(substr($color, 2, 2));
            $rgb['blue'] = hexdec(substr($color, 4, 2));
        } elseif ($characters === 3) {
            $rgb['red'] = hexdec(str_repeat(substr($color, 0, 1), 2));
            $rgb['green'] = hexdec(str_repeat(substr($color, 1, 1), 2));
            $rgb['blue'] = hexdec(str_repeat(substr($color, 2, 1), 2));
        }

        return $rgb;
    }


    /**
     * Parse RGB
     * @param $color
     *
     * @return array
     */
    public function fromRGB($color)
    {
        $rgb = ['red' => 0, 'green' => 0, 'blue' => 0, 'alpha' => ''];
        $matches = null;
        $color = str_replace(';', '', $color);

        // matches rgb(170, 161, 35, 0.85)
        preg_match('/rgb\( *(\d{1,3} *, *\d{1,3} *, *\d{1,3} *, *[0-1]*(\.\d{1,})?) *\)/i', $color, $matches);
        if(!empty($matches) && isset($matches[1])){
            $channels = explode(',', $matches[1]);
            $mapped = array_map('trim', $channels);
            $rgb['red'] = $mapped[0];
            $rgb['green'] = $mapped[1];
            $rgb['blue'] = $mapped[2];
            $rgb['alpha'] = $mapped[3];

            return $rgb;
        }

        // matches rgb(52 48 48 / 83%)
        preg_match('/rgb\( *(\d{1,3} * *\d{1,3} * *\d{1,3} * *\/ * *(\d+)%) *\)/i', $color, $matches);
        if (!empty($matches) && isset($matches[1])) {
            $start = explode('/', $matches[1]);
            $channels = explode(' ', trim($start[0]));
            $mapped = array_map('trim', $channels);

            $rgb['red'] = $mapped[0];
            $rgb['green'] = $mapped[1];
            $rgb['blue'] = $mapped[2];
            $rgb['alpha'] = $matches[2] / 100;

            return $rgb;
        }

        // matches rgb(170 161 35)
        preg_match('/rgb\( *(\d{1,3} * *\d{1,3} * *\d{1,3}) *\)/i', $color, $matches);
        if (!empty($matches) && isset($matches[1])) {
            $channels = explode(' ', $matches[1]);
            $mapped = array_map('trim', $channels);

            $rgb['red'] = $mapped[0];
            $rgb['green'] = $mapped[1];
            $rgb['blue'] = $mapped[2];

            return $rgb;
        }

        // matches rgb(170, 161, 35)
        preg_match('/rgb\( *(\d{1,3} *, *\d{1,3} *, *\d{1,3}) *\)/i', $color, $matches);
        $channels = explode(',', $matches[1]);
        $mapped = array_map('trim', $channels);

        $rgb['red'] = $mapped[0];
        $rgb['green'] = $mapped[1];
        $rgb['blue'] = $mapped[2];

        return $rgb;
    }


    /**
     * Parse HSL
     * @param $color
     *
     * @return array
     */
    public function fromHSL($color)
    {
        $rgb = ['red' => 0, 'green' => 0, 'blue' => 0, 'alpha' => ''];
        $hue = 0;
        $lightness = 0;
        $saturation = 0;

        if (is_string($color)) {
            $matches = null;
            preg_match('/hsl\( *(-?\d{1,3}) *, *(\d{1,3})%? *, *(\d{1,3})%? *\)/i', $color, $matches);

            if (empty($matches)) {
                return [];
            }

            $hue = $matches[1];
            $saturation = $matches[2];
            $lightness = $matches[3];
        }

        if (is_array($color)) {
            $hue = $color['hue'];
            $saturation = $color['saturation'];
            $lightness = $color['lightness'];
        }

        $h = (360 + (intval($hue) % 360)) % 360;  // hue values can be less than 0 and greater than 360. This normalises them into the range 0-360.
        $c = (1 - abs(2 * ($lightness / 100) - 1)) * ($saturation / 100);
        $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
        $m = ($lightness / 100) - ($c / 2);


        if ($h >= 0 && $h <= 60) {
            $rgb = [
                'red' => round(($c + $m) * 255),
                'green' => round(($x + $m) * 255, 2),
                'blue' => floor($m * 255)
            ];
        }

        if ($h > 60 && $h <= 120) {
            $rgb = [
                'red' => round(($x + $m) * 255),
                'green' => floor(($c + $m) * 255),
                'blue' => floor($m * 255)
            ];
        }

        if ($h > 120 && $h <= 180) {
            $rgb = [
                'red' => round($m * 255),
                'green' => floor(($c + $m) * 255),
                'blue' => floor(($x + $m) * 255)
            ];
        }

        if ($h > 180 && $h <= 240) {
            $rgb = [
                'red' => round($m * 255),
                'green' => floor(($x + $m) * 255),
                'blue' => floor(($c + $m) * 255)
            ];
        }

        if ($h > 240 && $h <= 300) {
            $rgb = [
                'red' => round(($x + $m) * 255),
                'green' => floor($m * 255),
                'blue' => floor(($c + $m) * 255)
            ];
        }

        if ($h > 300 && $h <= 360) {
            $rgb = [
                'red' => round(($c + $m) * 255),
                'green' => floor($m * 255),
                'blue' => floor(($x + $m) * 255)
            ];
        }

        return $rgb;
    }



    /**
     * Parse HWB
     * @param $color
     *
     * @return array
     */
    public function fromHWB($color)
    {
        $matches = null;
        preg_match('/hwb\( *(-?\d{1,3}) *, *(\d{1,3}%) *, *(\d{1,3}%) *\)/i', $color, $matches);
        if (empty($matches)) {
            preg_match('/hwb\( *(-?\d{1,3}) *, *(\d{1,3})%? *, *(\d{1,3})%? *\)/i', $color, $matches);
        }

        if (empty($matches)) {
            return [];
        }
        $h = intval($matches[1]);
        $w = $matches[2];
        $b = $matches[3];
        if (str_contains($w, '%')) {
            $w = str_replace('%', '', $w);
            $w = $w / 100;
        }
        if (str_contains($b, '%')) {
            $b = str_replace('%', '', $b);
            $b = $b / 100;
        }

        $rgb = [];
        $rgbArr = [];
        $rgbFromHLS = $this->hslToRgb($h, 1, 0.50);

        $rgbArr[] = $rgbFromHLS['red'] / 255;
        $rgbArr[] = $rgbFromHLS['green'] / 255;
        $rgbArr[] = $rgbFromHLS['blue'] / 255;

        $tot = $w + $b;
        if ($tot > 1) {
            $w = round($w / $tot, 2);
            $b = round($b / $tot, 2);
        }

        $rgbArr = array_map(function ($v) use ($b, $w) {
            $v *= (1 - ($w) - ($b));
            $v += $w;
            $v *= 255;

            /*if (is_numeric($v) && floor($v) != $v) {
                print_r("$v is decimal - ");
            }*/
            return round($v);
        }, $rgbArr);

        $rgb['red'] = $rgbArr[0];
        $rgb['green'] = $rgbArr[1];
        $rgb['blue'] = $rgbArr[2];

        return $rgb;
    }



    /**
     * Parse lab
     * @param $color
     *
     * @return array
     */
    public function fromLAB($color)
    {
        $matches = [];
        $separator = ',';
        preg_match('/cielab\( *(\d{1,3}\.?\d*%? *, *-?\d{1,3}\.?\d* *, *-?\d{1,3}\.?\d*) *\)/i', $color, $matches);

        if (empty($matches)) {
            preg_match('/lab\( *(\d{1,3}\.?\d*%? *, *-?\d{1,3}\.?\d* *, *-?\d{1,3}\.?\d*) *\)/i', $color, $matches);
        }

        if (empty($matches)) {
            preg_match('/lab\( *(\d{1,3}\.?\d*%? * *-?\d{1,3}\.?\d* * *-?\d{1,3}\.?\d*) *\)/i', $color, $matches);

            if (!empty($matches) && isset($matches[1])) {
                $separator = ' ';
            }
        }

        $channels = explode($separator, $matches[1]);
        $mapped = array_map('trim', $channels);
        $l = str_replace('%', '', $mapped[0]);
        $a = $mapped[1];
        $b = $mapped[2];

        $y = ($l + 16) / 116;
        $x = $a / 500 + $y;
        $z = $y - $b / 200;

        if (pow($y, 3) > 0.008856) {
            $y = pow($y, 3);
        } else {
            $y = ($y - 16 / 116) / 7.787;
        }

        if (pow($x, 3) > 0.008856) {
            $x = pow($x, 3);
        } else {
            $x = ($x - 16 / 116) / 7.787;
        }

        if (pow($z, 3) > 0.008856) {
            $z = pow($z, 3);
        } else {
            $z = ($z - 16 / 116) / 7.787;
        }

        $x = round(95.047 * $x, 4);
        $y = round(100.000 * $y, 4);
        $z = round(108.883 * $z, 4);

        if ($x > 95.047) {
            $x = 95.047;
        }
        if ($y > 100) {
            $y = 100;
        }
        if ($z > 108.883) {
            $z = 108.883;
        }

        $x = $x / 100;
        $y = $y / 100;
        $z = $z / 100;

        $r = $x * 3.2406 + $y * -1.5372 + $z * -0.4986;
        $g = $x * -0.9689 + $y * 1.8758 + $z * 0.0415;
        $b = $x * 0.0557 + $y * -0.2040 + $z * 1.0570;

        if ($r > 0.0031308) {
            $r = 1.055 * pow($r, (1 / 2.4)) - 0.055;
        } else {
            $r = 12.92 * $r;
        }

        if ($g > 0.0031308) {
            $g = 1.055 * pow($g, (1 / 2.4)) - 0.055;
        } else {
            $g = 12.92 * $g;
        }

        if ($b > 0.0031308) {
            $b = 1.055 * pow($b, (1 / 2.4)) - 0.055;
        } else {
            $b = 12.92 * $b;
        }

        $rgb = ['red' => 0, 'green' => 0, 'blue' => 0, 'alpha' => ''];
        $rgb['red'] = intval(max(0, min(255, $r * 255)));
        $rgb['green'] = intval(max(0, min(255, $g * 255)));
        $rgb['blue'] = intval(max(0, min(255, $b * 255)));

        return $rgb;
    }



    /**
     * Parse YUB
     * @param $color
     * TODO: parse YUB from string
     *
     * @return array
     */
    public function fromYUB_($color)
    {
        $rgb = ['red' => 0, 'green' => 0, 'blue' => 0, 'alpha' => ''];
        $y = intval($input[0], 10);
        $u = intval($input[1], 10) / 255 * 222 - 111;
        $v = intval($input[2], 10) / 255 * 312 - 155;

        $rgb['red'] = round($y + $v / 0.877);
        $rgb['green'] = round($y - 0.39466 * $u - 0.5806 * $v);
        $rgb['blue'] = round($y + $u / 0.493);

        return $rgb;
    }


    /**
     * To HEX
     * @param $colorData
     *
     * @return string
     */
    public function toHEX($colorData)
    {
        $color = $colorData['value'];
        $r = dechex($color['red']);
        $g = dechex($color['green']);
        $b = dechex($color['blue']);

        $hex = '#' . $r . $g . $b;

        if (!empty($color['alpha'])) {
            // https://davidwalsh.name/hex-opacity
            $alpha = (float) $color['alpha'];
            if ($alpha < 1) {
                $alpha = $alpha * 100;
            }
            $alphaInt = round($alpha / 100 * 255);
            $alpha = dechex($alphaInt);
            $hex .= $alpha;
        }

        return $hex;
    }


    /**
     * To RGB
     * @param $colorData
     *
     * @return string
     */
    public function toRGB($colorData)
    {
        $color = $colorData['value'];
        $r = $color['red'];
        $g = $color['green'];
        $b = $color['blue'];

        if (empty($color['alpha']) || (int)$color['alpha'] === 1) {
            return 'rgb(' . $r . ', ' . $g . ', ' . $b . ')';
        }

        $a = $color['alpha'];

        return 'rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . round($a, 2 ) .')';
    }


    /**
     * To HSL
     * @param $colorData
     *
     * @return string
     */
    public function toHSL($colorData) {
        $hsl = [];
        $color = $colorData['value'];
        $r = $color['red'];
        $g = $color['green'];
        $b = $color['blue'];

        $r /= 255;
        $g /= 255;
        $b /= 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);

        $h = 0;
        $s = 0;
        $l = ( $max + $min ) / 2;
        $d = $max - $min;

        if( $d === 0 ){
            $h = $s = 0; // achromatic
        } else {
            $s = $d / ( 1 - abs( 2 * $l - 1 ) );

            switch( $max ){
                case $r:
                    $h = 60 * fmod( ( ( $g - $b ) / $d ), 6 );
                    if ($b > $g) {
                        $h += 360;
                    }
                    break;

                case $g:
                    $h = 60 * ( ( $b - $r ) / $d + 2 );
                    break;

                case $b:
                    $h = 60 * ( ( $r - $g ) / $d + 4 );
                    break;
            }
        }

        $hsl['h'] = round($h, 2 );
        $hsl['s'] = round($s, 2 ) * 100;
        $hsl['l'] = round($l, 2 ) * 100;

        return 'hsl(' . $hsl['h'] . ', ' . $hsl['s'] . '%, ' . $hsl['l'] . '%)';
    }



    /**
     * To HWB
     * @param $colorData
     *
     * @return string
     */
    public function toHWB($colorData) {
        $hwb = [];
        $color = $colorData['value'];
        $r = $color['red'];
        $g = $color['green'];
        $b = $color['blue'];

        $r /= 255;
        $g /= 255;
        $b /= 255;

        $h = 0;
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $d = $max - $min;
        $w = min($r, $g, $b);
        $v = max($r, $g, $b);
        $black = 1 - $v;

        if( $d === 0 ){
            $h = 0;
        } else {
            switch( $max ){
                case $r:
                    $h = 60 * fmod(( ( $g - $b ) / $d ), 6 );
                    if ($b > $g) {
                        $h += 360;
                    }
                    break;

                case $g:
                    $h = 60 * ( ( $b - $r ) / $d + 2 );
                    break;

                case $b:
                    $h = 60 * ( ( $r - $g ) / $d + 4 );
                    break;
            }
        }

        $hwb['h'] = round($h, 0 );
        $hwb['w'] = round($w, 2 ) * 100;
        $hwb['b'] = round($black, 2 ) * 100;

        return 'hwb(' . $hwb['h'] . ', ' . $hwb['w'] . '%, ' . $hwb['b'] . '%)';
    }




    public function toHSV($colorData)
    {
        $hsv = [];
        $color = $colorData['value'];
        $r = $color['red'];
        $g = $color['green'];
        $b = $color['blue'];

        $r /= 255;
        $g /= 255;
        $b /= 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $h = 0;
        $d = $max - $min;
        $v = $max;

        if ($d === 0) {
            $h = $s = 0; // achromatic
        } else {
            $s = $d / $max;

            switch( $max ){
                case $r:
                    $h = 60 * fmod( ( ( $g - $b ) / $d ), 6 );
                    if ($b > $g) {
                        $h += 360;
                    }
                    break;

                case $g:
                    $h = 60 * ( ( $b - $r ) / $d + 2 );
                    break;

                case $b:
                    $h = 60 * ( ( $r - $g ) / $d + 4 );
                    break;
            }
        }

        $hsv['h'] = round($h, 0 );
        $hsv['s'] = round($s, 2 ) * 100;
        $hsv['v'] = round($v, 2 ) * 100;

        return 'hsv(' . $hsv['h'] . ', ' . $hsv['s'] . '%, ' . $hsv['v'] . '%)';
    }


    /**
     * To CMYK
     * @param $colorData
     *
     * @return string
     */
    public function toCMYK($colorData)
    {
        $color = $colorData['value'];
        $r = $color['red'];
        $g = $color['green'];
        $b = $color['blue'];

        $cyan = 255 - $r;
        $magenta = 255 - $g;
        $yellow = 255 - $b;
        $black = min($cyan, $magenta, $yellow);
        $cyan = @(($cyan - $black) / (255 - $black)) * 255;
        $magenta = @(($magenta - $black) / (255 - $black)) * 255;
        $yellow = @(($yellow - $black) / (255 - $black)) * 255;

        $cmyk = [
            'c' => round($cyan / 255, 2 ) * 100,
            'm' => round($magenta / 255, 2 ) * 100,
            'y' => round($yellow / 255, 2 ) * 100,
            'k' => round($black / 255, 2 ) * 100
        ];

        return 'cmyk(' . $cmyk['c'] . '%, ' . $cmyk['m'] . '%, ' . $cmyk['y'] . '%, ' . $cmyk['k'] . '%)';
    }


    /**
     * To YUV
     * @param $colorData
     *
     * @return string
     */
    public function toYUV($colorData)
    {
        $yuv = [];
        $color = $colorData['value'];

        $r = intval($color['red'], 10);
        $g = intval($color['green'], 10);
        $b = intval($color['blue'], 10);

        $y = round(0.299 * $r + 0.587 * $g + 0.114 * $b);
        $u = round(((($b - $y) * 0.493) + 111) / 222 * 255);
        $v = round(((($r - $y) * 0.877) + 155) / 312 * 255);

        $yuv['y'] = $y;
        $yuv['u'] = $u;
        $yuv['v'] = $v;

        return 'yuv(' . $yuv['y'] . ', ' . $yuv['u'] . ', ' . $yuv['v'] . ')';
    }



    /**
     * To XYZ
     * TODO: // check later when browser support is added
     *
     * @param $colorData
     *
     * @return string|array
     */
    public function toXYZ($colorData, $returnArray = false)
    {
        $xyz = [];
        $color = $colorData['value'];

        $r = $color['red'] / 255;
        $g = $color['green'] / 255;
        $b = $color['blue'] / 255;

        if ($r > 0.04045) {
            $r = pow((($r + 0.055) / 1.055), 2.4);
        } else {
            $r /= 12.92;
        }

        if ($g > 0.04045) {
            $g = pow((($g + 0.055) / 1.055), 2.4);
        } else {
            $g /= 12.92;
        }

        if ($b > 0.04045) {
            $b = pow((($b + 0.055) / 1.055), 2.4);
        } else {
            $b = $b / 12.92;
        }

        $r *= 100;
        $g *= 100;
        $b *= 100;

        $x = round($r * 0.4124 + $g * 0.3576 + $b * 0.1805, 4);
        $y = round($r * 0.2126 + $g * 0.7152 + $b * 0.0722, 4);
        $z = round($r * 0.0193 + $g * 0.1192 + $b * 0.9505, 4);

        if ($x > 95.047) {
            $x = 95.047;
        }
        if ($y > 100) {
            $y = 100;
        }
        if ($z > 108.883) {
            $z = 108.883;
        }

        if ($returnArray) {
            return [
                'x' => $x,
                'y' => $y,
                'z' => $z,
            ];
        }
        return 'color(xyz ' . $x . ' ' . $y . ' ' . $z . ')';
    }


    /**
     * To Ciec Lab
     * @param $colorData
     *
     * @return string
     */
    public function toLAB($colorData)
    {
        $xyz = $this->toXYZ($colorData, true);
        $x = $xyz['x'] / 95.047;
        $y = $xyz['y'] / 100.000;
        $z = $xyz['z'] / 108.883;

        if ($x > 0.008856) {
            $x = pow($x, 1 / 3);
        } else {
            $x = (7.787 * $x) + (16 / 116);
        }

        if ($y > 0.008856) {
            $y = pow($y, 1 / 3);
        } else {
            $y = (7.787 * $y) + (16 / 116);
        }

        if ($y > 0.008856) {
            $l = (116 * $y) - 16;
        } else {
            $l = 903.3 * $y;
        }

        if ($z > 0.008856) {
            $z = pow($z, 1 / 3);
        } else {
            $z = (7.787 * $z) + (16 / 116);
        }

        $l = round($l, 2);
        $a = round(500 * ($x - $y), 2);
        $b = round(200 * ($y - $z), 2);

        return 'lab(' . $l . '% ' . $a . ' ' . $b . ')';
    }


    public function hue2rgb($t1, $t2, $hue)
    {
        if ($hue < 0) $hue += 6;
        if ($hue >= 6) $hue -= 6;
        if ($hue < 1) return ($t2 - $t1) * $hue + $t1;
        else if($hue < 3) return $t2;
        else if($hue < 4) return ($t2 - $t1) * (4 - $hue) + $t1;
        else return $t1;

        return false;
    }


    function hslToRgb($hue, $sat, $light) {
        $t1 = 0;
        $t2 = 0;
        $r = 0;
        $g = 0;
        $b = 0;
        $hue /= 60;

        if ($light <= 0.5) {
            $t2 = $light * ($sat + 1);
        } else {
            $t2 = $light + $sat - ($light * $sat);
        }

        $t1 = $light * 2 - $t2;
        $r = $this->hue2rgb($t1, $t2, $hue + 2) * 255;
        $g = $this->hue2rgb($t1, $t2, $hue) * 255;
        $b = $this->hue2rgb($t1, $t2, $hue - 2) * 255;

        return [
            'red' => $r,
            'green' => $g,
            'blue' => $b,
        ];
}


    /**
     * Output
     * build the output the way
     * it should be displayed by Alfred
     *
     * @param array $result
     * @return array
     */
    public function output($values)
    {
        $strings = $this->lang;
        $items = [];

        if (empty($values)) {
            $items[] = [
                'title' => '...',
                'subtitle' => $strings['notvalid_subtitle'],
                'valid' => false,
            ];
            return $items;
        }

        foreach ($values as $value) {
            $items[] = [
                'title' => $value,
                //'subtitle' => sprintf($strings['format_subtitle'], $format),
                'arg' => $value
            ];
        }

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

}
