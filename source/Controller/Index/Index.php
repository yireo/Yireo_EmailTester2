<?php
namespace Yireo\EmailTester2\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        $this->view->loadLayout();
        $this->view->getLayout()->initMessages();
        $this->view->renderLayout();
    }
}
