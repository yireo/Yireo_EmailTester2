<?php
declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Integration\Stub;

use Magento\Framework\App\View;
use Magento\Framework\App\ObjectManager;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\InfoInterface;

/**
 * Mimics payment modules (like Stripe) whose dependency graph instantiates a fresh Magento\Framework\App\View,
 * which in turn creates a non-isolated page result that resets the builder of the shared layout
 */
class StrayPagePaymentHelper extends Data
{
    public const PAYMENT_HTML = 'stray-page-payment-html';

    public function getInfoBlockHtml(InfoInterface $info, $storeId)
    {
        ObjectManager::getInstance()->create(View::class);

        return self::PAYMENT_HTML;
    }
}
