<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Integration\Controller\Adminhtml;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;

use Yireo\EmailTester2\Model\Backend\Source\Email;

/**
 * @magentoAppArea adminhtml
 */
class PreviewTest extends AbstractBackendController
{
    /**
     * Setup method
     *
     * @codingStandardsIgnoreStart
     * @magentoConfigFixture current_store emailtester2/settings/default_transactional customer_create_account_email_template
     */
    public function setUp()
    {
        $this->resource = 'Yireo_EmailTester2::index';
        $this->uri = 'backend/emailtester/index/preview';

        parent::setUp();
    }

    /**
     * Test ACL access
     */
    public function testAclHasAccess()
    {
        $postData = [
            'template' => 'customer_create_account_email_template',
        ];
        $this->getRequest()->setParams($postData);

        parent::testAclHasAccess();
    }

    /**
     * Test whether the page contains valid body content
     *
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @magentoDataFixture Magento/Sales/_files/invoice.php
     * @magentoAppArea adminhtml
     *
     * @dataProvider getTemplateIds
     */
    public function testValidPreviewPageContent($templateId)
    {
        $this->assertValidPreviewPage((string)$templateId);
    }

    /**
     * @param string $templateId
     *
     * @return bool
     */
    private function assertValidPreviewPage(string $templateId): bool
    {
        $postData = [
            'template' => $templateId,
        ];

        $this->getRequest()->setParams($postData);

        try {
            $this->dispatch($this->uri);
        } catch (\Exception $e) {
            $message = 'Error while fetching template '.$templateId.': '.$e->getMessage();
            $this->assertFalse(true, $message);
            return false;
        }

        $body = $this->getResponse()->getBody();

        $needle = 'emailtester-index-preview';
        $message = sprintf('Template %s should contain keyword %s', $templateId, $needle);
        $this->assertContains($needle, $body, $message);
        return true;
    }

    /**
     * @return array
     */
    public function getTemplateIds(): array
    {
        $objectManager = Bootstrap::getObjectManager();
        $emailSource = $objectManager->get(Email::class);
        $emailTemplates = $emailSource->toOptionArray();
        $templateIds = [];

        foreach ($emailTemplates as $emailTemplate) {
            $templateId = (string)$emailTemplate['value'];
            $templateId = preg_replace('/\/(.*)/', '', $templateId);

            if (empty($templateId)) {
                continue;
            }

            $templateIds[] = $templateId;
        }

        $templateIds = array_unique($templateIds);
        $this->assertNotEmpty($templateIds);

        $options = [];
        foreach ($templateIds as $templateId) {
            $options[] = [$templateId];
        }

        return $options;
    }
}
