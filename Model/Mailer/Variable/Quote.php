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

use Yireo\EmailTester2\Model\Mailer\VariableInterface;

/**
 * EmailTester Core model
 */
class Quote implements VariableInterface
{
    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    private $order;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * Quote constructor.
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @return false|\Magento\Quote\Api\Data\CartInterface
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
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            return false;
        }
    }

    /**
     * @param $order \Magento\Sales\Api\Data\OrderInterface
     */
    public function setOrder(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $this->order = $order;
    }
}
