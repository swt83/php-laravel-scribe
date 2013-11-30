<?php

/**
 * A Laravel package for building a file-based blog.
 *
 * @package    swt83/php-laravel-scribe
 * @author     Scott Travis <scott@swt83.com>
 * @link       http://github.com/swt83/php-laravel-scribe
 * @license    MIT License
 */

namespace Travis\Scribe;

class Scribe
{
    /**
     * Magic method to build query and ultimately return results.
     *
     * @param   string  $method
     * @param   array   $args
     * @return  object
     */
    public static function __callStatic($method, $args)
    {
        // initiate query builder
        $object = new Tools\Query;

        // return object
        return call_user_func_array(array($object, $method), $args);
    }
}