=== ja WowToken ===
Contributors: Kaouthia
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BCBJMLC5LH7JJ
Tags: wow, wowtoken, guild, gold, widget, plugin, warcraft
Requires at least: 4.2
Tested up to: 4.2.3
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Plugin providing a widget which shows the current price of World of Warcraft game time tokens for your region.

== Description ==

This plugin pulls the latest data from [WoWToken.info](http://www.wowtoken.info/ "WoW Token prices and historical statistics") providing you with the ability to have the current World of Warcraft Token price for your region on your own WordPress website through the use of a widget.

== Installation ==

Requires: PHP5.4

Install and activate the plugin through the 'Plugins' menu in WordPress

Default styling of the widget is provided and modifications can be made through your theme's CSS file using the following div classes.

* .jawowtoken_text
* .jawowtoken_price
* .jawowtoken_credit

== Frequently Asked Questions ==

= How often does it get the latest price? =

WoWToken.info requires that you make requests no more often than once every ten minutes.  This widget makes requests at least 15 minutes apart.

== Screenshots ==

1. This is how the widget displays by default in the Twenty Fourteen theme.  Styles can be overridden in your theme's CSS.
2. Widget admin, where you can choose the region you wish to display.

== Changelog ==

= 1.0.1 =
* Added plugin activation/deactivation hooks

= 1.0 =
* First release

== Upgrade Notice ==

= 1.0.1 =
Minor update

= 1.0 =
This version gives you the functionality in the first place!