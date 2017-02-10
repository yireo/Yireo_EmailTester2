<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

namespace Yireo\EmailTester2\Model\Mailer\Variable;

/**
 * Class OtherVars
 *
 * @package Yireo\EmailTester2\Model\Mailer\Variable
 */
class OtherVars
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * OtherVars constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return string
     */
    public function getVariables()
    {
        $variables = array(
            'store_phone' => $this->getStorePhone(),
            'store_hours' => $this->getStoreHours(),
        );

        return $variables;
    }

    protected function getStoreHours()
    {
        return $this->scopeConfig->getValue('general/store_information/hours');
    }

    protected function getStorePhone()
    {
        return $this->scopeConfig->getValue('general/store_information/phone');
    }
}