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
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

class ProductSearch extends Action
{
    const ADMIN_RESOURCE = 'Yireo_EmailTester2::index';

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

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
     * @param ProductRepositoryInterface $productRepository
     * @param Http $request
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        Http $request,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->productRepository = $productRepository;
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
    public function execute(): Json
    {
        $productData = [];
        $searchResults = $this->productRepository->getList($this->loadSearchCriteria());

        foreach ($searchResults->getItems() as $product) {
            /** @var $product ProductInterface */
            $productData[] = [
                'id' => $product->getId(),
                'sku' => $product->getSku(),
                'name' => $product->getName()
            ];
        }

        return $this->resultJsonFactory->create()->setData(
            $productData
        );
    }

    /**
     * @return string
     */
    private function getSearchQuery(): string
    {
        return (string)$this->request->getParam('search');
    }

    /**
     * @return SearchCriteria
     */
    private function loadSearchCriteria()
    {
        $this->searchCriteriaBuilder->setCurrentPage(0);
        $this->searchCriteriaBuilder->setPageSize(10);

        $searchFields = ['name', 'sku'];
        $filters = [];
        foreach ($searchFields as $field) {
            $filters[] = $this->filterBuilder
                ->setField($field)
                ->setConditionType('like')
                ->setValue($this->getSearchQuery() . '%')
                ->create();
        }
        $this->searchCriteriaBuilder->addFilters($filters);

        return $this->searchCriteriaBuilder->create();
    }
}
