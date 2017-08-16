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

namespace Yireo\EmailTester2\Helper;

/**
 * Class \Yireo\EmailTester2\Helper\Output
 */
class Output extends Data
{
    /**
     * Output a string describing a customer record
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface  $customer
     *
     * @return string
     */
    public function getCustomerOutput(\Magento\Customer\Api\Data\CustomerInterface $customer) : string
    {
        return $customer->getName() . ' ['.$customer->getEmail().']';
    }

    /**
     * Output a string describing a product record
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     *
     * @return string
     */
    public function getProductOutput(\Magento\Catalog\Api\Data\ProductInterface $product) : string
    {
        return $product->getName() . ' ['.$product->getSku().']';
    }

    /**
     * Output a string describing a customer record
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     *
     * @return string
     */
    public function getOrderOutput(\Magento\Sales\Api\Data\OrderInterface $order) : string
    {
        return '#'.$order->getIncrementId() . ' ['.$order->getCustomerEmail().' / '.$order->getState().']';
    }
}
