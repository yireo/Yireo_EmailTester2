<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2017 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types = 1);

namespace Yireo\EmailTester2\Test\Unit\Helper;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class OutputTest
 *
 * @package Yireo\EmailTester2\Test\Unit\Helper
 */
class OutputTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Yireo\EmailTester2\Helper\Output
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
        $this->targetHelper = new \Yireo\EmailTester2\Helper\Output($context);
    }

    /**
     * @test
     * @covers \Yireo\EmailTester2\Helper\Output::getCustomerOutput
     */
    public function testGetCustomerOutput()
    {
        $customerStub = $this->getCustomerStub(['name' => 'John Doe', 'email' => 'john@example.com']);
        $this->assertEquals($this->targetHelper->getCustomerOutput($customerStub), 'John Doe [john@example.com]');
    }

    /**
     * Get a stub for the $appState object
     *
     * @param array $data
     *
     * @return \PHPUnit_Framework_MockObject_MockBuilder
     */
    private function getCustomerStub($data)
    {
        $customer = $this->getMockBuilder(\Magento\Customer\Model\Customer::class);

        $customer->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($data['name'])
            );

        $customer->expects($this->any())
            ->method('getEmail')
            ->will($this->returnValue($data['email'])
            );

        return $customer;
    }

    /**
     * Get a stub for the $context parameter of the helper
     *
     * @return \PHPUnit_Framework_MockObject_MockBuilder
     */
    private function _getContextStub()
    {
        $context = $this->getMockBuilder(\Magento\Framework\App\Helper\Context::class);

        $scopeConfig = $this->_getScopeConfigStub();
        $context->expects($this->any())
            ->method('getScopeConfig')
            ->will($this->returnValue($scopeConfig)
            );

        return $context;
    }

    /**
     * Get a stub for the $scopeConfig with a $context
     *
     * @return \PHPUnit_Framework_MockObject_MockBuilder
     */
    private function _getScopeConfigStub()
    {
        $scopeConfig = $this->getMockBuilder(\Magento\Framework\App\Config\ScopeConfigInterface::class);

        $scopeConfig->expects($this->any())->method('getValue')->will($this->returnCallback([$this, 'getScopeConfigMethodStub']));

        return $scopeConfig;
    }

    /**
     * Mimic configuration values for usage within $scopeConfig
     *
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
}
