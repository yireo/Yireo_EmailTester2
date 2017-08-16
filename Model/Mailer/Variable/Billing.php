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
 * Class Billing
 *
 * @package Yireo\EmailTester2\Model\Mailer\Variable
 */
class Billing implements \Yireo\EmailTester2\Model\Mailer\VariableInterface
{
    /**
     * @var \Magento\Customer\Model\Data\Customer
     */
    private $customer;

    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    private $order;

    /**
     * @return string
     */
    public function getVariable()
    {
        $billing = $this->order->getBillingAddress();

        return $billing;
    }

    /**
     * @param \Magento\Customer\Model\Data\CustomerSecure $customer
     */
    public function setCustomer(\Magento\Customer\Model\Data\CustomerSecure $customer)
    {
        $this->customer = $customer;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     */
    public function setOrder(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $this->order = $order;
    }
}
