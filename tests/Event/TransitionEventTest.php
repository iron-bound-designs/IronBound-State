<?php

/**
 * Test the TransitionEvent object.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Tests\Event;

use IronBound\State\Event\TransitionEvent;
use IronBound\State\StateMachine;
use IronBound\State\Transition\Transition;
use PHPUnit\Framework\TestCase;

class TransitionEventTest extends TestCase
{
    public function testGetTransition(): void
    {
        $transition = $this->createMock(Transition::class);
        $event      = new TransitionEvent($this->createMock(StateMachine::class), $transition, []);

        $this->assertSame($transition, $event->getTransition());
    }

    public function testGetMachine(): void
    {
        $machine = $this->createMock(StateMachine::class);
        $event   = new TransitionEvent($machine, $this->createMock(Transition::class), []);

        $this->assertSame($machine, $event->getMachine());
    }

    public function testGetParameters(): void
    {
        $parameters = [ 'a' => 1 ];
        $event      = new TransitionEvent(
            $this->createMock(StateMachine::class),
            $this->createMock(Transition::class),
            $parameters
        );

        $this->assertEquals($parameters, $event->getParameters());
    }
}
