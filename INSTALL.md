# Installation

## Requirements
* Magento 2.2.0 Stable or higher

## Installation
To install this module, first of all, copy the module files to Magento 2:

1) We recommend using `composer` to install any Magento extension, including our Yireo modules. For installing our commercial
modules using your subscription link, see the following link:
https://www.yireo.com/software/magento-extensions/emailtester2#quickstart

2) Alternatively, you can copy all files within this folder to a new folder `app/code/Yireo/EmailTester2`. This makes upgrading
harder and might lead to errors quicker. So we recommend you to use `composer` instead.

## Activation
Next, enable the new module using the following CLI commands:

```bash
./bin/magento module:enable Yireo_EmailTester2 Yireo_AdminSimpleSearchFields
./bin/magento setup:upgrade
```

Please note that we trust that you have familiarized yourself with the technical aspects of Magento 2: How to properly use
Deployment Modes (Production Mode, Developer Mode) and how to deploy changes from a developer site to a production site (static
content deployment). Our module poses no difference to the regular deployment strategy that Magento recommends.

## See also
- [ChangeLog](CHANGELOG.md)
- [Testing](TESTING.md)
- [License](LICENSE.txt)
