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
 * Class OrderVars
 *
 * @package Yireo\EmailTester2\Model\Mailer\Variable
 */
class OrderVars
{
    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    protected $order;

    /**
     * @var \Magento\Sales\Model\Order\Address\Renderer
     */
    protected $addressRenderer;

    /**
     * PaymentHtml constructor.
     *
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     */
    public function __construct(
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
    )
    {
        $this->addressRenderer = $addressRenderer;
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        $variables = array();
        $variables['formattedShippingAddress'] = $this->getFormattedShippingAddress();
        $variables['formattedBillingAddress'] = $this->getFormattedBillingAddress();

        return $variables;
    }

    /**
     * @param $order \Magento\Sales\Api\Data\OrderInterface
     */
    public function setOrder(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $this->order = $order;
    }

    /**
     * @return string|null
     */
    protected function getFormattedShippingAddress()
    {
        return $this->order->getIsVirtual()
            ? null
            : $this->addressRenderer->format($this->order->getShippingAddress(), 'html');
    }

    /**
     * @return string|null
     */
    protected function getFormattedBillingAddress()
    {
        return $this->addressRenderer->format($this->order->getBillingAddress(), 'html');
    }
}