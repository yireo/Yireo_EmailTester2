<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

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
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $productData = array();
        $searchResults = $this->productRepository->getList($this->loadSearchCriteria());

        foreach ($searchResults->getItems() as $product) {
            /** @var $product \Magento\Catalog\Api\Data\ProductInterface */
            $productData[] = array(
                'value' => $product->getId(),
                'label' => $this->getProductLabel($product),
            );
        }

        return $this->resultJsonFactory->create()->setData(
            $productData
        );
    }

    protected function getSearchQuery()
    {
        $search = $this->request->getParam('term');
        return $search;
    }

    protected function getProductLabel(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        return $product->getName() . ' ['.$product->getSku().']';
    }

    protected function loadSearchCriteria()
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