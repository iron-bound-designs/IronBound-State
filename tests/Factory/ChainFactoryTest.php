<?php

/**
 * Test the Chain Factory.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Tests\Factory;

use IronBound\State\Exception\UnsupportedSubject;
use IronBound\State\Factory\ChainStateMachineFactory;
use IronBound\State\Factory\StateMachineFactory;
use IronBound\State\Graph\GraphId;
use IronBound\State\StateMachine;
use PHPUnit\Framework\TestCase;

class ChainFactoryTest extends TestCase
{
    public function testMake(): void
    {
        $machine = $this->createMock(StateMachine::class);
        $subject = new \stdClass();
        $graphId = new GraphId('default');

        $unsupported = $this->createMock(StateMachineFactory::class);
        $unsupported->expects($this->once())->method('supports')->with($subject)->willReturn(false);
        $unsupported->expects($this->never())->method('make');

        $supported = $this->createMock(StateMachineFactory::class);
        $supported->expects($this->once())->method('supports')->with($subject)->willReturn(true);
        $supported->expects($this->once())->method('make')->with($subject, $graphId)->willReturn($machine);

        $factory = new ChainStateMachineFactory($unsupported);
        $factory->addFactory($supported);

        $this->assertSame($machine, $factory->make($subject, $graphId));
    }

    public function testMakeThrowsExceptionIfNoneSupport(): void
    {
        $subject = new \stdClass();
        $graphId = new GraphId('default');

        $unsupported = $this->createMock(StateMachineFactory::class);
        $unsupported->expects($this->once())->method('supports')->with($subject)->willReturn(false);

        $factory = new ChainStateMachineFactory($unsupported);
        $this->expectException(UnsupportedSubject::class);
        $factory->make(new \stdClass(), $graphId);
    }

    public function testMakeThrowsExceptionIfNoFactoriesAdded(): void
    {
        $this->expectException(UnsupportedSubject::class);
        (new ChainStateMachineFactory())->make(new \stdClass(), new GraphId('default'));
    }

    public function testSupports(): void
    {
        $subject = new \stdClass();

        $unsupported = $this->createMock(StateMachineFactory::class);
        $unsupported->expects($this->once())->method('supports')->with($subject)->willReturn(false);

        $supported = $this->createMock(StateMachineFactory::class);
        $supported->expects($this->once())->method('supports')->with($subject)->willReturn(true);

        $factory = new ChainStateMachineFactory($unsupported);
        $factory->addFactory($supported);

        $this->assertTrue($factory->supports($subject));
    }

    public function testSupportsReturnsFalseIfNoneSupport(): void
    {
        $subject = new \stdClass();

        $unsupported = $this->createMock(StateMachineFactory::class);
        $unsupported->expects($this->once())->method('supports')->with($subject)->willReturn(false);

        $factory = new ChainStateMachineFactory($unsupported);

        $this->assertFalse($factory->supports($subject));
    }

    public function testSupportsReturnsFalseIfNoFactoriesAdded(): void
    {
        $this->assertFalse((new ChainStateMachineFactory())->supports(new \stdClass()));
    }
}
