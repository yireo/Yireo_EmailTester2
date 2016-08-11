<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

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
    protected $objectManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $customerViewHelper;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Helper\View $customerViewHelper
    )
    {
        $this->objectManager = $objectManager;
        $this->customerRepository = $customerRepository;
        $this->customerViewHelper = $customerViewHelper;
    }

    /**
     * Create new config object
     *
     * @param array $data
     *
     * @return \Magento\Config\Model\Config
     */
    public function create(array $data = [])
    {
        $recipient = $this->objectManager->create('Yireo\EmailTester2\Model\Mailer\Recipient', $data);
        $this->addCustomerData($recipient, $data);
        $this->addEmail($recipient, $data);

        return $recipient;
    }

    /**
     * @param $recipient
     * @param $data
     *
     * @return bool
     */
    protected function addCustomerData(&$recipient, $data)
    {
        if (empty($data['customer_id'])) {
            return false;
        }

        /** @var  $customer \Magento\Customer\Api\Data\CustomerInterface */
        try {
            $customer = $this->customerRepository->getById($data['customer_id']);
        } catch(\Magento\Framework\Exception\NoSuchEntityException $exception) {
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
     * @param $recipient
     * @param $data
     *
     * @return bool
     */
    protected function addEmail(&$recipient, $data)
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
