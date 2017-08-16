<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types = 1);

namespace Yireo\EmailTester2\Helper;

/**
 * Class \Yireo\EmailTester2\Helper\Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Check whether the module is enabled
     *
     * @return bool
     */
    public function isEnabled() : bool
    {
        return (bool)$this->getConfigValue('enabled', false);
    }

    /**
     * Check whether the module is in debugging mode
     *
     * @return bool
     */
    public function isDebug() : bool
    {
        return (bool)$this->getConfigValue('debug');
    }

    /**
     * Debugging method
     *
     * @param string $string
     * @param string $variable
     *
     * @return bool
     */
    public function debug(string $string, $variable = '') : bool
    {
        if ($this->isDebug() == false) {
            return false;
        }

        if (!empty($variable)) {
            $string .= ': ' . var_export($variable, true);
        }

        $this->_logger->info('Yireo_EmailTester2: ' . $string);

        return true;
    }

    /**
     * Return a configuration value
     *
     * @param string $key
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public function getConfigValue(string $key = '', $defaultValue = null)
    {
        $value = $this->scopeConfig->getValue(
            'emailtester2/settings/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (empty($value)) {
            $value = $defaultValue;
        }

        return $value;
    }
}
