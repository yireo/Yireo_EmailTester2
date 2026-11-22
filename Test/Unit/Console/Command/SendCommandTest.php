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

    private function runCommand(Mailer $mailer, Config $config, array $options): void
    {
        $store = $this->createStub(StoreInterface::class);
        $store->method('getId')->willReturn(0);
        $storeManager = $this->createStub(StoreManagerInterface::class);
        $storeManager->method('getDefaultStoreView')->willReturn($store);

        $command = new SendCommand(
            $mailer,
            $storeManager,
            $config,
            $this->createStub(State::class)
        );

        $command->run(new ArrayInput($options), new NullOutput());
    }
}
