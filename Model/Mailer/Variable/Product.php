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

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\PhraseFactory;
use Yireo\EmailTester2\Model\Mailer\VariableInterface;

/**
 * EmailTester Core model
 */
class Product implements VariableInterface
{
    /**
     * @var int
     */
    private $productId;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var PhraseFactory
     */
    private $phraseFactory;

    /**
     * Quote constructor.
     *
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param PhraseFactory $phraseFactory
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PhraseFactory $phraseFactory
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->phraseFactory = $phraseFactory;
    }

    /**
     * @return ProductInterface
     * @throws NoSuchEntityException
     */
    public function getVariable(): ProductInterface
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
     * @return bool|ProductInterface
     * @throws NoSuchEntityException
     */
    private function getProductById(int $productId)
    {
        if (empty($productId)) {
            return false;
        }

        try {
            $product = $this->productRepository->getById($this->productId);
        } catch (CoreNoSuchEntityException $exception) {
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
