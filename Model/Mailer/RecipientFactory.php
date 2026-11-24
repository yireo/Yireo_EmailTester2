<?php
/**
 * Yireo EmailTester for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types = 1);

namespace Yireo\EmailTester2\Model\Mailer;

use Magento\Framework\ObjectManagerInterface;

class RecipientFactory
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager
    ) {
    }

    /**
     * @param array $data
     * @return Recipient
     */
    public function create(array $data = []): Recipient
    {
        return $this->objectManager->create(Recipient::class, $data);
    }
}
