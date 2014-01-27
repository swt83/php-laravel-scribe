<?php

namespace Travis\Scribe;

class File {

    /**
     * Return the text of a file.
     *
     * @return  string
     */
    public function text()
    {
        // name
        $cache = Cache::$names['text'].'_'.md5($this->path);

        // cache
        return \Cache::rememberForever($cache, function() use ($cache)
        {
            // register
            Cache::register($cache);

            // capture
            $text = Compile::get_file_text($this->path);

            // filter
            $filters = \Config::get('scribe::filters', array());
            $find = array_keys($filters);
            $replace = array_values($filters);
            $text = str_ireplace($find, $replace, $text);

            // return
            return $text;
        });
    }

}