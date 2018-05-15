# Magento 2 module for EmailTester2
Homepage: https://www.yireo.com/software/magento-extensions/emailtester2

Requirements:
* Magento 2.2.0 Stable or higher

## Installation
To install this module, first of all, copy the module files to Magento 2:

1) We recommend using `composer` to install any Magento extension, including our Yireo modules. For installing our commercial
modules using your subscription link, see the following tutorial:
https://www.yireo.com/tutorials/magento/magento-2/1840-using-composer-with-commercial-yireo-extensions

2) Alternatively, you can copy all files within this folder to a new folder `app/code/Yireo/EmailTester2`. This makes upgrading
harder and might lead to errors quicker. So please make sure to consider `composer` instead.

## Activation
Next, enable the new module using the following CLI commands:

    ./bin/magento module:enable Yireo_EmailTester2
    ./bin/magento setup:upgrade

Please note that we trust that you have familiarized yourself with the technical aspects of Magento 2: How to properly use
Deployment Modes (Production Mode, Developer Mode) and how to deploy changes from a developer site to a production site (static
content deployment). Our module poses no difference to the regular deployment strategy that Magento recommends.

## Unit testing
This extension ships with PHPUnit tests. The generic PHPUnit configuration in Magento 2 will pick up on these tests. To only
test Yireo extensions, you can also run the following:

    ./vendor/bin/phpunit -c ./vendor/yireo/magento2-emailtester2/phpunit.xml.yireo

## Integration testing
See the Magento document for the full procedure.