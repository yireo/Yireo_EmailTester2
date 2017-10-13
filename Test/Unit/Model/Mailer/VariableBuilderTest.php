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

namespace Yireo\EmailTester2\Test\Unit\Model\Mailer;

use Yireo\EmailTester2\Model\Mailer\VariableBuilder as Target;
use PHPUnit\Framework\TestCase;
use Yireo\EmailTester2\Model\Mailer\VariableFactory;
use Yireo\EmailTester2\Model\Mailer\VariableMethodFactory;

/**
 * Class VariableBuilderTest
 *
 * @package Yireo\EmailTester2\Test\Unit\Model\Mailer
 */
class VariableBuilderTest extends TestCase
{
    /**
     * @var Target
     */
    private $target;

    /**
     * Setup method
     */
    protected function setUp()
    {
        /** @var VariableFactory $variableFactory */
        $variableFactory = $this->getVariableFactoryStub();

        /** @var VariableMethodFactory $variableMethodFactory */
        $variableMethodFactory = $this->getVariableMethodFactoryStub();

        $this->target = new Target($variableFactory, $variableMethodFactory);
    }

    /**
     * @test
     * @covers Target::getVariables
     */
    public function testGetVariables()
    {
        $this->target->setStoreId('bar');
        //$this->target->setStoreId(42);

        $this->target->setTemplate('dummy');
        $variables = $this->target->getVariables();

        $this->assertArrayHasKey('template', $variables);
        $this->assertEquals($variables['template'], 'dummy');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getVariableFactoryStub()
    {
        $stub = $this->getMockBuilder(VariableFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $stub->method('create')
            ->will($this->returnCallback([$this, 'getAutoStub']));

        return $stub;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getVariableMethodFactoryStub()
    {
        $stub = $this->getMockBuilder(VariableMethodFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $stub;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function getAutoStub()
    {
        $args = func_get_args();
        $className = $args[0];

        $stub = $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();

        return $stub;
    }
}