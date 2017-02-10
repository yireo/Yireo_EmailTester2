<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

namespace Yireo\EmailTester2\Controller\Adminhtml\Index;

/**
 * Class Index
 *
 * @package Yireo\EmailTester2\Controller\Index
 */
class Send extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Yireo_EmailTester2::index';

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $redirectFactory;

    /**
     * @var \Yireo\EmailTester2\Model\Mailer
     */
    protected $mailer;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
     * @param \Yireo\EmailTester2\Model\Mailer $mailer
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory,

        \Yireo\EmailTester2\Model\Mailer $mailer
    ) {
        $this->redirectFactory = $redirectFactory;
        $this->mailer = $mailer;

        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequestData();
        $this->saveToSession($data);

        $this->mailer->setData($data);
        $this->mailer->send();

        $redirect = $this->redirectFactory->create();
        $redirect->setPath('*/*/index');

        return $redirect;
    }

    /**
     * @return array
     */
    protected function getRequestData()
    {
        $data = array();
        $data['store_id'] = $this->_request->getParam('store_id');
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
        $this->_session->setEmailtesterValues($data);
    }
}
