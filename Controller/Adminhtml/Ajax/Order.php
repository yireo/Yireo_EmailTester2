<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

namespace Yireo\EmailTester2\Controller\Adminhtml\Ajax;

use \Magento\Backend\App\Action;

/**
 * Class Index
 *
 * @package Yireo\EmailTester2\Controller\Ajax
 */
class Order extends Action
{
    /**
     * ACL resource
     */
    const ADMIN_RESOURCE = 'Yireo_EmailTester2::index';

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\App\Request\Http\Proxy $request
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\App\Request\Http\Proxy $request,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);

        $this->orderRepository = $orderRepository;
        $this->request = $request;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute() : \Magento\Framework\Controller\Result\Json
    {
        $orderData = [];
        $searchResults = $this->orderRepository->getList($this->loadSearchCriteria());

        foreach ($searchResults->getItems() as $order) {
            /** @var $order \Magento\Sales\Api\Data\OrderInterface */
            $orderData[] = [
                'value' => $order->getEntityId(),
                'label' => $this->getOrderLabel($order),
            ];
        }

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($orderData);
    }

    /**
     * @return string
     */
    private function getSearchQuery() : string
    {
        $search = $this->request->getParam('term');
        return $search;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     *
     * @return string
     */
    private function getOrderLabel(\Magento\Sales\Api\Data\OrderInterface $order) : string
    {
        return $order->getIncrementId() . ' [' . $order->getCustomerEmail() . ']';
    }

    /**
     * @return \Magento\Framework\Api\SearchCriteria
     */
    private function loadSearchCriteria() : \Magento\Framework\Api\SearchCriteria
    {
        $this->searchCriteriaBuilder->setCurrentPage(0);
        $this->searchCriteriaBuilder->setPageSize(10);
        $search = $this->getSearchQuery();

        if (!empty($search)) {
            $searchFields = ['customer_email'];
            $filters = [];
            foreach ($searchFields as $field) {
                $filters[] = $this->filterBuilder
                    ->setField($field)
                    ->setConditionType('like')
                    ->setValue('%' . $this->getSearchQuery() . '%')
                    ->create();
            }

            $this->searchCriteriaBuilder->addFilters($filters);
        }

        return $this->searchCriteriaBuilder->create();
    }
}
