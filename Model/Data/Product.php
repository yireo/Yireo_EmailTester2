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
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductSearchResultsInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Yireo\EmailTester2\Helper\Output;

/**
 * Class Product
 */
class Product
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var RequestInterface
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
     * @var Output
     */
    private $outputHelper;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * Product constructor.
     * @param Session $session
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param Output $outputHelper
     * @param ConfigInterface $config
     */
    public function __construct(
        Session $session,
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        Output $outputHelper,
        ConfigInterface $config
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
     * @return false|ProductInterface
     */
    public function getProduct(int $productId)
    {
        try {
            return $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $exception) {
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
            /** @var ProductInterface $product */
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
                /** @var ProductModel $product */
                $product = $this->productRepository->getById($productId);
            } catch (NoSuchEntityException $exception) {
                return '';
            }

            return $this->outputHelper->getProductOutput($product);
        }

        return '';
    }

    /**
     * @return ProductSearchResultsInterface
     */
    private function getProductCollection() : ProductSearchResultsInterface
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
