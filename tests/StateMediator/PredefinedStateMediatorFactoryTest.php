<?php

/**
 * Test the predefined state mediator factory.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Tests\StateMediator;

use IronBound\State\Exception\UnsupportedGraph;
use IronBound\State\Graph\GraphId;
use IronBound\State\StateMediator\PredefinedStateMediatorFactory;
use IronBound\State\StateMediator\StateMediator;
use PHPUnit\Framework\TestCase;

class PredefinedStateMediatorFactoryTest extends TestCase
{
    public function testSupports(): void
    {
        $factory = new PredefinedStateMediatorFactory();
        $factory->addMediator(new GraphId('known'), $this->createMock(StateMediator::class));
        $this->assertTrue($factory->supports(new GraphId('known')));
        $this->assertFalse($factory->supports(new GraphId('unknown')));
    }

    public function testMake(): void
    {
        $mediator = $this->createMock(StateMediator::class);
        $factory  = new PredefinedStateMediatorFactory();
        $factory->addMediator(new GraphId('known'), $mediator);
        $this->assertSame($mediator, $factory->make(new GraphId('known')));
    }

    public function testMakeThrowsExceptionIfUnsupported(): void
    {
        $factory = new PredefinedStateMediatorFactory();
        $this->expectException(UnsupportedGraph::class);
        $factory->make(new GraphId('unknown'));
    }
}
