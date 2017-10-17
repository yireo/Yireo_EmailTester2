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
 * Class Order
 *
 * @package Yireo\EmailTester2\Model\Mailer\Variable
 */
class Order implements \Yireo\EmailTester2\Model\Mailer\VariableInterface
{
    /**
     * @var int
     */
    private $orderId = 0;

    /**
     * @var int
     */
    private $customerId = 0;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Customer\Helper\View
     */
    private $customerViewHelper;

    /**
     * Order constructor.
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Customer\Helper\View $customerViewHelper
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerViewHelper = $customerViewHelper;
    }

    /**
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function getVariable()
    {
        $order = false;

        // Load the order by ID
        if (!empty($this->orderId)) {
            $order = $this->getOrderById((int)$this->orderId);
        }

        // Load the first order instead
        if (!$order || !$order->getEntityId() > 0) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $searchCriteria->setPageSize(1);
            $searchCriteria->setCurrentPage(1);
            $orders = $this->orderRepository->getList($searchCriteria)->getItems();

            if (!empty($orders)) {
                return array_shift($orders);
            }
        }

        // Set the customer into the order
        $customer = $this->getCustomerById((int)$this->customerId);
        if ($order instanceof \Magento\Sales\Api\Data\OrderInterface && $customer instanceof \Magento\Customer\Api\Data\CustomerInterface) {
            $order->setCustomerId($customer->getId());
            $order->setCustomerName($this->customerViewHelper->getCustomerName($customer));
            $order->setCustomerFirstname($customer->getFirstname());
            $order->setCustomerLastname($customer->getLastname());
            $order->setCustomerNote('Some customer note');
            $order->setCustomer($customer);
        }

        return $order;
    }

    /**
     * @param int $orderId
     *
     * @return bool|\Magento\Sales\Api\Data\OrderInterface
     */
    private function getOrderById(int $orderId)
    {
        if (empty($orderId)) {
            return false;
        }

        try {
            $order = $this->orderRepository->get($orderId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            return false;
        }

        return $order;
    }

    /**
     * @param int $customerId
     *
     * @return false|\Magento\Customer\Api\Data\CustomerInterface
     */
    private function getCustomerById(int $customerId)
    {
        if (empty($customerId)) {
            return false;
        }

        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            return false;
        }

        if (!$customer->getId() > 0) {
            return false;
        }

        return $customer;
    }

    /**
     * @param int $orderId
     */
    public function setOrderId(int $orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @param int $customerId
     */
    public function setCustomerId(int $customerId)
    {
        $this->customerId = $customerId;
    }
}
