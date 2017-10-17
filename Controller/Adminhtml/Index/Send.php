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

namespace Yireo\EmailTester2\Controller\Adminhtml\Index;

/**
 * Class Index
 *
 * @package Yireo\EmailTester2\Controller\Index
 */
class Send extends \Magento\Backend\App\Action
{
    /**
     * ACL resource
     */
    const ADMIN_RESOURCE = 'Yireo_EmailTester2::index';

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    private $redirectFactory;

    /**
     * @var \Yireo\EmailTester2\Model\Mailer
     */
    private $mailer;

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
    private function getRequestData(): array
    {
        $data = [];
        $data['store_id'] = (int) $this->_request->getParam('store_id');
        $data['customer_id'] = (int) $this->_request->getParam('customer_id');
        $data['product_id'] = (int) $this->_request->getParam('product_id');
        $data['order_id'] = (int) $this->_request->getParam('order_id');
        $data['template'] = (string) $this->_request->getParam('template');
        $data['email'] = (string) $this->_request->getParam('email');

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
