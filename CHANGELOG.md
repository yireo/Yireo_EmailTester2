# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.4.2] - 31 October 2019
### Fixed
- Fix CLI when `store_id` is set to 0
- Add checks to make sure customers, products and orders are created before using this module
- Fixed issue with empty theme ID in configuration throwing error
- Fix issue with `setEncoding()` missing in PHP 7.1

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

