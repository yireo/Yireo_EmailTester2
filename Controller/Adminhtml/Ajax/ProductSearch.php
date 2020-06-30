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
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SortOrderBuilderFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

class ProductSearch extends AbstractSearch
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * ProductSearch constructor.
     * @param Context $context
     * @param Http $request
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param FilterBuilder $filterBuilder
     * @param JsonFactory $resultJsonFactory
     * @param SortOrderBuilderFactory $sortOrderBuilderFactory
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Context $context,
        Http $request,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        FilterBuilder $filterBuilder,
        JsonFactory $resultJsonFactory,
        SortOrderBuilderFactory $sortOrderBuilderFactory,
        ProductRepositoryInterface $productRepository
    ) {
        parent::__construct(
            $context,
            $request,
            $searchCriteriaBuilderFactory,
            $filterBuilder,
            $resultJsonFactory,
            $sortOrderBuilderFactory
        );

        $this->productRepository = $productRepository;
    }

    /**
     * Index action
     *
     * @return Json
     */
    public function execute(): Json
    {
        $productData = [];
        $searchFields = ['name', 'sku'];
        $searchResults = $this->productRepository->getList($this->getSearchCriteria($searchFields));

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
}
