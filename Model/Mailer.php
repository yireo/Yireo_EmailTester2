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

/**
 * EmailTester Core model
 */
class Mailer extends \Magento\Framework\DataObject
{
    /**
     * Include the behaviour of handling errors
     */
    use \Yireo\EmailTester2\Behaviour\Errorable;

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
     * @var  \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    private $inlineTranslation;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var string
     */
    private $template;

    /**
     * Mailer constructor.
     *
     * @param Mailer\AddresseeFactory $addresseeFactory
     * @param Mailer\RecipientFactory $recipientFactory
     * @param Mailer\VariableBuilder $variableBuilder
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param array $data
     */
    public function __construct(
        Mailer\AddresseeFactory $addresseeFactory,
        Mailer\RecipientFactory $recipientFactory,
        Mailer\VariableBuilder $variableBuilder,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        array $data = []
    ) {
        $this->addresseeFactory = $addresseeFactory;
        $this->recipientFactory = $recipientFactory;
        $this->variableBuilder = $variableBuilder;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->eventManager = $eventManager;

        parent::__construct($data);
    }

    /**
     * Output the email
     *
     * @return string
     */
    public function getHtml() : string
    {
        $this->prepare();

        // Send some extra headers just make sure the document is compliant
        $this->sendHeaders();
        return $this->getRawContentFromTransportBuilder();
    }

    /**
     * Send the email
     *
     * @return bool
     */
    public function send() : bool
    {
        $this->prepare();
        $transport = $this->transportBuilder->getTransport();

        try {
            $transport->sendMessage();
            $sent = true;
        } catch (\Exception $e) {
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
     * @throws \Exception
     */
    protected function getRawContentFromTransportBuilder() : string
    {
        /** @var \Zend_Mime_Part $body */
        $message = $this->transportBuilder->getMessage();
        $body = $message->getBody();

        if (is_string($body)) {
            return $body;
        }

        if (method_exists($body, 'getRawContent')) {
            return $body->getRawContent();
        }

        throw new \Magento\Framework\Exception\LocalizedException(new \Magento\Framework\Phrase('Unexpected body type'));
    }

    /**
     * Send HTTP headers
     *
     * @return bool
     */
    private function sendHeaders(): bool
    {
        if (headers_sent()) {
            return false;
        }

        header('Content-Type: text/html; charset=UTF-8');
        return true;
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
     * @return \Yireo\EmailTester2\Model\Mailer\Recipient
     */
    private function getRecipient()
    {
        $data = [
            'customer_id' => $this->getData('customer_id'),
            'email' => $this->getData('email'),
        ];

        return $this->recipientFactory->create($data);
    }

    /**
     * Prepare for the main action
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
        /** @var \Yireo\EmailTester2\Model\Mailer\Addressee $sender */
        $sender = $this->addresseeFactory->create();

        $recipient = $this->getRecipient();
        $templateId = $this->getData('template');
        $storeId = $this->getStoreId();
        $variables = $this->buildVariables();

        if (preg_match('/^([^\/]+)\/(.*)$/', $templateId, $match)) {
            $templateId = $match[1];
            $theme = $match[2];
        }

        $this->eventManager->dispatch(
            'email_order_set_template_vars_before',
            ['sender' => $sender, 'transport' => $variables]
        );

        $this->eventManager->dispatch(
            'emailtester_variables',
            ['variables' => &$variables]
        );

        $this->transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId])
            ->setTemplateVars($variables)
            ->setFrom($sender->getAsArray())
            ->addTo($recipient->getEmail(), $recipient->getName());
    }

    /**
     * Make sure a valid store ID is set
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

    /**
     * @return string
     */
    private function getTemplate(): string
    {
        return $this->getData('template');
    }

    /**
     * @param string $template
     */
    private function setTemplate(string $template)
    {
        $this->setData('template', $template);
    }
}
