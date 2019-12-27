<?php

/**
 * Test the Concrete Factory implementation.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Tests\Factory;

use IronBound\State\Exception\UnsupportedSubject;
use IronBound\State\Factory\{ConcreteStateMachineFactory, SupportsTest};
use IronBound\State\Graph\{Graph, GraphId, GraphLoader};
use IronBound\State\StateMediator\{StateMediator, StateMediatorFactory};
use PHPUnit\Framework\TestCase;

class ConcreteFactoryTest extends TestCase
{
    public function testMake(): void
    {
        $subject  = new \stdClass();
        $graphId  = new GraphId('default');
        $mediator = $this->createMock(StateMediator::class);
        $graph    = $this->createMock(Graph::class);

        $mediatorFactory = $this->createMock(StateMediatorFactory::class);
        $mediatorFactory->expects($this->once())->method('make')->with($graphId)->willReturn($mediator);

        $loader = $this->createMock(GraphLoader::class);
        $loader->expects($this->once())->method('make')->with($graphId)->willReturn($graph);

        $supportsTest = $this->createMock(SupportsTest::class);
        $supportsTest->method('__invoke')->willReturn(true);

        $factory      = new ConcreteStateMachineFactory($mediatorFactory, $loader, $supportsTest);
        $stateMachine = $factory->make($subject, $graphId);

        $this->assertSame($graph, $stateMachine->getGraph());
        $this->assertSame($subject, $stateMachine->getSubject());
    }

    public function testMakeThrowsExceptionIfNotSupported(): void
    {
        $supportsTest = $this->createMock(SupportsTest::class);
        $supportsTest->method('__invoke')->willReturn(false);

        $factory = new ConcreteStateMachineFactory(
            $this->createMock(StateMediatorFactory::class),
            $this->createMock(GraphLoader::class),
            $supportsTest
        );
        $this->expectException(UnsupportedSubject::class);
        $factory->make(new \stdClass(), new GraphId('default'));
    }

    public function testSupports(): void
    {
        $subject = new \stdClass();

        $supportsTest = $this->createMock(SupportsTest::class);
        $supportsTest->expects($this->once())->method('__invoke')->with($subject)->willReturn(false);

        $factory = new ConcreteStateMachineFactory(
            $this->createMock(StateMediatorFactory::class),
            $this->createMock(GraphLoader::class),
            $supportsTest
        );
        $this->assertFalse($factory->supports($subject));
    }

    public function testClassTest(): void
    {
        $test = ConcreteStateMachineFactory::classTest('stdClass');
        $this->assertTrue($test(new \stdClass()));
        $this->assertTrue($test(new class extends \stdClass {
        }));

        $this->assertFalse($test(new GraphId('default')));
        $this->assertFalse($test(new class {
        }));
    }
}
