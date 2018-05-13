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

use Magento\Backend\Model\Session;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class \Yireo\EmailTester2\Helper\Form
 */
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
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Session $backendSession
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Session $backendSession
    ) {
        $this->storeManager = $storeManager;
        $this->backendSession = $backendSession;

        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getFormData() : array
    {
        $data = [
            'store_id' => $this->getDefaultStoreId(),
            'email' => $this->getConfigValue('default_email'),
            'template' => $this->getConfigValue('default_transactional'),
            'customer_id' => $this->getConfigValue('default_customer'),
            'order_id' => $this->getConfigValue('default_order'),
            'product_id' => $this->getConfigValue('default_product'),
        ];

        $data['product_search'] = $data['product_id'];
        $data['customer_search'] = $data['customer_id'];
        $data['order_search'] = $data['order_id'];

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
    private function getDataFromSession() : array
    {
        return (array) $this->backendSession->getData('emailtester_values');
    }

    /**
     * @return int
     */
    private function getDefaultStoreId() : int
    {
        return (int) $this->storeManager->getDefaultStoreView()->getId();
    }
}
