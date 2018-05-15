<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types = 1);

namespace Yireo\EmailTester2\Controller\Adminhtml\Ajax;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer as CustomerModel;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CustomerSearch
 *
 * @package Yireo\EmailTester2\Controller\Ajax
 */
class CustomerSearch extends Action
{
    const ADMIN_RESOURCE = 'Yireo_EmailTester2::index';

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var HttpRequest
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
     * @param Context $context
     * @param CustomerRepositoryInterface $customerRepository
     * @param HttpRequest $request
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        CustomerRepositoryInterface $customerRepository,
        HttpRequest $request,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);

        $this->customerRepository = $customerRepository;
        $this->request = $request;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Index action
     *
     * @return Json
     * @throws LocalizedException
     */
    public function execute(): Json
    {
        $customerData = [];
        $searchResults = $this->customerRepository->getList($this->loadSearchCriteria());

        foreach ($searchResults->getItems() as $customer) {
            /** @var $customer CustomerModel */
            $customerData[] = [
                'value' => $customer->getId(),
                'label' => $this->getCustomerLabel($customer),
            ];
        }

        return $this->resultJsonFactory->create()->setData($customerData);
    }

    /**
     * @return string
     */
    private function getSearchQuery(): string
    {
        $search = (string) $this->request->getParam('term');
        return $search;
    }

    /**
     * @param CustomerInterface $customer
     *
     * @return string
     */
    private function getCustomerLabel(CustomerInterface $customer): string
    {
        return $customer->getFirstname() . ' ' . $customer->getLastname() . ' [' . $customer->getEmail() . ']';
    }

    /**
     * @return SearchCriteria
     */
    private function loadSearchCriteria(): SearchCriteria
    {
        $this->searchCriteriaBuilder->setCurrentPage(0);
        $this->searchCriteriaBuilder->setPageSize(10);

        $searchFields = ['firstname', 'lastname', 'email'];
        $filters = [];

        foreach ($searchFields as $field) {
            $filters[] = $this->filterBuilder
                ->setField($field)
                ->setConditionType('like')
                ->setValue($this->getSearchQuery() . '%')
                ->create();
        }

        $this->searchCriteriaBuilder->addFilters($filters);

        return $this->searchCriteriaBuilder->create();
    }
}
