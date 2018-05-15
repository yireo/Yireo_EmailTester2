<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

namespace Yireo\EmailTester2\Model\Mailer\Variable;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Yireo\EmailTester2\Model\Mailer\VariableInterface;

/**
 * Class Store
 *
 * @package Yireo\EmailTester2\Model\Mailer\Variable
 */
class Store implements VariableInterface
{
    /**
     * @var int
     */
    private $storeId;

    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * Store constructor.
     *
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        StoreRepositoryInterface $storeRepository
    ) {
        $this->storeRepository = $storeRepository;
    }

    /**
     * @return false|StoreInterface
     */
    public function getVariable()
    {
        try {
            return $this->storeRepository->getById($this->storeId);
        } catch (NoSuchEntityException $exception) {
            return false;
        }
    }

    /**
     * @param int $storeId
     */
    public function setStoreId(int $storeId)
    {
        $this->storeId = $storeId;
    }
}
