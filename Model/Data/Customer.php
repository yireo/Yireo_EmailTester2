<?php
/**
 * Yireo EmailTester2 for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License
 */

declare(strict_types = 1);

namespace Yireo\EmailTester2\Model\Data;

use Magento\Framework\Api\FilterBuilder;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;

/**
 * Class Customer
 */
class Customer
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $session;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Framework\Api\Search\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var \Yireo\EmailTester2\Helper\Output
     */
    private $outputHelper;

    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    private $config;

    /**
     * Customer constructor.
     *
     * @param \Magento\Backend\Model\Auth\Session\Proxy $session
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session\Proxy $session,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        FilterBuilder $filterBuilder,
        \Yireo\EmailTester2\Helper\Output $outputHelper,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magento\Backend\App\ConfigInterface $config
    ) {
        $this->session = $session;
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->request = $request;
        $this->filterBuilder = $filterBuilder;
        $this->outputHelper = $outputHelper;
        $this->config = $config;
    }

    /**
     * @param int $customerId
     *
     * @return false|\Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomer(int $customerId)
    {
        try {
            return $this->customerRepository->getById($customerId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            return false;
        }
    }

    /**
     * Get the current customer ID
     *
     * @return int
     */
    public function getCustomerId() : int
    {
        $customerId = $this->request->getParam('customer_id');
        if (!empty($customerId)) {
            return (int) $customerId;
        }

        $userData = $this->session->getData();
        $customerId = (isset($userData['emailtester.customer_id'])) ? (int)$userData['emailtester.customer_id'] : null;
        if (!empty($customerId)) {
            return (int) $customerId;
        }

        $customerId = (int) $this->config->getValue('emailtester/settings/default_customer');
        return $customerId;
    }

    /**
     * Get an array of customer select options
     *
     * @return array
     */
    public function getCustomerOptions() : array
    {
        $options = [];
        $options[] = ['value' => '', 'label' => '', 'current' => ''];
        $currentValue = $this->getCustomerId();
        $customers = $this->getCustomerCollection();

        foreach ($customers as $customer) {
            /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
            $value = $customer->getId();
            $label = '[' . $customer->getId() . '] ' . $this->outputHelper->getCustomerOutput($customer);
            $current = ($customer->getId() == $currentValue) ? true : false;
            $options[] = ['value' => $value, 'label' => $label, 'current' => $current];
        }

        return $options;
    }

    /**
     * Get current customer result
     *
     * @return string
     */
    public function getCustomerSearch() : string
    {
        $customerId = $this->getCustomerId();

        if (!$this->outputHelper->isValidId($customerId)) {
            return '';
        }

        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            return '';
        }

        return $this->outputHelper->getCustomerOutput($customer);
    }

    /**
     * @return \Magento\Customer\Api\Data\CustomerSearchResultsInterface
     */
    private function getCustomerCollection()
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilder;
        $searchCriteriaBuilder->addSortOrder('entity_id', AbstractCollection::SORT_ORDER_DESC);

        $websiteId = $this->outputHelper->getWebsiteId();
        if ($websiteId > 0) {
            $filter = $this->filterBuilder
                ->setField('website_id')
                ->setConditionType('eq')
                ->setValue($websiteId)
                ->create();
            $searchCriteriaBuilder->addFilter($filter);
        }

        $customOptions = $this->outputHelper->getCustomOptions('customer');
        if (!empty($customOptions)) {
            $filter = $this->filterBuilder
                ->setField('entity_id')
                ->setConditionType('in')
                ->setValue($customOptions)
                ->create();
            $searchCriteriaBuilder->addFilter($filter);
        }

        $searchCriteria = $searchCriteriaBuilder->create();

        $limit = $this->getCustomerCollectionLimit();
        if ($limit > 0) {
            $searchCriteria->setPageSize($limit);
            $searchCriteria->setCurrentPage(0);
        }

        $searchCriteria->getSortOrders();

        $customers = $this->customerRepository->getList($searchCriteria);

        return $customers;
    }

    /**
     * @return null|string
     */
    private function getCustomerCollectionLimit()
    {
        return $this->config->getValue('emailtester/settings/limit_customer');
    }
}
