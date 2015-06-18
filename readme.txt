=== Homepage Control ===
Contributors: woothemes,mattyza,jameskoster,tiagonoronha
Donate link: http://woothemes.com/
Tags: homepage, hooks, theme-mod, components, customizer
Requires at least: 3.8.1
Tested up to: 4.2.2
Stable tag: 2.0.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Re-order or disable the homepage components in certain themes.

== Description ==

Using Homepage Control, any theme which uses a "homepage" (or other) hook to display components on a homepage, just got better. Re-order or disable any of the homepage components added by your theme, and control the display and order of any function hooked on to the "homepage" hook.

Looking for a helping hand? [View plugin documentation](http://docs.woothemes.com/document/homepage-control/).

== Usage ==

Once activated, a "Homepage Control" item will display in the Theme Customizer ( Appearance > Customizer ).

Visiting this screen will display a table of the possible homepage components, with options for re-ordering them, or disabling individual components.

Once done, click the "Save Changes" button, to commit your new component order into the system.

== Installation ==

Installing "Homepage Control" can be done either by searching for "Homepage Control" via the "Plugins > Add New" screen in your WordPress dashboard, or by using the following steps:

1. Download the plugin via WordPress.org.
1. Upload the ZIP file through the "Plugins > Add New > Upload" screen in your WordPress dashboard.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Visit the "Appearance > Customizer" section, select "Homepage Control". There you can select the components you'd like to display, and in which order.

== Frequently Asked Questions ==

= No components show up in the administration area. Is my theme broken? =

Not at all. This simply means your theme doesn't support the "homepage" action hook, which is a method of adding components to a homepage design.

Please consult your theme developer if you have further queries about customising your homepage.

= What happens if I switch themes? Do I lose my settings? =

Not at all. :) If you switch themes, Homepage Control will remember the homepage component settings for each theme you apply changes to, making switching themes a breeze.

= How do I contribute? =

We encourage everyone to contribute their ideas, thoughts and code snippets. This can be done by forking the [repository over at GitHub](http://github.com/woothemes/homepage-control/).

== Screenshots ==

1. The Homepage Control administration screen.


== Upgrade Notice ==

= 1.0.0 =
* 2014-03-18
* Initial release. Woo!

== Changelog ==

= 2.0.1 =
* 2015-06-01
* Make sure only components that actually exist are outputted to the customizer control (props valendesigns).
* Hide by default components added after the initial setup.

= 2.0.0 =
* 2015-04-28
* Removed custom admin page and moved Homepage Control to the WordPress Customizer in Appearance > Customizer.

= 1.0.1 =
* 2014-11-21
* Improved handling of functions hooked in via classes.

= 1.0.0 =
* 2014-03-18
* Initial release. Woo!