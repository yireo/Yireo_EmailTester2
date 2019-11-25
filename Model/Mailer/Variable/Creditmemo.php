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

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\PhraseFactory;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Creditmemo as CoreCreditmemo;
use Yireo\EmailTester2\Model\Mailer\VariableInterface;

/**
 * Class Creditmemo
 */
class Creditmemo implements VariableInterface
{
    /**
     * @var OrderInterface
     */
    private $order;

    /**
     * @var CreditmemoRepositoryInterface
     */
    private $creditmemoRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Constructor.
     *
     * @param CreditmemoRepositoryInterface $creditmemoRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param PhraseFactory $phraseFactory
     */
    public function __construct(
        CreditmemoRepositoryInterface $creditmemoRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PhraseFactory $phraseFactory
    ) {
        $this->creditmemoRepository = $creditmemoRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->phraseFactory = $phraseFactory;
    }

    /**
     * @return CoreCreditmemo|null
     * @throws NoSuchEntityException
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
     * @param $order OrderInterface
     */
    public function setOrder(OrderInterface $order)
    {
        $this->order = $order;
    }
}
