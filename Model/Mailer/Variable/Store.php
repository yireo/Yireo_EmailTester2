<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

namespace Yireo\EmailTester2\Model\Mailer\Variable;

/**
 * Class Store
 *
 * @package Yireo\EmailTester2\Model\Mailer\Variable
 */
class Store
{
    /**
     * @var int
     */
    protected $storeId;

    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * Store constructor.
     *
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository
    )
    {
        $this->storeRepository = $storeRepository;
    }

    /**
     * @return false|\Magento\Store\Model\Store
     */
    public function getVariable()
    {
        try {
            return $this->storeRepository->getById($this->storeId);
        }catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
            return false;
        }

    }

    /**
     * @param $storeId
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
    }
}