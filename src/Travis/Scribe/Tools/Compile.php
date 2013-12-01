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

use Kurenai\DocumentParser;

class Compile
{
    /**
     * Return master array of all posts.
     *
     * @return  array
     */
    public static function run()
    {
        // cache
        return \Cache::rememberForever(Cache::$names['master'], function()
        {
            // get list of all files
            $files = static::get_files();

            // foreach file...
            $master = array();
            foreach ($files as $file)
            {
                // parse
                $parse = static::get_file($file);

                // if successful...
                if ($parse)
                {
                    // save to master array
                    $master[] = $parse;
                }
            }

            // return
            return $master;
        });
    }

    /**
     * Return a hash value for all files.
     *
     * @return  string
     */
    public static function get_hash()
    {
        $hashes = array();
        foreach (static::get_files() as $file)
        {
            $hashes[] = md5_file($file);
        }

        return md5(serialize($hashes));
    }

    /**
     * Return path of storage directory.
     *
     * @return  string
     */
    protected static function get_dir()
    {
        // load config
        $dir = \Config::get('scribe::scribe.directory');

        // catch error
        if (!$dir) trigger_error('Config file not setup properly.');

        // return
        return rtrim($dir, '\\/');
    }

    /**
     * Return an array of all content files.
     *
     * @param   string  $dir
     * @param   array   $files
     * @return  array
     */
    protected static function get_files($dir = null, $files = array())
    {
        // get default dir
        if (!$dir) $dir = static::get_dir();

        // add new files to master
        $new_files = \File::files($dir);
        $files = array_merge($files, $new_files);

        // go thru subdirectories...
        $subdirs = \File::directories($dir);
        foreach ($subdirs as $subdir)
        {
            // add new files to master
            $files = static::get_files($subdir, $files);
        }

        // return
        return $files;
    }

    /**
     * Return a parsed file.
     *
     * @param   string  $file
     * @param   boolean $include_text
     * @return  array
     */
    protected static function get_file($path, $include_text = false)
    {
        // pull source
        $source = file_get_contents($path);

        // catch error
        if (!$source) trigger_error('File not found.');

        // initiate parser
        $parser = new DocumentParser;

        // parse document
        $document = $parser->parse($source);

        // save array
        $result = $document->get();

        // if include text...
        if ($include_text)
        {
            $mode = static::get_mode();
            if ($mode == 'markdown')
            {
                $result['text'] = $document->getContent();
            }
            else
            {
                $result['text'] = $document->getHtmlContent();
            }

        }

        // We are going to do some of our own tidying of the data
        // before we send it back to the Search class.

        // add path
        $result['path'] = $path;

        // if tags...
        if (isset($result['tags']))
        {
            // split
            $split = explode(',', $result['tags']);

            // trim
            foreach ($split as $key => $value) $split[$key] = trim($value);

            // save
            $result['tags'] = $split;
        }

        // return
        return $result;
    }
}