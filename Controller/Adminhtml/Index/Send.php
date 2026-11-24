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
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Yireo\EmailTester2\Model\Mailer;
use Yireo\EmailTester2\ViewModel\Form;

class Send extends Action implements HttpPostActionInterface
{
    /**
     * ACL resource
     */
    const ADMIN_RESOURCE = 'Yireo_EmailTester2::send';

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
    protected $messageManager;

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

        try {
            $this->mailer->setData($data);
            $sent = $this->mailer->send();
            $this->saveToSession($data);
            if ($sent) {
                $this->messageManager->addNoticeMessage(__('Message sent to %1', $data['email']));
            } else {
                $this->messageManager->addErrorMessage($this->mailer->getErrorString());
            }
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

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
        $data['email'] = (string)$this->request->getParam('mail_to');
        $data['sender'] = (string)$this->request->getParam('mail_from');

        return $data;
    }

    /**
     * @param array $data
     */
    private function saveToSession(array $data)
    {
        $sessionData = $data;
        $sessionData['mail_to'] = $data['email'];
        $sessionData['mail_from'] = $data['sender'];
        unset($sessionData['email'], $sessionData['sender']);
        $this->backendSession->setEmailtesterValues($sessionData);
    }
}
