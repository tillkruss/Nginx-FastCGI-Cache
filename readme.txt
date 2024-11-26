=== Nginx Cache ===
Contributors: tillkruess
Donate link: https://github.com/sponsors/tillkruss
Tags: nginx, cache, fastcgi, purge, flush
Requires at least: 3.1
Tested up to: 6.7
Stable tag: 1.0.7
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Purge the Nginx cache (FastCGI, Proxy, uWSGI) automatically when content changes or manually within WordPress.


== Description ==

Purge the [Nginx](http://nginx.org) cache (FastCGI, Proxy, uWSGI) automatically when content changes or manually within WordPress.

Requirements:

  * The [Filesystem API](http://codex.wordpress.org/Filesystem_API) needs to function without asking for credentials.
  * Nginx and PHP need to run under the same user, or PHP's user needs write access to Nginx's cache path.


== Installation ==

For detailed installation instructions, please read the [standard installation procedure for WordPress plugins](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins).

1. Install and activate plugin.
2. Enter "Cache Zone Path" under _Tools -> Nginx_.
3. Done.


== Screenshots ==

1. Plugin settings page.


== Changelog ==

= 1.0.7 =

  * Support newer versions of Nginx in `validate_dirlist()`

= 1.0.6 =

  * Fixed rare issue in `validate_dirlist()`

= 1.0.5 =

  * Added `nginx_cache_zone_purged` action

= 1.0.4 =

  * Improved translatable strings
  * Fixed auto-purge bug
  * Fixed bug when validating directory

= 1.0.3 =

  * Create cache directory if it doesn't exists
  * Re-create cache directory after cache purge
  * Allow post types to be excluded from triggering a cache purge

= 1.0.2 =

  * Fixed 4.6 issue with file-system credentials

= 1.0.1 =

  * Improved testing of file-system credentials

= 1.0 =

  * Initial release
