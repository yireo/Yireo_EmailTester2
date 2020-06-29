<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types = 1);

namespace Yireo\EmailTester2\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * Check whether the module is enabled
     *
     * @return bool
     * @deprecated Use \Yireo\EmailTester2\Config\Config instead
     */
    public function isEnabled() : bool
    {
        return (bool)$this->getConfigValue('enabled', false);
    }

    /**
     * Check whether the module is in debugging mode
     *
     * @return bool
     * @deprecated Use \Yireo\EmailTester2\Config\Config instead
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
     * @deprecated Use \Yireo\EmailTester2\Config\Config instead
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
     * @deprecated Use \Yireo\EmailTester2\Config\Config instead
     */
    public function getConfigValue(string $key = '', $defaultValue = null)
    {
        $value = $this->scopeConfig->getValue(
            'emailtester2/settings/' . $key,
            ScopeInterface::SCOPE_STORE
        );

        if (empty($value)) {
            $value = $defaultValue;
        }

        return $value;
    }
}
