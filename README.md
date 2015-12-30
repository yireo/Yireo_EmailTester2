# Magento 2 module for EmailTester2
================================
Homepage: http://www.yireo.com/software/magento-extensions/emailtester

Requirements:
* Magento 2.0.0 Stable or higher

Steps to get it working:
* Upload the files in the source/ folder to your site
* Flush the Magento cache
* Configure settings under System > Configuration > Advanced > EmailTester
* Done

## Unit testing
This extension ships with PHPUnit tests. The generic PHPUnit configuration in Magento 2 will pick up on these tests. To only
test Yireo extensions, copy the file `phpunit.xml.yireo` to your Magento folder `dev/tests/unit`. Next, from within that folder run PHPUnit. For instance:

    phpunit -c phpunit.xml.yireo
