# PHP requirement not met
Our extension is developed to incorporate newer PHP features like type hinting, spaceship operators and terniary operators. For this, we
recommend that PHP is running at least version PHP 7.1, but we recommend PHP 7.2. The actual requirements are always documented in our
`composer.json` file.

It might be that the very fact that our `composer.json` file is mentioning a certain PHP requirement is conflicting with another Magento
package that has defined a wrong dependency. If you are sure our extension meets the PHP requirements, you can skip this check using the
`--ignore-platform-reqs` flag of composer:

    composer require yireo/magento2-emailtester2 --ignore-platform-reqs

# Products do not show in transactional email
If you encounter an issue where an email override in your Magento Admin Panel doesn't parse the products output, while the original email does, note that this is not an issue with our extension but with newer versions of Magento. Specifically, since Magento 2.3.4, the following instruction is no longer allowed (specifically, the usage of an `$order` object in email templates stored in the database):

    {{layout handle="sales_email_order_items" order=$order}}

Either you can migrate your email templates towards newer code that works (where EmailTester will simply support those new variables), or mark your template as legacy. Open up the database table `email_template`, locate the `template_id` of the email template in question and set the flag `is_legacy` to `1`.
