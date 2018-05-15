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
 * Class Invoice
 *
 * @package Yireo\EmailTester2\Model\Mailer\Variable
 */
class Invoice implements \Yireo\EmailTester2\Model\Mailer\VariableInterface
{
    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    private $order;

    /**
     * @var \Magento\Sales\Api\InvoiceRepositoryInterface
     */
    private $invoiceRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Shipment constructor.
     *
     * @param \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\PhraseFactory $phraseFactory
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->phraseFactory = $phraseFactory;
    }

    /**
     * @return \Magento\Sales\Api\Data\InvoiceInterface
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
            $invoices = $this->invoiceRepository->getList($searchCriteria);

            if ($invoices) {
                return array_shift($invoices->getItems());
            }

            return $this->invoiceRepository->create();
        } catch (\Exception $e) {
            return $this->invoiceRepository->create();
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
