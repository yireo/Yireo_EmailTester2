<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Integration\Controller\Adminhtml\Ajax;

use Magento\Framework\Exception\AuthenticationException;

/**
 * @magentoAppArea adminhtml
 */
class ProductSearchTest extends AbstractAjaxTestCase
{
    /**
     * Setup method
     * @throws AuthenticationException
     */
    public function setUp()
    {
        $this->uri = 'backend/emailtester/ajax/productSearch';
        parent::setUp();
    }

    /**
     * Test whether the page contains invalid content
     *
     * @magentoAppArea adminhtml
     */
    public function testProductDataNotFound()
    {
        $data = $this->getDataFromUrl();
        $this->assertEmpty($data);
    }

    /**
     * Test whether the page contains valid body content
     *
     * @magentoAppArea adminhtml
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testProductDataFound()
    {
        $product = $this->getProduct();
        $data = $this->getDataFromUrl();
        $this->assertNotEmpty($data);

        $row = array_shift($data);
        $this->assertEquals($product->getId(), $row['id']);
        $this->assertEquals($product->getSku(), $row['sku']);
    }

    /**
     * Test whether the page contains valid body content
     *
     * @magentoAppArea adminhtml
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testProductNotFoundBySku()
    {
        $this->getRequest()->setParam('search', 'non-existing-sku');
        $data = $this->getDataFromUrl();
        $this->assertEmpty($data);
    }

    /**
     * Test whether the page contains valid body content
     *
     * @magentoAppArea adminhtml
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testProductFoundBySku()
    {
        $product = $this->getProduct();
        $this->getRequest()->setParam('search', $product->getSku());
        $data = $this->getDataFromUrl();
        $this->assertNotEmpty($data);

        $row = array_shift($data);
        $this->assertEquals($product->getId(), $row['id']);
        $this->assertEquals($product->getSku(), $row['sku']);
    }
}
