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
        // name
        $cache = Cache::$names['search'].'_'.md5(serialize($query));

        // cache
        return \Cache::rememberForever($cache, function() use ($cache, $query)
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
                        if (!static::compare(isset($value->$where['field']) ? $value->$where['field'] : null, $where['operator'], $where['value']))
                        {
                            // remove from array
                            unset($results[$key]);
                        }
                    }
                }

                // foreach orderby...
                foreach ($query->order_bys as $order_by)
                {
                    // sort
                    if (strtolower($order_by['value']) == 'desc')
                    {
                        /*
                        usort($results, function($a, $b) use ($order_by['field'], $order_by['value'])
                        {

                        });
                        */
                    }
                    else
                    {

                    }
                }

                // slice the array
                $results = array_slice($results, $query->skip, $query->take);

                // if "first"...
                if ($query->take === 1) // strict match is deliberate
                {
                    // flatten
                    $results = array_values($results);
                    $results = isset($results[0]) ? $results[0] : null;
                }
            }

            // register
            Cache::register($cache);

            // return
            return $results;
        });
    }

    /**
     * Return comparison analysis of field and value.
     *
     * @return  boolean
     */
    protected static function compare($value1, $operator, $value2)
    {
        switch (strtolower($operator))
        {
            case 'like':
                return preg_match('/'.$value2.'/i', $value1); // this might need some work
                break;
            case '=':
                return $value1 == $value2;
                break;
            case '!=':
                return $value1 != $value2;
                break;
            case '<':
                return $value1 < $value2;
                break;
            case '>':
                return $value1 > $value2;
                break;
            default:
                return false;
                break;
        }
    }
}