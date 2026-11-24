<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Integration\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\EmailTester2\Model\Mailer;

/**
 * @magentoAppArea adminhtml
 */
class MailerTest extends TestCase
{
    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Sales/_files/invoice.php
     * @magentoConfigFixture current_store trans_email/ident_sales/email sales@example.com
     */
    public function testConfiguredStoreIdentityIsAllowedAsSender()
    {
        $mailer = $this->createMailer('SALES@example.com');

        $this->assertNotEmpty($mailer->getHtml());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Sales/_files/invoice.php
     * @magentoConfigFixture default/emailtester2/settings/default_sender tester@example.com
     */
    public function testConfiguredDefaultSenderIsAllowedAsSender()
    {
        $mailer = $this->createMailer('tester@example.com');

        $this->assertNotEmpty($mailer->getHtml());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Sales/_files/invoice.php
     */
    public function testUnlistedSenderIsRejected()
    {
        $mailer = $this->createMailer('ceo@attacker.example');

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('is not one of the email addresses configured for this store');
        $mailer->getHtml();
    }

    private function createMailer(string $sender): Mailer
    {
        $objectManager = Bootstrap::getObjectManager();
        $order = $objectManager->get(OrderInterfaceFactory::class)->create()->loadByIncrementId('100000001');

        $mailer = $objectManager->create(Mailer::class);
        $mailer->setData([
            'template' => 'sales_email_order_template',
            'store_id' => 1,
            'order_id' => (int)$order->getEntityId(),
            'email' => 'recipient@example.com',
            'sender' => $sender,
        ]);

        return $mailer;
    }
}
