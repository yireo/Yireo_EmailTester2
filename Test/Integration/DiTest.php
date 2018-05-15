<?php
namespace Yireo\EmailTester2\Test\Integration;

use Magento\TestFramework\Helper\Bootstrap;
use Yireo\EmailTester2\Model\Mailer;

/**
 * Class DiTest
 *
 * @package Yireo\EmailTester2\Test\Integration
 */
class DiTest extends \PHPUnit\Framework\TestCase
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
