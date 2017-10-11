<?php
namespace Yireo\EmailTester2\Test\Integration;

use Magento\TestFramework\Helper\Bootstrap;
use Yireo\EmailTester2\Model\Mailer;
use Yireo\EmailTester2\Model\TransportBuilder;


class DITest extends \PHPUnit_Framework_TestCase
{
    public function testMailerTransportBuilder()
    {
        /** @var Mailer $mailer */
        $mailer = Bootstrap::getObjectManager()->create(Mailer::class);
        $this->assertEquals(get_class($mailer), Mailer::class);
    }
}