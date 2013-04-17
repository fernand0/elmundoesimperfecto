Changelog
===========================================================================

Please see the [commit log][1] for all single changes.

[1] https://github.com/haschek/PubwichFork/commits

PubwichFork 2.1 (2012-10-18)
---------------------------------------------------------------------------
* update PEAR libraries
* fix PHP strict errors
* added support for responsive styles for small devices (mobile styles)
* added Identi.ca service class
* updated Github service class
* updated Twitter service class
* updated reset styles (Meyer 2.0)
* updated Delicious service class
* removed copyright notice from default template

PubwichFork 2.0 (2012-03-18)
---------------------------------------------------------------------------
* merged all existing Pubwich forks
* fixed plural form usage in gettext
* added support for german language (de_DE)
* added support to filter data before output
* added SimplePie to have stable RSS/ATOM API
* added Media RSS service class
* added example filters to default theme
* added support for server proxies
* updated Feed service class
* updated Delicious service class
* updated Flickr service class
* updated Last.fm service class
* updated Vimeo service class
* updated Youtube service class
* updated manual
* improved cache processing (single invalidation times + random displacements)
* simplified default theme layout

Pubwich 1.6 (?)
---------------------------------------------------------------------------
* Added StatusNet service.
* Added SlideShare service.
* Fixed Dribbble image regex.

Pubwich 1.5 (2010-06-04)
---------------------------------------------------------------------------
* Added Dribbble service.
* Added Reddit service.
* Change Gowalla user URLs from `/users/username/` to `/username/`.
* Add YoutubeFavorites method

Pubwich 1.41 (2010-06-04)
---------------------------------------------------------------------------
* Fix default theme templates.

Pubwich 1.4 (2010-05-29)
---------------------------------------------------------------------------
* Added Goodreads service.
* GitHub service now has 3 methods: GithubRecentActivity, GithubRepositories & GithubGists.
* Added LastFMTopAlbums method to LastFM service.
* Removed support for Twitter Basic Authentication — now using only OAuth.
* Removed phpsavant templating engine — now using Mustache.php.
* Added two new templates: `columnTemplate` and `layoutTemplate`.
* Fix several minor bugs.

Pubwich 1.3 (2010-02-18)
---------------------------------------------------------------------------
* Added GitHub service.
* Added Gowalla service.
* Added support for custom services in `/themes/theme_name/lib/Services`.
* Added custom HTTP headers capabilities to FileFetcher.
* Modified the standard way to extend existing services, with the `requireServiceFile` method.
* Merged pubwich-i18n repository into core.
* Modified the default theme to add `box-shadow` to individual service boxes.
* Moved `Zend_Json` from Twitter service to the main Pubwich class, so other services can use it.
* Fixed a bug in Readernaut service where `{%size%}` would not work properly.

Pubwich 1.2 (2009-12-08)
---------------------------------------------------------------------------
* Added support to add `<head>` links for services.
* Added some .htaccess files to prevent from loading PHP files from the browser.
* Added `{%content%}` tag for the RSS service.
* Added Sample service (Sample.php).

Pubwich 1.1 (2009-10-11)
---------------------------------------------------------------------------
* Added multi-methods support for services. For example, the Flickr service now has FlickrUser, FlickrGroup and FlickrTags methods.
* Added custom callback support for services that might not use XML (like JSON) Default callback is still `simplexml_load_string`.
* Added `Zend_Json` library, in case PHP's json extension is not enabled.
* Removed PHP Smartypants and PHP Markdown.
* Changed the default theme's font.
* Removed PEAR dependency.
* Updated Savant3 to its latest codebase (compatible with PHP 5.3).
* Services that use RSS (like Delicious) and Atom (like YouTube) feeds are now extending RSS or Atom service class.
* Fixed Twitter negative time bug (#19).

Pubwich 1.0 (2009-09-20)
---------------------------------------------------------------------------
* Initial release
