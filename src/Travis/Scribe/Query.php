<?php

namespace Travis\Scribe;

class Query {

    /**
     * Storage array for "where" clauses.
     * @var     array
     */
    public $wheres = array();

    /**
     * Storage var for "skip" clause.
     * @var     array
     */
    public $skip = 0;

    /**
     * Storage var for "take" clause.
     * @var     array
     */
    public $take = 0;

    /**
     * Storage array for "order by" clauses.
     * @var     array
     */
    public $order_bys = array();

    /**
     * Add "where" search param.
     *
     * @param   string  $field
     * @param   string  $operator
     * @param   string  $value
     * @return  object
     */
    public function where($field, $operator, $value)
    {
        // save
        $this->wheres[] = array(
            'field' => $field,
            'operator' => $operator,
            'value' => $value
        );

        // return
        return $this;
    }

    /**
     * Add "skip" search param.
     *
     * @param   int     $int
     * @return  object
     */
    public function skip($int)
    {
        // save
        $this->skip = $int;

        // return
        return $this;
    }

    /**
     * Add "take" search param.
     *
     * @param   int     $int
     * @return  object
     */
    public function take($int)
    {
        // save
        $this->take = $int;

        // return
        return $this;
    }

    /**
     * Add "order by" search param.
     *
     * @param   string  $field
     * @param   string  $value
     * @return  object
     */
    public function order_by($field, $value)
    {
        // save
        $this->order_bys[] = array('field' => $field, 'value' => $value);

        // return
        return $this;
    }

    /**
     * Return "first" search results.
     *
     * @return  array
     */
    public function first()
    {
        // modify limit
        $this->take = 1;

        // return from search
        return Search::run($this);
    }

    /**
     * Return "get" search results.
     *
     * @return  array
     */
    public function get()
    {
        // return from search
        return Search::run($this);
    }

    /**
     * Return "all" search results.
     *
     * @return  array
     */
    public function all()
    {
        // return from search
        return Search::run(null);
    }

}