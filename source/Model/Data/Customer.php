<?php
/**
 * Yireo EmailTester2 for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2016 Yireo (https://www.yireo.com/)
 * @license     Open Source License
 */

namespace Yireo\EmailTester2\Model\Data;

use Magento\Framework\Api\Filter;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;

/**
 * Class Customer
 */
class Customer extends Generic
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Framework\Api\Search\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Customer constructor.
     *
     * @param \Magento\Backend\Model\Auth\Session $session
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $session,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Yireo\EmailTester2\Helper\Output $outputHelper,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magento\Backend\App\ConfigInterface $config
    )
    {
        $this->session = $session;
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->request = $request;

        parent::__construct($outputHelper, $session, $storeRepository, $request, $config);
    }

    /**
     * @param int $customerId
     *
     * @return false|\Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomer($customerId)
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
    public function getCustomerId()
    {
        $customerId = $this->request->getParam('customer_id');
        if (!empty($customerId)) {
            return $customerId;
        }

        $userData = $this->session->getData();
        $customerId = (isset($userData['emailtester.customer_id'])) ? (int)$userData['emailtester.customer_id'] : null;
        if (!empty($customerId)) {
            return $customerId;
        }

        $customerId = $this->getStoreConfig('emailtester/settings/default_customer');
        return $customerId;
    }

    /**
     * Get an array of customer select options
     *
     * @return array
     */
    public function getCustomerOptions()
    {
        $options = array();
        $options[] = array('value' => '', 'label' => '', 'current' => null);
        $currentValue = $this->getCustomerId();
        $customers = $this->getCustomerCollection();

        foreach ($customers as $customer) {
            /** @var \Magento\Customer\Model\Customer $customer */
            $value = $customer->getId();
            $label = '[' . $customer->getId() . '] ' . $this->outputHelper->getCustomerOutput($customer);
            $current = ($customer->getId() == $currentValue) ? true : false;
            $options[] = array('value' => $value, 'label' => $label, 'current' => $current);
        }

        return $options;
    }

    /**
     * Get current customer result
     *
     * @return string
     */
    public function getCustomerSearch()
    {
        $customerId = $this->getCustomerId();

        if (!$this->isValidId($customerId)) {
            return '';
        }

        /** @var \Magento\Customer\Model\Customer $customer */
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
    protected function getCustomerCollection()
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilder;
        $searchCriteriaBuilder->addSortOrder('entity_id', AbstractCollection::SORT_ORDER_DESC);

        $websiteId = $this->getWebsiteId();
        if ($websiteId > 0) {
            $searchCriteriaBuilder->addFilter(
                new Filter([
                    Filter::KEY_FIELD => 'website_id',
                    Filter::KEY_CONDITION_TYPE => 'eq',
                    Filter::KEY_VALUE => $websiteId
                ]));
        }

        $customOptions = $this->getCustomOptions('customer');
        if (!empty($customOptions)) {
            $searchCriteriaBuilder->addFilter(
                new Filter([
                    Filter::KEY_FIELD => 'entity_id',
                    Filter::KEY_CONDITION_TYPE => 'in',
                    Filter::KEY_VALUE => $customOptions
                ]));
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
    protected function getCustomerCollectionLimit()
    {
        return $this->getStoreConfig('emailtester/settings/limit_customer');
    }
}