<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

namespace Yireo\EmailTester2\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Result\PageFactory;
use Yireo\EmailTester2\Block\Adminhtml\Preview as PreviewBlock;

/**
 * Class Index
 *
 * @package Yireo\EmailTester2\Controller\Index
 */
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
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Config $pageConfig
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Config $pageConfig
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->pageConfig = $pageConfig;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
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
     * @return string
     */
    private function getTemplateName(): string
    {
        return (string)$this->_request->getParam('template');
    }
}
