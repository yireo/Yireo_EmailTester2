<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

namespace Yireo\EmailTester2\Controller\Adminhtml\Ajax;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SortOrderBuilderFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class OrderSearch extends AbstractSearch
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * OrderSearch constructor.
     * @param Context $context
     * @param Http $request
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param FilterBuilder $filterBuilder
     * @param JsonFactory $resultJsonFactory
     * @param SortOrderBuilderFactory $sortOrderBuilderFactory
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Context $context,
        Http $request,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        FilterBuilder $filterBuilder,
        JsonFactory $resultJsonFactory,
        SortOrderBuilderFactory $sortOrderBuilderFactory,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct(
            $context,
            $request,
            $searchCriteriaBuilderFactory,
            $filterBuilder,
            $resultJsonFactory,
            $sortOrderBuilderFactory
        );

        $this->orderRepository = $orderRepository;
    }

    /**
     * Index action
     *
     * @return Json
     */
    public function execute(): Json
    {
        $orderData = [];
        $searchFields = ['increment_id', 'customer_email'];
        $searchResults = $this->orderRepository->getList($this->getSearchCriteria($searchFields));

        foreach ($searchResults->getItems() as $order) {
            /** @var $order OrderInterface */
            $orderData[] = [
                'id' => $order->getEntityId(),
                'increment_id' => $order->getIncrementId(),
                'customer_email' => $order->getCustomerEmail(),
                'created_at' => $order->getCreatedAt(),
            ];
        }

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($orderData);
    }
}
