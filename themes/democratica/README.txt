DEMOCRATICA THEME README
v0.8.0.3 Chris Messina
Last updated 2005-02-16 5:22:55 PM

COMMENTS
========

This theme was originally developed for CivicSpace to match 
JohnKerry.com.

v0.8.1 adds the ability to have a 1, 2 or 3 column layout. However, 
I don't know how to achieve a one-column layout at this time with 
Drupal... but if you need it, it's there!

I'm also implementing an attempted fix at column overlap by tables
and images with a javascript solution. Not sure if it'll be ready
in time for "print". In fact, well, nope, it's definitely not ready,
but it's in the scripts folder if you want to try your own hand at it!

I've gutted all the unnecessary styles I could find and pushed all
the typographic styles into basic.css. This is a new technique that
I'm developing. It might seem annoying at first, but really makes it
*much* easier to find those annoying font styles usually buried deep 
in structural markup.

All images are now PNGs instead of a mix of GIFs and PNGs. The 
interesting issue is that, at least in Safari, colors in an image 
in Photoshop turn up differently in a web browser next to their hex-
adecimal equivalent. This of course is a problem when dealing with 
transparent elements, like the old slogan/search. But since I
abandoned those styles, the problem no longer needs to be fixed!

I also have hopefully converted completely to UNIX line endings.


KNOWN ISSUES
============

At lower resolutions, IE and Firefox do weird things with the content.
In three column-mode, IE will drop the content in a waterfall; Firefox
will overlap the columns. I tried a number of things but nothing fixed
the problem as of 0.8.0.3.


TO DO
=====

* Print styles.
* More custom HTML output.
x -Fix poll content dropping in IE
x -Fix drupal.css-induced clearing bugs.