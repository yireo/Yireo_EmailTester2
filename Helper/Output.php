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

namespace Yireo\EmailTester2\Helper;

use Magento\Backend\Model\Session;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreRepository;

/**
 * Class \Yireo\EmailTester2\Helper\Output
 */
class Output extends Data
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    private $session;

    /**
     * @var StoreRepository
     */
    private $storeRepository;

    /**
     * Output constructor.
     * @param Session $session
     * @param StoreRepository $storeRepository
     * @param Context $context
     */
    public function __construct(
        Session $session,
        StoreRepository $storeRepository,
        Context $context
    )
    {
        $this->session = $session;
        $this->storeRepository = $storeRepository;

        parent::__construct($context);
    }

    /**
     * Output a string describing a customer record
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     *
     * @return string
     */
    public function getCustomerOutput(\Magento\Customer\Api\Data\CustomerInterface $customer) : string
    {
        return $customer->getEmail() . ' [' . $customer->getId() . ']';
    }

    /**
     * Output a string describing a product record
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     *
     * @return string
     */
    public function getProductOutput(\Magento\Catalog\Api\Data\ProductInterface $product) : string
    {
        return $product->getName() . ' [' . $product->getSku() . ']';
    }

    /**
     * Output a string describing a customer record
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     *
     * @return string
     */
    public function getOrderOutput(\Magento\Sales\Api\Data\OrderInterface $order) : string
    {
        return '#' . $order->getIncrementId() . ' [' . $order->getCustomerEmail() . ' / ' . $order->getState() . ']';
    }

    /**
     * @param $id
     *
     * @return bool
     */
    public function isValidId($id): bool
    {
        if (empty($id)) {
            return false;
        }

        if (!is_numeric($id)) {
            return false;
        }

        if ($id < 1) {
            return false;
        }

        return true;
    }

    /**
     * Get an array of all options defined in the extension settings
     *
     * @param string $type
     *
     * @return array
     */
    public function getCustomOptions(string $type = ''): array
    {
        $customOptions = $this->getConfigValue('custom_' . $type);
        if (empty($customOptions)) {
            return [];
        }

        $options = [];
        $customOptions = explode(',', $customOptions);
        foreach ($customOptions as $customOption) {
            $customOption = (int)trim($customOption);
            if ($customOption > 0) {
                $options[] = $customOption;
            }
        }

        return $options;
    }

    /**
     * Get the current store
     *
     * @return int
     */
    public function getStoreId(): int
    {
        $storeId = (int)$this->_request->getParam('store');
        if (!$storeId > 0) {
            $storeId = (int)$this->session->getData('emailtester.store');
        }

        return $storeId;
    }

    /**
     * @return array
     */
    public function getStoreIds(): array
    {
        $storeIds = [];

        $storeId = $this->getStoreId();
        if (empty($storeId)) {
            return $storeIds;
        }

        try {
            /** @var $store \Magento\Store\Api\Data\StoreInterface */
            $store = $this->storeRepository->getById($storeId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            return $storeIds;
        }

        $website = $store->getWebsite();

        foreach ($website->getStores() as $store) {
            /** @var $store \Magento\Store\Api\Data\StoreInterface */
            $storeIds[] = $store->getId();
        }

        return $storeIds;
    }

    /**
     * @return int
     */
    public function getWebsiteId(): int
    {
        $storeId = $this->getStoreId();
        if ($storeId > 0) {
            try {
                $store = $this->storeRepository->getById($storeId);
                return (int)$store->getWebsiteId();
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                return 0;
            }
        }

        return 0;
    }
}
