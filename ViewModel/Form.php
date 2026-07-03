<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\ViewModel;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class Form implements ArgumentInterface
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
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
