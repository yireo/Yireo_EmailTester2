<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

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
    protected $addresseeFactory;

    /**
     * @var Mailer\RecipientFactory
     */
    protected $recipientFactory;

    /**
     * @var Mailer\VariableBuilder
     */
    protected $variableBuilder;

    /**
     * @var  \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var int
     */
    protected $storeId;

    /**
     * @var string
     */
    protected $template;

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
        \Yireo\EmailTester2\Model\Mailer\AddresseeFactory $addresseeFactory,
        \Yireo\EmailTester2\Model\Mailer\RecipientFactory $recipientFactory,
        \Yireo\EmailTester2\Model\Mailer\VariableBuilder $variableBuilder,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        array $data = []
    )
    {
        $this->addresseeFactory = $addresseeFactory;
        $this->recipientFactory = $recipientFactory;
        $this->variableBuilder = $variableBuilder;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;

        parent::__construct($data);
    }

    /**
     * Output the email
     *
     * @return string
     */
    public function getHtml()
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
    public function send()
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

        if ($sent == false) {
            $this->processMailerErrors($transport);
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    protected function getRawContentFromTransportBuilder()
    {
        /** @var \Zend_Mime_Part $body */
        $message = $this->transportBuilder->getMessage();
        $body = $message->getBody();
        return $body->getRawContent();
    }

    /**
     * Send HTTP headers
     */
    protected function sendHeaders()
    {
        if (headers_sent()) {
            return;
        }

        header('Content-Type: text/html; charset=UTF-8');
    }

    /**
     * @param $transport
     */
    protected function processMailerErrors($transport)
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
    protected function getRecipient()
    {
        $data = array(
            'customer_id' => $this->getData('customer_id'),
            'email' => $this->getData('email'),
        );

        return $this->recipientFactory->create($data);
    }

    /**
     * Prepare for the main action
     */
    protected function prepare()
    {
        $this->setDefaultStoreId();

        $this->inlineTranslation->suspend();
        $this->prepareTransportBuilder();
        $this->inlineTranslation->resume();
    }

    /**
     * Prepare the transport builder
     */
    protected function prepareTransportBuilder()
    {
        /** @var \Yireo\EmailTester2\Model\Mailer\Addressee $sender */
        $sender = $this->addresseeFactory->create();

        $recipient = $this->getRecipient();
        $templateId = $this->getData('template');
        $storeId = $this->getStoreId();
        $variables = $this->collectVariables();

        $this->transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId])
            ->setTemplateVars($variables)
            ->setFrom($sender->getAsArray())
            ->addTo($recipient->getEmail(), $recipient->getName());
    }

    /**
     * Make sure a valid store ID is set
     */
    protected function setDefaultStoreId()
    {
        $storeId = $this->getStoreId();

        if (empty($storeId)) {
            $storeId = $this->storeManager->getStore()->getId();
            $this->setStoreId($storeId);
        }
    }

    /**
     * Collect all variables to insert into the email template
     *
     * @return array
     */
    protected function collectVariables()
    {
        $variableBuilder = $this->variableBuilder;
        $variableBuilder->setData($this->getData());
        $variables = $variableBuilder->getVariables();

        return $variables;
    }

    /**
     * @return int
     */
    protected function getStoreId()
    {
        return $this->getData('store_id');
    }

    /**
     * @param int $storeId
     */
    protected function setStoreId($storeId)
    {
        $this->setData('store_id', $storeId);
    }

    /**
     * @return string
     */
    protected function getTemplate()
    {
        return $this->getData('template');
    }

    /**
     * @param string $template
     */
    protected function setTemplate($template)
    {
        $this->setData('template', $template);
    }
}