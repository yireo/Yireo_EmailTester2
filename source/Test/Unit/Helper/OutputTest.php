<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @package     Yireo_EmailTester2
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2015 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

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
    protected $targetHelper;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

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
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getCustomerStub($data)
    {
        // @todo: Rewrite to getMockObject()
        $customerStub = $this->getMock(
            '\Magento\Customer\Model\Customer',
            ['getName', 'getEmail'],
            [$data['name'], $data['email']],
            '',
            false,
            false
        );

        $customerStub->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($data['name'])
            );

        $customerStub->expects($this->any())
            ->method('getEmail')
            ->will($this->returnValue($data['email'])
            );

        return $customerStub;
    }

    /**
     * Get a stub for the $context parameter of the helper
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getContextStub()
    {
        $scopeConfig = $this->_getScopeConfigStub();

        // @todo: Rewrite to getMockObject()
        $context = $this->getMock(
            'Magento\Framework\App\Helper\Context',
            ['getScopeConfig'],
            [],
            '',
            false,
            false
        );

        $context->expects($this->any())
            ->method('getScopeConfig')
            ->will($this->returnValue($scopeConfig)
            );

        return $context;
    }

    /**
     * Get a stub for the $scopeConfig with a $context
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getScopeConfigStub()
    {
        $scopeConfig = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');

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
