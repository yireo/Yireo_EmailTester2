<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Integration\Controller\Adminhtml\Index;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Yireo\EmailTester2\Model\Mailer\Variable\Customer as CustomerVariable;
use Yireo\EmailTester2\Test\Integration\Stub\StrayPagePaymentHelper;

/**
 * @magentoAppArea adminhtml
 */
class PreviewTest extends AbstractBackendController
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = 'Yireo_EmailTester2::preview';
        $this->uri = 'backend/emailtester/index/preview';
    }

    protected function tearDown(): void
    {
        /** @var ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->removeSharedInstance(PaymentHelper::class);
        parent::tearDown();
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Sales/_files/invoice.php
     */
    public function testPreviewRendersOrderEmail()
    {
        $body = $this->dispatchPreview('sales_email_order_template');

        $this->assertStringContainsString('class="meta-info"', $body);
        $this->assertStringContainsString('100000001', $body);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Sales/_files/invoice.php
     */
    public function testPreviewSurvivesPaymentMethodCreatingStrayPage()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->addSharedInstance(
            $objectManager->create(StrayPagePaymentHelper::class),
            PaymentHelper::class
        );

        $body = $this->dispatchPreview('sales_email_order_template');

        $this->assertStringContainsString(StrayPagePaymentHelper::PAYMENT_HTML, $body);
        $this->assertStringContainsString('class="meta-info"', $body);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Sales/_files/invoice.php
     */
    public function testAnonymousRequestIsForwardedToLogin()
    {
        $this->_auth->logout();

        $this->setPreviewParams('sales_email_order_template');
        $this->dispatch($this->uri);
        $body = (string)$this->getResponse()->getBody();

        $this->assertSame('login', $this->getRequest()->getActionName());
        $this->assertStringNotContainsString('class="meta-info"', $body);
        $this->assertStringNotContainsString('100000001', $body);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Sales/_files/invoice.php
     */
    public function testPreviewIsServedInSandbox()
    {
        $this->dispatchPreview('sales_email_order_template');

        $header = $this->getResponse()->getHeader('Content-Security-Policy');
        $this->assertNotFalse($header);
        $this->assertStringStartsWith('sandbox', $header->getFieldValue());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Sales/_files/invoice.php
     */
    public function testPreviewWithUnknownOrderDoesNotFallBackToOtherOrder()
    {
        $body = $this->dispatchPreview('sales_email_order_template', ['order_id' => 999999]);

        $this->assertStringContainsString('Order with ID 999999 does not exist', $body);
        $this->assertStringNotContainsString('100000001', $body);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Sales/_files/invoice.php
     */
    public function testPreviewDoesNotLeakRealPasswordResetToken()
    {
        $objectManager = Bootstrap::getObjectManager();
        $customerId = (int)$objectManager->get(CustomerRepositoryInterface::class)
            ->get('customer@example.com')
            ->getId();
        $customerRegistry = $objectManager->get(CustomerRegistry::class);
        $customerModel = $customerRegistry->retrieve($customerId);
        $customerResource = $objectManager->get(CustomerResource::class);
        $customerResource->changeResetPasswordLinkToken($customerModel, 'real-secret-reset-token');
        $customerResource->save($customerModel);
        $customerRegistry->remove($customerId);

        $body = $this->dispatchPreview('customer_password_forgot_email_template');

        $this->assertStringNotContainsString('real-secret-reset-token', $body);
        $this->assertStringContainsString(CustomerVariable::DUMMY_RP_TOKEN, $body);
    }

    private function dispatchPreview(string $templateId, array $overrides = []): string
    {
        $this->setPreviewParams($templateId, $overrides);
        $this->dispatch($this->uri);

        $body = (string)$this->getResponse()->getBody();
        $this->assertNotEmpty($body, 'Preview response should not be empty');

        return $body;
    }

    private function setPreviewParams(string $templateId, array $overrides = []): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $order = $objectManager->get(OrderInterfaceFactory::class)->create()->loadByIncrementId('100000001');
        $customer = $objectManager->get(CustomerRepositoryInterface::class)->get('customer@example.com');
        $product = $objectManager->get(ProductRepositoryInterface::class)->get('simple');

        $this->getRequest()->setParams([
            'template' => $templateId,
            'store_id' => 1,
            'order_id' => $order->getEntityId(),
            'customer_id' => $customer->getId(),
            'product_id' => $product->getId(),
        ]);
        $this->getRequest()->setParams(array_merge($this->getRequest()->getParams(), $overrides));
    }
}
