<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

namespace Yireo\EmailTester2\Model\Mailer\Variable;

/**
 * Class Shipment
 *
 * @package Yireo\EmailTester2\Model\Mailer\Variable
 */
class Shipment
{
    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    protected $order;

    /**
     * @var \Magento\Sales\Api\ShipmentRepositoryInterface
     */
    protected $shipmentRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Shipment constructor.
     *
     * @param \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository
     */
    public function __construct(
        \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->shipmentRepository = $shipmentRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getVariable()
    {
        try {
            $this->searchCriteriaBuilder->addFilter('order_id', $this->order->getEntityId());
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $shipments = $this->shipmentRepository->getList($searchCriteria);

            if ($shipments) {
                $shipment = $shipments->getFirstItem();
            } else {
                $shipment = $this->shipmentRepository->create();
            }
        } catch (\Exception $e) {
            $shipment = $this->shipmentRepository->create();
        }
        
        return $shipment;
    }

    /**
     * @param $order \Magento\Sales\Api\Data\OrderInterface
     */
    public function setOrder(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $this->order = $order;
    }
}