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
use Magento\Framework\Mail\EmailMessageInterface;
use Magento\Framework\Mail\MailMessageInterface;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\Template\TransportBuilder as OriginalTransportBuilder;

class TransportBuilder extends OriginalTransportBuilder
{
    /**
     * @return MessageInterface|MailMessageInterface|EmailMessageInterface
     * @throws LocalizedException
     */
    public function getMessage()
    {
        $this->prepareMessage();
        return $this->message;
    }
}
