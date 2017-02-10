<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

namespace Yireo\EmailTester2\Model\Mailer\Variable;

/**
 * Class Billing
 *
 * @package Yireo\EmailTester2\Model\Mailer\Variable
 */
class Billing
{
    /**
     * @var \Magento\Customer\Model\Data\Customer
     */
    protected $customer;

    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    protected $order;
    
    /**
     * @return string
     */
    public function getVariable()
    {
        $billing = $this->order->getBillingAddress();

        return $billing;
    }

    /**
     * @param \Magento\Customer\Model\Data\Customer $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }
}