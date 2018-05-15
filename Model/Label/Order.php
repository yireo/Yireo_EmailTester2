<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Model\Label;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class Order
 *
 * @package Yireo\EmailTester2\Model\Label
 */
class Order
{
    /**
     * @param OrderInterface $order
     *
     * @return string
     */
    public function getLabel(OrderInterface $order): string
    {
        return $order->getIncrementId() . ': '.$order->getCreatedAt();
    }
}
