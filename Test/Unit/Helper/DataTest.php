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
 * Class DataTest
 *
 * @package Yireo\EmailTester2\Test\Unit\Helper
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Yireo\EmailTester2\Helper\Data
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

        /** @var \Magento\Framework\App\Helper\Context $context */
        $context = $this->_getContextStub();
        $this->targetHelper = new \Yireo\EmailTester2\Helper\Data($context);
    }

    /**
     * @test
     * @covers \Yireo\EmailTester2\Helper\Data::isEnabled
     */
    public function testIsEnabled()
    {
        $this->assertTrue($this->targetHelper->isEnabled());
    }

    /**
     * @test
     * @covers \Yireo\EmailTester2\Helper\Data::isDebug
     */
    public function testIsDebug()
    {
        $this->assertTrue($this->targetHelper->isDebug());
    }

    /**
     * @test
     * @covers \Yireo\EmailTester2\Helper\Data::getConfigValue
     */
    public function testGetConfigValue()
    {
        $this->assertEquals($this->targetHelper->getConfigValue('enabled'), 1);
    }

    /**
     * Get a stub for the $context parameter of the helper
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function _getContextStub()
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
    private function _getScopeConfigStub()
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
            'emailtester2/settings/enabled' => '1',
            'emailtester2/settings/debug' => '1',
        ];

        if (array_key_exists($hashName, $defaultConfig)) {
            return $defaultConfig[$hashName];
        }

        throw new \InvalidArgumentException('Unknown id = ' . $hashName);
    }
}
