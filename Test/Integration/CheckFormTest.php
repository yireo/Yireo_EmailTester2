<?php
namespace Yireo\EmailTester2\Test\Integration;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Framework\App\Config\MutableScopeConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;

class CheckFormTest extends AbstractBackendController
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->uri = 'backend/emailtester/index';
        $this->resource = 'Yireo_EmailTester2::index';
    }

    /**
     * @magentoConfigFixture default/emailtester2/settings/default_email dummy@example.com
     */
    public function testFormDisplays()
    {
        /** @var \Magento\TestFramework\Config $config */
        $config = Bootstrap::getObjectManager()->create(MutableScopeConfigInterface::class);
        $configPath = 'emailtester2/settings/default_email';
        $config->setValue($configPath, 'dummy@example.com', ScopeConfigInterface::SCOPE_TYPE_DEFAULT);

        //$this->getRequest()->setParam('block', 'tab_orders');
        $this->dispatch('backend/emailtester/index');

        $body = $this->getResponse()->getBody();
        $this->assertTrue((bool)strpos($body, 'Yireo EmailTester'));
        $this->assertTrue((bool)strpos($body, 'Send Email'));
    }
}
