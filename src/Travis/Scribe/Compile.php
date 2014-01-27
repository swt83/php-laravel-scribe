<?php

namespace Travis\Scribe;

use Kurenai\DocumentParser;

class Compile {

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
        $dir = \Config::get('scribe::directory');

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
     * @return  array
     */
    protected static function get_file($path)
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

        // We are going to do some of our own tidying of the data
        // before we send it back to the Search class.

        // new object
        $file = new File;

        // add slug
        $file->slug = isset($result['title']) ? \Str::slug($result['title']) : null;

        // add known properties
        foreach ($result as $key => $value)
        {
            $field = \Str::slug($key, '_');
            if (in_array($key, \Config::get('scribe::splits', array())))
            {
                $splits = explode(',', $value);
                foreach ($splits as $key => $value)
                {
                    $splits[$key] = trim($value);
                }
                $file->$field = $splits;
            }
            else
            {
                $file->$field = $value;
            }
        }

        // add path
        $file->path = $path;

        // return
        return $file;
    }

    /**
     * Return the text from a file.
     *
     * @param   string  $path
     * @return  string
     */
    public static function get_file_text($path)
    {
        // pull source
        $source = file_get_contents($path);

        // catch error
        if (!$source) trigger_error('File not found.');

        // initiate parser
        $parser = new DocumentParser;

        // parse document
        $document = $parser->parse($source);

        // determine extension
        $extension = \File::extension($path);

        // based on mode...
        if (strtolower($extension) == 'md')
        {
            $text = $document->getHtmlContent();
        }
        else
        {
            $text = $document->getContent();
        }

        // return
        return $text;
    }

}