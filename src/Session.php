<?php

namespace Ohanzee\Helper;

class Session {


    /**
      * Returns current session id
      *
      *
      * @return  session_id || boolean(false)
      *
      */
    public static function getId(){
        return empty(session_id()) ? FALSE : session_id();
    }

    /**
      * Override current session_id
      *
      *
      * @param string $session_id
      *
      * @return  session_id || boolean(false)
      *
      */
    public static function setId($session_id){
        return self::isActive() ? FALSE : session_id($session_id);
    }

    /**
      * Check if there is an active session
      *
      * 
      *
      * @return  boolean
      *
      */
    public static function isActive(){
        if ( version_compare(phpversion(), '5.4.0', '>=') ) {
            return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
        } else {
            return self::getId() ? TRUE : FALSE;
        }
    }

    /**
      * Start a new session if one is not already started
      *
      *
      * @param boolean $supress_errors 
      *
      * @return  session_id || boolean(false)
      *
      */
    public static function start($supress_errors=TRUE){
        if( !self::isActive() ){
            if( !$session = $supress_errors ? @session_start() : session_start() ){
                return FALSE;
            }
        }

        return self::getId();
    }

    /**
      * Destroy the current session if one is active
      *
      * 
      *
      * @return  boolean
      *
      */
    public static function end(){
        $destroyed = true;
        if( self::isActive() ){
            self::start();
            $destroyed = session_destroy();
        }

        return $destroyed;
    }

    /**
      * Set a single session variable
      *
      * @param string|int $key
      * @param string|int|float|array|object|boolean $val
      *
      * @return  boolean(true)
      *
      */
    public static function setVar($key, $val){
        $_SESSION[$key] = $val;

        return true;
    }

    /**
      * Set multiple session variables
      *
      * @param array $vars
      *
      * @return  boolean(true)
      *
      */
    public static function setVars(array $vars){
        foreach( $vars as $key => $val ){
            $_SESSION[$key] = $val;
        }

        return true;
    }

    /**
      * Convert exception error code 0 - 3 into custom session alerts
      *
      * @param Exception $exception
      *
      * @return  boolean(true)
      *
      */
    public static function setAlert($exception){
        switch( $exception->getCode() )
        {
            case 0: self::setVar('_error', $exception->getMessage());
            break;
            case 1: self::setVar('_warning', $exception->getMessage());
            break;
            case 2: self::setVar('_success', $exception->getMessage());
            break;
            case 3: self::setVar('_info', $exception->getMessage());
            break;
            default: self::setVar('_error', $exception->getMessage());
        }

        return TRUE;
    }

    /**
      * Get a session variable if it exists
      *
      * @param string|int $key
      *
      * @return  $_SESSION[$key] || boolean(false)
      *
      */
    public static function getVar($key, $default=null){
        return self::isActive() && isset($_SESSION[$key]) ? $_SESSION[$key] : (!is_null($default) ? $default : FALSE);
    }

    /**
      * Get a session variables if session is active
      *
      * @param string|int $key
      *
      * @return  $_SESSION[$key] || boolean(false)
      *
      */
    public static function getVars(){
        return self::isActive() ? $_SESSION : FALSE;
    }

    /**
      * Destroy a single variable
      *
      * @param string|int $key
      *
      * @return  boolean(true)
      *
      */
    public static function destroyVar($key){
        unset( $_SESSION[$key] );

        return TRUE;
    }
    
    /**
      * Destroy all variables
      *
      *
      * @return  boolean(true)
      *
      */
    public static function destroyVars(){
        $_SESSION = array();

        return TRUE;
    }

}
