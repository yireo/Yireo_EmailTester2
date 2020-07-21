<?php

declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Integration\Controller\Adminhtml\Ajax;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\LocalizedException;

/**
 * @magentoAppArea adminhtml
 */
class CustomerSearchTest extends AbstractAjaxTestCase
{
    /**
     * Setup method
     * @throws AuthenticationException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->uri = 'backend/emailtester/ajax/customerSearch';
    }

    /**
     * Test whether the page contains invalid content
     *
     * @magentoAppArea adminhtml
     */
    public function testCustomerDataNotFound()
    {
        $data = $this->getDataFromUrl();
        $this->assertEmpty($data);
    }

    /**
     * Test whether the page contains valid body content
     *
     * @magentoAppArea adminhtml
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @throws LocalizedException
     */
    public function testCustomerDataFound()
    {
        $customer = $this->getCustomer();
        $data = $this->getDataFromUrl();
        $this->assertNotEmpty($data);

        $row = array_shift($data);
        $this->assertEquals($customer->getId(), $row['id']);
        $this->assertEquals($customer->getEmail(), $row['email']);
    }

    /**
     * Test whether the page contains valid body content
     *
     * @magentoAppArea adminhtml
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @throws LocalizedException
     */
    public function testCustomerDataFoundByEmail()
    {
        $customer = $this->getCustomer();
        $this->getRequest()->setParam('search', $customer->getEmail());
        $data = $this->getDataFromUrl();
        $this->assertNotEmpty($data);

        $row = array_shift($data);
        $this->assertEquals($customer->getId(), $row['id']);
        $this->assertEquals($customer->getEmail(), $row['email']);
    }

    /**
     * Test whether the page contains valid body content
     *
     * @magentoAppArea adminhtml
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @throws LocalizedException
     */
    public function testCustomerDataNotFoundByEmail()
    {
        $this->getRequest()->setParam('search', 'something-not-existing@example.com');
        $data = $this->getDataFromUrl();
        $this->assertEmpty($data);
    }

    /**
     * Test whether the page contains valid body content with a search for firstname
     *
     * @magentoAppArea adminhtml
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @throws LocalizedException
     */
    public function testCustomerDataFoundByFirstname()
    {
        $customer = $this->getCustomer();
        $this->getRequest()->setParam('search', $customer->getFirstname());
        $data = $this->getDataFromUrl();
        $this->assertNotEmpty($data);

        $row = array_shift($data);
        $this->assertEquals($customer->getId(), $row['id']);
        $this->assertEquals($customer->getEmail(), $row['email']);
    }

    /**
     * Test whether the page contains valid body content with a search for lastname
     *
     * @magentoAppArea adminhtml
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @throws LocalizedException
     */
    public function testCustomerDataFoundByLastname()
    {
        $customer = $this->getCustomer();
        $this->getRequest()->setParam('search', $customer->getLastname());
        $data = $this->getDataFromUrl();
        $this->assertNotEmpty($data);

        $row = array_shift($data);
        $this->assertEquals($customer->getId(), $row['id']);
        $this->assertEquals($customer->getEmail(), $row['email']);
    }

    /**
     * Test whether the page contains valid body content with a full name search
     *
     * @magentoAppArea adminhtml
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @throws LocalizedException
     */
    public function testCustomerDataFoundByFullname()
    {
        $customer = $this->getCustomer();
        $fullname = $customer->getFirstname() . ' ' . $customer->getLastname();
        $this->getRequest()->setParam('search', $fullname);
        $data = $this->getDataFromUrl();
        $this->assertNotEmpty($data);

        $row = array_shift($data);
        $this->assertEquals($customer->getId(), $row['id']);
        $this->assertEquals($customer->getEmail(), $row['email']);
    }
}
