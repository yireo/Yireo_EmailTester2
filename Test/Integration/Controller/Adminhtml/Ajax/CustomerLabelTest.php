<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Integration\Controller\Adminhtml\Ajax;

use Magento\Framework\Exception\AuthenticationException;

/**
 * @magentoAppArea adminhtml
 */
class CustomerLabelTest extends AbstractAjaxTestCase
{
    /**
     * Setup method
     * @throws AuthenticationException
     */
    public function setUp()
    {
        $this->uri = 'backend/emailtester/ajax/customerLabel';
        parent::setUp();
    }

    /**
     * Test whether the page contains invalid content
     *
     * @magentoAppArea adminhtml
     */
    public function testCustomerDataNotFound()
    {
        $data = $this->getDataFromUrl();
        $this->assertEquals(0, $data['id']);
        $this->assertEquals('No customer data found', $data['label']);
    }

    /**
     * Test whether the page contains valid body content
     *
     * @magentoAppArea adminhtml
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testCustomerDataFound()
    {
        $this->getRequest()->setParam('id', 1);
        $data = $this->getDataFromUrl();
        $this->assertTrue((int)$data['id'] > 0);
        $this->assertNotEquals('No customer data found', $data['label']);
    }
}
