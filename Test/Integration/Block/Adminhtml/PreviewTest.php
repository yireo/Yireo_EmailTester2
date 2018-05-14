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
     *
     * @magentoConfigFixture current_store emailtester2/settings/default_email info@example.com
     * @magentoConfigFixture current_store emailtester2/settings/default_transactional customer_create_account_email_template
     *
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testValidBodyContent()
    {
        $this->dispatch($this->uri);
        $body = $this->getResponse()->getBody();
        $this->assertContains('test', $body);
    }
}
