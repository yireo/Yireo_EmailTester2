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

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class OrderSearch
 */
class OrderSearch extends Action
{
    /**
     * ACL resource
     */
    const ADMIN_RESOURCE = 'Yireo_EmailTester2::index';

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var Http
     */
    private $request;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @param Context $context
     * @param OrderRepositoryInterface $orderRepository
     * @param Http $request
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        Http $request,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        JsonFactory $resultJsonFactory
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
     * @return Json
     */
    public function execute() : Json
    {
        $orderData = [];
        $searchResults = $this->orderRepository->getList($this->loadSearchCriteria());

        foreach ($searchResults->getItems() as $order) {
            /** @var $order OrderInterface */
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
        $search = (string) $this->request->getParam('term');
        return $search;
    }

    /**
     * @param OrderInterface $order
     *
     * @return string
     */
    private function getOrderLabel(OrderInterface $order) : string
    {
        return $order->getIncrementId() . ' [' . $order->getCustomerEmail() . ']';
    }

    /**
     * @return SearchCriteria
     */
    private function loadSearchCriteria() : SearchCriteria
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
