=== PluginPass - WordPress PRO Plugin/Theme Licensing (Public Alpha) ===
Author URI: https://netlicensing.io
Plugin URI: https://github.com/Labs64/PluginPass
Contributors: labs64
Donate link: https://www.paypal.me/labs64
Tags: license manager, software license, license, activation, validation, license key, monetization, NetLicensing
Requires at least: 4.9.7
Tested up to: 5.2.2
Requires PHP: 5.6
Stable tag: 0.9.10
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

PluginPass is a WordPress license manager that makes it easy to monetize your WordPress plugins and themes.

== Description ==

Easily control the use and monetize your WordPress plugins and themes using PluginPass - a *WordPress License Manager* backed by [Labs64 NetLicensing](https://netlicensing.io "Software License Management").

The Plugin is designed to be easy-to-use, which you easily define as a dependency to your plugin or theme, so this takes care of the plugin’s & theme’s features activation and validation.

= Features =

* Enable the subscription model for the plugin/theme
* Sell PRO versions of your plugin/theme
* Offer trial/evaluation version on the PRO features (with the fallback to the lite version)
* Enable up-selling by offering additional plugin/theme features
* Use plugin or theme on the given domain only
* License as many plugins/themes as you want
* Need more features? - [Let us know](https://github.com/Labs64/PluginPass/issues)

= Payment Gateways =

Payment Gateways supported in the plugin:

* PayPal
* Stripe
* MyCommerce
* FastSpring
* Braintree - *PLANNED*
* Authorize.net - *PLANNED*
* 2Checkout - *PLANNED*
* SOFORT Banking - *PLANNED*

= Quickstart =

Add this code-snippet to your plugin:

`
$quard = new \PluginPass\Inc\Common\PluginPass_Guard( $api_key, $product_number, $plugin_folder );
if ($quard->validate( $product_module_number )) {
    // do something
    $quard->open_shop();
}
`

Detailed integration instructions, NetLicensing products configuration tips and troubleshooting can be found on plugin's [Wiki page](https://github.com/Labs64/PluginPass/wiki).

== Installation ==

This plugin provides an interface to the Labs64 NetLicensing license management services and needs to be installed as a dependency to the plugins & themes.

Please refer PluginPass [Wiki page](https://github.com/Labs64/PluginPass/wiki) for the configuration details.

= Minimum Requirements =

* WordPress 4.9.7 or greater
* PHP version 5.6 or greater

== Upgrade Notice ==

Follow standard Wordpress plugin update process.

== Frequently Asked Questions ==

= Are recurring payments supported? =

Yes; see Payment Gateways supported.

= Will PluginPass work with my plugin or theme? =

Yes. PluginPass is designed to work with any plugin and theme, but it may require some coding to achieve that seamless integration.

= Where can I find complete documentation? =

Full searchable docs can be found at [GitHub Wiki](https://github.com/Labs64/PluginPass/wiki)

= Can I request new features and extensions to be included in future releases of the plugin? =

We always welcome your feedback and would love to know what you would like to see done next with the plugin and what features you would like integrated. You can vote on and request new features and extensions in our PluginPass [Issue Tracker](https://github.com/Labs64/PluginPass/issues)

= Where can I report bugs? =

If you have discovered a bug, we want to know so that we can get it fixed as soon as possible! We always work to make sure that the plugin is working fully prior to releasing an update but sometimes problems do arise. All bugs and issues can be reported on the PluginPass [Issue Tracker](https://github.com/Labs64/PluginPass/issues).

= I love PluginPass, it’s awesome! Can I contribute? =

Yes, you can! Join in on our [GitHub repository](https://github.com/Labs64/PluginPass) :) You can also leave us a nice review on the WordPress site to let others know what you think of the plugin!

== Privacy Policy & GDPR ==

This plugin integrating components of an external service - [Labs64 NetLicensing](https://netlicensing.io "Software License Management") (processor). Labs64 NetLicensing is an online license management service provider. License validation requests are processed using plugin or theme developer (controller) NetLicensing account.

By using this plugin validation requests will be sent to the Labs64 NetLicensing in order to verify valid use of the plugin or theme. Personal data may be transferred with these requests such as Unique Identifiers, Plugin and Theme Details, WordPress Instance Name, Domain Name, System Details of the data subject.

The European operating company of Labs64 NetLicensing is:
Labs64 GmbH
Radlkoferstr. 2
81373 Munich, Germany
Labs64 NetLicensing website: [NetLicensing.IO](https://netlicensing.io)

The applicable data protection provisions of Labs64 NetLicensing may be retrieved under Labs64 [Privacy Policy](https://www.labs64.com/legal/privacy-policy/).

We strongly encourage you to comply with WordPress [Plugin Guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/) and General Data Protection Regulation (GDPR) while developing your plugins and themes and interfacing with [Labs64 NetLicensing Services](https://netlicensing.io).

For more details on Labs64 NetLicensing data protection provisions visit Labs64 [Privacy Center](https://www.labs64.de/confluence/x/vQEKAQ).

= Controller’s Responsibilities =

The controller is the principal party for data collection responsibilities. These responsibilities include collecting individual’s consent, storing of the data, managing consent-revoking, enabling the right to access, etc.

If an individual revokes consent, the controller will be responsible for initiating this request. Therefore, on receipt of this request, it will be responsible to remove the revoked data through NetLicensing vendor account.

== Screenshots ==

1. PluginPass Overview
2. PluginPass Settings

== Changelog ==

= 0.9.10 =
* Update: Plugin description and banner

= 0.9.9 =
* Add plugins meta info
* Improve example code

= 0.9.8 =
* Fix: New Host/Licensee validation fails with HTTP400 - Licensee does not exist #19

= 0.9.7 =
* Fix: Adjust User-Agent to reflect plugin name and version #14
* Update: User consent before validation #13

= 0.9.10 =
* Fix: Adjust User-Agent to reflect plugin name and version #14
* Update: plugin documentation

= 0.9.5 =
* Update: plugin documentation

= 0.9.4 =
* Update: Add legal info and references to the plugin #12
* Update: Document MyCommerce, FastSpring as the supported payment gateways

= 0.9.3 =
* New: User consent before validation #13
* New: Add deregister plugin function #15
* Fix: Column "Expiration Date" used wrongly #18
* Update: Verify / implement / fix licensee / license deactivation roundtrip #17
* Update: How To: Describe plugin usage #4
* New: Install & activate PluginPass as dependency #2
* Update: Force validate #3
* Update: Bulk validate plugins error #11
* Fix: HTTP 4xx/5xx handling #7
* Update: Optimize DB table structure #10
* Update: Validation response output variables #9

= 0.9.2 =
* Update: Plugin documentation
* Fix: PluginPass_Guard PHP reference error #8

= 0.9.1 =
* Fix: plugin publish script #6
* Fix: Plugin activation error #1
* Update: Deleted dependency "selvinortiz/dot" #5

= 0.9.0 =
* New: PluginPass is born! This release is only available via download at https://github.com/Labs64/PluginPass
