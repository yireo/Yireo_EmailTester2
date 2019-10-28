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
     */
    public function isEnabled(): bool
    {
        return (bool)$this->getConfigValue('enabled', false);
    }

    /**
     * Check whether the module is in debugging mode
     *
     * @return bool
     */
    public function isDebug(): bool
    {
        return (bool)$this->getConfigValue('debug');
    }

    /**
     * @return string
     */
    public function getDefaultSender(): string
    {
        return (string)$this->getConfigValue('default_sender');
    }

    /**
     * @return string
     */
    public function getDefaultEmail(): string
    {
        return (string)$this->getConfigValue('default_email');
    }

    /**
     * @return string
     */
    public function getDefaultTransactional(): string
    {
        return (string)$this->getConfigValue('default_transactional');
    }

    /**
     * @return int
     */
    public function getDefaultCustomer(): int
    {
        return (int)$this->getConfigValue('default_customer');
    }

    /**
     * @return int
     */
    public function getDefaultProduct(): int
    {
        return (int)$this->getConfigValue('default_product');
    }

    /**
     * @return int
     */
    public function getDefaultOrder(): int
    {
        return (int)$this->getConfigValue('default_order');
    }

    /**
     * @return int
     */
    public function getLimitCustomer(): int
    {
        return (int)$this->getConfigValue('limit_customer');
    }

    /**
     * @return int
     */
    public function getLimitProduct(): int
    {
        return (int)$this->getConfigValue('limit_product');
    }

    /**
     * @return int
     */
    public function getLimitOrder(): int
    {
        return (int)$this->getConfigValue('limit_order');
    }

    /**
     * @return string
     */
    public function getCustomOrder(): string
    {
        return (string)$this->getConfigValue('custom_order');
    }

    /**
     * @return string
     */
    public function getCustomCustomer(): string
    {
        return (string)$this->getConfigValue('custom_customer');
    }

    /**
     * @return string
     */
    public function getCustomProduct(): string
    {
        return (string)$this->getConfigValue('custom_product');
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
