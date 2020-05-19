<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

namespace Yireo\EmailTester2\Block\Adminhtml;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Session;
use Magento\Framework\View\Element\Text;
use Yireo\EmailTester2\Model\Mailer;

/**
 * Class Preview
 */
class Preview extends Text
{
    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * @var Session
     */
    protected $backendSession;

    /**
     * @param Mailer $mailer
     * @param Context $context
     * @param Session $backendSession
     * @param array $data
     */
    public function __construct(
        Mailer $mailer,
        Context $context,
        Session $backendSession,
        array $data = []
    ) {
        $this->mailer = $mailer;
        $this->backendSession = $backendSession;
        parent::__construct($context, $data);
    }

    /**
     * Additional constructor
     * @codingStandardsIgnoreStart
     */
    protected function _construct()
    {
        $data = $this->getRequestData();
        $this->saveToSession($data);
        $this->setMailerOutput($data);
    }

    /**
     * @param array $data
     *
     * @throws Exception
     */
    private function setMailerOutput(array $data)
    {
        $this->mailer->setData($data);
        $text = $this->mailer->getHtml();
        $text .= '<div class="meta-info">Subject: ' . $this->mailer->getSubject() . '</div>';

        $this->setText($text);
    }

    /**
     * @return array
     */
    private function getRequestData(): array
    {
        $data = [];
        $data['store_id'] = (int)$this->_request->getParam('store_id');
        $data['customer_id'] = (int)$this->_request->getParam('customer_id');
        $data['product_id'] = (int)$this->_request->getParam('product_id');
        $data['order_id'] = (int)$this->_request->getParam('order_id');
        $data['template'] = (string)$this->_request->getParam('template');
        $data['email'] = (string)$this->_request->getParam('email');
        $data['sender'] = (string)$this->_request->getParam('sender');

        return $data;
    }

    /**
     * @param array $data
     */
    private function saveToSession(array $data)
    {
        $this->backendSession->setEmailtesterValues($data);
    }
}
