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
 * Class PaymentHtml
 *
 * @package Yireo\EmailTester2\Model\Mailer\Variable
 */
class PaymentHtml
{
    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    protected $order;

    /**
     * @var int
     */
    protected $storeId;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

    /**
     * PaymentHtml constructor.
     *
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param \Magento\Framework\View\LayoutInterface $layout
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentHelper
    )
    {
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * @return string
     */
    public function getVariable()
    {
        // Try to load the payment block
        try {
            $paymentBlockHtml = $this->getPaymentBlockHtml($this->order, $this->storeId);
        } catch (\Exception $e) {
            $paymentBlockHtml = 'No payment-data available';
        }

        return $paymentBlockHtml;
    }

    /**
     * Get the payment HTML block
     *
     * @param $order \Magento\Sales\Api\Data\OrderInterface
     * @param $storeId int
     *
     * @return string
     */
    public function getPaymentBlockHtml(\Magento\Sales\Api\Data\OrderInterface $order, $storeId)
    {
        try {
            $paymentInfo = $this->order->getPayment();
            return $this->paymentHelper->getInfoBlockHtml($paymentInfo, $storeId);
        } catch (\Exception $exception) {
            return '';
        }
    }

    /**
     * @param $order \Magento\Sales\Api\Data\OrderInterface
     */
    public function setOrder(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $this->order = $order;
    }

    /**
     * @param $storeId
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
    }
}