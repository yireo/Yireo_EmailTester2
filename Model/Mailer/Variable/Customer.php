<?php
/**
 * Yireo EmailTester for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types = 1);

namespace Yireo\EmailTester2\Model\Mailer\Variable;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Helper\View;
use Magento\Customer\Model\Data\CustomerSecure;
use Magento\Customer\Model\Data\CustomerSecureFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\PhraseFactory;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Sales\Api\Data\OrderInterface;
use Yireo\EmailTester2\Model\Mailer\VariablesInterface;

class Customer implements VariablesInterface
{
    public const DUMMY_RP_TOKEN = 'emailtester-dummy-token';

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
     * @var CustomerSecureFactory
     */
    private $customerSecureFactory;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var View
     */
    private $customerViewHelper;

    /**
     * @var PhraseFactory
     */
    private $phraseFactory;

    /**
     * Order constructor.
     *
     * @param CustomerRepositoryInterface $customerRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CustomerSecureFactory $customerSecureFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param View $customerViewHelper
     * @param PhraseFactory $phraseFactory
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CustomerSecureFactory $customerSecureFactory,
        DataObjectProcessor $dataObjectProcessor,
        View $customerViewHelper,
        PhraseFactory $phraseFactory
    ) {
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerSecureFactory = $customerSecureFactory;
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
        $customer = $this->getCustomer();

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

        $customerData = $this->dataObjectProcessor
            ->buildOutputDataArray($customer, CustomerInterface::class);
        unset(
            $customerData['password_hash'],
            $customerData['rp_token'],
            $customerData['rp_token_created_at'],
            $customerData['failures_num'],
            $customerData['first_failure'],
            $customerData['lock_expires']
        );

        $customerSecure = $this->customerSecureFactory->create();
        $customerSecure->addData($customerData);
        $customerSecure->setData('name', $this->customerViewHelper->getCustomerName($customer));
        $customerSecure->setData('rp_token', self::DUMMY_RP_TOKEN);

        return $customerSecure;
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

    /**
     * @return false|CustomerInterface
     * @throws LocalizedException
     */
    private function getCustomer(): ?CustomerInterface
    {
        /** @var CustomerInterface $customer */
        if (!empty($this->order) && $this->order->getCustomerId() > 0 && $this->customerId == 0) {
            return $this->getCustomerById((int)$this->order->getCustomerId()) ?: null;
        }

        if ($this->customerId) {
            $customer = $this->getCustomerById((int)$this->customerId);
            if (!$customer) {
                throw new LocalizedException(__('Customer with ID %1 does not exist', $this->customerId));
            }

            return $customer;
        }

        return null;
    }
}
