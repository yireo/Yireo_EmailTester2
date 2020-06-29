<?php
/**
 * Yireo EmailTester for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types = 1);

namespace Yireo\EmailTester2\Model\Mailer\Variable;

use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\PhraseFactory;
use Magento\Payment\Helper\Data;
use Magento\Sales\Api\Data\OrderInterface;
use Yireo\EmailTester2\Model\Mailer\VariableInterface;

class PaymentHtml implements VariableInterface
{
    /**
     * @var OrderInterface
     */
    private $order;

    /**
     * @var int
     */
    private $storeId = 0;

    /**
     * @var Data
     */
    private $paymentHelper;

    /**
     * PaymentHtml constructor.
     *
     * @param Data $paymentHelper
     * @param PhraseFactory $phraseFactory
     */
    public function __construct(
        Data $paymentHelper,
        PhraseFactory $phraseFactory
    ) {
        $this->paymentHelper = $paymentHelper;
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

        try {
            $paymentBlockHtml = $this->getPaymentBlockHtml($this->order, $this->storeId);
        } catch (Exception $e) {
            $paymentBlockHtml = 'No payment-data available';
        }

        return $paymentBlockHtml;
    }

    /**
     * Get the payment HTML block
     *
     * @param $order OrderInterface
     * @param $storeId int
     *
     * @return string
     */
    public function getPaymentBlockHtml(OrderInterface $order, int $storeId): string
    {
        try {
            $paymentInfo = $order->getPayment();
            return (string)$this->paymentHelper->getInfoBlockHtml($paymentInfo, $storeId);
        } catch (Exception $exception) {
            return '';
        }
    }

    /**
     * @param $order OrderInterface
     */
    public function setOrder(OrderInterface $order)
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
