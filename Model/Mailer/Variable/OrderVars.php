<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

namespace Yireo\EmailTester2\Model\Mailer\Variable;

use Magento\Framework\PhraseFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class OrderVars
 *
 * @package Yireo\EmailTester2\Model\Mailer\Variable
 */
class OrderVars implements \Yireo\EmailTester2\Model\Mailer\VariablesInterface
{
    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    private $order;

    /**
     * @var \Magento\Sales\Model\Order\Address\Renderer
     */
    private $addressRenderer;

    /**
     * PaymentHtml constructor.
     *
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     */
    public function __construct(
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        PhraseFactory $phraseFactory
    )
    {
        $this->addressRenderer = $addressRenderer;
        $this->phraseFactory = $phraseFactory;
    }

    /**
     * @return array
     */
    public function getVariables(): array
    {
        if (empty($order)) {
            $phrase = $this->phraseFactory->create(['text' => 'Could not find any order entity']);
            throw new NoSuchEntityException($phrase);
        }

        $variables = [];
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
     * @return string
     */
    private function getFormattedShippingAddress(): string
    {
        return $this->order->getIsVirtual()
            ? ''
            : (string)$this->addressRenderer->format($this->order->getShippingAddress(), 'html');
    }

    /**
     * @return string
     */
    private function getFormattedBillingAddress(): string
    {
        return (string)$this->addressRenderer->format($this->order->getBillingAddress(), 'html');
    }
}
