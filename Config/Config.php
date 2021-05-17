<?php

declare(strict_types=1);

namespace Yireo\EmailTester2\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

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
        $defaultEmail = (string)$this->getConfigValue('default_sender');
        if ($defaultEmail) {
            return $defaultEmail;
        }

        return (string) $this->getConfigValue('trans_email/ident_general/email');
    }

    /**
     * @return string
     */
    public function getDefaultEmail(): string
    {
        $defaultEmail = (string)$this->getConfigValue('default_email');
        if ($defaultEmail) {
            return $defaultEmail;
        }

        return (string) $this->getConfigValue('trans_email/ident_general/email');
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
     * @param string $path
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    private function getConfigValue(string $path = '', $defaultValue = null)
    {
        if (!strstr($path, '/')) {
            $path = 'emailtester2/settings/' . $path;
        }

        $value = $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
        if (empty($value)) {
            $value = $defaultValue;
        }

        return $value;
    }
}
