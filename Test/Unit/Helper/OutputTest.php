<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Unit\Helper;

use Yireo\EmailTester2\Helper\Output as TargetHelper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit\Framework\TestCase;

/**
 * Class OutputTest
 *
 * @package Yireo\EmailTester2\Test\Unit\Helper
 */
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

        $context = $this->_getContextStub();
        $session = $this->_getSessionStub();
        $storeRepository = $this->_getStoreRepositoryStub();

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
        $customer = $this->getMockBuilder(\Magento\Customer\Api\Data\CustomerInterface::class)
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
    private function _getContextStub()
    {
        $stub = $this->getMockBuilder(\Magento\Framework\App\Helper\Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $scopeConfig = $this->_getScopeConfigStub();
        $stub
            ->method('getScopeConfig')
            ->will($this->returnValue($scopeConfig));

        return $stub;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function _getScopeConfigStub()
    {
        $stub = $this->getMockBuilder(\Magento\Framework\App\Config\ScopeConfigInterface::class)
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

        throw new \InvalidArgumentException('Unknown id = ' . $hashName);
    }

    /**
     * Get a stub for the $context parameter of the helper
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function _getSessionStub()
    {
        $stub = $this->getMockBuilder(\Magento\Backend\Model\Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $stub;
    }

    /**
     * Get a stub for the $context parameter of the helper
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function _getStoreRepositoryStub()
    {
        $stub = $this->getMockBuilder(\Magento\Store\Model\StoreRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $stub;
    }
}
