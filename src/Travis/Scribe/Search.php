<?php

namespace Travis\Scribe;

class Search {

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
                    $field = $order_by['field']; // as own var bc usort has issues w/ array value
                    $value = strtolower($order_by['value']);

                    // skip bogus sorts...
                    if (!in_array($value, array('asc', 'desc')) or is_array($field))
                    {
                        break;
                    }

                    // if sort desc...
                    if ($value == 'desc')
                    {
                        usort($results, function($a, $b) use ($field)
                        {
                            return $a->$field > $b->$field ? 1 : 0;
                        });
                    }

                    // else if sort asc...
                    else
                    {
                        usort($results, function($a, $b) use ($field)
                        {
                            return $a->$field > $b->$field ? 0 : 1;
                        });
                    }
                }

                // if skip or take options...
                if ($query->skip or $query->take)
                {
                    // slice the array
                    $results = array_slice($results, $query->skip, $query->take);
                }

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
        // build function
        $compare = function($alpha, $operator, $beta) {
            switch (strtolower($operator))
            {
                case 'like':
                    return preg_match('/'.$beta.'/i', $alpha); // this might need some work
                    break;
                case '=':
                    return $alpha == $beta;
                    break;
                case '!=':
                    return $alpha != $beta;
                    break;
                case '<':
                    return $alpha < $beta;
                    break;
                case '>':
                    return $alpha > $beta;
                    break;
                default:
                    return false;
                    break;
            }
        };

        // value1 might be an array...
        if (is_array($value1))
        {
            $test = false;
            foreach ($value1 as $value)
            {
                if ($compare($value, $operator, $value2))
                {
                    $test = true;
                }
            }
            return $test;
        }
        // else if normal string...
        else
        {
            return $compare($value1, $operator, $value2);
        }
    }

}