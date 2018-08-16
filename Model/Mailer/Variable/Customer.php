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

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Helper\View;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\Data\CustomerSecure;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\PhraseFactory;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Sales\Api\Data\OrderInterface;
use Yireo\EmailTester2\Model\Mailer\VariablesInterface;

/**
 * Class Customer
 *
 * @package Yireo\EmailTester2\Model\Mailer\Variable
 */
class Customer implements VariablesInterface
{
    /**
     * @var int
     */
    private $customerId = 0;

    /**
     * @var OrderInterface
     */
    private $order;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var View
     */
    private $customerViewHelper;

    /**
     * Order constructor.
     *
     * @param CustomerRepositoryInterface $customerRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CustomerRegistry $customerRegistry
     * @param DataObjectProcessor $dataObjectProcessor
     * @param View $customerViewHelper
     * @param PhraseFactory $phraseFactory
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CustomerRegistry $customerRegistry,
        DataObjectProcessor $dataObjectProcessor,
        View $customerViewHelper,
        PhraseFactory $phraseFactory
    ) {
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerRegistry = $customerRegistry;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->customerViewHelper = $customerViewHelper;
        $this->phraseFactory = $phraseFactory;
    }

    /**
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getVariables(): array
    {
        $customer = $this->getVariable();

        return [
            'customerFirstName' => $customer->getFirstName(),
            'customerLastName' => $customer->getLastName(),
            'customerName' => $customer->getName(),
            'customerEmail' => $customer->getEmail(),
        ];
    }

    /**
     * @return CustomerSecure
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getVariable() : CustomerSecure
    {
        /** @var CustomerInterface $customer */
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

            if (!empty($customers)) {
                $customer = $customers[0];
            }
        }

        if (empty($customer)) {
            $phrase = $this->phraseFactory->create(['text' => 'Could not find any customer entity']);
            throw new NoSuchEntityException($phrase);
        }

        $mergedCustomerData = $this->customerRegistry->retrieveSecureData($customer->getId());
        $customerData = $this->dataObjectProcessor
            ->buildOutputDataArray($customer, CustomerInterface::class);
        $mergedCustomerData->addData($customerData);
        $mergedCustomerData->setData('name', $this->customerViewHelper->getCustomerName($customer));

        return $mergedCustomerData;
    }

    /**
     * @param int $customerId
     *
     * @return false|CustomerInterface
     * @throws LocalizedException
     */
    public function getCustomerById(int $customerId)
    {
        $customerId = (int)$customerId;

        if (empty($customerId)) {
            return false;
        }

        try {
            return $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $exception) {
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
     * @param $order OrderInterface
     */
    public function setOrder(OrderInterface $order)
    {
        $this->order = $order;
    }
}
