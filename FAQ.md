# PHP requirement not met
Our extension is developed to incorporate newer PHP features like type hinting, spaceship operators and terniary operators. For this, we
recommend that PHP is running at least version PHP 7.1, but we recommend PHP 7.2. The actual requirements are always documented in our
`composer.json` file.

It might be that the very fact that our `composer.json` file is mentioning a certain PHP requirement is conflicting with another Magento
package that has defined a wrong dependency. If you are sure our extension meets the PHP requirements, you can skip this check using the
`--ignore-platform-reqs` flag of composer:

    composer require yireo/magento2-emailtester2 --ignore-platform-reqs
