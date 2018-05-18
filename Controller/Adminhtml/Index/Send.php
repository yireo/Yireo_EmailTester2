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

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Yireo\EmailTester2\Model\Mailer;

/**
 * Class Index
 *
 * @package Yireo\EmailTester2\Controller\Index
 */
class Send extends Action
{
    /**
     * ACL resource
     */
    const ADMIN_RESOURCE = 'Yireo_EmailTester2::index';

    /**
     * @var RedirectFactory
     */
    private $redirectFactory;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @param Context $context
     * @param RedirectFactory $redirectFactory
     * @param ManagerInterface $messageManager
     * @param Mailer $mailer
     */
    public function __construct(
        Context $context,
        RedirectFactory $redirectFactory,
        ManagerInterface $messageManager,
        Mailer $mailer
    ) {
        parent::__construct($context);
        $this->redirectFactory = $redirectFactory;
        $this->mailer = $mailer;
        $this->messageManager = $messageManager;
    }

    /**
     * Index action
     *
     * @return Redirect
     */
    public function execute()
    {
        $data = $this->getRequestData();
        $this->saveToSession($data);

        $this->mailer->setData($data);
        $this->mailer->send();

        $this->messageManager->addNoticeMessage('Message sent to '.$data['email']);
        $redirect = $this->redirectFactory->create();
        $redirect->setPath('*/*/index', ['form_id' => 0]);

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
