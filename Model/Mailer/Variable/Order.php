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
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Yireo\EmailTester2\Model\Mailer\VariableInterface;

/**
 * Class Order
 *
 * @package Yireo\EmailTester2\Model\Mailer\Variable
 */
class Order implements VariableInterface
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
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var View
     */
    private $customerViewHelper;

    /**
     * Order constructor.
     *
     * @param OrderRepositoryInterface $orderRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param View $customerViewHelper
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        View $customerViewHelper
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerViewHelper = $customerViewHelper;
    }

    /**
     * @return OrderInterface
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
        if ($order instanceof OrderInterface && $customer instanceof CustomerInterface) {
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
     * @return bool|OrderInterface
     */
    private function getOrderById(int $orderId)
    {
        if (empty($orderId)) {
            return false;
        }

        try {
            $order = $this->orderRepository->get($orderId);
        } catch (NoSuchEntityException $exception) {
            return false;
        }

        return $order;
    }

    /**
     * @param int $customerId
     *
     * @return false|CustomerInterface
     * @throws LocalizedException
     */
    private function getCustomerById(int $customerId)
    {
        if (empty($customerId)) {
            return false;
        }

        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $exception) {
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
