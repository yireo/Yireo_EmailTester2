<?php declare(strict_types=1);

/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

namespace Yireo\EmailTester2\Model\Mailer\Variable;

use Exception;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\PhraseFactory;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Yireo\EmailTester2\Model\Mailer\VariablesInterface;

class Invoice implements VariablesInterface
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
     * @return array
     * @throws LocalizedException
     */
    public function getVariables(): array
    {
        $invoice = $this->getInvoice();

        return [
            'invoice' => $invoice,
            'invoice_data' => $invoice,
            'invoice_id' => $invoice->getEntityId(),
        ];
    }

    /**
     * @return InvoiceInterface
     * @throws NoSuchEntityException
     */
    private function getInvoice(): InvoiceInterface
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

            if (!$searchResult) {
                return $this->getAnyInvoice();
            }

            $invoices = $searchResult->getItems();
            $invoice = array_shift($invoices);
            if (!$invoice instanceof InvoiceInterface) {
                return $this->getAnyInvoice();
            }

            return $invoice;
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

        if (!$searchResult) {
            return $this->invoiceRepository->create();
        }

        $invoices = $searchResult->getItems();
        $invoice = array_shift($invoices);
        if (!$invoice instanceof InvoiceInterface) {
            return $this->invoiceRepository->create();
        }

        return $invoice;
    }

    /**
     * @param $order OrderInterface
     */
    public function setOrder(OrderInterface $order)
    {
        $this->order = $order;
    }
}
