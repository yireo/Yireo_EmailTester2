<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Unit\Model\Mailer\Variable;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\PhraseFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\Data\ShipmentItemInterfaceFactory;
use Magento\Sales\Api\Data\ShipmentSearchResultInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Yireo\EmailTester2\Model\Mailer\Variable\Shipment;

class ShipmentTest extends TestCase
{
    /**
     * Shipment repository results are keyed by entity ID, not by a zero-based index
     */
    public function testGetVariablesReturnsExistingShipmentKeyedByEntityId()
    {
        $existingShipment = $this->createMock(ShipmentInterface::class);
        $existingShipment->method('getEntityId')->willReturn(42);

        $searchResult = $this->createMock(ShipmentSearchResultInterface::class);
        $searchResult->method('getItems')->willReturn([42 => $existingShipment]);

        $shipmentRepository = $this->createMock(ShipmentRepositoryInterface::class);
        $shipmentRepository->method('getList')->willReturn($searchResult);
        $shipmentRepository->expects($this->never())->method('create');

        $searchCriteriaBuilder = $this->createMock(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->method('addFilter')->willReturnSelf();
        $searchCriteriaBuilder->method('create')->willReturn($this->createMock(SearchCriteriaInterface::class));

        $order = $this->createMock(OrderInterface::class);
        $order->method('getEntityId')->willReturn(1);

        $target = new Shipment(
            $shipmentRepository,
            $this->createMock(ShipmentItemInterfaceFactory::class),
            $searchCriteriaBuilder,
            $this->createMock(PhraseFactory::class)
        );
        $target->setOrder($order);

        $variables = $target->getVariables();

        $this->assertSame($existingShipment, $variables['shipment']);
        $this->assertSame(42, $variables['shipment_id']);
    }
}
