=== Restrict Multisite Plugins ===
Contributors: kawauso
Donate link: http://adamharley.co.uk/buy-me-a-coffee/
Tags: multisite, plugins, restrict
Requires at least: 3.0
Tested up to: 3.2
Stable tag: 1.1.3

Allows network admins to restrict which plugins are available on sites, similar to themes.

== Description ==

A quick adaptation of the theme restriction code for plugins. Can only restrict on a site-wide basis, though it will only affect those sites on which it's activated. Does not restrict super admins. Plugins <strong>must</strong> be active on the main site to be controlled by this plugin (this is an issue with WordPress' plugin structure).

See also: <a href="http://wordpress.org/extend/plugins/restrict-multisite-widgets/">Restrict Multisite Widgets</a>

== Installation ==

1. Upload `restrict-multisite-plugins.php` to `/wp-content/plugins/` directory
2. Network Activate the plugin through the 'Plugins' menu OR Activate the plugin through the 'Plugins' menu of every WordPress site you wish to restrict
3. Select plugins to make available under the 'Restrictions' section in the 'Plugins' menu in the Network Admin or the 'Plugins' section in the 'Super Admin' menu

== Frequently Asked Questions ==

= Can I enable plugins only for individual sites? =

No. You can as a Network Admin however activate plugins for individual sites without restrictions.

= Will this plugin affect plugins already activated by site admins? =

No. This plugin only prevents changes to the state of plugins and hides them from Admins. You will need to deactivate any plugins manually.

== Screenshots ==

It looks much the same as the 'Themes' section in the 'Super Admin' menu in WordPress 3.0.

== Changelog ==

= 1.1.3 =
* Activation state check for menus to support single blog activation

= 1.1.2 =
* Changed class loading to admin-only

= 1.1.1 =
* Standardised capability used to 'manage_network_plugins'

= 1.1 =
* Added support for Network Admin
* Moved page to Network Admin -> Plugins -> Restrictions under WordPress 3.1 and higher
* Hid Network plugins
* Changed plugin class to static callbacks

= 1.0 =
* First public release

== Upgrade Notice ==

= 1.1.3 =
Support for single blog activation (menu item under Site Admin for single blogs).

= 1.1.2 =
Limits restrictions to admin-only.

= 1.1.1 =
Standardised user capability used to 'manage_network_plugins'.

= 1.1 =
Support for WordPress 3.1. Page is now under Network Admin -> Plugins -> Restrictions in WordPress 3.1 and higher. Hides Network plugins.