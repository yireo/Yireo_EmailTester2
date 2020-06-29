<?php
/**
 * Yireo EmailTester for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types = 1);

namespace Yireo\EmailTester2\Model\Mailer\Variable;

use Exception;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\PhraseFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Yireo\EmailTester2\Model\Mailer\VariableInterface;

class Shipment implements VariableInterface
{
    /**
     * @var OrderInterface
     */
    private $order;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var PhraseFactory
     */
    private $phraseFactory;
    /**
     * @var \Magento\Sales\Api\Data\ShipmentItemCreationInterfaceFactory
     */
    private $shipmentItemFactory;

    /**
     * Shipment constructor.
     *
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param \Magento\Sales\Api\Data\ShipmentItemInterfaceFactory $shipmentItemFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param PhraseFactory $phraseFactory
     */
    public function __construct(
        ShipmentRepositoryInterface $shipmentRepository,
        \Magento\Sales\Api\Data\ShipmentItemInterfaceFactory $shipmentItemFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PhraseFactory $phraseFactory
    ) {
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentItemFactory = $shipmentItemFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->phraseFactory = $phraseFactory;
    }

    /**
     * @return ShipmentInterface
     * @throws NoSuchEntityException
     */
    public function getVariable()
    {
        if (empty($this->order)) {
            $phrase = $this->phraseFactory->create(['text' => 'Could not find any order entity']);
            throw new NoSuchEntityException($phrase);
        }

        try {
            $this->searchCriteriaBuilder->addFilter('order_id', $this->order->getEntityId());
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $searchCriteria->setCurrentPage(1);
            $searchCriteria->setPageSize(1);
            $shipments = $this->shipmentRepository->getList($searchCriteria)->getItems();

            if ($shipments) {
                $shipment = $shipments[0];
                return $shipment;
            }

            return $this->createDummyShipment();
        } catch (Exception $e) {
            return $this->createDummyShipment();
        }
    }

    /**
     * @return ShipmentInterface
     */
    private function createDummyShipment(): ShipmentInterface
    {
        $shipment = $this->shipmentRepository->create();
        $shipmentItems = $this->getShipmentItems($this->order);
        $shipment->setItems($shipmentItems);
        return $shipment;
    }

    /**
     * @param OrderInterface $order
     * @return array
     */
    private function getShipmentItems(OrderInterface $order): array
    {
        $shipmentItems = [];
        $orderItems = $order->getItems();
        foreach ($orderItems as $orderItem) {
            $item = $this->shipmentItemFactory->create();
            $item->setData($orderItem->getData());
            $item->setOrderItemId($orderItem->getId());
            $item->setQty($orderItem->getQtyOrdered());
            $shipmentItems[] = $item;
        }

        return $shipmentItems;
    }

    /**
     * @param $order OrderInterface
     */
    public function setOrder(OrderInterface $order)
    {
        $this->order = $order;
    }
}
