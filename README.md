# PluginPass - WordPress PRO Plugin/Theme Licensing

<a href="https://github.com/Labs64/PluginPass"><img src="https://raw.githubusercontent.com/Labs64/PluginPass/master/assets/banner-772x250.png" alt="PluginPass - WordPress PRO Plugin/Theme Licensing"></a>

---

[![WordPress tested](http://img.shields.io/wordpress/v/PluginPass.svg?style=flat-square)](https://wordpress.org/plugins/PluginPass/)
[![WordPress Plugin version](http://img.shields.io/wordpress/plugin/v/PluginPass.svg?style=flat-square)](https://wordpress.org/plugins/PluginPass/)
[![WordPress Plugin Downloads](http://img.shields.io/wordpress/plugin/dt/PluginPass.svg?style=flat-square)](https://wordpress.org/plugins/PluginPass/)
[![WordPress Plugin Rating](http://img.shields.io/wordpress/plugin/r/PluginPass.svg?style=flat-square)](https://wordpress.org/plugins/PluginPass/)
[![License](http://img.shields.io/badge/license-GPLv2-red.svg?style=flat-square)](http://opensource.org/licenses/GPL-2.0)


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

Payment gateways supported in the core, free plugin:

* PayPal Standard
* Stripe

Payment gateways supported in the premium version:

* Braintree - *PLANNED*
* Authorize.net - *PLANNED*
* 2Checkout - *PLANNED*
* SOFORT Banking - *PLANNED*
* BitPay - *PLANNED*
* Coinbase - *PLANNED*

## Quickstart

1. Clone repository
```
$ git clone https://github.com/Labs64/PluginPass.git
```

2. Start environment
```
$ docker-compose up -d
```

3. Build project
```
$ docker exec --workdir=/var/www/html/wp-content/plugins/pluginpass pluginpass-wordpress ./dockerfiles/bin/prj-build.sh
```

Now you can browse the site at [http://localhost:8000](http://localhost:8000) (user/pass: pluginpass/pluginpass)

---

4. Stop environment
```
$ docker-compose down
```

## Contributing

Anyone and everyone is welcome to contribute. Dozens of developers have helped make the PluginPass what it is today.


## Related Links

* Source: [https://github.com/Labs64/PluginPass](https://github.com/Labs64/PluginPass)
* WordPress plugin page: [https://wordpress.org/plugins/PluginPass/](https://wordpress.org/plugins/PluginPass/)
* NetLicensing page: [https://netlicensing.io](https://netlicensing.io)
* Author: [Labs64](https://www.labs64.com)
