<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types = 1);

namespace Yireo\EmailTester2\Model\Mailer\Variable;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * EmailTester Core model
 */
class Product implements \Yireo\EmailTester2\Model\Mailer\VariableInterface
{
    /**
     * @var int
     */
    private $productId;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Quote constructor.
     *
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\PhraseFactory $phraseFactory
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->phraseFactory = $phraseFactory;
    }

    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    public function getVariable(): \Magento\Catalog\Api\Data\ProductInterface
    {
        $product = $this->getProductById((int)$this->productId);

        // Load the first product instead
        if ($product === false) {
            $this->searchCriteriaBuilder->setPageSize(1);
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $products = $this->productRepository->getList($searchCriteria)->getItems();

            if (!empty($products)) {
                $product = array_shift($products);
            }
        }

        if (empty($product)) {
            $phrase = $this->phraseFactory->create(['text' => 'Could not find any customer entity']);
            throw new NoSuchEntityException($phrase);
        }

        return $product;
    }

    /**
     * @param int $productId
     *
     * @return bool|\Magento\Catalog\Api\Data\ProductInterface
     */
    private function getProductById(int $productId)
    {
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
    public function setProductId(int $productId)
    {
        $this->productId = $productId;
    }
}
