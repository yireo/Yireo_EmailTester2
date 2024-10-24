<?php
/**
 * Yireo EmailTester for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License
 */

declare(strict_types = 1);

namespace Yireo\EmailTester2\Model\Data;

use Magento\Backend\App\ConfigInterface;
use Magento\Backend\Model\Auth\Session;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order as OrderModel;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;

use Yireo\EmailTester2\Helper\Output;

class Order
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var Output
     */
    private $outputHelper;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * Order constructor.
     *
     * @param Session $session
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param Output $outputHelper
     * @param ConfigInterface $config
     */
    public function __construct(
        Session $session,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        Output $outputHelper,
        ConfigInterface $config
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
     * @return false|OrderInterface
     */
    public function getOrder(int $orderId)
    {
        return $this->orderRepository->get($orderId);
    }

    /**
     * Get the current order ID
     *
     * @return int
     */
    public function getOrderId(): int
    {
        $orderId = (int) $this->request->getParam('order_id');
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
     * @return string
     */
    public function getOrderIncrementId(): string
    {
        return (string)$this->getOrder($this->getOrderId())->getIncrementId();
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
            /** @var OrderModel $order */
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
            /** @var OrderModel $order */
            $order = $this->orderRepository->get($orderId);

            return (string)$this->outputHelper->getOrderOutput($order);
        }

        return '';
    }

    /**
     * @return OrderSearchResultInterface
     */
    private function getOrderCollection() : OrderSearchResultInterface
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilder;
        $searchCriteriaBuilder->addSortOrder('entity_id', AbstractCollection::SORT_ORDER_DESC);

        $customOptions = $this->outputHelper->getCustomOptions('order');
        if (!empty($customOptions)) {
            $filter = $this->filterBuilder
                ->setField('entity_id')
                ->setConditionType('in')
                ->setValue(implode(',', $customOptions))
                ->create();
            $searchCriteriaBuilder->addFilter($filter);
        }

        $storeIds = $this->outputHelper->getStoreIds();
        if (!empty($storeIds)) {
            $filter = $this->filterBuilder
                ->setField('store_id')
                ->setConditionType('in')
                ->setValue(implode(',', $storeIds))
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
     * @return int
     */
    private function getOrderCollectionLimit() : int
    {
        return (int) $this->config->getValue('emailtester/settings/limit_order');
    }
}
