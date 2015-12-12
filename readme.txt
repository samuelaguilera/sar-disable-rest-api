=== SAR Disable REST API ===
Contributors: samuelaguilera
Tags: api, rest api
Requires at least: 4.4
Tested up to: 4.4
Stable tag: 1.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.en.html

Disable WP core REST API introduced in WP 4.4. and remove its HTTP header and link tag

== Description ==

REST API introduced in WordPress 4.4 is a great resource for people interested in using it, but if you don't want to use it probably you will want to close this new door to your WordPress.

Fortunately the WP core team provides hooks and filters to turn it off. This plugin simply makes use of them to disable the REST API server and remove its HTTP header and link tag.
                                                                
= Requirements =

* WordPress 4.4 or higher.
    	
== Installation ==

* Extract the zip file and just drop the contents in the <code>wp-content/plugins/</code> directory of your WordPress installation (or install it directly from your dashboard) and then activate the plugin from Plugins page.
* There's not options page, simply install and activate.
  
== Changelog ==

= 1.0 =

* Initial release.