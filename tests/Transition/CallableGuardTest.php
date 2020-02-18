<?php

/**
 * Test the callable guard.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Tests\Transition;

use IronBound\State\StateMachine;
use IronBound\State\Transition\CallableGuard;
use IronBound\State\Transition\Evaluation;
use IronBound\State\Transition\Transition;
use PHPUnit\Framework\TestCase;

class CallableGuardTest extends TestCase
{
    public function test(): void
    {
        $expectedMachine    = $this->createMock(StateMachine::class);
        $expectedTransition = $this->createMock(Transition::class);
        $expectedParameters = [ 'a' => 1 ];

        $guard = new CallableGuard(
            function (
                StateMachine $machine,
                Transition $transition,
                array $parameters
            ) use (
                $expectedMachine,
                $expectedTransition,
                $expectedParameters
            ) {
                $this->assertSame($expectedMachine, $machine);
                $this->assertSame($expectedTransition, $transition);
                $this->assertSame($expectedParameters, $parameters);

                return Evaluation::valid();
            }
        );

        $guard($expectedMachine, $expectedTransition, $expectedParameters);
    }
}
