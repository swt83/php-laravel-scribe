# Scribe for Laravel

A Laravel package for building a file-based blog. This is a not a static file blogging system that crunches files together into raw HTML, rather it's a blogging system that just doesn't use a database. Some technical know-how is still required by the user, but this library aims to take most of the work out of working w/ the files.

WARNING: This is my first package using Composer and Laravel 4. Please forgive me while I learn.

## Install

Normal composer install.

## Usage

### The File Structure

The package contains a directory ``_`` which is where you'll place your content. There are no folders provided, but you might make folders like ``pages`` or ``posts`` to start with.  Nested folders are okay.

Inside these folders is where you'll put your files that represent you pages and posts.  You can take a look at the provided ``template.txt`` file to see an example of how they should be formatted. File parsing uses [Kurenai](https://github.com/daylerees/kurenai) by Dayle Rees.

```
title: Vulputate Dapibus Vehicula Magna
subtitle: Cras justo odio, dapibus ac facilisis in, egestas eget quam.
-------
Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Nulla vitae elit libero, a pharetra augue. Cras mattis consectetur purus sit amet fermentum.
```

What's important here is how you declare your fields and values, not the field names themselves.  You can have any fields you want and in any order you want, doesn't matter. What fields you use are what will be returned to you by the library.

### The Library

Now for how to use these files in PHP:

```php

// get page
$page = Scribe::where('pages/my_page')->first();

// get custom file
$content = Scribe::get('my/random/content/type/foobar');

// get post
$post = Scribe::where('posts/my_post')->first();

// get all posts
$posts = Scribe::where('posts')->get();

// get last 10 posts
$posts = Scribe::where('posts')->take(10)->get();

// get last 10 posts w/ tag "foobar"
$posts = Scribe::where('posts')->where()->take(10)->get();

// get last 10 posts w/ tags "foo" and "bar"
$posts = Scribe::get('posts', 10, array('foo', 'bar'));

// get rss feed of posts
$rss = Scribe::to_rss($posts);

```

When the ``get()`` method detects that it's returning a list of posts, it will automatically sort that list based on filename.

### Conclusion

The idea is to provide a library that tries to get out of your way.  I think this provides a great deal of flexibility to the developer while leaving him free to handle particulars in the manner he chooses.  Like caching, URIs, frameworks, etc.