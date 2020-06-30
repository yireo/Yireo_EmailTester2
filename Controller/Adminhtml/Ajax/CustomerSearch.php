<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

namespace Yireo\EmailTester2\Controller\Adminhtml\Ajax;

use Magento\Backend\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Model\Customer as CustomerModel;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SortOrderBuilderFactory;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Api\WebsiteRepositoryInterface;

class CustomerSearch extends AbstractSearch
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var GroupRepositoryInterface
     */
    private $customerGroupRepository;

    /**
     * @var WebsiteRepositoryInterface
     */
    private $websiteRepository;

    /**
     * @param Context $context
     * @param HttpRequest $request
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param FilterBuilder $filterBuilder
     * @param JsonFactory $resultJsonFactory
     * @param SortOrderBuilderFactory $sortOrderBuilderFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param GroupRepositoryInterface $customerGroupRepository
     * @param WebsiteRepositoryInterface $websiteRepository
     */
    public function __construct(
        Context $context,
        HttpRequest $request,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        FilterBuilder $filterBuilder,
        JsonFactory $resultJsonFactory,
        SortOrderBuilderFactory $sortOrderBuilderFactory,
        CustomerRepositoryInterface $customerRepository,
        GroupRepositoryInterface $customerGroupRepository,
        WebsiteRepositoryInterface $websiteRepository
    ) {
        parent::__construct(
            $context,
            $request,
            $searchCriteriaBuilderFactory,
            $filterBuilder,
            $resultJsonFactory,
            $sortOrderBuilderFactory
        );

        $this->customerRepository = $customerRepository;
        $this->customerGroupRepository = $customerGroupRepository;
        $this->websiteRepository = $websiteRepository;
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
        $searchFields = ['firstname', 'lastname', 'email'];
        $searchResults = $this->customerRepository->getList($this->getSearchCriteria($searchFields));

        foreach ($searchResults->getItems() as $customer) {
            /** @var $customer CustomerModel */
            $customerGroup = $this->customerGroupRepository->getById($customer->getGroupId());
            $website = $this->websiteRepository->getById($customer->getWebsiteId());

            $customerData[] = [
                'id' => $customer->getId(),
                'name' => $this->getCustomerName($customer),
                'email' => $customer->getEmail(),
                'group_id' => $customerGroup->getId(),
                'group_label' => $customerGroup->getCode(),
                'website_id' => $website->getId(),
                'website_label' => $website->getName(),
            ];
        }

        return $this->resultJsonFactory->create()->setData($customerData);
    }

    /**
     * @param CustomerInterface $customer
     *
     * @return string
     */
    private function getCustomerName(CustomerInterface $customer): string
    {
        return $customer->getFirstname() . ' ' . $customer->getLastname();
    }
}
