About PubwichFork
===========================================================================

[PubwichFork][1] is an open-source PHP web application that allows you to
aggregate your published data from multiple websites and services into a
single HTML page.

PubwichFork is an improved version of the original Pubwich application,
since Pubwich is not actively maintained anymore by the original
author. PubwichFork fixes several bugs and enables filtering of the data
streams.

[1]: http://48augen.de/projects/pubwichfork.html

Installation
---------------------------------------------------------------------------

_If you need to upgrade from your current Pubwich(Fork) install, please
check the **upgrading** section in this file._

### Installing PubwichFork 2.9+

1. Get it from the repository and change to development branch:
   ```
   $ git clone https://github.com/haschek/PubwichFork.git
   $ cd PubwichFork
   $ git pull
   ```

2. Install necessary vendor libraries, you need to install [Bower][3]
   before you can use it for that step:
   ```
   $ bower install
   ```

3. Change the permissions on the cache directory to make it writeable for
   for you and the user group of the webserver, e.g. on Ubuntu:
   ```
   $ sudo chown youruser:www-data usr/cache/
   $ sudo chmod ug+w usr/cache/
   ```
   _It may be easier to allow writing to all (777) but this is not
   recommended for security reasons._

4. Duplicate ``usr/configuration/config.sample.php`` to 
   ``usr/configuration/config.php``. (Optional: if you want to use a custom
   theme, duplicate ``app/themes/default`` to ``usr/themes/your_theme_name``
   and edit the ``PUBWICH_THEME`` constant in ``usr/configuration/config.php``
   to ``'your_theme_name'``.

5. Edit the newly created config.php to fill the blank spaces with your
   informations (API keys, usernames, site’s URL, etc.) and to modify the
   arguments passed to ``Pubwich::setServices()``. See the **Service
   configuration** section of this file or read the
   [Service Class manual][4].

Everything should be working now.

[3]: https://github.com/bower/bower/#installing-bower
[4]: https://github.com/haschek/PubwichFork/wiki/SocialWebServices

Upgrading
---------------------------------------------------------------------------

Currently we have 3 different Pubwich(Fork) development branches:

1. ``1.x``: the first official Pubwich application, the last official
   release was Pubwich 1.5 - Pubwich is not maintained anymore since 2011

2. ``2.1``: May 2011 Pubwich was forked, community commits and fixes
   were merged in, and it was extended (e.g. adding filters and improved
   caching options, responsive default theme). Around 80 commits were made
   since then. The current stable release is PubwichFork 2.1

3. ``2.9+``: the current version, an intermediate release to ``3.x`` that
   is not finished yet. Nonetheless, it includes a lot of changes: project
   folders were restructured, usage process of vendor libraries was changed,
   the template system has been improved and the default theme is updated and
   mobile first now.

### Upgrade from Pubwich 1.x to PubwichFork 2.0 and 2.1

Last official Pubwich release was 1.5., please remember that important
changes were made in Pubwich 1.4., e.g. Twitter OAuth support and the new
template engine. Please check the old [Pubwich manual about upgrading][5]
if you are still using a Pubwich install before version 1.4.

Upgrading from Pubwich 1.5+ to PubwichFork 2.1 should be simple:

1. Backup your ``cfg/config.php`` and your customized user theme.

2. [Install PubwichFork][5] and re-configure it, using your configuration
   backup.

3. Copy your theme folder to ``themes`` again.

Usually your old config file and user theme should work with PubwichFork.
If not, please [report bugs][6].

### Upgrade from PubwichFork 2.1 to 2.9+

1. Backup your PubwichFork installation.

2. Install PubwichFork 3.x and copy your configuration backup file to
   ``usr/configuration/config.php``.

3. Copy your theme folder to ``usr/themes``.

4. If you developed own service classes, copy them to ``usr/services``.

PubwichFork 2.9+ is compatible to configurations and filters from 2.1. The old
template system is now deprecated but it is still supported in 2.9+

[5]: https://github.com/remiprev/pubwich#upgrading-to-pubwich-14
[6]: https://github.com/haschek/PubwichFork/issues

Service configuration
---------------------------------------------------------------------------

All services are configured in the `config.php` file, usually a service
looks like this:

```php
array(
    'Flickr', // service name
    'photos', // service id
    array(
        'method' => 'FlickrUser',
        'title' => 'Flick<em>r</em>',
        'description' => 'latest photos',
        'total' => 16,
        'key' => '________',
        'userid' => '________',
        'username' => '__________',
        'row' => 4,
    )
)
```

In this example ``Flickr`` is the **service name**, ``photos`` is the
**service ID** and the inner array is the service configuration. Some
parameters can be used for all services:

* ``title`` → box title
* ``description`` → box description
* ``total`` → number of items to display
* ``cache_limit`` → cache invalidation time in seconds

The simplest and wide supported [`Feed` service][7] can be configured by
additional parameters:

* ``url`` →  URI of the feed
* ``contenttype`` →  either `application/rss+xml` or `application/atom+xml`
* ``link`` →  the URI of the website the feed is used for

There are several other service classes, e.g. for Delicious, Vimeo,
YouTube, Flickr, Last.fm, simple Text, Dribbble, Facebook, Foursquare,
Github, Goodreads, Gowalla, Instapaper, Pinboard, Readernaut, Readitlater,
Reddit, Slideshare, GNU-Social/StatusNet and Twitter. For further info please
read the [Service documentation][8].

[7]: https://github.com/haschek/PubwichFork/wiki/ServiceFeed
[8]: https://github.com/haschek/PubwichFork/wiki/SocialWebServices

Custom themes and templates
---------------------------------------------------------------------------

Themes contain all main and sub templates for page chrome, layout containers,
channel boxes, list items; and the public graphics and stylesheets for the
layout. Your theme folder must be in ``usr/themes/``.

All templates are written in simple [Mustache][9] syntax, all main and sub
templates are in ``yourtheme/templates`` folder, using ``.mustache`` as file
extension.

There are currently four template types: box templates, item
templates, container templates, layout templates and site template:

* ``site.mustache``: page layout including HTML head, can use those template
  tags:
  * ``{{{title}}}``: the configured title
  * ``{{{themeurl}}}``: base url of the theme (used for CSS/JS links)
  * ``{{{version}}}``: PubwichFork version info
  * ``{{{headerinclude}}}``: necessary content for HTML head, included by
    PubwichFork (e.g. Feed URLs)
  * ``{{{content}}}``: insert layout containers and its content
  * ``{{{info}}}``: short info about PubwichFork usage
  * ``{{{footerinclude}}}``: necessary content, included by PubwichFork

* ``layout.mustache``: use this only if you know how many layout containers are
  configured. If ``layout.mustache`` is not available, it is generated automatically
  by PubwichFork (recommended). It can use various ``{{{containerX}}}`` tags,
  ``X`` is the container number, starting with 1. 

* ``container.mustache``: layout containers, each of them holds various boxes
  of the aggregared data channels. The container uses:
  * ``{{{number}}}``: number of container, starting with 1
  * ``{{{content}}}``: insert content of container

* 

[9]: http://mustache.github.io/


### Box templates

Box templates control the way whole boxes are displayed. There are a few
different ways to define them:

* `boxTemplate()` (applies to all boxes, **must** be defined in
  `functions.php`)
* `<Service>_boxTemplate()`
* `<Service>_<Method>_boxTemplate()`
* `<Service>_<Variable>_boxTemplate()`
* `<Service>_<Method>_<Variable>_boxTemplate()`

Example:

```php
function boxTemplate() {
    return '
        <div class="boite {{class}}" id="{{id}}">
            <h2><a rel="me" href="{{{url}}}">{{{title}}}</a> <span>{{{description}}}</span></h2>
            <div class="boite-inner">
                <ul class="clearfix">
                    {{{items}}}
                </ul>
            </div>
        </div>';
}
```

### Item templates

Item templates control the way each box item is displayed. Each service has
its own default templates, but using the following function names, you can
redefine them:

* `<Service>_itemTemplate()`
* `<Service>_<Method>_itemTemplate()`
* `<Service>_<Variable>_itemTemplate()`
* `<Service>_<Method>_<Variable>_itemTemplate()`

Example:

```php
function Twitter_TwitterUser_itemTemplate() {
    return '<li class="clearfix {{#in_reply_to_screen_name}}reply{{/in_reply_to_screen_name}}"><span class="date"><a href="{{{link}}}">{{{date}}}</a></span>{{{text}}}</li>'."\n";
}
```

There’s currently no documentation about which tag you can put between
`{{{}}}` braces for which service. In the meantime, you can check a service
file (located in `lib/Services/<Service>.php`) and look for the
`populateItemTemplate` function.

### Column templates

The column template defines how each column is rendered. You don’t have to
define this template; the default used by Pubwich is this:

```php
'<div class="col{{{number}}}">{{{content}}}</div>'
```

Where `{{{number}}}` is replaced by the column number and `{{{content}}}`
is replaced by the column content (the *boxes*). For instance, you could
put this in your `functions.php` file:

```php
funtion columnTemplate() {
    '<div class="column column-{{{number}}}"><div class="column-inner">{{{content}}}</div></div>';
}
```

### Layout templates

The layout template defines the columns layout. Again, you don’t have to
define this template; the default layout used by Pubwich is this (eg. if
you defined 3 columns in your `config.php` file):

```php
'{{{col1}}} {{{col2}}} {{{col3}}}'
```

So each column is displayed one after the other. But if you’d like to
change that layout, you can use this:

```php
function layoutTemplate() {
    return '<div class="first-column">{{{col1}}}</div><div class="other-columns">{{{col2}}} {{{col3}}}</div>';
}
```
