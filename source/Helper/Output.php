<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

namespace Yireo\EmailTester2\Helper;
use Magento\Customer\Model\Customer;

/**
 * Class \Yireo\EmailTester2\Helper\Output
 */
class Output extends Data
{
    /**
     * Output a string describing a customer record
     *
     * @param \Magento\Customer\Model\Customer  $customer
     *
     * @return string
     */
    public function getCustomerOutput(\Magento\Customer\Model\Customer $customer)
    {
        return $customer->getName() . ' ['.$customer->getEmail().']';
    }

    /**
     * Output a string describing a product record
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getProductOutput(\Magento\Catalog\Model\Product $product)
    {
        return $product->getName() . ' ['.$product->getSku().']';
    }

    /**
     * Output a string describing a customer record
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return string
     */
    public function getOrderOutput(\Magento\Sales\Model\Order $order)
    {
        return '#'.$order->getIncrementId() . ' ['.$order->getCustomerEmail().' / '.$order->getState().']';
    }
}
