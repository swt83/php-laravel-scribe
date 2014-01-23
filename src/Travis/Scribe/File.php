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

            // determine extension
            $extension = \File::extension($this->path);

            // return text
            return Compile::get_file_text($this->path, $extension);
        });
    }

}