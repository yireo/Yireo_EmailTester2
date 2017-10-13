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
 * Class \Yireo\EmailTester2\Helper\Form
 */
class Form extends Data
{
    /**
     * @var \Yireo\EmailTester2\Model\Backend\Source\Email
     */
    private $emailSource;

    /**
     * @var \Magento\Store\Ui\Component\Listing\Column\Store\Options
     */
    private $storeSource;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Backend\Model\Session
     */
    private $backendSession;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Ui\Component\Listing\Column\Store\Options $storeSource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Backend\Model\Session $session
     * @param \Yireo\EmailTester2\Model\Backend\Source\Email $emailSource
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Ui\Component\Listing\Column\Store\Options $storeSource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\Model\Session $backendSession,
        \Yireo\EmailTester2\Model\Backend\Source\Email $emailSource
    ) {
        $this->emailSource = $emailSource;
        $this->storeSource = $storeSource;
        $this->storeManager = $storeManager;
        $this->backendSession = $backendSession;

        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getTemplateOptions() : array
    {
        return $this->emailSource->toOptionArray();
    }

    /**
     * @return array
     */
    public function getStoreOptions() : array
    {
        return $this->storeSource->toOptionArray();
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
