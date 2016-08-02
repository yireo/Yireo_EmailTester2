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
 * Class Creditmemo
 *
 * @package Yireo\EmailTester2\Model\Mailer\Variable
 */
class Creditmemo
{
    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    protected $order;

    /**
     * @var \Magento\Sales\Api\CreditmemoRepositoryInterface
     */
    protected $creditmemoRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Constructor.
     *
     * @param \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->creditmemoRepository = $creditmemoRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }
    
    /**
     * @return \Magento\Sales\Model\Order\Creditmemo
     */
    public function getVariable()
    {
        $this->searchCriteriaBuilder->addFilter('order_id', $this->order->getEntityId());
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $creditmemos = $this->creditmemoRepository->getList($searchCriteria);

        if (!empty($creditmemos)) {
            $creditmemo = $creditmemos->getFirstItem();
        } else {
            $creditmemo = null;
        }

        return $creditmemo;
    }

    /**
     * @param $order \Magento\Sales\Api\Data\OrderInterface
     */
    public function setOrder(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $this->order = $order;
    }
}