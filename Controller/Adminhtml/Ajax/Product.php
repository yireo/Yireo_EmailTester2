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
class Product extends Action
{
    const ADMIN_RESOURCE = 'Yireo_EmailTester2::index';

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

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
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
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
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute() : \Magento\Framework\Controller\Result\Json
    {
        $productData = [];
        $searchResults = $this->productRepository->getList($this->loadSearchCriteria());

        foreach ($searchResults->getItems() as $product) {
            /** @var $product \Magento\Catalog\Api\Data\ProductInterface */
            $productData[] = [
                'value' => $product->getId(),
                'label' => $this->getProductLabel($product),
            ];
        }

        return $this->resultJsonFactory->create()->setData(
            $productData
        );
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
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     *
     * @return string
     */
    private function getProductLabel(\Magento\Catalog\Api\Data\ProductInterface $product) : string
    {
        return $product->getName() . ' ['.$product->getSku().']';
    }

    /**
     * @return \Magento\Framework\Api\SearchCriteria
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
