<?php
namespace Yireo\EmailTester2\Test\Integration;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Framework\App\Config\MutableScopeConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class CheckFormTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    protected function setUp()
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
        $config->setValue('emailtester2/settings/default_email', 'dummy@example.com', ScopeConfigInterface::SCOPE_TYPE_DEFAULT);

        //$this->getRequest()->setParam('block', 'tab_orders');
        $this->dispatch('backend/emailtester/index');

        $body = $this->getResponse()->getBody();
        $this->assertContains('Yireo Email Tester', $body);
        $this->assertContains('Send Email', $body);
        //$this->assertContains('dummy@example.com', $body);
    }
}