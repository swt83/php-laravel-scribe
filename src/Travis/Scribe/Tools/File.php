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

class File
{
    public function text()
    {
        // name
        $cache = \Cache::$names['text'].'_'.md5($this->path);

        // cache
        return \Cache::rememberForever($cache, function() use ($cache)
        {
            // determine extension
            $extension = \File::extension($this->path);

            // based on mode...
            if (strtolower($extension) == 'md')
            {
                $text = $document->getContent();
            }
            else
            {
                $text = $document->getHtmlContent();
            }

            // register
            Cache::register($cache);

            // return
            return $text;
        });
    }
}