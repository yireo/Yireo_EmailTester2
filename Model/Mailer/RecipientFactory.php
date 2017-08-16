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

/**
 * Class RecipientFactory
 *
 * @package Yireo\EmailTester2\Model\Mailer
 */
class RecipientFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Customer\Helper\View
     */
    private $customerViewHelper;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Helper\View $customerViewHelper
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
     * @return \Yireo\EmailTester2\Model\Mailer\Recipient
     */
    public function create(array $data = []): \Yireo\EmailTester2\Model\Mailer\Recipient
    {
        $recipient = $this->objectManager->create(\Yireo\EmailTester2\Model\Mailer\Recipient::class, $data);
        $this->addCustomerData($recipient, $data);
        $this->addEmail($recipient, $data);

        return $recipient;
    }

    /**
     * @param Recipient $recipient
     * @param array $data
     *
     * @return bool
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

        /** @var  $customer \Magento\Customer\Api\Data\CustomerInterface */
        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
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
