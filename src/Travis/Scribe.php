<?php

namespace Travis;

class Scribe {

    /**
     * Magic method to build query and ultimately return results.
     *
     * @param   string  $method
     * @param   array   $args
     * @return  object
     */
    public static function __callStatic($method, $args)
    {
        // check for changes
        Scribe\Cache::check();

        // initiate query builder
        $object = new Scribe\Query;

        // return object
        return call_user_func_array(array($object, $method), $args);
    }

}