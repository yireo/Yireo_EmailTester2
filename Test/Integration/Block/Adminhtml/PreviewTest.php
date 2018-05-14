<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Integration\Block\Adminhtml;

use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea adminhtml
 */
class PreviewTest extends AbstractBackendController
{
    /**
     * Setup method
     */
    public function setUp()
    {
        $this->resource = 'Yireo_EmailTester2::index';
        $this->uri = 'backend/emailtester/preview';
        parent::setUp();
    }

    /**
     * Test whether the page contains valid body content
     */
    public function testValidBodyContent()
    {
        $this->dispatch($this->uri);
        $body = $this->getResponse()->getBody();
        $this->assertContains('test', $body);
    }
}
