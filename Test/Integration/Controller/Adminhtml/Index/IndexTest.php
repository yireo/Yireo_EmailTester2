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
    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = 'Yireo_EmailTester2::index';
        $this->uri = 'backend/emailtester/index/index';
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
        $this->assertTrue((bool)strpos($body, 'Send Email'));
        $this->assertTrue((bool)strpos($body, 'Preview Email'));
        $this->assertTrue((bool)strpos($body, 'Store View'));

        $this->analyseDatabaseStatistics(['select' => 100]);
    }
}
