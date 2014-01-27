<?php

namespace Travis\Scribe;

class Cache {

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
        'text' => 'scribe_text'
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
        // get refresh clock
        $clock = \Config::get('scribe::refresh', 5);

        // if cache is off...
        if (!$clock)
        {
            // reset
            static::reset();

            // escape
            return null;
        }

        // if times up...
        \Cache::remember(static::$names['timer'], $clock, function()
        {
            // new hash
            $new_hash = Compile::get_hash();

            // old hash
            $old_hash = \Cache::get(static::$names['hash']);

            // if different...
            if ($new_hash != $old_hash)
            {
                // reset
                static::reset();

                // save most recent hash
                \Cache::forever(static::$names['hash'], $new_hash);
            }

            // return
            return true;
        });
    }

    protected static function reset()
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

}