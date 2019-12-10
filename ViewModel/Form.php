<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\ViewModel;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class Form
 */
class Form implements ArgumentInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * Form constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param ProductRepositoryInterface $productRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository,
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
    }

    /**
     * @return bool
     * @throws LocalizedException
     */
    public function hasCustomers(): bool
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->setPageSize(1);
        $searchResult = $this->customerRepository->getList($searchCriteriaBuilder->create());
        $items = $searchResult->getItems();
        return (bool)count($items);
    }

    /**
     * @return bool
     */
    public function hasProducts(): bool
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->setPageSize(1);
        $searchCriteria = $searchCriteriaBuilder->create();
        $searchResult = $this->productRepository->getList($searchCriteria);
        $items = $searchResult->getItems();
        return (bool)count($items);
    }

    /**
     * @return bool
     */
    public function hasOrders(): bool
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->setPageSize(1);
        $searchResult = $this->orderRepository->getList($searchCriteriaBuilder->create());
        $items = $searchResult->getItems();
        return (bool)count($items);
    }
}
