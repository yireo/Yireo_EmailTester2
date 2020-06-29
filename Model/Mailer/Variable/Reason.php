<?php
/**
 * Yireo EmailTester for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2018 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types = 1);

namespace Yireo\EmailTester2\Model\Mailer\Variable;

use Yireo\EmailTester2\Model\Mailer\VariableInterface;

class Reason implements VariableInterface
{
    /**
     * @return string
     */
    public function getVariable() : string
    {
        return 'No real reason';
    }
}
