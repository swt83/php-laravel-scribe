# Scribe for Laravel

A Laravel package for building a file-based blog. This is a not a "static file" blogging system that crunches files together into raw HTML, rather it's a blogging system that just doesn't use a database. Some technical know-how is still required by the user, but this library aims to take most of the work out of working w/ the files.

WARNING: This is my first package using Composer and Laravel 4. Please forgive me while I learn.

## Install

Normal composer install.

## Usage

### Storing Your Files

The config file contains a setting for where your content files will be located, which by default is ``app/views/scribe/``.  You can create any folder structure you want, as nested as you want.  You may consider ``posts/`` and ``pages/``, for example, to organize your content.

Inside these folders is where you'll put your files.  Each file represents a single "post" or "page".  These files are processed using the [Kurenai](https://github.com/daylerees/kurenai) library by Dayle Rees, allowing you to use HTML or Markdown.  Here is a sample file:

```
title: Vulputate Dapibus Vehicula Magna
subtitle: Cras justo odio, dapibus ac facilisis in, egestas eget quam.
-------
Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Nulla vitae elit libero, a pharetra augue. Cras mattis consectetur purus sit amet fermentum.
```

You are free to create any fields you wish above the dashed lines.  Also, the system will automatically determine which coding method to use (Markdown or HTML) based on the file extension.  Use ``.md`` for Markdown or ``.html`` for HTML.

### Using Your Files

The query methods are written to try and mimic Eloquent:

```php

// get file by slug
$page = Scribe::where('slug', '=', 'my_page')->first();

// get file by date created
$post = Scribe::order_by('date', 'desc')->take(1)->first();

// get all posts
$posts = Scribe::all();

// get last 10 posts
$posts = Scribe::where('category', '=', 'posts')->take(10)->get();

// get last 10 posts w/ tag "foobar"
$posts = Scribe::where('category', '=', 'posts')->where('tag', '=', 'foobar')->take(10)->get();

```

Just note that the fields used in the ``where()`` and ``order_by()`` methods are dependant on those fields being available in your files!  If a file does not have the field in question a default of ``null`` will be assumed.

### Under the Hood

The system relies heavily on caching.  It builds a master array of all data from all posts and uses that array to calculate search results.  This master array is cached, as are all search results, and will be remembered forever.  However, the system periodically checks the hash of the content directory to detect changes, and if discovered, the caches will be reset and rebuilt.

## To Do

- Add RSS feed builder.
- Add pagination methods.