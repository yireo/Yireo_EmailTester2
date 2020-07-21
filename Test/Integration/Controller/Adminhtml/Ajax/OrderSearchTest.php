<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Integration\Controller\Adminhtml\Ajax;

use Magento\Framework\Exception\AuthenticationException;

/**
 * @magentoAppArea adminhtml
 */
class OrderSearchTest extends AbstractAjaxTestCase
{
    /**
     * Setup method
     * @throws AuthenticationException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->uri = 'backend/emailtester/ajax/orderSearch';
    }

    /**
     * Test whether the page contains invalid content
     *
     * @magentoAppArea adminhtml
     */
    public function testOrderDataNotFound()
    {
        $data = $this->getDataFromUrl();
        $this->assertEmpty($data);
    }

    /**
     * Test whether the page contains valid body content
     *
     * @magentoAppArea adminhtml
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testOrderDataFound()
    {
        $order = $this->getOrder();
        $data = $this->getDataFromUrl();
        $this->assertNotEmpty($data);

        $row = array_shift($data);
        $this->assertEquals($order->getId(), $row['id']);
        $this->assertEquals($order->getIncrementId(), $row['increment_id']);
        $this->assertEquals($order->getCustomerEmail(), $row['customer_email']);
    }
}
