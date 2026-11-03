<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Integration\Model\Mailer\Variable;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\EmailTester2\Model\Mailer\Variable\Creditmemo;

class CreditmemoTest extends TestCase
{
    /**
     * @magentoDataFixture Magento/Sales/_files/creditmemo_for_get.php
     */
    public function testGetVariablesReturnsExistingCreditmemoOfOrder()
    {
        $objectManager = Bootstrap::getObjectManager();
        $order = $objectManager->get(OrderInterfaceFactory::class)->create()->loadByIncrementId('100000001');

        $searchCriteria = $objectManager->create(SearchCriteriaBuilder::class)
            ->addFilter('order_id', $order->getEntityId())
            ->create();
        $existingCreditmemos = $objectManager->get(CreditmemoRepositoryInterface::class)
            ->getList($searchCriteria)
            ->getItems();
        $this->assertNotEmpty($existingCreditmemos);
        $existingCreditmemo = reset($existingCreditmemos);

        $target = $objectManager->create(Creditmemo::class);
        $target->setOrder($order);
        $variables = $target->getVariables();

        $this->assertEquals($existingCreditmemo->getEntityId(), $variables['creditmemo_id']);
    }
}
