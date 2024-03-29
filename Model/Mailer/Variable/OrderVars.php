<?php declare(strict_types=1);
/**
 * Yireo EmailTester for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

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
     * @var PhraseFactory
     */
    private $phraseFactory;

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
        $address = $this->order->getShippingAddress();
        if (!$address) {
            $address = $this->order->getBillingAddress();
        }

        return $this->order->getIsVirtual() || !$address
            ? ''
            : (string)$this->addressRenderer->format($address, 'html');
    }

    /**
     * @return string
     */
    private function getFormattedBillingAddress(): string
    {
        return !$this->order->getBillingAddress()
            ? ''
            : (string)$this->addressRenderer->format($this->order->getBillingAddress(), 'html');
    }
}
