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

namespace Yireo\EmailTester2\Model;

use Magento\Framework\App\Area;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\FactoryInterface as TemplateFactoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\PhraseFactory;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Email\Model\Template\Config as TemplateConfig;
use Zend_Mime_Part;
use Exception;
use Yireo\EmailTester2\Behaviour\Errorable;
use Yireo\EmailTester2\Model\Mailer\Addressee;
use Yireo\EmailTester2\Model\Mailer\Recipient;

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
     * @var PhraseFactory
     */
    private $phraseFactory;

    /**
     * @var TemplateFactoryInterface
     */
    private $templateFactory;
    /**
     * @var TemplateConfig
     */
    private $templateConfig;

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
     * @param PhraseFactory $phraseFactory
     * @param TemplateFactoryInterface $templateFactory
     * @param TemplateConfig $templateConfig
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
        PhraseFactory $phraseFactory,
        TemplateFactoryInterface $templateFactory,
        TemplateConfig $templateConfig,
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
        $this->phraseFactory = $phraseFactory;
        $this->templateFactory = $templateFactory;
        $this->templateConfig = $templateConfig;
    }

    /**
     * Output the email
     *
     * @return string
     * @throws Exception
     */
    public function getHtml() : string
    {
        $this->prepare();

        return $this->getRawContentFromTransportBuilder();
    }

    /**
     * Send the email
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function send() : bool
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
            return false;
        }

        return true;
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    protected function getRawContentFromTransportBuilder() : string
    {
        /** @var Zend_Mime_Part $body */
        $message = $this->transportBuilder->getMessage();
        $body = $message->getBody();

        if (is_string($body)) {
            return $body;
        }

        if (method_exists($body, 'getRawContent')) {
            return $body->getRawContent();
        }

        throw new LocalizedException($this->phraseFactory->create('Unexpected body type'));
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
     * Prepare for the main action
     *
     * @throws NoSuchEntityException
     */
    private function prepare()
    {
        $this->setDefaultStoreId();

        $this->inlineTranslation->suspend();
        $this->prepareTransportBuilder();
        $this->inlineTranslation->resume();
    }

    /**
     * Prepare the transport builder
     */
    private function prepareTransportBuilder()
    {
        /** @var Addressee $sender */
        $sender = $this->addresseeFactory->create();

        $recipient = $this->getRecipient();
        $templateId = $this->getData('template');
        $storeId = $this->getStoreId();
        $variables = $this->buildVariables();

        if (preg_match('/^([^\/]+)\/(.*)$/', $templateId, $match)) {
            $templateId = $match[1];
        }

        $template = $this->templateFactory->get($templateId);
        $variables['subject'] = $template->getSubject();

        $this->eventManager->dispatch(
            'email_order_set_template_vars_before',
            ['sender' => $sender, 'transport' => $variables]
        );

        $this->eventManager->dispatch(
            'emailtester_variables',
            ['variables' => &$variables]
        );

        $area = $this->templateConfig->getTemplateArea($templateId);

        $this->transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions(['area' => $area, 'store' => $storeId])
            ->setTemplateVars($variables)
            ->setFrom($sender->getAsArray())
            ->addTo($recipient->getEmail(), $recipient->getName());
    }

    /**
     * Make sure a valid store ID is set
     *
     * @throws NoSuchEntityException
     */
    private function setDefaultStoreId()
    {
        $storeId = $this->getStoreId();

        if (empty($storeId)) {
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
