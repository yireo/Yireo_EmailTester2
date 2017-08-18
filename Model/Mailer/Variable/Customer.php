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

/**
 * Class Customer
 *
 * @package Yireo\EmailTester2\Model\Mailer\Variable
 */
class Customer implements \Yireo\EmailTester2\Model\Mailer\VariableInterface
{
    /**
     * @var int
     */
    private $customerId = 0;

    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    private $order;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var \Magento\Customer\Helper\View
     */
    private $customerViewHelper;

    /**
     * Order constructor.
     *
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Customer\Helper\View $customerViewHelper
    ) {
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerRegistry = $customerRegistry;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->customerViewHelper = $customerViewHelper;
    }

    /**
     * @return \Magento\Customer\Model\Data\CustomerSecure
     */
    public function getVariable() : \Magento\Customer\Model\Data\CustomerSecure
    {
        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        if (!empty($this->order) && $this->order->getCustomerId() > 0 && $this->customerId == 0) {
            $customer = $this->getCustomerById((int) $this->order->getCustomerId());
        } elseif ($this->customerId) {
            $customer = $this->getCustomerById((int) $this->customerId);
        }

        // Load the first customer instead
        if (empty($customer) || !$customer->getId() > 0) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $searchCriteria->setPageSize(1);
            $searchCriteria->setCurrentPage(1);
            $customers = $this->customerRepository->getList($searchCriteria)->getItems();
            $customer = $customers[0];
        }

        // Complete other customer fields
        // @todo: This does not work because setPassword() is not found
        //$customer->setPassword('p@$$w0rd');

        $mergedCustomerData = $this->customerRegistry->retrieveSecureData($customer->getId());
        $customerData = $this->dataObjectProcessor
            ->buildOutputDataArray($customer, \Magento\Customer\Api\Data\CustomerInterface::class);
        $mergedCustomerData->addData($customerData);
        $mergedCustomerData->setData('name', $this->customerViewHelper->getCustomerName($customer));

        return $mergedCustomerData;
    }

    /**
     * @param int $customerId
     *
     * @return false|\Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomerById(int $customerId)
    {
        $customerId = (int)$customerId;

        if (empty($customerId)) {
            return false;
        }

        try {
            return $this->customerRepository->getById($customerId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            return false;
        }
    }

    /**
     * @param int $customerId
     */
    public function setCustomerId(int $customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * @param $order \Magento\Sales\Api\Data\OrderInterface
     */
    public function setOrder(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $this->order = $order;
    }
}
