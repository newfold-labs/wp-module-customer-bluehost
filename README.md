<a href="https://newfold.com/" target="_blank">
    <img src="https://newfold.com/content/experience-fragments/newfold/site-header/master/_jcr_content/root/header/logo.coreimg.svg/1621395071423/newfold-digital.svg" alt="Newfold Logo" title="Newfold Digital" align="right" 
height="42" />
</a>
<a href="https://bluehost.com/" target="_blank">
    <img src="https://github.com/bluehost/bluehost-wordpress-plugin/raw/master/static/images/logo.svg" alt="Bluehost Logo" title="Bluehost" align="right" height="32" />
</a>

# WordPress Customer Bluehost Module
[![Version Number](https://img.shields.io/github/v/release/newfold-labs/wp-module-customer-bluehost?color=21a0ed&labelColor=333333)](https://github.com/newfold/wp-module-customer-bluehost/releases)
[![License](https://img.shields.io/github/license/newfold-labs/wp-module-customer-bluehost?labelColor=333333&color=666666)](https://raw.githubusercontent.com/newfold-labs/wp-module-customer-bluehost/master/LICENSE)

This package integrates with the newfold [wp-data-module](https://github.com/newfold-labs/wp-module-data/) and integrates bluehost specific customer data into the module to better serve customers. 

## Installation

### 1. Add the Newfold Satis to your `composer.json`.

 ```bash
 composer config repositories.newfold composer https://newfold-labs.github.io/satis/
 ```

### 2. Require the `newfold-labs/wp-module-customer-bluehost` package.

 ```bash
 composer require newfold-labs/wp-module-customer-bluehost
 ```

## Data flow

The guapi endpoint doesn't return properly unless the site's domian has fully resolved (which takes some time on intitial install) and the site is hosted on bluehost proper (not other brands or even bh india). The bluehost plugin expects the site installer to provide certain customer data (id, plan, signupdate etc) as json data in the `bh_cdata_guapi` option when the site is initially installed (since version 1.4). Historically this data was then saved as a transient for a week (and deleted) before checking the API endpoint for updates. However, we found that deleting the provided data was not optimal and opted to soft expire the data and save as an option as to not lose any valid data.

The data is now (since version 1.5) converted to a persisting `bh_cdata` option as a serialized object (rather than a transient). There is an additional option `bh_cdata_expiration` for a soft expiration in 30 days time (MONTH_IN_SECONDS) at which point we attempt to fetch fresh data from the guapi api and saved to the `bh_cdata` option.

The module checks for customer data in the following order:
- customer data in option `bh_cdata`.
- customer data in legacy transient `bh_cdata`.
- as provided data (from installer).
- Finally if not found anywhere else, (or if the option is expired) data is fetched from the guapi endpoint which is then saved to the option for future use (with soft expiration).

Note: The requests to the guapi endpoint are throttled when they don't return valid date. This helps us control how much various sites (with the plugin installed) ping the api. In the case where a site is not on bluehost it will send 10 requests per week attempting to collect customer information. When data is stored, the soft expiration is set to a full month, and will not delete the stale data until valid data is returned.

[More on NewFold WordPress Modules](https://github.com/newfold-labs/wp-module-loader)