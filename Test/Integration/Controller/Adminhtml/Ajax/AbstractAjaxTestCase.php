<?php

declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Integration\Controller\Adminhtml\Ajax;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\AbstractBackendController;

class AbstractAjaxTestCase extends AbstractBackendController
{
    /**
     * Setup method
     * @throws AuthenticationException
     */
    public function setUp()
    {
        $this->resource = 'Yireo_EmailTester2::index';
        parent::setUp();
    }

    /**
     * Test whether the page contains invalid content
     *
     * @magentoAppArea adminhtml
     */
    protected function getDataFromUrl(): array
    {
        $this->dispatch($this->uri);
        $body = $this->getResponse()->getBody();
        $data = $this->getSerializer()->unserialize($body);

        $this->assertTrue(is_array($data), 'Not data: ' . $body);
        return $data;
    }

    /**
     * @return SerializerInterface
     */
    protected function getSerializer(): SerializerInterface
    {
        return ObjectManager::getInstance()->get(SerializerInterface::class);
    }

    /**
     * @param array $data
     * @return string
     */
    protected function dump(array $data): string
    {
        return $this->getSerializer()->serialize($data);
    }

    /**
     * @return OrderInterface
     */
    protected function getOrder(): OrderInterface
    {
        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = ObjectManager::getInstance()->get(OrderRepositoryInterface::class);
        $searchCriteria = ObjectManager::getInstance()->get(SearchCriteria::class);
        $searchResults = $orderRepository->getList($searchCriteria);
        $items = $searchResults->getItems();
        return array_shift($items);
    }

    /**
     * @return CustomerInterface
     * @throws LocalizedException
     */
    protected function getCustomer(): CustomerInterface
    {
        /** @var CustomerRepositoryInterface $customerRepository */
        $customerRepository = ObjectManager::getInstance()->get(CustomerRepositoryInterface::class);
        $searchCriteria = ObjectManager::getInstance()->get(SearchCriteria::class);
        $searchResults = $customerRepository->getList($searchCriteria);
        $items = $searchResults->getItems();
        return array_shift($items);
    }

    /**
     * @return ProductInterface
     */
    protected function getProduct(): ProductInterface
    {
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = ObjectManager::getInstance()->get(ProductRepositoryInterface::class);
        $searchCriteria = ObjectManager::getInstance()->get(SearchCriteria::class);
        $searchResults = $productRepository->getList($searchCriteria);
        $items = $searchResults->getItems();
        return array_shift($items);
    }
}
