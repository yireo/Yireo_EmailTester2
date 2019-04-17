<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 *
 * @package Yireo\EmailTester2\Config
 */
class Config
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check whether the module is enabled
     *
     * @return bool
     * @deprecated Use \Yireo\EmailTester2\Config\Config instead
     */
    public function isEnabled(): bool
    {
        return (bool)$this->getConfigValue('enabled', false);
    }

    /**
     * Check whether the module is in debugging mode
     *
     * @return bool
     * @deprecated Use \Yireo\EmailTester2\Config\Config instead
     */
    public function isDebug(): bool
    {
        return (bool)$this->getConfigValue('debug');
    }

    /**
     * @return string
     */
    public function getDefaultEmail(): string
    {
        return (string)$this->getConfigValue('default_email');
    }

    /**
     * Return a configuration value
     *
     * @param string $key
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    private function getConfigValue(string $key = '', $defaultValue = null)
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
