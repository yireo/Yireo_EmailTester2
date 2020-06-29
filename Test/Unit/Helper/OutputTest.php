<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Unit\Helper;

use InvalidArgumentException;
use Magento\Backend\Model\Session;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreRepository;
use Yireo\EmailTester2\Helper\Output as TargetHelper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit\Framework\TestCase;

class OutputTest extends TestCase
{
    /**
     * @var TargetHelper
     */
    private $targetHelper;

    /**
     * @var ObjectManagerHelper
     */
    private $objectManagerHelper;

    /**
     * Setup method
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $context = $this->getContextStub();
        $session = $this->getSessionStub();
        $storeRepository = $this->getStoreRepositoryStub();

        $this->targetHelper = new TargetHelper($session, $storeRepository, $context);
    }

    /**
     * @test
     * @covers \Yireo\EmailTester2\Helper\Output::getCustomerOutput
     */
    public function testGetCustomerOutput()
    {
        $customerStub = $this->getCustomerStub(['id' => 42, 'email' => 'john@example.com']);
        $this->assertEquals($this->targetHelper->getCustomerOutput($customerStub), 'john@example.com [42]');
    }

    /**
     * @param array $data
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getCustomerStub($data)
    {
        $customer = $this->getMockBuilder(CustomerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $customer
            ->method('getId')
            ->will($this->returnValue($data['id']));

        $customer
            ->method('getEmail')
            ->will($this->returnValue($data['email']));

        return $customer;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getContextStub()
    {
        $stub = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $scopeConfig = $this->getScopeConfigStub();
        $stub
            ->method('getScopeConfig')
            ->will($this->returnValue($scopeConfig));

        return $stub;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getScopeConfigStub()
    {
        $stub = $this->getMockBuilder(ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $stub
            ->method('getValue')
            ->will($this->returnCallback([$this, 'getScopeConfigMethodStub']));

        return $stub;
    }

    /**
     * @param $hashName
     *
     * @return mixed
     */
    public function getScopeConfigMethodStub($hashName)
    {
        $defaultConfig = [
        ];

        if (array_key_exists($hashName, $defaultConfig)) {
            return $defaultConfig[$hashName];
        }

        throw new InvalidArgumentException('Unknown id = ' . $hashName);
    }

    /**
     * Get a stub for the $context parameter of the helper
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getSessionStub()
    {
        $stub = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $stub;
    }

    /**
     * Get a stub for the $context parameter of the helper
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getStoreRepositoryStub()
    {
        $stub = $this->getMockBuilder(StoreRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $stub;
    }
}
