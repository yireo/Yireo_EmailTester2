<?php
declare(strict_types=1);
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
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\BlockFactory;
use Yireo\EmailTester2\Block\Adminhtml\Preview as PreviewBlock;
use Yireo\EmailTester2\ViewModel\Form;

class Preview extends Action
{
    /**
     * ACL resource
     */
    const ADMIN_RESOURCE = 'Yireo_EmailTester2::index';

    /**
     * Styles for the meta-info bar, previously loaded via a layout page
     */
    private const META_INFO_STYLE = '<style>'
        . 'body{padding-bottom:80px !important;}'
        . '.meta-info{position:fixed;bottom:0;left:0;right:0;background-color:#fff6dc;'
        . 'border:1px solid #ccc;margin-bottom:0;padding:10px;}'
        . '</style>';

    /**
     * @param Context $context
     * @param RawFactory $resultRawFactory
     * @param BlockFactory $blockFactory
     * @param Form $formViewModel
     * @param Escaper $escaper
     */
    public function __construct(
        Context $context,
        private readonly RawFactory $resultRawFactory,
        private readonly BlockFactory $blockFactory,
        private readonly Form $formViewModel,
        private readonly Escaper $escaper,
    ) {
        parent::__construct($context);
    }

    /**
     * Render the email preview as raw HTML
     *
     * A layout page is deliberately not used: rendering the email might instantiate a new page result
     * (for instance via payment method dependencies), which resets the shared layout and drops its blocks.
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

        /** @var PreviewBlock $previewBlock */
        $previewBlock = $this->blockFactory->createBlock(PreviewBlock::class);

        $result = $this->resultRawFactory->create();
        $result->setHeader('Content-Type', 'text/html; charset=utf-8');
        $result->setContents($previewBlock->getText() . $this->getTitle() . self::META_INFO_STYLE);

        return $result;
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
    private function getTitle(): string
    {
        $templateName = (string)$this->getRequest()->getParam('template');
        return '<title>' . $this->escaper->escapeHtml($templateName) . '</title>';
    }
}
