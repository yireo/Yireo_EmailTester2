<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Integration\Controller\Adminhtml\Index;

use Magento\TestFramework\TestCase\AbstractBackendController;
use Yireo\EmailTester2\Test\Integration\Behaviors\CheckDatabaseStatistics;

/**
 * @magentoAppArea adminhtml
 */
class IndexTest extends AbstractBackendController
{
    use CheckDatabaseStatistics;

    /**
     * Setup method
     */
    public function setUp()
    {
        $this->resource = 'Yireo_EmailTester2::index';
        $this->uri = 'backend/emailtester/index/index';
        parent::setUp();
    }

    /**
     * Test whether the page contains valid body content
     *
     * @magentoAppArea adminhtml
     */
    public function testValidBodyContent()
    {
        $this->startDatabaseStatistics();

        $this->dispatch($this->uri);
        $body = $this->getResponse()->getBody();
        $this->assertContains('Send Email', $body);
        $this->assertContains('Preview Email', $body);
        $this->assertContains('Store View', $body);

        $this->analyseDatabaseStatistics(['select' => 100]);
    }
}
