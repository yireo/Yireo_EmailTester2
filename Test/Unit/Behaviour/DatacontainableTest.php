<?php
/**
 * EmailTester2 plugin for Magento
 *
 * @author      Yireo (https://www.yireo.com/)
 * @copyright   Copyright 2018 Yireo (https://www.yireo.com/)
 * @license     Open Source License (OSL v3)
 */

declare(strict_types=1);

namespace Yireo\EmailTester2\Test\Unit\Behaviour;

use Yireo\EmailTester2\Behaviour\Datacontainable as Target;
use PHPUnit\Framework\TestCase;

class DatacontainableTest extends TestCase
{
    /**
     * @var Target
     */
    private $targetTrait;

    /**
     * Setup method
     */
    protected function setUp()
    {
        $this->targetTrait = $this->getMockForTrait(Target::class);
    }

    /**
     * @test
     * @covers Target::getData()
     */
    public function testGetData()
    {
        $this->targetTrait->setData('foobar', 1);
        $this->assertEquals($this->targetTrait->getData('foobar'), 1);

        $this->targetTrait->resetData();
        $this->assertEquals($this->targetTrait->getData('foobar'), null);
        $this->assertEquals($this->targetTrait->getData(), []);
    }

    /**
     * @test
     * @covers Target::isDataLowerThanOne()
     */
    public function testIsDataLowerThanOne()
    {
        $this->targetTrait->setData('foobar', 1);
        $this->assertFalse($this->targetTrait->isDataLowerThanOne('foobar'));

        $this->targetTrait->setData('foobar', 0);
        $this->assertTrue($this->targetTrait->isDataLowerThanOne('foobar'));

        $this->targetTrait->setData('foobar', 'whoops');
        $this->assertTrue($this->targetTrait->isDataLowerThanOne('foobar'));
    }

    /**
     * @test
     * @covers Target::isDataEmpty()
     */
    public function testIsDataEmpty()
    {
        $this->targetTrait->setData('foobar', 1);
        $this->assertFalse($this->targetTrait->isDataEmpty('foobar'));

        $this->targetTrait->setData('foobar', 0);
        $this->assertTrue($this->targetTrait->isDataEmpty('foobar'));

        $this->targetTrait->setData('foobar', null);
        $this->assertTrue($this->targetTrait->isDataEmpty('foobar'));

        $this->assertTrue($this->targetTrait->isDataEmpty('foobar2'));
    }
}
