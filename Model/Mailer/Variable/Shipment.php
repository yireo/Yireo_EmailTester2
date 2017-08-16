<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types = 1);

namespace Yireo\EmailTester2\Model\Mailer\Variable;

/**
 * Class Shipment
 *
 * @package Yireo\EmailTester2\Model\Mailer\Variable
 */
class Shipment implements \Yireo\EmailTester2\Model\Mailer\VariableInterface
{
    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    private $order;

    /**
     * @var \Magento\Sales\Api\ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Shipment constructor.
     *
     * @param \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository
     */
    public function __construct(
        \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->shipmentRepository = $shipmentRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @return \Magento\Sales\Api\Data\ShipmentInterface
     */
    public function getVariable()
    {
        try {
            $this->searchCriteriaBuilder->addFilter('order_id', $this->order->getEntityId());
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $searchCriteria->setCurrentPage(1);
            $searchCriteria->setPageSize(1);
            $shipments = $this->shipmentRepository->getList($searchCriteria)->getItems();

            if ($shipments) {
                return $shipments[0];
            }

            return $this->shipmentRepository->create();
        } catch (\Exception $e) {
            return $this->shipmentRepository->create();
        }
    }

    /**
     * @param $order \Magento\Sales\Api\Data\OrderInterface
     */
    public function setOrder(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $this->order = $order;
    }
}
