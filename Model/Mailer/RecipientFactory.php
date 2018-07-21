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

namespace Yireo\EmailTester2\Model\Mailer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Helper\View;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class RecipientFactory
 *
 * @package Yireo\EmailTester2\Model\Mailer
 */
class RecipientFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var View
     */
    private $customerViewHelper;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param CustomerRepositoryInterface $customerRepository
     * @param View $customerViewHelper
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        CustomerRepositoryInterface $customerRepository,
        View $customerViewHelper
    ) {
        $this->objectManager = $objectManager;
        $this->customerRepository = $customerRepository;
        $this->customerViewHelper = $customerViewHelper;
    }

    /**
     * Create new config object
     *
     * @param array $data
     *
     * @return Recipient
     * @throws LocalizedException
     */
    public function create(array $data = []): Recipient
    {
        $recipient = $this->objectManager->create(Recipient::class, $data);
        $this->addCustomerData($recipient, $data);
        $this->addEmail($recipient, $data);

        return $recipient;
    }

    /**
     * @param Recipient $recipient
     * @param array $data
     *
     * @return bool
     * @throws LocalizedException
     */
    private function addCustomerData(Recipient &$recipient, array $data): bool
    {
        if (!isset($data['customer_id'])) {
            return false;
        }

        $customerId = (int)$data['customer_id'];

        if (empty($customerId)) {
            return false;
        }

        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $exception) {
            return false;
        }

        if (!$customer->getId() > 0) {
            return false;
        }

        $recipient->setName($this->customerViewHelper->getCustomerName($customer));
        $recipient->setEmail($customer->getEmail());

        return true;
    }

    /**
     * @param Recipient $recipient
     * @param array $data
     *
     * @return bool
     */
    private function addEmail(Recipient &$recipient, array $data): bool
    {
        if (empty($data['email'])) {
            return false;
        }

        $recipientEmail = $data['email'];
        if (empty($recipientEmail)) {
            return false;
        }

        $recipient->setEmail($recipientEmail);

        return false;
    }
}
