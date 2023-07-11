<?php declare(strict_types=1);
/**
 * EmailTester2 plugin for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

namespace Yireo\EmailTester2\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Yireo\EmailTester2\Model\Mailer;
use Yireo\EmailTester2\ViewModel\Form;

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
     * @var MessageManager
     */
    private $messageManager;

    /**
     * @var Form
     */
    private $formViewModel;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Session
     */
    private $backendSession;

    /**
     * @param Context $context
     * @param RedirectFactory $redirectFactory
     * @param MessageManager $messageManager
     * @param Mailer $mailer
     * @param Form $formViewModel
     * @param RequestInterface $request
     * @param Session $backendSession
     */
    public function __construct(
        Context $context,
        RedirectFactory $redirectFactory,
        MessageManager $messageManager,
        Mailer $mailer,
        Form $formViewModel,
        RequestInterface $request,
        Session $backendSession
    ) {
        parent::__construct($context);
        $this->redirectFactory = $redirectFactory;
        $this->mailer = $mailer;
        $this->messageManager = $messageManager;
        $this->formViewModel = $formViewModel;
        $this->request = $request;
        $this->backendSession = $backendSession;
    }

    /**
     * Index action
     *
     * @return ResultInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        if ($this->hasValidData() === false) {
            $this->messageManager->addWarningMessage(
                'You have not added the required customers, products or orders yet'
            );
            return $this->redirectFactory->create()->setPath('*/*/index');
        }

        $data = $this->getRequestData();
        $this->saveToSession($data);

        $this->mailer->setData($data);
        $this->mailer->send();

        $this->messageManager->addNoticeMessage('Message sent to ' . $data['email']);
        $redirect = $this->redirectFactory->create();
        $redirect->setPath('*/*/index', ['form_id' => 0]);

        return $redirect;
    }

    /**
     * @return bool
     * @throws LocalizedException
     */
    private function hasValidData(): bool
    {
        if ($this->formViewModel->hasCustomers() === false) {
            return false;
        }

        if ($this->formViewModel->hasProducts() === false) {
            return false;
        }

        if ($this->formViewModel->hasOrders() === false) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    private function getRequestData(): array
    {
        $data = [];
        $data['store_id'] = (int)$this->request->getParam('store_id');
        $data['customer_id'] = (int)$this->request->getParam('customer_id');
        $data['product_id'] = (int)$this->request->getParam('product_id');
        $data['order_id'] = (int)$this->request->getParam('order_id');
        $data['template'] = (string)$this->request->getParam('template');
        $data['email'] = (string)$this->request->getParam('email');
        $data['sender'] = (string)$this->request->getParam('sender');

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
