<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

namespace Yireo\EmailTester2\Controller\Adminhtml\Ajax;

use Magento\Backend\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class CustomerId
 *
 * @package Yireo\EmailTester2\Controller\Ajax
 */
class CustomerId extends AbstractId
{
    const ADMIN_RESOURCE = 'Yireo_EmailTester2::index';

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param Context $context
     * @param Http $request
     * @param JsonFactory $resultJsonFactory
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Context $context,
        Http $request,
        JsonFactory $resultJsonFactory,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context, $request, $resultJsonFactory);
        $this->customerRepository = $customerRepository;
    }

    /**
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function getLabel(): string
    {
        $id = $this->getId();
        if (!$id > 0) {
            return 'No customer found';
        }

        $customer = $this->customerRepository->getById($id);

        return $customer->getFirstname() . ' ' . $customer->getLastname() . ' ['.$customer->getEmail().']';
    }
}
