<?php

namespace Ohanzee\Helper;

class Data {

    /**
     *
     * Regexp to validate string as Binary
     * @var PCRE
     *
     */
    private static $binary_regexp       = '/^(?:[01]{8}){0,12}$/'; // UNTESTED
    
    /**
     *
     * Regexp to validate string as Timestamp
     * @var PCRE
     *
     */
    private static $timestamp_regexp    = '/\d{4}-\d{1,2}-\d{1,2}\s\d{2}:\d{2}:\d{2}/';
    
    /**
     *
     * Array of invalid Timestamp values
     * @var array
     *
     */
    private static $invalid_timestamps  = array(
                                            '0000-00-00 00:00:00'
                                        );

    /**
     *
     * Regexp to validate string as Date
     * @var PCRE
     *
     */
    private static $date_regexp         = '/\d{4}-\d{1,2}-\d{1,2}/';

    /**
     *
     * Array of invalid Date values
     * @var array
     *
     */
    private static $invalid_dates       = array(
                                            '0000-00-00'
                                        );
    
     /**
      * Tests if data is a valid string
      *
      *     // Returns true
      *     Data::isString('some string');
      *
      *     // Returns false
      *     Data::isString(98765);
      *
      * @param   string   $data  data to check
      * @return  boolean
      */
    public static function isString($data){
        if( !empty( $data ) && strlen( $data ) > 0 && !is_null( $data ) && is_string( $data ) ){
            return true;
        }

        return false;
    }

     /**
      * Tests if data is a valid integer
      *
      *     // Returns true
      *     Data::isInt(98765);
      *
      *     // Returns false
      *     Data::isInt('some string');
      *
      * @param   integer   $data  data to check
      * @return  boolean
      */
    public static function isInt($data){
        if( !empty( $data ) && is_int( $data ) ){
            return true;
        }

        return false;
    }

     /**
      * Tests if data is a valid float
      *
      *     // Returns true
      *     Data::isFloat(987.65);
      *
      *     // Returns false
      *     Data::isFloat(98765);
      *
      * @param   float   $data  data to check
      * @return  boolean
      */
    public static function isFloat($data){
        if( !empty( $data ) && is_float( $data ) ){
            return true;
        }

        return false;
    }
   
     /**
      * Tests if data is a valid binary string
      *
      *     // Returns true
      *     Data::isBinary('01001000 01100101 01101100 01101100 01101111 00100000 01010111 01101111 01110010 01101100 01100100');
      *
      *     // Returns false
      *     Data::isBinary('some non binary string');
      *
      * @param   string   $data  data to check
      * @return  boolean
      */
    /*
    UNTESTED - NOT EVEN SURE IF THERE'S MUCH USE CASE FOR THIS METHOD
    public static function isBinary($data){
        if( !empty( $data ) && preg_match(static::$binary_regexp, $data) ){
            return true;
        }

        return false;
    }
    */

     /**
      * Tests if data is a valid timestamp
      *
      *     // Returns true
      *     Data::isTimestamp('2014-10-31 12:22:36');
      *
      *     // Returns false
      *     Data::isTimestamp('0000-00-00 00:00:00');
      *
      * @param   timestamp   $data  data to check
      * @return  boolean
      */
    //TODO: Add support for "real" datetime check, instead of possibly allowing something like 2014-99-99 88:77:66
    public static function isTimestamp($data){
        if( !empty( $data ) && preg_match(static::$timestamp_regexp, $data) && !in_array($data, static::$invalid_timestamps) ){
            return true;
        }

        return false;
    }

     /**
      * Tests if data is a valid date
      *
      *     // Returns true
      *     Data::isDate('2014-10-31');
      *
      *     // Returns false
      *     Data::isDate('0000-00-00');
      *
      * @param   string   $data  data to check
      * @return  boolean
      */
    //TODO: Add support for "real" date check, instead of possibly allowing something like 2014-99-99
    public static function isDate($data){
        if( !empty( $data ) && preg_match(static::$date_regexp, $data) && !in_array($data, static::$invalid_dates) ){
            return true;
        }

        return false;
    }

     /**
      * Tests if data is a valid email
      *
      *     // Returns true
      *     Data::isEmail('foo@bar.com');
      *
      *     // Returns false
      *     Data::isEmail('foo[at]bar[dot]com');
      *
      * @param   string   $data  data to check
      * @return  boolean
      */
    public static function isEmail($data){
        if( filter_var($data, FILTER_VALIDATE_EMAIL) ){
            return true;
        }

        return false;
    }

     /**
      * Tests if data is a valid url
      *
      *     // Returns true
      *     Data::isUrl('http://devlifeline.com');
      *
      *     // Returns false
      *     Data::isUrl('#definitelynotarealurl');
      *
      * @param   string   $data  data to check
      * @return  boolean
      */
    public static function isUrl($data){
        if( filter_var($data, FILTER_VALIDATE_URL) ){
            return true;
        }

        return false;
    }

}