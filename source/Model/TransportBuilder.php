<?php
/**
 * Yireo EmailTester for Magento
 *
 * @package     Yireo_EmailTester
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

namespace Yireo\EmailTester2\Model;

/**
 * Class TransportBuilder
 *
 * @package Yireo\EmailTester2\Model
 */
class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /**
     * @return \Magento\Framework\Mail\Message|\Magento\Framework\Mail\MessageInterface
     */
    public function getMessage()
    {
        $this->prepareMessage();
        return $this->message;
    }
}