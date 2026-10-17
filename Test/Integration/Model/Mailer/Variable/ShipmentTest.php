<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Integration\Model\Mailer\Variable;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\EmailTester2\Model\Mailer\Variable\Shipment;

class ShipmentTest extends TestCase
{
    /**
     * @magentoDataFixture Magento/Sales/_files/shipment.php
     */
    public function testGetVariablesReturnsExistingShipmentOfOrder()
    {
        $objectManager = Bootstrap::getObjectManager();
        $order = $objectManager->get(OrderInterfaceFactory::class)->create()->loadByIncrementId('100000001');

        $searchCriteria = $objectManager->create(SearchCriteriaBuilder::class)
            ->addFilter('order_id', $order->getEntityId())
            ->create();
        $existingShipments = $objectManager->get(ShipmentRepositoryInterface::class)
            ->getList($searchCriteria)
            ->getItems();
        $this->assertNotEmpty($existingShipments);
        $existingShipment = reset($existingShipments);

        $target = $objectManager->create(Shipment::class);
        $target->setOrder($order);
        $variables = $target->getVariables();

        $this->assertNotEmpty($variables['shipment_id'], 'Expected the existing shipment, not a dummy shipment');
        $this->assertEquals($existingShipment->getEntityId(), $variables['shipment_id']);
    }
}
