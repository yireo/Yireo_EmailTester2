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

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Yireo\EmailTester2\Model\Mailer\VariableInterface;

class Quote implements VariableInterface
{
    /**
     * @var OrderInterface
     */
    private $order;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * Quote constructor.
     *
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @return false|CartInterface
     */
    public function getVariable()
    {
        if (empty($this->order)) {
            return false;
        }

        $quoteId = (int)$this->order->getQuoteId();

        if (empty($quoteId)) {
            return false;
        }

        try {
            return $this->quoteRepository->get($quoteId);
        } catch (NoSuchEntityException $exception) {
            return false;
        }
    }

    /**
     * @param $order OrderInterface
     */
    public function setOrder(OrderInterface $order)
    {
        $this->order = $order;
    }
}
