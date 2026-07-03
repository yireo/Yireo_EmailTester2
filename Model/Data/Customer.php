<?php
/**
 * Yireo EmailTester2 for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License
 */

declare(strict_types = 1);

namespace Yireo\EmailTester2\Model\Data;

use Magento\Backend\App\ConfigInterface;
use Magento\Backend\Model\Auth\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Customer
{
    public function __construct(
        private readonly Session $session,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly RequestInterface $request,
        private readonly ConfigInterface $config
    ) {
    }

    /**
     * @param int $customerId
     *
     * @return false|CustomerInterface
     * @throws LocalizedException
     */
    public function getCustomer(int $customerId)
    {
        try {
            return $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $exception) {
            return false;
        }
    }

    /**
     * Get the current customer ID
     *
     * @return int
     */
    public function getCustomerId() : int
    {
        $customerId = (int) $this->request->getParam('customer_id');
        if (!empty($customerId)) {
            return (int) $customerId;
        }

        $userData = $this->session->getData();
        $customerId = (isset($userData['emailtester.customer_id'])) ? (int)$userData['emailtester.customer_id'] : null;
        if (!empty($customerId)) {
            return (int) $customerId;
        }

        $customerId = (int) $this->config->getValue('emailtester/settings/default_customer');
        return $customerId;
    }
}
