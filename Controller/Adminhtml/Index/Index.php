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
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Module\ModuleList;
use Magento\Framework\View\Result\PageFactory;
use Yireo\EmailTester2\Config\Config;
use Yireo\EmailTester2\ViewModel\Form;

class Index implements HttpGetActionInterface
{
    /**
     * ACL resource
     */
    const ADMIN_RESOURCE = 'Yireo_EmailTester2::index';

    public function __construct(
        private PageFactory $resultPageFactory,
        private ManagerInterface $messageManager,
        private Form $formViewModel,
        private ComponentRegistrar $componentRegistrar,
        private ModuleList $moduleList
    ) {
    }

    /**
     * Index action
     *
     * @return Page
     * @throws LocalizedException
     */
    public function execute(): Page
    {
        if ($this->formViewModel->hasCustomers() === false) {
            $this->messageManager->addWarningMessage(__('Please add some customers to your shop first'));
        }

        if ($this->formViewModel->hasProducts() === false) {
            $this->messageManager->addWarningMessage(__('Please add some products to your shop first'));
        }

        if ($this->formViewModel->hasOrders() === false) {
            $this->messageManager->addWarningMessage(__('Please add some orders to your shop first'));
        }

        $this->checkForDependencies();

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Yireo_EmailTester2::index');
        $resultPage->addBreadcrumb(__('Yireo EmailTester'), __('Yireo EmailTester'));
        $resultPage->getConfig()->getTitle()->prepend(__('Yireo EmailTester'));

        return $resultPage;
    }

    private function checkForDependencies()
    {
        $modules = [
            'Loki_Base',
            'Loki_CssUtils',
            'Loki_Components',
            'Loki_AdminComponents',
        ];

        foreach ($modules as $module) {
            $path = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, $module);
            if (empty($path)) {
                $msg = __('Module "%1" is required but is not installed', $module);
                $this->messageManager->addErrorMessage($msg);
            }

            $moduleList = $this->moduleList->getOne($module);
            if (empty($moduleList)) {
                $msg = __('Module "%1" is required but is not enabled', $module);
                $this->messageManager->addErrorMessage($msg);
            }
        }
    }
}
