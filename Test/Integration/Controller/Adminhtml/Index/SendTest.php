<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Integration\Controller\Adminhtml\Index;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea adminhtml
 */
class SendTest extends AbstractBackendController
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = 'Yireo_EmailTester2::send';
        $this->uri = 'backend/emailtester/index/send';
        $this->httpMethod = HttpRequest::METHOD_POST;
    }

    public function testGetRequestIsRejected()
    {
        $this->getRequest()->setMethod(HttpRequest::METHOD_GET);
        $this->dispatch($this->uri);

        $this->assertSame(404, $this->getResponse()->getHttpResponseCode());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Sales/_files/invoice.php
     */
    public function testInvalidSenderIsRejected()
    {
        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
        $this->getRequest()->setPostValue([
            'form_key' => $this->_objectManager->get(FormKey::class)->getFormKey(),
            'template' => 'sales_email_order_template',
            'store_id' => 1,
            'mail_from' => 'not-an-email-address',
            'mail_to' => 'recipient@example.com',
        ]);

        $this->dispatch($this->uri);

        $this->assertRedirect();
        $this->assertSessionMessages(
            $this->callback(
                fn (array $messages) => str_contains(implode(' ', $messages), 'Invalid email address')
            ),
            MessageInterface::TYPE_ERROR
        );
    }
}
