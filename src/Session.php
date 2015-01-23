<?php

namespace Ohanzee\Helper;

class Session 
{
    /**
     * Returns current session id
     *
     *
     * @return  session_id || boolean(false)
     *
     */
    public static function getId()
    {
        return session_id() ?: false;
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
    public static function setId($session_id)
    {
        return self::isActive() ? false : session_id($session_id);
    }

    /**
     * Check if there is an active session
     *
     * 
     *
     * @return  boolean
     *
     */
    public static function isActive()
    {
        return self::status() === PHP_SESSION_ACTIVE ? true : false;
    }

    /**
     * Get session status
     *
     *
     * @return constant
     *
     */
    public static function status()
    {
        return session_status();
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
    public static function start($supress_errors=true)
    {
        if (!self::isActive()) {
            if (!$session = $supress_errors ? @session_start() : session_start()) {
                return false;
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
    public static function end()
    {
        $destroyed = true;
        if (self::isActive()) {
            self::start();
            $destroyed = session_destroy();
        }

        return $destroyed;
    }

    /**
     * Replace the current session id with a new one
     *
     * @param boolean $delete Whether or not to delete the old session data
     *
     * @return  boolean
     *
     */
    public static function refresh($delete=false)
    {
        return self::isActive() ? session_regenerate_id($delete) : false;
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
    public static function setVar($key, $val)
    {
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
    public static function setVars(array $vars)
    {
        return array_replace_recursive($_SESSION, $vars) ? true : false;
    }

    /**
     * Get a session variable if it exists
     *
     * @param string|int $key
     *
     * @return  $_SESSION[$key] || boolean(false)
     *
     */
    public static function getVar($key, $default=null)
    {
        return self::isActive() && array_key_exists($key, $_SESSION) ? $_SESSION[$key] : (!is_null($default) ? $default : false);
    }

    /**
     * Get all or a subset of session variables if session is active
     *
     * @param array $vars
     *
     * @return  array || boolean(false)
     *
     */
    public static function getVars($vars=array())
    {
        return self::isActive() ? (!empty($vars) ? array_intersect_key($_SESSION, array_flip($vars)) : $_SESSION) : false;
    }

    /**
     * Destroy a single variable
     *
     * @param string|int $key
     *
     * @return  boolean(true)
     *
     */
    public static function destroyVar($key)
    {
        unset($_SESSION[$key]);

        return true;
    }
    
    /**
     * Destroy all or a subset of variables
     *
     *
     * @return  boolean(true)
     *
     */
    public static function destroyVars($vars=array())
    {
        $_SESSION = !empty( $vars ) ? array_diff_key($_SESSION, array_flip($vars)) : array();

        return true;
    }

}
