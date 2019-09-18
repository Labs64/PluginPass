# PluginPass - WordPress PRO Plugin/Theme Licensing (Public Alpha)

<p align="center"><img src="https://raw.githubusercontent.com/Labs64/PluginPass/master/assets/banner-1544x500.png" alt="PluginPass - WordPress PRO Plugin/Theme Licensing"></p>

---

[![WordPress tested](https://img.shields.io/wordpress/v/pluginpass-pro-plugintheme-licensing.svg?style=flat-square)](https://wordpress.org/plugins/PluginPass/)
[![WordPress Plugin version](https://img.shields.io/wordpress/plugin/v/pluginpass-pro-plugintheme-licensing.svg?style=flat-square)](https://wordpress.org/plugins/PluginPass/)
[![WordPress Plugin Downloads](https://img.shields.io/wordpress/plugin/dt/pluginpass-pro-plugintheme-licensing.svg?style=flat-square)](https://wordpress.org/plugins/PluginPass/)
[![WordPress Plugin Rating](https://img.shields.io/wordpress/plugin/r/pluginpass-pro-plugintheme-licensing.svg?style=flat-square)](https://wordpress.org/plugins/PluginPass/)
[![License](https://img.shields.io/badge/license-GPLv2-red.svg?style=flat-square)](https://opensource.org/licenses/GPL-2.0)


---

## How to enable WordPress Plugin or Theme licensing

With [Labs64 NetLicensing](https://netlicensing.io), it’s incredibly easy to enable license management for any software product, whether you’re selling a WordPress plugin, theme, desktop app, or even SaaS and IoT products.

PluginPass is a WordPress license manager that makes it easy to monetize your WordPress plugins and themes.

The Plugin is designed to be easy-to-use, which you easily define as a dependency to your plugin or theme, so this takes care of the plugin's & theme's features activation and validation.

### Features

* Enable the subscription model for the plugin/theme
* Sell PRO versions of your plugin/theme
* Offer trial/evaluation version on the PRO features (with the fallback to the lite version)
* Enable up-selling by offering additional plugin/theme features
* Use plugin or theme on the given domain only
* License as many plugins/themes as you want
* Need more features? - [Let us know](https://github.com/Labs64/PluginPass/issues)

### Payment Gateways

Payment gateways supported in the plugin:

* PayPal
* Stripe
* MyCommerce
* FastSpring
* Braintree - *PLANNED*
* Authorize.net - *PLANNED*
* 2Checkout - *PLANNED*
* SOFORT Banking - *PLANNED*

## PluginPass Overview

[PluginPass](https://wordpress.org/plugins/pluginpass-pro-plugintheme-licensing/) is defined as a dependency to your WordPress plugin/theme and handles entire communication in a **secure way** with the [Labs64 NetLicensing](https://netlicensing.io), and allows plugins/themes activation and validation at customer's WordPress instance, as well as new Licenses acquisition or Licenses renewal.

![NetLicensing / WordPress Overview](https://github.com/Labs64/PluginPass/blob/master/docs/pluginpass-overview.png)

## Quickstart

Add this code-snippet to your plugin:
```
$quard = new \PluginPass\Inc\Common\PluginPass_Guard( $api_key, $product_number, $plugin_folder );
if ($quard->validate( $product_module_number )) {
    // do something
    $quard->open_shop();
}
```

Detailed integration instructions, [ NetLicensing](https://netlicensing.io) products configuration tips and troubleshooting can be found on plugin's [Wiki page](https://github.com/Labs64/PluginPass/wiki).


## Contributing

Anyone and everyone is welcome to contribute. Dozens of developers have helped make the PluginPass what it is today.

Check out also our [Developers Guide here](https://github.com/Labs64/PluginPass/wiki/Developers-Guide).


## Related Links

* WordPress plugin page: [https://wordpress.org/plugins/pluginpass-pro-plugintheme-licensing/](https://wordpress.org/plugins/pluginpass-pro-plugintheme-licensing/)
* NetLicensing page: [https://netlicensing.io](https://netlicensing.io)
* Author: [Labs64](https://www.labs64.com)
* Source: [https://github.com/Labs64/PluginPass](https://github.com/Labs64/PluginPass)
