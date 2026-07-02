<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

namespace Yireo\EmailTester2\Helper;

use Magento\Backend\Model\Session;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Yireo\EmailTester2\Config\Config;

class Form extends Data
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Session
     */
    private $backendSession;
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Session $backendSession
     * @param Config $config
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Session $backendSession,
        Config $config
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->backendSession = $backendSession;
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getFormData(): array
    {
        $data = [
            'store_id' => $this->getDefaultStoreId(),
            'mail_from' => $this->config->getDefaultSender(),
            'mail_to' => $this->config->getDefaultEmail(),
            'template' => $this->config->getDefaultTransactional(),
            'customer_id' => $this->config->getDefaultCustomer(),
            'order_id' => $this->config->getDefaultOrder(),
            'product_id' => $this->config->getDefaultProduct(),
        ];

        $sessionData = $this->getDataFromSession();

        if (!empty($sessionData)) {
            foreach ($sessionData as $sessionName => $sessionValue) {
                if (isset($data[$sessionName]) && !empty($sessionValue)) {
                    $data[$sessionName] = $sessionValue;
                }
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    private function getDataFromSession(): array
    {
        return (array)$this->backendSession->getData('emailtester_values');
    }

    /**
     * @return int
     */
    private function getDefaultStoreId(): int
    {
        return (int)$this->storeManager->getDefaultStoreView()->getId();
    }
}
