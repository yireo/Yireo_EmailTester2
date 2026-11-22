<?php

declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Unit\Console\Command;

use Magento\Framework\App\State;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Yireo\EmailTester2\Config\Config;
use Yireo\EmailTester2\Console\Command\SendCommand;
use Yireo\EmailTester2\Model\Mailer;

class SendCommandTest extends TestCase
{
    public function testEachIdOptionIsReadFromItsOwnOption(): void
    {
        $mailer = $this->createMock(Mailer::class);
        $mailer->expects($this->once())
            ->method('setData')
            ->with($this->callback(function (array $data): bool {
                $this->assertSame(11, $data['customer_id']);
                $this->assertSame(22, $data['product_id']);
                $this->assertSame(33, $data['order_id']);
                $this->assertSame(0, $data['store_id']);

                return true;
            }));
        $mailer->expects($this->once())->method('send')->willReturn(true);

        $this->runCommand($mailer, $this->createStub(Config::class), [
            '--customer_id' => '11',
            '--product_id' => '22',
            '--order_id' => '33',
        ]);
    }

    public function testMissingIdOptionsFallBackToConfiguredDefaults(): void
    {
        $config = $this->createStub(Config::class);
        $config->method('getDefaultCustomer')->willReturn(44);
        $config->method('getDefaultProduct')->willReturn(55);
        $config->method('getDefaultOrder')->willReturn(66);

        $mailer = $this->createMock(Mailer::class);
        $mailer->expects($this->once())
            ->method('setData')
            ->with($this->callback(function (array $data): bool {
                $this->assertSame(44, $data['customer_id']);
                $this->assertSame(55, $data['product_id']);
                $this->assertSame(66, $data['order_id']);

                return true;
            }));
        $mailer->method('send')->willReturn(true);

        $this->runCommand($mailer, $config, []);
    }

    public function testNonNumericIdOptionsFallBackToConfiguredDefaults(): void
    {
        $config = $this->createStub(Config::class);
        $config->method('getDefaultCustomer')->willReturn(44);
        $config->method('getDefaultProduct')->willReturn(55);
        $config->method('getDefaultOrder')->willReturn(66);

        $mailer = $this->createMock(Mailer::class);
        $mailer->expects($this->once())
            ->method('setData')
            ->with($this->callback(function (array $data): bool {
                $this->assertSame(44, $data['customer_id']);
                $this->assertSame(55, $data['product_id']);
                $this->assertSame(66, $data['order_id']);

                return true;
            }));
        $mailer->method('send')->willReturn(true);

        $this->runCommand($mailer, $config, [
            '--customer_id' => 'abc',
            '--product_id' => 'abc',
            '--order_id' => '0',
        ]);
    }

    public function testEmptyStringOptionsFallBackToConfiguredDefaults(): void
    {
        $config = $this->createStub(Config::class);
        $config->method('getDefaultEmail')->willReturn('default@example.com');
        $config->method('getDefaultTransactional')->willReturn('customer_create_account_email_template');

        $mailer = $this->createMock(Mailer::class);
        $mailer->expects($this->once())
            ->method('setData')
            ->with($this->callback(function (array $data): bool {
                $this->assertSame('default@example.com', $data['email']);
                $this->assertSame('default@example.com', $data['sender']);
                $this->assertSame('customer_create_account_email_template', $data['template']);

                return true;
            }));
        $mailer->method('send')->willReturn(true);

        $this->runCommand($mailer, $config, [
            '--email' => '  ',
            '--sender' => '',
            '--template' => '',
        ]);
    }

    public function testStringOptionsOverrideConfiguredDefaults(): void
    {
        $config = $this->createStub(Config::class);
        $config->method('getDefaultEmail')->willReturn('default@example.com');
        $config->method('getDefaultTransactional')->willReturn('default_template');

        $mailer = $this->createMock(Mailer::class);
        $mailer->expects($this->once())
            ->method('setData')
            ->with($this->callback(function (array $data): bool {
                $this->assertSame('to@example.com', $data['email']);
                $this->assertSame('from@example.com', $data['sender']);
                $this->assertSame('sales_email_order_template', $data['template']);

                return true;
            }));
        $mailer->method('send')->willReturn(true);

        $this->runCommand($mailer, $config, [
            '--email' => ' to@example.com ',
            '--sender' => 'from@example.com',
            '--template' => 'sales_email_order_template',
        ]);
    }

    public function testStoreIdOptionSetsCurrentStore(): void
    {
        $store = $this->createStub(StoreInterface::class);
        $store->method('getCode')->willReturn('german');

        $defaultStore = $this->createStub(StoreInterface::class);
        $defaultStore->method('getId')->willReturn(1);

        $storeManager = $this->createMock(StoreManagerInterface::class);
        $storeManager->method('getDefaultStoreView')->willReturn($defaultStore);
        $storeManager->expects($this->once())->method('getStore')->with(2)->willReturn($store);
        $storeManager->expects($this->once())->method('setCurrentStore')->with('german');

        $mailer = $this->createMock(Mailer::class);
        $mailer->expects($this->once())
            ->method('setData')
            ->with($this->callback(function (array $data): bool {
                $this->assertSame(2, $data['store_id']);

                return true;
            }));
        $mailer->method('send')->willReturn(true);

        $this->runCommand($mailer, $this->createStub(Config::class), ['--store_id' => '2'], $storeManager);
    }

    public function testDefaultStoreViewDoesNotChangeCurrentStore(): void
    {
        $store = $this->createStub(StoreInterface::class);
        $store->method('getId')->willReturn(0);

        $storeManager = $this->createMock(StoreManagerInterface::class);
        $storeManager->method('getDefaultStoreView')->willReturn($store);
        $storeManager->expects($this->never())->method('setCurrentStore');

        $mailer = $this->createMock(Mailer::class);
        $mailer->method('send')->willReturn(true);

        $this->runCommand($mailer, $this->createStub(Config::class), [], $storeManager);
    }

    private function runCommand(
        Mailer $mailer,
        Config $config,
        array $options,
        ?StoreManagerInterface $storeManager = null
    ): void {
        if ($storeManager === null) {
            $store = $this->createStub(StoreInterface::class);
            $store->method('getId')->willReturn(0);
            $storeManager = $this->createStub(StoreManagerInterface::class);
            $storeManager->method('getDefaultStoreView')->willReturn($store);
        }

        $command = new SendCommand(
            $mailer,
            $storeManager,
            $config,
            $this->createStub(State::class)
        );

        $command->run(new ArrayInput($options), new NullOutput());
    }
}
