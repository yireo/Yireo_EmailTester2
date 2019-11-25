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

/**
 * EmailTester model
 */
class VariableFactory
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
     * @param string $variableClassName
     * @return AbstractVariableInterface
     */
    public function create($variableClassName)
    {
        /** @var AbstractVariableInterface $variable */
        $variable = $this->objectManager->create($variableClassName);
        return $variable;
    }
}
