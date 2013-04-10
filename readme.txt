=== PBS What's On API ===
Contributors: thomascrenshaw
Tags: api, dashboard, tv schedules
Requires at least: 3.1.3
Tested up to: 3.2.1
Stable tag: 1.0

Determine visitors ZIP code from their IP address and list available program air dates in their area

== Description ==

This plugin allows you to set the desired program ID (using the PBS SODOR Program ID).  When a visitor arrives on the page for the first time, their IP address is detected and a ZIP code lookup happens. If the ZIP code is valid a request is made to the PBS API for the available stations. From this list of station(s), the applicable airdates are displayed. The plugin handles the authentication of requests using values maintained in the WordPress Admin. Results are cached using the WordPress Transients API.

The plugin uses the a new PBS SODOR PHP class created for KQED.

Links: [**SODOR API (aka TV Schedules API v2.0**]()

== Installation ==

1. Upload the pbs-whatson.zip file to the /wp-content/plugins directory and unzip
1. Activate the plugin from the Plugins menu in your admin menu
1. Configure the plugin by going to the PBS What's On menu item that appears in your admin menu


After activating the plugin, enter the API Key, the Cache TTL time, and any defaults in the PBS What's OnOptions screen.

== Frequently Asked Questions ==

None yet.

== Screenshots ==

1. Option Page
2. Dashboard Widget

== Changelog ==

= 1.0 =
* Initial external release

== Other Notes ==

W3TC users, if using the dashboard widget be sure that Object Cache is enabled and that "Don't cache WordPress Admin" is unchecked.

