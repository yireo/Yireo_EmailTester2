<?php
/**
 * Yireo EmailTester2 for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License
 */

declare(strict_types = 1);

namespace Yireo\EmailTester2\Model\Data;

use Magento\Backend\App\ConfigInterface;
use Magento\Backend\Model\Auth\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerSearchResultsInterface;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Yireo\EmailTester2\Helper\Output;

class Customer
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var RequestInterface
     */
    private $request;

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
     * Customer constructor.
     *
     * @param Session $session
     * @param CustomerRepositoryInterface $customerRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param Output $outputHelper
     * @param ConfigInterface $config
     */
    public function __construct(
        Session $session,
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        Output $outputHelper,
        ConfigInterface $config
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
     * @return false|CustomerInterface
     * @throws LocalizedException
     */
    public function getCustomer(int $customerId)
    {
        try {
            return $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $exception) {
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
        $customerId = (int) $this->request->getParam('customer_id');
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
     * @throws LocalizedException
     */
    public function getCustomerOptions() : array
    {
        $options = [];
        $options[] = ['value' => '', 'label' => '', 'current' => ''];
        $currentValue = $this->getCustomerId();
        $customers = $this->getCustomerCollection();

        foreach ($customers as $customer) {
            /** @var CustomerInterface $customer */
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
     * @throws LocalizedException
     */
    public function getCustomerSearch() : string
    {
        $customerId = $this->getCustomerId();

        if (!$this->outputHelper->isValidId($customerId)) {
            return '';
        }

        /** @var CustomerInterface $customer */
        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $exception) {
            return '';
        }

        return $this->outputHelper->getCustomerOutput($customer);
    }

    /**
     * @return CustomerSearchResultsInterface
     * @throws LocalizedException
     */
    private function getCustomerCollection() : CustomerSearchResultsInterface
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
                ->setValue(implode(',', $customOptions))
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
     * @return int
     */
    private function getCustomerCollectionLimit() : int
    {
        return (int) $this->config->getValue('emailtester/settings/limit_customer');
    }
}
