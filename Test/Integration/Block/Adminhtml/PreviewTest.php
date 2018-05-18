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
        $this->uri = 'backend/emailtester/index/preview';

        parent::setUp();
    }

    /**
     * Test whether the page contains valid body content
     *
     * @magentoConfigFixture current_store emailtester2/settings/default_transactional customer_create_account_email_template
     *
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @magentoDataFixture Magento/Sales/_files/invoice.php
     * @magentoAppArea adminhtml
     */
    public function testValidBodyContent()
    {
        $postData = [
            'template' => 'customer_create_account_email_template',
        ];
        $this->getRequest()->setParams($postData);

        $this->dispatch($this->uri);
        $body = $this->getResponse()->getBody();
        $this->assertContains('test', $body);
    }
}
