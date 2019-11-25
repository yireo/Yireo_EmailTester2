<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

namespace Yireo\EmailTester2\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface as MessageManager;
use Magento\Store\Model\StoreManagerInterface;
use Yireo\EmailTester2\Model\Mailer;
use Yireo\EmailTester2\ViewModel\Form;

/**
 * Class Index
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
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var Form
     */
    private $formViewModel;

    /**
     * @param Context $context
     * @param RedirectFactory $redirectFactory
     * @param MessageManager $messageManager
     * @param Mailer $mailer
     * @param StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepository
     * @param Form $formViewModel
     */
    public function __construct(
        Context $context,
        RedirectFactory $redirectFactory,
        MessageManager $messageManager,
        Mailer $mailer,
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        Form $formViewModel
    ) {
        parent::__construct($context);
        $this->redirectFactory = $redirectFactory;
        $this->mailer = $mailer;
        $this->messageManager = $messageManager;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->formViewModel = $formViewModel;
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
            $this->messageManager->addWarningMessage('You have not added the required customers, products or orders yet');
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
        $this->_session->setEmailtesterValues($data);
    }
}
