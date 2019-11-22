<?php

/*
 * This file is part of the Convertor package.
 *
 * (c) Oliver Folkerd <oliver.folkerd@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Olifolkerd\Convertor;

use function Composer\Autoload\includeFile;
use Olifolkerd\Convertor\Exceptions\ConvertorDifferentTypeException;
use Olifolkerd\Convertor\Exceptions\ConvertorException;
use Olifolkerd\Convertor\Exceptions\ConvertorInvalidUnitException;
use Olifolkerd\Convertor\Exceptions\FileNotFoundException;
use PHPUnit\Runner\Exception;

class Convertor
{
    private $value = null; //value to convert
    private $baseUnit = false; //base unit of value

    //array to hold unit conversion functions
    private $units = array();


    /**
     * Allow switching between different unit definition files. Defaults to src/Config/Units.php
     * @param $unitFile - either the filename in src/Config folder OR a path to another file that exists.
     * @throws FileNotFoundException if the file does not exist.
     */
    function defineUnits($unitFile)
    {
        $configDir = __DIR__ . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR;
        //default to the newest Units.php file
        if (!isset($unitFile))
            $unitFile = $configDir . 'Units.php';
        //if only the filename is given and it exists in the config folder add the path to the file
        if (!is_array($unitFile)) {
            $configFiles = scandir($configDir);
            if (in_array($unitFile, $configFiles))
                $unitFile = $configDir . $unitFile;
        } //if an array is given, use the array.
        else {
            $this->units = $unitFile;
            return;
        }

        //lastly check if the file exists, then include or throw an error.
        if (file_exists($unitFile))
            $this->units = include $unitFile;
        else
            throw new FileNotFoundException("File could not be found. Given path='$unitFile'" .
                "either use the name of one of the pre defined configuration files or pass the complete path to the file.");
    }


    /**
     * Convertor constructor.
     * @param $value - to convert
     * @param $unit - base unit
     */
    function __construct($value = null, $unit = null, $unitFile = null)
    {//

        //create units array
        $this->defineUnits($unitFile);

        //unit optional
        if (!is_null($value) && !is_null($unit)) {

            //set from unit
            $this->from($value, $unit);
        }

    }

    /**
     * Set from conversion value / unit
     *
     * @param    number $value -  a numeric value to base conversions on
     * @param    string $unit (optional) - the unit symbol for the start value
     * @return   mixed
     * @throws ConvertorException - general errors
     * @throws ConvertorInvalidUnitException - specific invalid unit exception
     */
    public function from($value, $unit)
    {

        //check if value has been set
        if (is_null($value)) {
            throw new ConvertorException("Value Not Set");
        }

        if ($unit) {
            //check that unit exists
            if (array_key_exists($unit, $this->units)) {
                if (isset($this->units[$unit]))
                    $unitLookup = $this->units[$unit];

                if (isset($unitLookup)) {

                    //convert unit to base unit for this unit type
                    $this->baseUnit = $unitLookup["base"];
                    $this->value = $this->convertToBase($value, $unitLookup);
                }
            } else {
                throw new ConvertorInvalidUnitException("Conversion from Unit u=$unit not possible - unit does not exist.");
            }
        } else {
            $this->value = $value;
        }
    }

    /**
     * Convert from value to new unit
     *
     * @param    mixed $unit -  the unit symbol (or array of symbols) for the conversion unit
     * @param    int $decimals (optional, default-null) - the decimal precision of the conversion result
     * @param    boolean $round (optional, default-true) - round or floor the conversion result
     * @return   mixed
     */
    public function to($unit, $decimals = null, $round = true)
    {

        //check if from value is set
        if (is_null($this->value)) {
            throw new ConvertorException("From Value Not Set.");
        }

        //check if to unit is set
        if (!$unit) {
            throw new ConvertorException("Unit Not Set");
        }

        //if unit is array, iterate through each unit
        if (is_array($unit)) {
            return $this->toMany($unit, $decimals, $round);
        } else {
            //check unit symbol exists
            if (array_key_exists($unit, $this->units)) {
                $unitLookup = $this->units[$unit];

                $result = 0;

                //if from unit not provided, assume base unit of to unit type
                if ($this->baseUnit) {
                    if ($unitLookup["base"] != $this->baseUnit) {
                        throw new ConvertorDifferentTypeException("Cannot Convert Between Units of Different Types");
                    }
                } else {
                    $this->baseUnit = $unitLookup["base"];
                }

                //calculate converted value
                if (is_callable($unitLookup["conversion"])) {
                    // if unit has a conversion function, run value through it
                    $result = $unitLookup["conversion"]($this->value, true);
                } else {
                    $result = $this->value / $unitLookup["conversion"];
                }

                //result precision and rounding
                if (!is_null($decimals)) {
                    if ($round) {
                        //round to the specifidd number of decimals
                        $result = round($result, $decimals);
                    } else {
                        //truncate to the nearest number of decimals
                        $shifter = $decimals ? pow(10, $decimals) : 1;
                        $result = floor($result * $shifter) / $shifter;
                    }
                }

                return $result;
            } else {
                throw new ConvertorInvalidUnitException("Conversion to Unit u=$unit not possible - unit does not exist.");
            }
        }
    }

    /**
     * Itterate through multiple unit conversions
     *
     * @param    string[] $unit -  the array of symblos for the conversion units
     * @param    int $decimals (optional, default-null) - the decimal precision of the conversion result
     * @param    boolean $round (optional, default-true) - round or floor the conversion result
     * @return   array - results of the coversions
     */
    private function toMany($unitList = [], $decimals = null, $round = true)
    {

        $resultList = array();

        foreach ($unitList as $key) {
            //convert units for each element in the array
            $resultList[$key] = $this->to($key, $decimals, $round);
        }

        return $resultList;
    }


    /**
     * Convert from value to all compatable units
     *
     * @param    int $decimals (optional, default-null) - the decimal precision of the conversion result
     * @param    boolean $round (optional, default-true) - round or floor the conversion result
     * @return   array - results of conversion to all units with matching base units
     */
    public function toAll($decimals = null, $round = true)
    {

        //ensure the from value has been set correctly
        if (is_null($this->value)) {
            throw new ConvertorException("From Value Not Set");
        }

        //ensure the base unit has been set correctly
        if ($this->baseUnit) {

            $unitList = array();

            //build array of units that share the same base unit.
            foreach ($this->units as $key => $values) {
                if ($values["base"] == $this->baseUnit) {
                    array_push($unitList, $key);
                }
            }

            //convert units for all matches
            return $this->toMany($unitList, $decimals, $round);

        } else {
            throw new ConvertorException("No From Unit Set");
        }

    }


    /**
     * Add Conversion Unit
     *
     * @param    string $unit - the symbol for the new unit
     * @param    string $base - the symbol for the base unit of this unit
     * @param    number /function() - the conversion ration or conversion function from this unit to its base unit
     * @return   boolean - true - if successfull
     */
    public function addUnit($unit, $base, $conversion)
    {

        //check that the new unit does not ealread exist
        if (array_key_exists($unit, $this->units)) {
            throw new ConvertorException("Unit Already Exists");
        } else {
            //make sure the base unit for the new unit exists or that the new unit is a base unit itself
            if (!array_key_exists($base, $this->units) && $base != $unit) {
                throw new ConvertorException("Base Unit Does Not Exist");
            } else {
                //add unit to units array
                $this->units[$unit] = array("base" => $base, "conversion" => $conversion);
                return true;
            }
        }

    }


    /**
     * Remove Conversion Unit
     *
     * @param    string $unit - the symbol for the unit to be removed
     * @return   boolean - true - if successful
     */
    public function removeUnit($unit)
    {
        //check unit exists
        if (array_key_exists($unit, $this->units)) {

            //if unit is base unit remove all dependant units
            if ($this->units[$unit]["base"] == $unit) {
                foreach ($this->units as $key => $values) {
                    if ($values["base"] == $unit) {
                        //remove unit
                        unset($this->units[$key]);
                    }
                }

            } else {
                //remove unit
                unset($this->units[$unit]);
            }

            return true;

        } else {
            throw new ConvertorInvalidUnitException("Removal of Unit u=$unit not possible - unit does not exist.");
        }
    }

    /**
     * List all available conversion units for given unit
     *
     * @param    string $unit - the symbol to search for available conversion units
     * @return   array - list of all available conversion units
     */
    public function getUnits($unit)
    {
        if (array_key_exists($unit, $this->units)) {
            //find base unit
            $baseUnit = $this->units[$unit]["base"];

            $unitList = array();
            //find all units that are linked to the base unit
            foreach ($this->units as $key => $values) {
                if ($values["base"] == $baseUnit) {
                    array_push($unitList, $key);
                }
            }

            return $unitList;
        } else {
            throw new ConvertorInvalidUnitException("Unit u=$unit Does Not Exist");
        }
    }

    /**
     * Convert from value to its base unit
     *
     * @param    number $value - from value
     * @param    array $unitArray - unit array from object units array
     * @return   number - converted value
     */
    private function convertToBase($value, $unitArray)
    {

        if (is_callable($unitArray["conversion"])) {
            // if unit has a conversion function, run value through it
            return $unitArray["conversion"]($value, false);
        } else {
            return $value * $unitArray["conversion"];
        }
    }
}
