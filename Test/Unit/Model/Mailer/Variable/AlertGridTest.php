<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Unit\Model\Mailer\Variable;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\ProductAlert\Block\Email\Price;
use Magento\ProductAlert\Block\Email\Stock;
use Magento\Store\Api\StoreRepositoryInterface;
use Yireo\EmailTester2\Model\Mailer\Variable\AlertGrid;

class AlertGridTest extends TestCase
{
    /**
     * @var AlertGrid
     */
    private $target;

    protected function setUp(): void
    {
        parent::setUp();

        $this->target = new AlertGrid(
            $this->createMock(State::class),
            $this->createMock(StoreRepositoryInterface::class),
            $this->createMock(LayoutFactory::class),
            $this->createMock(ScopeConfigInterface::class),
            $this->createMock(DesignInterface::class),
            $this->createMock(ProductRepositoryInterface::class)
        );
    }

    /**
     * @dataProvider blockClassDataProvider
     */
    public function testGetBlockClass(string $templateCode, string $template, string $expectedClass)
    {
        if ($templateCode !== '') {
            $this->target->setTemplateCode($templateCode);
        }

        if ($template !== '') {
            $this->target->setTemplate($template);
        }

        $method = new ReflectionMethod(AlertGrid::class, 'getBlockClass');
        $method->setAccessible(true);

        $this->assertSame($expectedClass, $method->invoke($this->target));
    }

    /**
     * @return array
     */
    public static function blockClassDataProvider(): array
    {
        return [
            'magento stock template code' => [
                'catalog_productalert_email_stock_template',
                '',
                Stock::class,
            ],
            'amasty stock template code' => [
                'amxnotif_customer_notifications_stock_template',
                '',
                Stock::class,
            ],
            'price template code' => [
                'catalog_productalert_email_price_template',
                '',
                Price::class,
            ],
            'numeric template id without code falls back to price' => [
                '',
                '5',
                Price::class,
            ],
            'template code takes precedence over template' => [
                'catalog_productalert_email_stock_template',
                '5',
                Stock::class,
            ],
        ];
    }
}
