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

namespace Yireo\EmailTester2\Model;

/**
 * Class TransportBuilder
 *
 * @package Yireo\EmailTester2\Model
 */
class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /**
     * @return \Magento\Framework\Mail\Message
     */
    public function getMessage() : \Magento\Framework\Mail\Message
    {
        $this->prepareMessage();

        return $this->message;
    }
}