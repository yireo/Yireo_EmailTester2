<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types = 1);

namespace Yireo\EmailTester2\Block\Adminhtml;

/**
 * Class Preview
 *
 * @package Yireo\EmailTester2\Block\Adminhtml
 */
class Preview extends \Magento\Framework\View\Element\Text
{
    /**
     * @var \Yireo\EmailTester2\Model\Mailer
     */
    private $mailer;

    /**
     * @param \Yireo\EmailTester2\Model\Mailer $mailer
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Yireo\EmailTester2\Model\Mailer $mailer,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {

        $this->mailer = $mailer;
        parent::__construct($context, $data);
    }

    /**
     * Additional constructor
     */
    protected function _construct()
    {
        $data = $this->getRequestData();
        $this->saveToSession($data);
        $this->setMailerOutput($data);
    }

    /**
     * @param array $data
     */
    private function setMailerOutput(array $data)
    {
        $this->mailer->setData($data);
        $text = $this->mailer->getHtml();
        $this->setText($text);
    }

    /**
     * @return array
     */
    private function getRequestData() : array
    {
        $data = [];
        $data['store_id'] = (int) $this->_request->getParam('store_id');
        $data['customer_id'] = (int) $this->_request->getParam('customer_id');
        $data['product_id'] = (int) $this->_request->getParam('product_id');
        $data['order_id'] = (int) $this->_request->getParam('order_id');
        $data['template'] = $this->_request->getParam('template');
        $data['email'] = $this->_request->getParam('email');

        return $data;
    }

    /**
     * @param array $data
     */
    private function saveToSession(array $data)
    {
        $this->_session->setEmailtesterValues($data);
    }
}
