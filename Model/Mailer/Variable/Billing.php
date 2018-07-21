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

use Magento\Customer\Model\Data\Customer;
use Magento\Customer\Model\Data\CustomerSecure;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\PhraseFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Yireo\EmailTester2\Model\Mailer\VariableInterface;

/**
 * Class Billing
 *
 * @package Yireo\EmailTester2\Model\Mailer\Variable
 */
class Billing implements VariableInterface
{
    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var OrderInterface
     */
    private $order;

    /**
     * Billing constructor.
     * @param PhraseFactory $phraseFactory
     */
    public function __construct(
        PhraseFactory $phraseFactory
    )
    {
        $this->phraseFactory = $phraseFactory;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getVariable()
    {
        if (empty($this->order)) {
            $phrase = $this->phraseFactory->create(['text' => 'Could not find any order entity']);
            throw new NoSuchEntityException($phrase);
        }

        $billing = $this->order->getBillingAddress();

        return $billing;
    }

    /**
     * @param CustomerSecure $customer
     */
    public function setCustomer(CustomerSecure $customer)
    {
        $this->customer = $customer;
    }

    /**
     * @param OrderInterface $order
     */
    public function setOrder(OrderInterface $order)
    {
        $this->order = $order;
    }
}
