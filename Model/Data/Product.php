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
 * Class Product
 */
class Product
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $session;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Framework\Api\Search\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * Product constructor.
     * @param \Magento\Backend\Model\Auth\Session $session
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param \Yireo\EmailTester2\Helper\Output $outputHelper
     * @param \Magento\Backend\App\ConfigInterface $config
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $session,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        FilterBuilder $filterBuilder,
        \Yireo\EmailTester2\Helper\Output $outputHelper,
        \Magento\Backend\App\ConfigInterface $config
    ) {
        $this->session = $session;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->request = $request;
        $this->filterBuilder = $filterBuilder;
        $this->outputHelper = $outputHelper;
        $this->config = $config;
    }

    /**
     * @param int $productId
     *
     * @return false|\Magento\Catalog\Api\Data\ProductInterface
     */
    public function getProduct(int $productId)
    {
        try {
            return $this->productRepository->getById($productId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            return false;
        }
    }

    /**
     * Return the current product ID
     *
     * @return int
     */
    public function getProductId() : int
    {
        $productId = (int) $this->request->getParam('product_id', 0);
        if (!empty($productId)) {
            return (int) $productId;
        }

        $userData = $this->session->getData();
        $productId = (isset($userData['emailtester.product_id'])) ? (int)$userData['emailtester.product_id'] : 0;

        if (!empty($productId)) {
            return (int) $productId;
        }

        $productId = (int) $this->config->getValue('emailtester/settings/default_product');
        return $productId;
    }

    /**
     * Return a list of product select options
     *
     * @return array
     */
    public function getProductOptions() : array
    {
        $options = [];
        $options[] = ['value' => '', 'label' => '', 'current' => ''];
        $currentValue = $this->getProductId();
        $products = $this->getProductCollection();

        foreach ($products as $product) {
            /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
            $value = $product->getId();
            $label = '[' . $product->getId() . '] ' . $this->outputHelper->getProductOutput($product);
            $current = ($product->getId() == $currentValue) ? true : false;
            $options[] = ['value' => $value, 'label' => $label, 'current' => $current];
        }

        return $options;
    }

    /**
     * Get current product result
     *
     * @return string
     */
    public function getProductSearch() : string
    {
        $productId = $this->getProductId();

        if ($this->outputHelper->isValidId($productId)) {
            try {
                /** @var \Magento\Catalog\Model\Product $product */
                $product = $this->productRepository->getById($productId);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                return '';
            }

            return $this->outputHelper->getProductOutput($product);
        }

        return '';
    }

    /**
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     */
    private function getProductCollection() : \Magento\Catalog\Api\Data\ProductSearchResultsInterface
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilder;
        $searchCriteriaBuilder->addSortOrder('entity_id', AbstractCollection::SORT_ORDER_DESC);

        $customOptions = $this->outputHelper->getCustomOptions('product');
        if (!empty($customOptions)) {
            $filter = $this->filterBuilder
                ->setField('entity_id')
                ->setConditionType('in')
                ->setValue(implode(',', $customOptions))
                ->create();
            $searchCriteriaBuilder->addFilter($filter);
        }

        $searchCriteria = $searchCriteriaBuilder->create();

        $limit = $this->getProductCollectionLimit();
        if ($limit > 0) {
            $searchCriteria->setPageSize($limit);
            $searchCriteria->setCurrentPage(0);
        }

        $searchCriteria->getSortOrders();

        $products = $this->productRepository->getList($searchCriteria);

        return $products;
    }

    /**
     * @return int
     */
    private function getProductCollectionLimit() : int
    {
        return (int) $this->config->getValue('emailtester/settings/limit_product');
    }
}
