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

use Exception;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\PhraseFactory;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Yireo\EmailTester2\Model\Mailer\VariableInterface;

class Invoice implements VariableInterface
{
    /**
     * @var OrderInterface
     */
    private $order;

    /**
     * @var InvoiceRepositoryInterface
     */
    private $invoiceRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var PhraseFactory
     */
    private $phraseFactory;

    /**
     * Shipment constructor.
     *
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param PhraseFactory $phraseFactory
     */
    public function __construct(
        InvoiceRepositoryInterface $invoiceRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        PhraseFactory $phraseFactory
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->phraseFactory = $phraseFactory;
    }

    /**
     * @return InvoiceInterface
     * @throws NoSuchEntityException
     */
    public function getVariable()
    {
        if (empty($this->order)) {
            $phrase = $this->phraseFactory->create(['text' => 'Could not find any order entity']);
            throw new NoSuchEntityException($phrase);
        }

        try {
            $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
            $searchCriteriaBuilder->addFilter('order_id', $this->order->getEntityId());
            $searchCriteria = $searchCriteriaBuilder->create();
            $searchCriteria->setCurrentPage(1);
            $searchCriteria->setPageSize(1);
            $searchResult = $this->invoiceRepository->getList($searchCriteria);

            if ($searchResult) {
                $invoices = $searchResult->getItems();
                return array_shift($invoices);
            }

            return $this->getAnyInvoice();
        } catch (Exception $e) {
            return $this->getAnyInvoice();
        }
    }

    /**
     * @return InvoiceInterface
     */
    private function getAnyInvoice(): InvoiceInterface
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder->create();
        $searchCriteria->setCurrentPage(1);
        $searchCriteria->setPageSize(1);
        $searchResult = $this->invoiceRepository->getList($searchCriteria);
        if ($searchResult) {
            $invoices = $searchResult->getItems();
            return array_shift($invoices);
        }

        return $this->invoiceRepository->create();
    }

    /**
     * @param $order OrderInterface
     */
    public function setOrder(OrderInterface $order)
    {
        $this->order = $order;
    }
}
