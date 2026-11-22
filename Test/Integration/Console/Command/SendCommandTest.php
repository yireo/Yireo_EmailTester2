<?php

declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Integration\Console\Command;

use Magento\Framework\Console\CommandListInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Yireo\EmailTester2\Console\Command\SendCommand;
use Yireo\EmailTester2\Model\Mailer;

class SendCommandTest extends TestCase
{
    public function testCommandIsRegisteredWithAllOptions(): void
    {
        $commands = Bootstrap::getObjectManager()->get(CommandListInterface::class)->getCommands();
        $this->assertArrayHasKey('emailtester2_send', $commands);

        $command = $commands['emailtester2_send'];
        $this->assertInstanceOf(SendCommand::class, $command);
        $this->assertSame('yireo_emailtester2:send', $command->getName());

        $definition = $command->getDefinition();
        foreach (['email', 'sender', 'template', 'customer_id', 'order_id', 'product_id', 'store_id'] as $option) {
            $this->assertTrue($definition->hasOption($option), sprintf('Missing option "%s"', $option));
        }
    }

    public function testIdOptionsArePassedToTheMailer(): void
    {
        $mailer = $this->createMock(Mailer::class);
        $mailer->expects($this->once())
            ->method('setData')
            ->with($this->callback(function (array $data): bool {
                $this->assertSame('to@example.com', $data['email']);
                $this->assertSame('sales_email_order_template', $data['template']);
                $this->assertSame(11, $data['customer_id']);
                $this->assertSame(22, $data['product_id']);
                $this->assertSame(33, $data['order_id']);
                $this->assertSame(1, $data['store_id']);

                return true;
            }));
        $mailer->expects($this->once())->method('send')->willReturn(true);

        $command = Bootstrap::getObjectManager()->create(SendCommand::class, ['mailer' => $mailer]);
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute([
            '--email' => 'to@example.com',
            '--template' => 'sales_email_order_template',
            '--customer_id' => '11',
            '--product_id' => '22',
            '--order_id' => '33',
            '--store_id' => '1',
        ]);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString('Mail has been sent', $commandTester->getDisplay());
    }
}
