<?php
/**
 * Yireo EmailTester for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

namespace Yireo\EmailTester2\Model\Mailer\Variable;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Yireo\EmailTester2\Model\Mailer\VariablesInterface;

class OtherVars implements VariablesInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var int
     */
    private $storeId = 0;

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
            'checkoutType' => 'Dummy Checkout',
        ];

        foreach ($this->getScopeConfigVariables() as $name => $path) {
            $variables[$name] = (string)$this->scopeConfig->getValue($path, 'store', $this->storeId);
        }

        return $variables;
    }

    /**
     * This method is made public to serve as a way to extend things using DI plugins
     *
     * @return array
     */
    public function getScopeConfigVariables(): array
    {
        return [
            'store_hours' => 'general/store_information/hours',
            'store_phone' => 'general/store_information/phone',
        ];
    }

    /**
     * This method is called from the VariableBuilder to insert the current Store ID
     *
     * @param $storeId int
     */
    public function setStoreId(int $storeId)
    {
        $this->storeId = $storeId;
    }
}
