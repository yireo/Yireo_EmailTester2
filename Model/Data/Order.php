<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License
 */

declare(strict_types = 1);

namespace Yireo\EmailTester2\Model\Data;

use Magento\Framework\Api\FilterBuilder;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;

/**
 * Class Yireo\EmailTester2\Model\Data\Order
 */
class Order
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $session;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Magento\Framework\Api\Search\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var \Yireo\EmailTester2\Helper\Output
     */
    private $outputHelper;

    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    private $config;

    /**
     * Order constructor.
     *
     * @param \Magento\Backend\Model\Auth\Session\Proxy $session
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param \Yireo\EmailTester2\Helper\Output $outputHelper
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     * @param \Magento\Backend\App\ConfigInterface $config
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session\Proxy $session,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        FilterBuilder $filterBuilder,
        \Yireo\EmailTester2\Helper\Output $outputHelper,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magento\Backend\App\ConfigInterface $config
    ) {
        $this->session = $session;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->request = $request;
        $this->filterBuilder = $filterBuilder;
        $this->outputHelper = $outputHelper;
        $this->config = $config;
    }

    /**
     * @param int $orderId
     *
     * @return false|\Magento\Sales\Api\Data\OrderInterface
     */
    public function getOrder(int $orderId)
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
    public function getOrderId(): int
    {
        $orderId = $this->request->getParam('order_id');
        if (!empty($orderId)) {
            return (int)$orderId;
        }

        $userData = $this->session->getData();
        $orderId = (isset($userData['emailtester.order_id'])) ? (int)$userData['emailtester.order_id'] : null;

        if (!empty($orderId)) {
            return (int)$orderId;
        }

        $orderId = (int)$this->config->getValue('emailtester/settings/default_order');
        return $orderId;
    }

    /**
     * Get an array of order select options
     *
     * @return array
     */
    public function getOrderOptions(): array
    {
        $options = [];
        $options[] = ['value' => '', 'label' => '', 'current' => ''];
        $currentValue = $this->getOrderId();
        $orders = $this->getOrderCollection();

        foreach ($orders as $order) {
            /** @var \Magento\Sales\Model\Order $order */
            $value = $order->getId();
            $label = '[' . $order->getId() . '] ' . $this->outputHelper->getOrderOutput($order);
            $current = ($order->getId() == $currentValue) ? true : false;
            $options[] = ['value' => $value, 'label' => $label, 'current' => $current];
        }

        return $options;
    }

    /**
     * Get current order result
     *
     * @return string
     */
    public function getOrderSearch(): string
    {
        $orderId = $this->getOrderId();

        if ($this->outputHelper->isValidId($orderId)) {
            try {
                /** @var \Magento\Sales\Model\Order $order */
                $order = $this->orderRepository->get($orderId);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                return '';
            }

            return (string)$this->outputHelper->getOrderOutput($order);
        }

        return '';
    }

    /**
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface
     */
    private function getOrderCollection()
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilder;
        $searchCriteriaBuilder->addSortOrder('entity_id', AbstractCollection::SORT_ORDER_DESC);

        $customOptions = $this->outputHelper->getCustomOptions('order');
        if (!empty($customOptions)) {
            $filter = $this->filterBuilder
                ->setField('entity_id')
                ->setConditionType('in')
                ->setValue($customOptions)
                ->create();
            $searchCriteriaBuilder->addFilter($filter);
        }

        $storeIds = $this->outputHelper->getStoreIds();
        if (!empty($storeIds)) {
            $filter = $this->filterBuilder
                ->setField('store_id')
                ->setConditionType('in')
                ->setValue($storeIds)
                ->create();
            $searchCriteriaBuilder->addFilter($filter);
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
     * @return null|string
     */
    private function getOrderCollectionLimit()
    {
        return $this->getStoreConfig('emailtester/settings/limit_order');
    }
}
