<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

namespace Yireo\EmailTester2\Model\Mailer\Variable;

/**
 * EmailTester Core model
 */
class Product
{
    /**
     * @var int
     */
    protected $productId;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Quote constructor.
     *
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getVariable()
    {
        $product = $this->getProductById($this->productId);

        // Load the first product instead
        if ($product === false) {
            $this->searchCriteriaBuilder->setPageSize(1);
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $products = $this->productRepository->getList($searchCriteria)->getItems();

            if (count($products) > 0) {
                $product = array_shift($products);
            }
        }

        return $product;
    }

    /**
     * @param $productId
     *
     * @return bool|\Magento\Catalog\Api\Data\ProductInterface
     */
    private function getProductById($productId)
    {
        $productId = (int)$productId;

        if (empty($productId)) {
            return false;
        }

        try {
            $product = $this->productRepository->getById($this->productId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            return false;
        }

        if (!$product->getId() > 0) {
            return false;
        }

        return $product;
    }

    /**
     * @param int $productId
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }
}