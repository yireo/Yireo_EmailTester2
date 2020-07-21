<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Integration\Controller\Adminhtml\Ajax;

use Magento\Framework\Exception\AuthenticationException;

/**
 * @magentoAppArea adminhtml
 */
class ProductLabelTest extends AbstractAjaxTestCase
{
    /**
     * Setup method
     * @throws AuthenticationException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->uri = 'backend/emailtester/ajax/productLabel';
    }

    /**
     * Test whether the page contains invalid content
     *
     * @magentoAppArea adminhtml
     */
    public function testProductDataNotFound()
    {
        $data = $this->getDataFromUrl();
        $this->assertEquals(0, $data['id']);
        $this->assertEquals('No product found', $data['label']);
    }

    /**
     * Test whether the page contains valid body content
     *
     * @magentoAppArea adminhtml
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testProductDataFound()
    {
        $this->getRequest()->setParam('id', 1);
        $data = $this->getDataFromUrl();
        $this->assertTrue((int)$data['id'] > 0);
        $this->assertNotEquals('No product found', $data['label']);
    }
}
