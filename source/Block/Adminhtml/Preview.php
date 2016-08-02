<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

namespace Yireo\EmailTester2\Block\Adminhtml;

/**
 * Class Preview
 *
 * @package Yireo\EmailTester2\Block\Adminhtml
 */
class Preview extends \Magento\Framework\View\Element\Text
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $adminSession;

    /**
     * @var \Yireo\EmailTester2\Model\Mailer
     */
    protected $mailer;

    /**
     * @param \Yireo\EmailTester2\Model\Mailer $mailer
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session $adminSession
     * @param array $data
     */
    public function __construct(
        \Yireo\EmailTester2\Model\Mailer $mailer,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session $adminSession,
        array $data = []
    )
    {
        $this->adminSession = $adminSession;
        $this->mailer = $mailer;
        parent::__construct($context, $data);

        $data = $this->getRequestData();
        $this->saveToSession($data);
        $this->setMailerOutput($data);
    }

    /**
     * @param $data
     */
    protected function setMailerOutput($data)
    {
        $this->mailer->setData($data);
        $text = $this->mailer->getHtml();
        $this->setText($text);
    }

    /**
     * @return array
     */
    protected function getRequestData()
    {
        $data = array();
        $data['customer_id'] = $this->_request->getParam('customer_id');
        $data['product_id'] = $this->_request->getParam('product_id');
        $data['order_id'] = $this->_request->getParam('order_id');
        $data['template'] = $this->_request->getParam('template');
        $data['email'] = $this->_request->getParam('email');

        return $data;
    }

    /**
     * @param $data
     */
    protected function saveToSession($data)
    {
        $this->adminSession->setEmailtesterValues($data);
    }
}
