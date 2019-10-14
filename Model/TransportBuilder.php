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

namespace Yireo\EmailTester2\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\MailMessageInterface;
use Magento\Framework\Mail\Template\TransportBuilder as OriginalTransportBuilder;

/**
 * Class TransportBuilder
 *
 * @package Yireo\EmailTester2\Model
 */
class TransportBuilder extends OriginalTransportBuilder
{
    /**
     * @return MailMessageInterface
     * @throws LocalizedException
     */
    public function getMessage(): MailMessageInterface
    {
        $this->prepareMessage();

        return $this->message;
    }
}
