<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

namespace Yireo\EmailTester2\Model\Mailer;

/**
 * Class RecipientFactory
 *
 * @package Yireo\EmailTester2\Model\Mailer
 */
class AddresseeFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->objectManager = $objectManager;
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
        return $this->objectManager->create('Yireo\EmailTester2\Model\Mailer\Addressee', $data);
    }
}
