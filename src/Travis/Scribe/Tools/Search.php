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

class Search
{
    /**
     * Perform search on posts array.
     *
     * @param   object  $query
     * @return  array
     */
    public static function run($query)
    {
        // We're going to cache search results to keep the system
        // from having to do array crunching every pageload.

        // build cache name
        $cache = 'scribe_search_'.md5(serialize($query));

        // load from cache
        $results = \Cache::get($cache);

        // if not found...
        if (!$results or 1)
        {
            // get master record of all posts
            $results = Compile::run();

            // if query provided...
            if ($query)
            {
                // What we're doing isn't really a search, but rather a
                // process of elimination. We'll go thru each search param
                // and knock off posts from the master array as we go.

                // foreach where...
                foreach ($query->wheres as $where)
                {
                    // foreach file...
                    foreach ($results as $key => $value)
                    {
                        // if comparison fails...
                        if (!static::compare($value[$where['field']], $where['operator'], $where['value']))
                        {
                            // remove from array
                            unset($results[$key]);
                        }
                    }
                }

                foreach ($query->order_bys as $order_by)
                {

                }

                // slice the array
                $results = array_slice($results, $query->skip, $query->take);

                // if "first"...
                if ($query->take === 1) // strict match is deliberate
                {
                    // flatten
                    $results = $results[0];
                }
            }

            // save results
            \Cache::put($cache, $results, 5);
        }

        // return
        return $results;
    }

    /**
     * Return comparison analysis of field and value.
     *
     * @return  boolean
     */
    protected static function compare($value1, $operator, $value2)
    {
        return version_compare($value1, $value2, $operator);
    }
}