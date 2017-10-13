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
 * Interface VariablesInterface
 *
 * @package Yireo\EmailTester2\Model\Mailer
 */
interface VariablesInterface extends AbstractVariableInterface
{
    /**
     * @return mixed
     */
    public function getVariables();
}
