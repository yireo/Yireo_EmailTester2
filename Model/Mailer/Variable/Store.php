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

/**
 * Class Store
 *
 * @package Yireo\EmailTester2\Model\Mailer\Variable
 */
class Store implements \Yireo\EmailTester2\Model\Mailer\VariableInterface
{
    /**
     * @var int
     */
    private $storeId;

    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * Store constructor.
     *
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository
    ) {
        $this->storeRepository = $storeRepository;
    }

    /**
     * @return false|\Magento\Store\Api\Data\StoreInterface
     */
    public function getVariable()
    {
        try {
            return $this->storeRepository->getById($this->storeId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            return false;
        }

    }

    /**
     * @param mixed $storeId
     */
    public function setStoreId($storeId)
    {
        $this->storeId = (int) $storeId;
    }
}
