<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

namespace Yireo\EmailTester2\Model\Mailer\Variable;

/**
 * Class Order
 *
 * @package Yireo\EmailTester2\Model\Mailer\Variable
 */
class Order
{
    /**
     * @var int
     */
    protected $orderId = 0;

    /**
     * @var int
     */
    protected $customerId = 0;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $customerViewHelper;

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
    )
    {
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
            $order = $this->orderRepository->get($this->orderId);
        }

        // Load the first order instead
        if (!$order || !$order->getEntityId() > 0) {
            $this->searchCriteriaBuilder->setPageSize(1);
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $orders = $this->orderRepository->getList($searchCriteria)->getItems();

            if (count($orders) > 0) {
                $order = array_shift($orders);
            }
        }

        // Set the customer into the order
        if (!empty($order) && !empty($this->customerId)) {
            $customer = $this->customerRepository->getById($this->customerId);
            if ($customer->getId() > 0) {
                $order->setCustomerId($customer->getId());
                $order->setCustomerName($this->customerViewHelper->getCustomerName($customer));
                $order->setCustomerFirstname($customer->getFirstname());
                $order->setCustomerLastname($customer->getLastname());
                $order->setCustomerNote('Some customer note');
                $order->setCustomer($customer);
            }
        }
        
        return $order;
    }

    /**
     * @param mixed $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @param mixed $customerId
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    }
}