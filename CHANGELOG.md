# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

## [1.7.2] - 19 April 2023
### Fixed
- Change composer name of React module
- Update XML structure to fix fields not being picked up

## [1.7.1] - 25 March 2023
### Fixed
- Swap dep of ExtensionChecker with core module info

## [1.7.0] - 24 March 2023
### Added
- Added dependency with new `Yireo_AdminReactComponents` module

## [1.6.25] - 8 November 2022
### Fixed
- Calculate if order is virtual by using products

## [1.6.24] - 31 July 2022
### Fixed
- Bump

## [1.6.23] - 31 July 2022
### Fixed
- Bump

## [1.6.22] - 31 July 2022
### Fixed
- Fix empty output in AlertGrid causing `preg_replace` error in Magento core

## [1.6.21] - 31 July 2022
### Fixed
- Undefined flag with virtual orders (PR 2, @cloudsfactory)

## [1.6.20] - 26 June 2022
### Fixed
- Copy shipping address from billing address when empty
- Fix PHP 8.1 error with Magento AlertGrid (issue 35569)

## [1.6.21] - 11 July 2022
### Fixed
- Version bump

## [1.6.20] - 28 June 2021
### Fixed
- Version bump

## [1.6.19] - 12 August 2021
### Fixed
- Prevent duplicate AJAX call when searching in modal popup

## [1.6.18] - 17 May 2021
### Fixed
- Cleanup of DI code
- Use General Contact as a fallback
- Fix for "Send" not picking up on right variables anymore

## [1.6.17] - 13 April 2021
### Fixed
- Prevent exception when order exists with no invoice

## [1.6.16] - 2 March 2021
### Fixed
- Remove fake shipping ID because it will only generate an error

## [1.6.15] - 1 March 2021
### Fixed
- Fixed missing `shipping_id`

## [1.6.14] - 1 March 2021
### Fixed
- Fixed missing `invoice_id` and `creditmemo_id` just in case

## [1.6.13] - 26 February 2021
### Fixed
- Remove yarn.lock
- Fixed missing `order_id`
- De-duplicate address code of both Order and OrderVars class

## [1.6.12] - 19 December 2020
### Fixed
- Re-add `setup_version` for Magento 2.2 compatibility

## [1.6.11] - 30 November 2020
### Fixed
- Use better method to inject form values in preview URL

### Added
- Add new customer note variables (2.4+)
- Documented non-extension issue on outdated templates (FAQ)

## [1.6.10] - 19 November 2020
### Fixed
- Make sure form values for product, order and customer are transferred to preview

## [1.6.9] - 2 November 2020
### Fixed
- Change form IDs to become more unique to avoid conflicts with other extensions

## [1.6.8] - 30 October 2020
### Fixed
- Change namespace of UiComponents buttons (`preview`) to avoid conflicts with other mixins

## [1.6.7] - 28 July 2020
### Fixed
- Bump Magento 2.4 dependencies

## [1.6.6] - 23 July 2020
### Fixed
- Remove all unneeded CSS from preview page

## [1.6.5] - 21 July 2020
### Fixed
- Set order ID when creating dummy shipment
- Upgrade tests to PHPUnit 8+ (Magento 2.4 compatible)

## [1.6.4] - 30 June 2020
### Added
- Enhance integration tests for AJAX URLs
- Allow for customer search based on full name
- Allow column sorting in search popups for products, orders and customers

### Removed
- Remove `setup_version` because there is no setup

### Fixed
- Show messages on empty data right away on page
- Additional compliance to PHP CodeSniffer

## [1.6.3] - 3 June 2020
### Added
- Cleaned up configuration section
- Fix CSS issue in header
- Cleanup outdated jQuery autocomplete files

## [1.6.2] - 19 May 2020
### Added
- Little footer in preview to show email subject

## [1.6.1] - 12 May 2020
### Fixed
- Fix issue with admin keys for Preview page
- Fix order_data not working, with empty greeting in order emails

## [1.6.0] - 9 May 2020
### Added
- Open a tab when previewing
- Reuse `react_loader.html` Knockout HTML
- Initialize components via UiComponent XML

## [1.5.0] - 21 April 2020
### Added
- Major rewrite of UiComponents using React to increase usability of searching for products, customers and orders

## [1.4.3] - 10 December 2019
### Fixed
- Performance fix for checking if there are any products, customers or orders
- Add default value 50 for limiting collections

## [1.4.2] - 31 October 2019


## [1.4.1] - 30 October 2019
### Fixed
- New CLI did not properly translate per Store View

## [1.4.0] - 29 October 2019
### Added
- List all available transactional emails via the CLI (`bin/magento yireo_emailtester2:list`)
- Send mails via the CLI (`bin/magento yireo_emailtester2:send`)

## [1.3.3] - 28 October 2019
### Fixed
- Remove debugging statement

## [1.3.2] - 28 October 2019
### Fixed
- Make sure "store_phone" (and other variables) switch per scope (Website) properly
- Allow to set *Sender* in Store Configuration and/or form

## [1.3.1] - 16 October 2019
### Fixed
- Fix issue with invoice items not showing in table

### Added
- Support for event email_shipment_set_template_vars_before`

## [1.3.0] - 12 October 2019
### Fixed
- Fix issue with return value in M2.3.3
- Remove PHP requirement in composer
- Remember form values in forms

### Added
- Basic MFTF support

## [1.2.2] - September 2019
### Added
- Initial MFTF navigation test

## [1.2.1] - July 2019
### Added
- Add missing `etc/config.xml`
- Move configuration to separate **Yireo** section

## [1.2.0] - April 2019
### Changed
- Converted CHANGELOG to KeepAChangelog format

### Added
- Add Config class to phase out data-helper
- Add suggestion of entering config values
- Add some UI improvements for using autocomplete field
- Add module version to main page 

## [1.1.9] - February 2019
### Changed
- Duplicate environment emulation in alertGrid caused "theme_dir" error

## [1.1.8] - February 2019
### Changed
- Refactoring of Zend Mime to ZF3 causes failure

## [1.1.7] - February 2019
### Changed
- Fix issue with changed event email_order_set_template_vars_before in M2.3

## [1.1.6] - January 2019
### Changed
- Passing array to email_order_set_template_vars_before causes Fatal Error

## [1.1.5] - January 2019
### Changed
- Add complete GitLab pipeline with live Magento checks
- Try to load proper address before formatting it

## [1.1.4] - November 2018
### Changed
- Rename "Email Tester" to "EmailTester" for marketplace

## [1.1.3] - November 2018
### Changed
- Magento 2.3 compatibility

## [1.1.2] - November 2018
### Changed
- Minor CSS improvements

## [1.1.1] - November 2018
### Changed
- Prevent invalid order ID to throw unwanted exception
- Fix untested issue in product exception

## [1.1.0] - September 2018
### Changed
- Add AlertGrid support
- Tighten composer dependencies

## [1.0.5] - September 2018
### Changed
- Fix shipment items in shipment email
- Add addresses to shipment emails
- Testing with Magento 2.2.6 approved without any issues

## [1.0.4] - August 2018
### Changed
- Supply extra variables
- Fix `checkout_payment_failed_template` email
- Add Integration Test to test all preview emails automatically

## [1.0.3] - August 2018
### Changed
- Bugfix for backend template email override not working
- Add proper CHANGELOG.md

## [1.0.2] - August 2018
### Changed
- Add Trait to check for database statistics
- Fix error with adminhtml mails that don't properly set area

## [1.0.1] - July 2018
### Changed
- ExtDN compliance

## [1.0.0] - June 2018
### Changed
- Rewrite manual form into UiComponent form 
- Magento ECG compliance 
- Add unit testing and integration testing 
- Only compatible with Magento 2.2 and up

### Removed
- Compatibility with 2.1 in favor of ViewModels

## [0.2.12] - May 2018
### Changed
- Remove unneeded composer dev requirements

## [0.2.11] - January 2018
### Changed
- Fix notice when no creditmemos are available

## [0.2.10] - December 2017
### Changed
- Change title in backend menu

## [0.2.9] - November 2017
### Changed
- Code beautification

## [0.2.8] - October 2017
### Changed
- Fix fatal error when Store View is incorrectly configured

## [0.2.7] - September 2017
### Changed
- Add basic Jasmine JS testing

## [0.2.6] - August 2017
### Changed
- Code cleanup

## [0.2.5] - August 2017
### Changed
- Fix compilation issues due to 1 PHP notice

## [0.2.4] - August 2017
### Changed
- EQP compliance 
- Raise PHP requirement to PHP7

## [0.2.3] - May 2017
### Changed
- Remove adminhtml XML layout handles which cause wrong CSS

## [0.2.2] - April 2017
### Changed
- Add events `email_order_set_template_vars_before` and `emailtester_variables`

## [0.2.1] - April 2017
### Changed
- Version bump

## [0.2.0] - February 2017
### Changed
- Added Store View filter 
- Move all files from source/ to package root (M2.1 compliance)

## [0.1.5] - November 2016
### Changed
- Fix for loading theme-specific templates

## [0.1.4] - September 2016
### Changed
- Code compliance

## [0.1.3] - August 2016
### Changed
- Load entities only by catching exceptions

## [0.1.2] - August 2016
### Changed
- Fix DI compilation issue with wrong $context usage 
- Implement mail functionality

## [0.1.1] - August 2016
### Changed
- Packaging issue

## [0.1.0] - July 2016
### Changed
- Initial public release 
- Working edition with main variables 
- Some unit tests 
- Pending documentation

