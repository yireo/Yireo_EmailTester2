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

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Creditmemo
 *
 * @package Yireo\EmailTester2\Model\Mailer\Variable
 */
class Creditmemo implements \Yireo\EmailTester2\Model\Mailer\VariableInterface
{
    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    private $order;

    /**
     * @var \Magento\Sales\Api\CreditmemoRepositoryInterface
     */
    private $creditmemoRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Constructor.
     *
     * @param \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\PhraseFactory $phraseFactory
    ) {
        $this->creditmemoRepository = $creditmemoRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->phraseFactory = $phraseFactory;
    }

    /**
     * @return \Magento\Sales\Model\Order\Creditmemo|null
     */
    public function getVariable()
    {
        if (empty($this->order)) {
            $phrase = $this->phraseFactory->create(['text' => 'Could not find any order entity']);
            throw new NoSuchEntityException($phrase);
        }

        $this->searchCriteriaBuilder->addFilter('order_id', $this->order->getEntityId());
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchCriteria->setCurrentPage(1);
        $searchCriteria->setPageSize(1);
        $searchResult = $this->creditmemoRepository->getList($searchCriteria);
        $creditmemos = $searchResult->getItems();

        if (!empty($creditmemos[0])) {
            $creditmemo = $creditmemos[0];
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
