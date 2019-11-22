<?php
/**
 * - Adapted notation to the one used in ECMWF Weather data.
 *      - Original Units had km^2 as km2 - exponents are now marked up with '**' making it km**2
 * - Added Area Density functions
 */
return array(
    ///////Units Of Length///////
    "m" => array("base" => "m", "conversion" => 1), //meter - base unit for distance
    "km" => array("base" => "m", "conversion" => 1000), //kilometer
    "dm" => array("base" => "m", "conversion" => 0.1), //decimeter
    "cm" => array("base" => "m", "conversion" => 0.01), //centimeter
    "mm" => array("base" => "m", "conversion" => 0.001), //milimeter
    "μm" => array("base" => "m", "conversion" => 0.000001), //micrometer
    "µm" => array("base" => "m", "conversion" => 0.000001), //micrometer
    "nm" => array("base" => "m", "conversion" => 0.000000001), //nanometer
    "pm" => array("base" => "m", "conversion" => 0.000000000001), //picometer
    "in" => array("base" => "m", "conversion" => 0.0254), //inch
    "ft" => array("base" => "m", "conversion" => 0.3048), //foot
    "yd" => array("base" => "m", "conversion" => 0.9144), //yard
    "mi" => array("base" => "m", "conversion" => 1609.344), //mile
    "h" => array("base" => "m", "conversion" => 0.1016), //hand
    "ly" => array("base" => "m", "conversion" => 9460730472580800), //lightyear
    "au" => array("base" => "m", "conversion" => 149597870700), //astronomical unit
    "pc" => array("base" => "m", "conversion" =>  3.08567782E16), //parsec


    ///////Units Of Area///////
    "m**2" => array("base" => "m**2", "conversion" => 1), //meter square - base unit for area
    "km**2" => array("base" => "m**2", "conversion" => 1000000), //kilometer square
    "cm**2" => array("base" => "m**2", "conversion" => 0.0001), //centimeter square
    "mm**2" => array("base" => "m**2", "conversion" => 0.000001), //milimeter square
    "ft**2" => array("base" => "m**2", "conversion" => 0.092903), //foot square
    "mi**2" => array("base" => "m**2", "conversion" => 2589988.11), //mile square
    "ac" => array("base" => "m**2", "conversion" => 4046.86), //acre
    "ha" => array("base" => "m**2", "conversion" => 10000), //hectare

    ///////Units Of Volume///////
    "dm3" => array("base" => "l", "conversion" => 1), //cubic decimeter - litre
    "l" => array("base" => "l", "conversion" => 1), //litre - base unit for volume
    "ml" => array("base" => "l", "conversion" => 0.001), //mililitre
    "cm3" => array("base" => "l", "conversion" => 0.001), //cubic centimeter - mililitre
    "hl" => array("base" => "l", "conversion" => 100), //hectolitre
    "kl" => array("base" => "l", "conversion" => 1000), //kilolitre
    "m3" => array("base" => "l", "conversion" => 1000), //meters cubed - kilolitre
    "pt" => array("base" => "l", "conversion" => 0.56826125), //pint
    "gal" => array("base" => "l", "conversion" => 4.405), //gallon
    "qt" => array("base" => "l", "conversion" => 1.1365225), //quart
    "ft3" => array("base" => "l", "conversion" => 28.316846592), //cubic feet
    "in3" => array("base" => "l", "conversion" => 0.016387064), //cubic inches

    ///////Units Of Weight///////
    "kg" => array("base" => "kg", "conversion" => 1), //kilogram - base unit for weight
    "g" => array("base" => "kg", "conversion" => 0.001), //gram
    "mg" => array("base" => "kg", "conversion" => 0.000001), //miligram
    "N" => array("base" => "kg", "conversion" => 1 / 9.80665002863885), //Newton (based on earth gravity)
    "st" => array("base" => "kg", "conversion" => 6.35029), //stone
    "lb" => array("base" => "kg", "conversion" => 0.453592), //pound
    "oz" => array("base" => "kg", "conversion" => 0.0283495), //ounce
    "t" => array("base" => "kg", "conversion" => 1000), //metric tonne
    "ukt" => array("base" => "kg", "conversion" => 1016.047), //UK Long Ton
    "ust" => array("base" => "kg", "conversion" => 907.1847), //US short Ton

    //////Units Of Speed///////
    "m s**-1" => array("base" => "m s**-1", "conversion" => 1), //meter per seond - base unit for speed
    "km h**-1" => array("base" => "m s**-1", "conversion" => 1/3.6), //kilometer per hour
    "mi h**-1" => array("base" => "m s**-1", "conversion" => 1.60934*1/3.6), //mi => km then convert like km/h

    ///////Units Of Rotation///////
    "deg" => array("base" => "deg", "conversion" => 1), //degrees - base unit for rotation
    "rad" => array("base" => "deg", "conversion" => 57.2958), //radian

    ///////Units Of Temperature///////
    "k" => array("base" => "k", "conversion" => 1), //kelvin - base unit for distance
    "c" => array("base" => "k", "conversion" => function ($val, $tofrom) {
        return $tofrom ? $val - 273.15 : $val + 273.15;
    }), //celsius
    "f" => array("base" => "k", "conversion" => function ($val, $tofrom) {
        return $tofrom ? ($val * 9 / 5 - 459.67) : (($val + 459.67) * 5 / 9);
    }), //Fahrenheit

    ///////Units Of Pressure///////
    "pa" => array("base" => "pa", "conversion" => 1), //Pascal - base unit for Pressure
    "hpa" => array("base" => "pa", "conversion" => 100), //hpa
    "kpa" => array("base" => "pa", "conversion" => 1000), //kilopascal
    "mpa" => array("base" => "pa", "conversion" => 1000000), //megapascal
    "bar" => array("base" => "pa", "conversion" => 100000), //bar
    "mbar" => array("base" => "pa", "conversion" => 100), //milibar
    "psi" => array("base" => "pa", "conversion" => 6894.76), //pound-force per square inch

    ///////Units Of Time///////
    "s" => array("base" => "s", "conversion" => 1), //second - base unit for time
    "year" => array("base" => "s", "conversion" => 31536000), //year - standard year
    "month" => array("base" => "s", "conversion" => 18748800), //month - 31 days
    "week" => array("base" => "s", "conversion" => 604800), //week
    "day" => array("base" => "s", "conversion" => 86400), //day
    "hr" => array("base" => "s", "conversion" => 3600), //hour
    "min" => array("base" => "s", "conversion" => 60), //minute
    "ms" => array("base" => "s", "conversion" => 0.001), //milisecond
    "μs" => array("base" => "s", "conversion" => 0.000001), //microsecond
    "ns" => array("base" => "s", "conversion" => 0.000000001), //nanosecond

    ///////Units Of Power///////
    "j" => array("base" => "j", "conversion" => 1), //joule - base unit for energy
    "kj" => array("base" => "j", "conversion" => 1000), //kilojoule
    "mj" => array("base" => "j", "conversion" => 1000000), //megajoule
    "cal" => array("base" => "j", "conversion" => 4184), //calorie
    "Nm" => array("base" => "j", "conversion" => 1), //newton meter
    "ftlb" => array("base" => "j", "conversion" => 1.35582), //foot pound
    "whr" => array("base" => "j", "conversion" => 3600), //watt hour
    "kwhr" => array("base" => "j", "conversion" => 3600000), //kilowatt hour
    "mwhr" => array("base" => "j", "conversion" => 3600000000), //megawatt hour
    "mev" => array("base" => "j", "conversion" => 0.00000000000000016), //mega electron volt

    ///////Area density///////
    "kg m**-2" => array("base" => "kg m**-2", "conversion" => 1),
    //vary area
    "kg km**-2" => array("base" => "kg m**-2", "conversion" => 0.000001),
    "kg cm**-2" => array("base" => "kg m**-2", "conversion" => 1e4),
    "kg mm**-2" => array("base" => "kg m**-2", "conversion" => 1e6),
    //vary weight
    "g m**-2" => array("base" => "kg m**-2", "conversion" => 0.001), //gram
    "mg m**-2" => array("base" => "kg m**-2", "conversion" => 0.000001), //miligram
    "st m**-2" => array("base" => "kg m**-2", "conversion" => 6.35029), //stone
    "lb m**-2" => array("base" => "kg m**-2", "conversion" => 0.453592), //pound
    "oz m**-2" => array("base" => "kg m**-2", "conversion" => 0.0283495), //ounce
    //todo: add your density conversions here if you need them.

    /* "kph" => array("base" => "kph", "conversion" => 1),
    "mph" => array("base" => "kph", "conversion" => 1.60934),
    "mps" => array("base" => "kph", "conversion" => 3.6), */
    "kph" => array("base" => "mps", "conversion" => 0.27777778),
    "mph" => array("base" => "mps", "conversion" => 0.44704),
    "mps" => array("base" => "mps", "conversion" => 1),
    "fps" => array("base" => "mps", "conversion" => 0.3048),
);
