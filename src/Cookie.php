<?php

/**
 * Ohanzee Components by Kohana
 *
 * @package    Ohanzee
 * @author     Kohana Team <team@kohanaframework.org>
 * @copyright  2007-2014 Kohana Team
 * @link       http://ohanzee.org/
 * @license    http://ohanzee.org/license
 * @version    0.1.0
 *
 * BSD 2-CLAUSE LICENSE
 * 
 * This license is a legal agreement between you and the Kohana Team for the use
 * of Kohana Framework and Ohanzee Components (the "Software"). By obtaining the
 * Software you agree to comply with the terms and conditions of this license.
 * 
 * Copyright (c) 2007-2014 Kohana Team
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * 
 * 1) Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 * 2) Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace Ohanzee\Helper;

class Cookie
{

    /**
     * @var  string  Magic salt to add to the cookie
     */
    public static $salt = null;

    /**
     * @var  integer  Number of seconds before the cookie expires
     */
    public static $expiration = 0;

    /**
     * @var  string  Restrict the path that the cookie is available to
     */
    public static $path = '/';

    /**
     * @var  string  Restrict the domain that the cookie is available to
     */
    public static $domain = null;

    /**
     * @var  boolean  Only transmit cookies over secure connections
     */
    public static $secure = false;

    /**
     * @var  boolean  Only transmit cookies over HTTP, disabling Javascript access
     */
    public static $httponly = false;

    /**
     * Gets the value of a signed cookie. Cookies without signatures will not
     * be returned. If the cookie signature is present, but invalid, the cookie
     * will be deleted.
     *
     *     // Get the "theme" cookie, or use "blue" if the cookie does not exist
     *     $theme = Cookie::get('theme', 'blue');
     *
     * @param   string  $key        cookie name
     * @param   mixed   $default    default value to return
     * @return  string
     */
    public static function get($key, $default = null)
    {
        if (!isset($_COOKIE[$key])) {
            // The cookie does not exist
            return $default;
        }

        // Get the cookie value
        $cookie = $_COOKIE[$key];

        // Find the position of the split between salt and contents
        $split = strlen(static::salt($key, null));

        if (isset($cookie[$split]) && $cookie[$split] === '~') {
            // Separate the salt and the value
            list ($hash, $value) = explode('~', $cookie, 2);

            if (static::salt($key, $value) === $hash) {
                // Cookie signature is valid
                return $value;
            }

            // The cookie signature is invalid, delete it
            static::delete($key);
        }

        return $default;
    }

    /**
     * Sets a signed cookie. Note that all cookie values must be strings and no
     * automatic serialization will be performed!
     *
     *     // Set the "theme" cookie
     *     Cookie::set('theme', 'red');
     *
     * @param   string  $name       name of cookie
     * @param   string  $value      value of cookie
     * @param   integer $expiration lifetime in seconds
     * @return  boolean
     * @uses    Cookie::salt
     */
    public static function set($name, $value, $expiration = null)
    {
        if ($expiration === null) {
            // Use the default expiration
            $expiration = static::$expiration;
        }

        if ($expiration !== 0) {
            // The expiration is expected to be a UNIX timestamp
            $expiration += time();
        }

        // Add the salt to the cookie value
        $value = static::salt($name, $value).'~'.$value;

        return setcookie($name, $value, $expiration, static::$path, static::$domain, static::$secure, static::$httponly);
    }

    /**
     * Deletes a cookie by making the value null and expiring it.
     *
     *     Cookie::delete('theme');
     *
     * @param   string  $name   cookie name
     * @return  boolean
     */
    public static function delete($name)
    {
        // Remove the cookie
        unset($_COOKIE[$name]);

        // Nullify the cookie and make it expire
        return setcookie($name, null, -86400, static::$path, static::$domain, static::$secure, static::$httponly);
    }

    /**
     * Generates a salt string for a cookie based on the name and value.
     *
     *     $salt = Cookie::salt('theme', 'red');
     *
     * @param   string  $name   name of cookie
     * @param   string  $value  value of cookie
     * @return  string
     */
    public static function salt($name, $value)
    {
        // Require a valid salt
        if (!static::$salt) {
            throw new InvalidArgumentException(
                'A valid cookie salt is required. Please set Cookie::$salt before calling this method.' .
                'For more information check the documentation'
            );
        }

        // Determine the user agent
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : 'unknown';

        return sha1($agent.$name.$value.static::$salt);
    }
}
