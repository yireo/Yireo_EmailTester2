<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types = 1);

namespace Yireo\EmailTester2\Model\Mailer\Variable;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Yireo\EmailTester2\Model\Mailer\VariablesInterface;

/**
 * Class OtherVars
 *
 * @package Yireo\EmailTester2\Model\Mailer\Variable
 */
class OtherVars implements VariablesInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * OtherVars constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return array
     */
    public function getVariables(): array
    {
        $variables = [
            'store_phone' => $this->getStorePhone(),
            'store_hours' => $this->getStoreHours(),
            'checkoutType' => 'Dummy Checkout',
        ];

        return $variables;
    }

    /**
     * @return string
     */
    private function getStoreHours(): string
    {
        return (string)$this->scopeConfig->getValue('general/store_information/hours');
    }

    /**
     * @return string
     */
    private function getStorePhone(): string
    {
        return (string)$this->scopeConfig->getValue('general/store_information/phone');
    }
}
