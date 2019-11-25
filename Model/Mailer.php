<?php
/**
 * Yireo EmailTester for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

namespace Yireo\EmailTester2\Model;

use Magento\Framework\App\Area;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Message;
use Magento\Framework\Mail\Template\FactoryInterface as TemplateFactoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Email\Model\BackendTemplateFactory;
use Magento\Framework\Mail\TemplateInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Phrase\RendererInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Email\Model\Template\Config as TemplateConfig;
use Magento\Store\Model\StoreManagerInterface;
use Exception;
use Yireo\EmailTester2\Behaviour\Errorable;
use Yireo\EmailTester2\Model\Mailer\Addressee;
use Yireo\EmailTester2\Model\Mailer\Recipient;
use Zend\Mime\Message as MessageAlias;

/**
 * EmailTester Core model
 */
class Mailer extends DataObject
{
    /**
     * Include the behaviour of handling errors
     */
    use Errorable;

    /**
     * @var Mailer\AddresseeFactory
     */
    private $addresseeFactory;

    /**
     * @var Mailer\RecipientFactory
     */
    private $recipientFactory;

    /**
     * @var Mailer\VariableBuilder
     */
    private $variableBuilder;

    /**
     * @var  TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var StateInterface
     */
    private $inlineTranslation;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var TemplateFactoryInterface
     */
    private $templateFactory;

    /**
     * @var TemplateConfig
     */
    private $templateConfig;

    /**
     * @var BackendTemplateFactory
     */
    private $backendTemplateFactory;

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * Mailer constructor.
     *
     * @param Mailer\AddresseeFactory $addresseeFactory
     * @param Mailer\RecipientFactory $recipientFactory
     * @param Mailer\VariableBuilder $variableBuilder
     * @param TransportBuilder $transportBuilder
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param StateInterface $inlineTranslation
     * @param ManagerInterface $eventManager
     * @param TemplateFactoryInterface $templateFactory
     * @param BackendTemplateFactory $backendTemplateFactory
     * @param TemplateConfig $templateConfig
     * @param RendererInterface $renderer
     * @param array $data
     */
    public function __construct(
        Mailer\AddresseeFactory $addresseeFactory,
        Mailer\RecipientFactory $recipientFactory,
        Mailer\VariableBuilder $variableBuilder,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        StateInterface $inlineTranslation,
        ManagerInterface $eventManager,
        TemplateFactoryInterface $templateFactory,
        BackendTemplateFactory $backendTemplateFactory,
        TemplateConfig $templateConfig,
        RendererInterface $renderer,
        array $data = []
    ) {
        parent::__construct($data);
        $this->addresseeFactory = $addresseeFactory;
        $this->recipientFactory = $recipientFactory;
        $this->variableBuilder = $variableBuilder;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->eventManager = $eventManager;
        $this->templateFactory = $templateFactory;
        $this->templateConfig = $templateConfig;
        $this->backendTemplateFactory = $backendTemplateFactory;
        $this->renderer = $renderer;
    }

    /**
     * Output the email
     *
     * @return string
     * @throws Exception
     */
    public function getHtml(): string
    {
        $this->prepare();

        return $this->getRawContentFromTransportBuilder();
    }

    /**
     * Send the email
     *
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function send(): bool
    {
        $this->prepare();
        $transport = $this->transportBuilder->getTransport();

        try {
            $transport->sendMessage();
            $sent = true;
        } catch (Exception $e) {
            $this->addError($e->getMessage());
            $sent = false;
        }

        if ($sent === false) {
            $this->processMailerErrors();
        }

        $this->wrapUp();
        return $sent;
    }

    /**
     *
     * @return string
     */
    protected function getRawContentFromTransportBuilder(): string
    {
        /** @var Message $message */
        /** @var MessageAlias $body */
        $message = $this->transportBuilder->getMessage();
        $body = $message->getBody();

        if (is_string($body)) {
            return $body;
        }

        if (method_exists($body, 'getRawContent')) {
            return $body->getRawContent();
        }

        $content = '';
        $parts = $body->getParts();
        foreach ($parts as $part) {
            $part->setEncoding('');
            $content .= $part->getContent();
        }

        return $this->cleanContent($content);
    }

    /**
     * @param string $content
     * @return string
     */
    private function cleanContent(string $content): string
    {
        return quoted_printable_decode($content);
    }

    /**
     *
     */
    private function processMailerErrors()
    {
        if ($this->scopeConfig->getValue('system/smtp/disable')) {
            $this->addError('SMTP is disabled');
        }

        if (!$this->hasErrors()) {
            $this->addError('Check your logs for unknown error');
        }
    }

    /**
     * @return Recipient
     * @throws LocalizedException
     */
    private function getRecipient(): Recipient
    {
        $data = [
            'customer_id' => $this->getData('customer_id'),
            'email' => $this->getData('email'),
        ];

        return $this->recipientFactory->create($data);
    }

    /**
     * @return Addressee
     */
    private function getSender(): Addressee
    {
        $addressee = $this->addresseeFactory->create();

        $sender = (string)$this->getData('sender');
        if ($sender) {
            $addressee->setEmail($sender);
        }

        return $addressee;
    }

    /**
     * Prepare for the main action
     *
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    private function prepare()
    {
        $this->setDefaultStoreId();

        $this->inlineTranslation->suspend();
        Phrase::setRenderer($this->renderer);

        $this->prepareTransportBuilder();
    }

    /**
     * Wrapup the main action
     */
    private function wrapUp()
    {
        $this->inlineTranslation->resume();
    }

    /**
     * Prepare the transport builder
     * @throws LocalizedException
     */
    private function prepareTransportBuilder()
    {
        $template = $this->getTemplate();
        $recipient = $this->getRecipient();

        $this->transportBuilder->setTemplateIdentifier($template->getId())
            ->setTemplateOptions($this->getTemplateOptions())
            ->setTemplateVars($this->getTemplateVariables())
            ->setFrom($this->getSender()->getAsArray())
            ->addTo($recipient->getEmail(), $recipient->getName());
    }

    /**
     * @return string
     */
    private function getTemplateId(): string
    {
        $templateId = (string)$this->getData('template');

        if (preg_match('/^([^\/]+)\/(.*)$/', $templateId, $match)) {
            $templateId = (string)$match[1];
        }

        return $templateId;
    }

    /**
     * @return TemplateInterface
     */
    private function getTemplate()
    {
        return $this->loadTemplate($this->getTemplateId());
    }

    /**
     * @return array
     */
    private function getTemplateVariables(): array
    {
        $template = $this->getTemplate();
        $variables = $this->buildVariables();
        $variables['subject'] = $template->getSubject();

        if ($this->matchTemplate($template, 'checkout_payment_failed_template')) {
            $variables['customer'] = $variables['customerName'];
        }

        $this->dispatchEventEmailOrderSetTemplateVarsBefore($variables);
        $this->dispatchEventEmailShipmentSetTemplateVarsBefore($variables);
        $this->dispatchEventEmailCreditmemoSetTemplateVarsBefore($variables);
        $this->dispatchEventEmailtesterVariables($variables);

        return $variables;
    }

    /**
     * @return array
     */
    private function getTemplateOptions(): array
    {
        $templateId = $this->getTemplateId();
        $area = Area::AREA_FRONTEND;
        if (!preg_match('/^([0-9]+)$/', $templateId)) {
            $area = $this->templateConfig->getTemplateArea($templateId);
        }

        return ['area' => $area, 'store' => $this->getStoreId()];
    }

    /**
     * @param array $variables
     */
    private function dispatchEventEmailOrderSetTemplateVarsBefore(array &$variables)
    {
        $eventTransport = new DataObject($variables);
        $this->eventManager->dispatch(
            'email_order_set_template_vars_before',
            [
                'sender' => $this,
                'transport' => $eventTransport,
                'transportObject' => $eventTransport,
            ]
        );

        $variables = $eventTransport->getData();
    }

    /**
     * @param array $variables
     */
    private function dispatchEventEmailShipmentSetTemplateVarsBefore(array &$variables)
    {
        $transport = new DataObject($variables);
        $this->eventManager->dispatch(
            'email_shipment_set_template_vars_before',
            [
                'sender' => $this,
                'transport' => $variables,
                'transportObject' => $transport
            ]
        );

        $variables = $transport->getData();
    }

    /**
     * @param array $variables
     */
    private function dispatchEventEmailCreditmemoSetTemplateVarsBefore(array &$variables)
    {
        $transport = new DataObject($variables);
        $this->eventManager->dispatch(
            'email_creditmemo_set_template_vars_before',
            [
                'sender' => $this,
                'transport' => $variables,
                'transportObject' => $transport
            ]
        );

        $variables = $transport->getData();
    }

    /**
     * @param array $variables
     */
    private function dispatchEventEmailtesterVariables(array &$variables)
    {
        $this->eventManager->dispatch(
            'emailtester_variables',
            ['variables' => &$variables]
        );
    }

    /**
     * @param mixed $templateId
     *
     * @return TemplateInterface
     */
    private function loadTemplate($templateId): TemplateInterface
    {
        if (preg_match('/^([0-9]+)$/', $templateId)) {
            $template = $this->backendTemplateFactory->create();
            $template->load($templateId);
            return $template;
        }

        $template = $this->templateFactory->get($templateId);
        return $template;
    }

    /**
     * @param TemplateInterface $template
     * @param string $name
     *
     * @return bool
     */
    private function matchTemplate(TemplateInterface $template, string $name): bool
    {
        if ($template->getId() === $name) {
            return true;
        }

        if ($template->getOrigTemplateCode() === $name) {
            return true;
        }

        return false;
    }

    /**
     * Make sure a valid store ID is set
     *
     * @throws NoSuchEntityException
     */
    private function setDefaultStoreId()
    {
        $storeId = $this->getStoreId();

        if (!$storeId > 0) {
            $storeId = (int)$this->storeManager->getStore()->getId();
            $this->setStoreId($storeId);
        }
    }

    /**
     * Collect all variables to insert into the email template
     *
     * @return array
     */
    private function buildVariables(): array
    {
        $variableBuilder = $this->variableBuilder;
        $data = $this->getData();
        $variableBuilder->setData($data);
        $variables = $variableBuilder->getVariables();

        return $variables;
    }

    /**
     * @return int
     */
    private function getStoreId(): int
    {
        return (int)$this->getData('store_id');
    }

    /**
     * @param int $storeId
     */
    private function setStoreId(int $storeId)
    {
        $this->setData('store_id', $storeId);
    }
}
