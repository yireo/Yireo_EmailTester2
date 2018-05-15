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

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class PaymentHtml
 *
 * @package Yireo\EmailTester2\Model\Mailer\Variable
 */
class PaymentHtml implements \Yireo\EmailTester2\Model\Mailer\VariableInterface
{
    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    private $order;

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    private $paymentHelper;

    /**
     * PaymentHtml constructor.
     *
     * @param \Magento\Payment\Helper\Data $paymentHelper
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Framework\PhraseFactory $phraseFactory
    ) {
        $this->paymentHelper = $paymentHelper;
        $this->phraseFactory = $phraseFactory;
    }

    /**
     * @return string
     */
    public function getVariable()
    {
        if (empty($this->order)) {
            $phrase = $this->phraseFactory->create(['text' => 'Could not find any order entity']);
            throw new NoSuchEntityException($phrase);
        }

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
    public function getPaymentBlockHtml(\Magento\Sales\Api\Data\OrderInterface $order, int $storeId): string
    {
        try {
            $paymentInfo = $order->getPayment();
            return (string)$this->paymentHelper->getInfoBlockHtml($paymentInfo, $storeId);
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
     * @param int $storeId
     */
    public function setStoreId(int $storeId)
    {
        $this->storeId = $storeId;
    }
}
