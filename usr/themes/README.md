Create your own user theme
===========================================================================

Themes contain all main and sub templates for page chrome, layout containers,
channel boxes, list items; and the public graphics and stylesheets for the
layout. Your theme folder must be in ``usr/themes/``.

All templates are written in simple [Mustache][1] syntax, all main and sub
templates are in ``yourtheme/templates`` folder, using ``.mustache`` as file
extension.

There are currently four template types: box templates, item
templates, container template, layout template and the global site template:

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
  by PubwichFork (recommended). It can hold various ``{{{containerX}}}`` tags,
  ``X`` is the container number, starting with 1, e.g.
  
  ```html
  <div>
    {{{container1}}}
  </div>
  <div>
    <div>
        {{{container3}}}
    </div>
    <div>
        {{{container2}}}
    </div>
  </div>
  ```

* ``container.mustache``: layout containers, each of them holds various boxes
  of the aggregared data channels. The container uses:
  * ``{{{number}}}``: number of container, starting with 1
  * ``{{{content}}}``: insert content of container

[1]: http://mustache.github.io/


### Box templates

Box templates control the way aggregated content from the Service classes are
displayed. There are a few file name patterns that you can use to define them,
from globally used box templates to more specialized box templates:

* ``box.mustache``: applies to all boxes, **must** be defined
* ``#ParentClass_box.mustache``
* ``[#ParentClass_]#ServiceName_box.mustache``
* ``[#ParentClass_]#ServiceName_#MethodName_box.mustache``
* ``#ServiceId_box.mustache``

The patterns include all parent classes of the service, except the Service core
class itself. PubwichFork always uses the most unique pattern.

For compatibility issues with older versions of PubwichFork themes the patterns
include all those combinations extended by the configured service id for each
channel, e.g. ``#ServiceName_#ServiceId_box.mustache``, as well as the template
definitions by similar method names in ``functions.php`` located in the theme
folder. It is not recommended to use this now deprecated template definitions.

* ``#ServiceName_#ServiceId_box.mustache``: in ``functions.php`` the method
  need to be named ``#ServiceName_#ServiceId_boxTemplate()``

Box templates support following template tags:

* ``{{id}}``: configured service id
* ``{{class}}``: CSS classes generated from service class names and its parents
* ``{{{title}}}``: configured title
* ``{{{url}}}``: configured URL for the service
* ``{{{description}}}``: configured description
* ``{{{items}}}``: aggregated items of the Social Web service, each item is
  is rendered by own item template


### Item templates

Item templates control the way each Service class item is displayed. Each
service has its own default templates, but using the following template names,
you can redefine them, it uses the same patterns like the box templates.

* ``#ParentClass_item.mustache``
* ``[#ParentClass_]#ServiceName_item.mustache``
* ``[#ParentClass_]#ServiceName_#MethodName_item.mustache``
* ``#ServiceId_item.mustache``

The same goes for the support of the old template name patterns and the support
of functions in ``functions.php``.

Check the [service overview][2] for documentation of template tags that can be
used for for each service. Additionally, you can check files (located in
`app/services/`) and look for the `populateItemTemplate` or `processDataItem`
methods.

[2]: https://github.com/haschek/PubwichFork/wiki/SocialWebServices

