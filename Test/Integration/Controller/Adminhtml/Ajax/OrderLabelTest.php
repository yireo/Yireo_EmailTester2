<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Integration\Controller\Adminhtml\Ajax;

use Magento\Framework\Exception\AuthenticationException;

/**
 * @magentoAppArea adminhtml
 */
class OrderLabelTest extends AbstractAjaxTestCase
{
    /**
     * Setup method
     * @throws AuthenticationException
     */
    public function setUp()
    {
        $this->uri = 'backend/emailtester/ajax/orderLabel';
        parent::setUp();
    }

    /**
     * Test whether the page contains invalid content
     *
     * @magentoAppArea adminhtml
     */
    public function testOrderDataNotFound()
    {
        $data = $this->getDataFromUrl();
        $this->assertEquals(0, $data['id']);
        $this->assertEquals('No order found', $data['label']);
    }

    /**
     * Test whether the page contains valid body content
     *
     * @magentoAppArea adminhtml
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testOrderDataFound()
    {
        $orderId = $this->getOrder()->getId();
        $this->getRequest()->setParam('id', $orderId);
        $data = $this->getDataFromUrl();
        $this->assertEquals($orderId, $data['id']);
        $this->assertNotEquals('No order found', $data['label']);
    }
}
