# Scribe for Laravel

A Laravel package for building a file-based blog. This is a not a "static file" blogging system that crunches files together into raw HTML, rather it's a blogging system that just doesn't use a database. Some technical know-how is still required by the user, but this library aims to take most of the work out of the process.

**WARNING: This is my first package using Composer and Laravel 4. Please forgive me while I learn.**

## Install

Normal composer install.

## Usage

### Storing Your Files

The config file contains a setting for where your content files will be located, which by default is ``app/views/scribe/``.  You can create any folder structure you want, as nested as you want.  You may consider ``posts/`` and ``pages/``, for example, to organize your content.

Inside these folders is where you'll put your files.  Each file represents a single "post" or "page".  These files are processed using the [Kurenai](https://github.com/daylerees/kurenai) library by Dayle Rees, which allows you to use HTML or Markdown. Here is a sample file which shows some basic field/value pairs, followed by some text:

```
title: Vulputate Dapibus Vehicula Magna
subtitle: Cras justo odio, dapibus ac facilisis in, egestas eget quam.
-------
Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Nulla vitae elit libero, a pharetra augue. Cras mattis consectetur purus sit amet fermentum.
```

You are free to create any fields you wish above the dashed line.  Below the dashed line you will write the primary text, which can be written in Markdown or HTML.  The system will automatically translate either of these two coding methods based on the file extension, ``.md`` for Markdown or ``.html`` for HTML.

### Using Your Files

The query methods are written to try and mimic Eloquent:

```php

// get all files
$posts = Scribe::all();

// get file by slug
$page = Scribe::where('slug', '=', 'my_page')->first();

// get file by date created
$post = Scribe::order_by('date', 'desc')->take(1)->first();

// get last 10 files w/ category "post"
$posts = Scribe::where('category', '=', 'post')->take(10)->get();

// get last 10 posts w/ tag "foobar"
$posts = Scribe::where('category', '=', 'post')->where('tag', '=', 'foobar')->take(10)->get();

```

Just note that the fields used in the ``where()`` and ``order_by()`` methods are dependant on those fields being available in your files!  If a file does not have the field in question a default of ``null`` will be assumed.

The search results will deliver an object (or array of objects) that contains all the information available from the file.  All the meta data will be object properties, but the actual text will require the ``text()`` method.

```php
$title = $file->title; // object property
$date = $file->date; // object property
$text = $file->text(); // object method to translate from Markdown or HTML
```

### Under the Hood

The system relies heavily on caching.  It builds a master array of all data from all posts and uses that array to calculate search results.  This master array is cached, as are all search results, and will be remembered forever.  However, the system periodically checks the hash of the content directory to detect changes, and if discovered, the caches will be reset and rebuilt.

## To Do

- Add RSS feed builder.
- Add pagination methods.