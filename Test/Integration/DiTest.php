<?php
namespace Yireo\EmailTester2\Test\Integration;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\EmailTester2\Model\Mailer;

class DiTest extends TestCase
{
    /**
     * Test whether fetching the mailer through the Object Manager works
     */
    public function testMailerTransportBuilder()
    {
        /** @var Mailer $mailer */
        $mailer = Bootstrap::getObjectManager()->create(Mailer::class);
        $this->assertEquals(get_class($mailer), Mailer::class);
    }
}
