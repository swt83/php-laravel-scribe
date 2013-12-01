<?php

/**
 * A Laravel package for building a file-based blog.
 *
 * @package    swt83/php-laravel-scribe
 * @author     Scott Travis <scott@swt83.com>
 * @link       http://github.com/swt83/php-laravel-scribe
 * @license    MIT License
 */

namespace Travis\Scribe\Tools;

class Cache
{
    /**
     * Naming schema for various caches.
     * @var     array
     */
    public static $names = array(
        'hash' => 'scribe_hash',
        'master' => 'scribe_master',
        'search' => 'scribe_search',
        'registry' => 'scribe_registry',
        'timer' => 'scribe_timer',
    );

    /**
     * Register a cache as having taken place.
     *
     * @param   string  $name
     * @return  void
     */
    public static function register($name)
    {
        // We need to register each cache that we save because
        // eventually we will need to erase them.  Can't erase caches
        // that we don't have a record for.;

        // get existing register
        $register = \Cache::get(static::$names['registry'], array());

        // add new record
        $register[] = $name;

        // save
        \Cache::forever(static::$names['registry'], $register);
    }

    /**
     * Check for changes, then erase all the existing caches.
     *
     * @return  void
     */
    public static function check()
    {
        // if times up...
        \Cache::remember(static::$names['timer'], \Config::get('scribe::scribe.refresh', 5), function()
        {
            // detect changes
            if (Compile::get_hash() != \Cache::get(static::$names['hash']))
            {
                // get register
                $register = \Cache::get(static::$names['registry'], array());

                // add names
                $register = array_merge($register, static::$names);

                // foreach cache...
                foreach ($register as $name)
                {
                    // forget
                    \Cache::forget($name);
                }
            }
        });
    }
}