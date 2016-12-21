<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2016 Yireo (https://www.yireo.com/)
 * @license     Open Source License
 */

namespace Yireo\EmailTester2\Model\Data;

use Magento\Framework\Api\Filter;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;

/**
 * Class Yireo\EmailTester2\Model\Data\Order
 */
class Order extends Generic
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Framework\Api\Search\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Order constructor.
     *
     * @param \Magento\Backend\Model\Auth\Session $session
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Yireo\EmailTester2\Helper\Output $outputHelper
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     * @param \Magento\Backend\App\ConfigInterface $config
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $session,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Yireo\EmailTester2\Helper\Output $outputHelper,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magento\Backend\App\ConfigInterface $config
    )
    {
        $this->session = $session;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->request = $request;

        parent::__construct($outputHelper, $session, $storeRepository, $request, $config);
    }

    /**
     * @param int $orderId
     *
     * @return false|\Magento\Sales\Model\Order
     */
    public function getOrder($orderId)
    {
        try {
            return $this->orderRepository->get($orderId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            return false;
        }
    }

    /**
     * Get the current order ID
     *
     * @return int
     */
    public function getOrderId()
    {
        $orderId = $this->request->getParam('order_id');
        if (!empty($orderId)) {
            return $orderId;
        }

        $userData = $this->session->getData();
        $orderId = (isset($userData['emailtester.order_id'])) ? (int)$userData['emailtester.order_id'] : null;

        if (!empty($orderId)) {
            return $orderId;
        }

        $orderId = $this->getStoreConfig('emailtester/settings/default_order');
        return $orderId;
    }

    /**
     * Get an array of order select options
     *
     * @return array
     */
    public function getOrderOptions()
    {
        $options = array();
        $options[] = array('value' => '', 'label' => '', 'current' => null);
        $currentValue = $this->getOrderId();
        $orders = $this->getOrderCollection();

        foreach ($orders as $order) {
            /** @var \Magento\Sales\Model\Order $order */
            $value = $order->getId();
            $label = '[' . $order->getId() . '] ' . $this->outputHelper->getOrderOutput($order);
            $current = ($order->getId() == $currentValue) ? true : false;
            $options[] = array('value' => $value, 'label' => $label, 'current' => $current);
        }

        return $options;
    }

    /**
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface
     */
    protected function getOrderCollection()
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilder;
        $searchCriteriaBuilder->addSortOrder('entity_id', AbstractCollection::SORT_ORDER_DESC);

        $customOptions = $this->getCustomOptions('order');
        if (!empty($customOptions)) {
            $searchCriteriaBuilder->addFilter(
                new Filter([
                    Filter::KEY_FIELD => 'entity_id',
                    Filter::KEY_CONDITION_TYPE => 'in',
                    Filter::KEY_VALUE => $customOptions
                ]));
        }

        $storeIds = $this->getStoreIds();
        if (!empty($storeIds)) {
            $searchCriteriaBuilder->addFilter(
                new Filter([
                    Filter::KEY_FIELD => 'store_id',
                    Filter::KEY_CONDITION_TYPE => 'in',
                    Filter::KEY_VALUE => $storeIds
                ])
            );
        }

        $searchCriteria = $searchCriteriaBuilder->create();

        $limit = $this->getOrderCollectionLimit();
        if ($limit > 0) {
            $searchCriteria->setPageSize($limit);
            $searchCriteria->setCurrentPage(0);
        }

        $searchCriteria->getSortOrders();

        $orders = $this->orderRepository->getList($searchCriteria);

        return $orders;
    }

    /**
     * @return array
     */
    protected function getStoreIds()
    {
        $storeIds = array();

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
     * Get current order result
     *
     * @return string
     */
    public function getOrderSearch()
    {
        $orderId = $this->getOrderId();

        if ($this->isValidId($orderId)) {
            try {
                /** @var \Magento\Sales\Model\Order $order */
                $order = $this->orderRepository->get($orderId);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                return '';
            }

            return $this->outputHelper->getOrderOutput($order);
        }

        return '';
    }

    /**
     * @return null|string
     */
    protected function getOrderCollectionLimit()
    {
        return $this->getStoreConfig('emailtester/settings/limit_order');
    }
}