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

use Magento\Config\Model\Config;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class RecipientFactory
 *
 * @package Yireo\EmailTester2\Model\Mailer
 */
class AddresseeFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Create new config object
     *
     * @param array $data
     *
     * @return Config
     */
    public function create(array $data = [])
    {
        return $this->objectManager->create(Addressee::class, $data);
    }
}
