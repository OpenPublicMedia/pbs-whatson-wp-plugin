=========================
PBS Sodor API Plugin
=========================

Description:
============
This plugin allows you to retrieve the following:
User's IP Address
User's ZIP code based on IP address
Applicable stations based on ZIP code
Air dates and times for a single program using the Sodor programID.
The plugin handles the authentication of requests using values maintained in the WordPress Admin. Results are cached using the WordPress Transients API.

The plugin uses the pbs-sodor-api-class php class provided by Thomas Crenshaw of Crenshawed Solutions for making requests to the api. 

Installation:
=============
1. Upload the pbs-whatson-wp-plugin.zip file to the /wp-content/plugins directory and unzip
2. Activate the plugin from the Plugins menu in your admin menu
3. Configure the plugin by going to the PBS What's Options menu item that appears in your admin menu
4. After activating the plugin, enter the API Key, the Cache TTL time, and Program ID.
5. The 'Test Station' field allows you to test using a single station call sign (e.g. KQED)

Links: 
======
`Locator API v1`_

`TV Schedules API v2`_

`PBS Q&A Site`_

Requirements:
=============
* Requires at least: WordPress 3.1.3
* Tested up to: WordPress 3.5.1
* Requires PHP >= 5.2
* Stable tag: 1.0

Frequently Asked Questions:
===========================
Visit the `PBS Q&A Site`_ for frequently asked questions

Screenshots:
============
1. Option Page

Changelog:
==========
:Version 1.0: Initial external release

Other Notes:
=============
* Tags: api, dashboard, station locator, tv schedules

Initial Contributors:
=====================
* KQED

* `Crenshawed Solutions`_

  - Thomas Crenshaw (`thomascrenshaw`_)
  
* `Open Public Media GitHub`_

  
.. _Locator API v1: 
    https://projects.pbs.org/confluence/display/localization/Locator
    
.. _TV Schedules API v2:
    https://projects.pbs.org/confluence/display/tvsapi/TV+Schedules+Version+2
    
.. _PBS Q&A Site:
    http://open.pbs.org/answers/
    
.. _Crenshawed Solutions:
    https://crenshawed.com/
    
.. _thomascrenshaw:
    https://github.com/thomascrenshaw

.. _Open Public Media GitHub:
    https://github.com/openpublicmedia