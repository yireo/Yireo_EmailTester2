<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Integration\Controller\Adminhtml\Index;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Yireo\EmailTester2\Test\Integration\Stub\StrayPagePaymentHelper;

/**
 * @magentoAppArea adminhtml
 */
class PreviewTest extends AbstractBackendController
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = 'Yireo_EmailTester2::index';
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

    private function dispatchPreview(string $templateId): string
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

        $this->dispatch($this->uri);

        $body = (string)$this->getResponse()->getBody();
        $this->assertNotEmpty($body, 'Preview response should not be empty');

        return $body;
    }
}
