<?php
/**
 * Yireo EmailTester for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

namespace Yireo\EmailTester2\Model\Mailer\Variable;

use Magento\Framework\PhraseFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Address\Renderer;
use Yireo\EmailTester2\Model\Mailer\VariablesInterface;

class OrderVars implements VariablesInterface
{
    /**
     * @var OrderInterface
     */
    private $order;

    /**
     * @var Renderer
     */
    private $addressRenderer;

    /**
     * PaymentHtml constructor.
     *
     * @param Renderer $addressRenderer
     * @param PhraseFactory $phraseFactory
     */
    public function __construct(
        Renderer $addressRenderer,
        PhraseFactory $phraseFactory
    ) {
        $this->addressRenderer = $addressRenderer;
        $this->phraseFactory = $phraseFactory;
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getVariables(): array
    {
        if (empty($this->order)) {
            $phrase = $this->phraseFactory->create(['text' => 'Could not find any order entity']);
            throw new NoSuchEntityException($phrase);
        }

        $variables = [];
        $variables['formattedShippingAddress'] = $this->getFormattedShippingAddress();
        $variables['formattedBillingAddress'] = $this->getFormattedBillingAddress();
        $variables['created_at_formatted'] = $this->order->getCreatedAtFormatted(2);

        return $variables;
    }

    /**
     * @param $order OrderInterface
     */
    public function setOrder(OrderInterface $order)
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
