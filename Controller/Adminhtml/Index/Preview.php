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
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Result\PageFactory;
use Yireo\EmailTester2\Block\Adminhtml\Preview as PreviewBlock;
use Yireo\EmailTester2\ViewModel\Form;

class Preview extends Action
{
    /**
     * ACL resource
     */
    const ADMIN_RESOURCE = 'Yireo_EmailTester2::index';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var Config
     */
    private $pageConfig;

    /**
     * @var Form
     */
    private $formViewModel;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Config $pageConfig
     * @param Form $formViewModel
     * @param RequestInterface $request
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Config $pageConfig,
        Form $formViewModel,
        RequestInterface $request
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->pageConfig = $pageConfig;
        $this->formViewModel = $formViewModel;
        $this->request = $request;
    }

    /**
     * Index action
     *
     * @return ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        if ($this->hasValidData() === false) {
            $msg = 'You have not added the required customers, products or orders yet';
            $this->messageManager->addWarningMessage(__($msg));
            return $this->resultRedirectFactory->create()->setPath('*/*/index');
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $this->pageConfig->getTitle()->set($this->getTemplateName());
        $layout = $resultPage->getLayout();

        $update = $layout->getUpdate();
        $update->removeHandle('default');
        $update->addHandle('emailtester_index_preview');

        /** @var $previewBlock PreviewBlock */
        $previewBlock = $layout->createBlock(PreviewBlock::class);
        $resultPage->addContent($previewBlock);

        return $resultPage;
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
     * @return string
     */
    private function getTemplateName(): string
    {
        return (string)$this->request->getParam('template');
    }
}
