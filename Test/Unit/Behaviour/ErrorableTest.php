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

use Yireo\EmailTester2\Behaviour\Errorable as Target;
use PHPUnit\Framework\TestCase;

class ErrorableTest extends TestCase
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
     * @test Target::hasError()
     */
    public function testHasError()
    {
        $error1 = 'My first error';
        $this->targetTrait->addError($error1);
        $this->assertSame($this->targetTrait->getErrors(), [$error1]);
        $this->assertSame($this->targetTrait->getErrorString(), $error1);

        $error2 = 'My second error';
        $this->targetTrait->addError($error2);
        $this->assertSame($this->targetTrait->getErrors(), [$error1, $error2]);
        $this->assertSame($this->targetTrait->getErrorString(), $error1.'; '.$error2);
    }
}
