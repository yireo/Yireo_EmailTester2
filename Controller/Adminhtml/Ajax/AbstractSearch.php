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
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\SortOrderBuilderFactory;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Controller\Result\JsonFactory;

abstract class AbstractSearch extends Action
{
    const ADMIN_RESOURCE = 'Yireo_EmailTester2::index';

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var HttpRequest
     */
    protected $request;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilderFactory;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;
    /**
     * @var SortOrderBuilderFactory
     */
    private $sortOrderBuilderFactory;

    /**
     * @param Context $context
     * @param HttpRequest $request
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param FilterBuilder $filterBuilder
     * @param JsonFactory $resultJsonFactory
     * @param SortOrderBuilderFactory $sortOrderBuilderFactory
     */
    public function __construct(
        Context $context,
        HttpRequest $request,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        FilterBuilder $filterBuilder,
        JsonFactory $resultJsonFactory,
        SortOrderBuilderFactory $sortOrderBuilderFactory
    ) {
        parent::__construct($context);

        $this->request = $request;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->filterBuilder = $filterBuilder;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->sortOrderBuilderFactory = $sortOrderBuilderFactory;
    }

    /**
     * @return string
     */
    protected function getSearchQuery(): string
    {
        return (string)$this->request->getParam('search');
    }

    /**
     * @return string
     */
    protected function getSortField(): string
    {
        return (string)$this->request->getParam('sortField');
    }

    /**
     * @return string
     */
    protected function getSortDirection(): string
    {
        return (string)$this->request->getParam('sortDirection');
    }

    /**
     * @param array $searchFields
     * @return SearchCriteria
     */
    protected function getSearchCriteria(array $searchFields): SearchCriteria
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->setCurrentPage(0);
        $searchCriteriaBuilder->setPageSize(10);

        $filters = $this->getSearchFilters($searchFields);
        $searchCriteriaBuilder->addFilters($filters);

        $sortOrder = $this->getSortOrder();
        $searchCriteriaBuilder->setSortOrders([$sortOrder]);

        return $searchCriteriaBuilder->create();
    }

    /**
     * @param array $searchFields
     * @return array
     */
    private function getSearchFilters(array $searchFields = []): array
    {
        $filters = [];

        $searchQuery = $this->getSearchQuery();
        $searchWords = explode(' ', $searchQuery);

        foreach ($searchFields as $field) {
            foreach ($searchWords as $searchWord) {
                $filters[] = $this->filterBuilder
                    ->setField($field)
                    ->setConditionType('like')
                    ->setValue($searchWord . '%')
                    ->create();
            }
        }

        return $filters;
    }

    /**
     * @return SortOrder
     */
    private function getSortOrder(): SortOrder
    {
        $sortField = $this->getSortField();
        if ($sortField === 'id') {
            $sortField = 'entity_id';
        }

        if (preg_match('/(.*)_label$/', $sortField, $match)) {
            $sortField = $match[0] . '_id';
        }

        /** @var SortOrderBuilder $sortOrderBuilder */
        $sortOrderBuilder = $this->sortOrderBuilderFactory->create();

        if ($sortField) {
            $sortOrderBuilder
                ->setField($sortField)
                ->setDirection($this->getSortDirection());
        }

        $sortOrder = $sortOrderBuilder->create();
        return $sortOrder;
    }
}
