<?php

/**
 * Test the mutable state class.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Tests\State;

use IronBound\State\Exception\CannotAddTransitionToFinalState;
use IronBound\State\State\MutableState;
use IronBound\State\State\StateId;
use IronBound\State\State\StateType;
use IronBound\State\Transition\TransitionId;
use PHPUnit\Framework\TestCase;

use function IronBound\State\mapMethod;

class MutableStateTest extends TestCase
{
    public function testGetTransitions(): void
    {
        $state       = $this->makeState();
        $transitions = $state->getTransitions();

        $this->assertCount(1, $transitions);
        $this->assertEquals('activate', $transitions[0]->getName());
    }

    public function testGetId(): void
    {
        $this->assertEquals('pending', $this->makeState()->getId()->getName());
    }

    public function testGetType(): void
    {
        $state = $this->makeState();
        $this->assertEquals(StateType::INITIAL()->getValue(), $state->getType()->getValue());
    }

    public function testThrowsExceptionIfProvidedTransitionsAndInFinalState(): void
    {
        $this->expectException(CannotAddTransitionToFinalState::class);
        new MutableState(new StateId('test'), StateType::FINAL(), [ new TransitionId('transition') ]);
    }

    public function testAddTransition(): void
    {
        $state      = $this->makeState();
        $transition = new TransitionId('deactivate');
        $state->addTransition($transition);

        $transitions = $state->getTransitions();
        $this->assertCount(2, $transitions);
        $this->assertEquals('deactivate', $transitions[1]->getName());
    }

    public function testAddTransitionThrowsExceptionIfInFinalState(): void
    {
        $state      = new MutableState(new StateId('test'), StateType::FINAL());
        $transition = new TransitionId('deactivate');

        $this->expectException(CannotAddTransitionToFinalState::class);
        $state->addTransition($transition);
    }

    public function testToImmutable(): void
    {
        $state     = $this->makeState();
        $immutable = $state->toImmutable();

        $this->assertEquals($state->getId()->getName(), $immutable->getId()->getName());
        $this->assertEquals($state->getType()->getValue(), $immutable->getType()->getValue());
        $this->assertEquals(
            mapMethod($state->getTransitions(), 'getName'),
            mapMethod($immutable->getTransitions(), 'getName')
        );
    }

    private function makeState(): MutableState
    {
        return new MutableState(
            new StateId('pending'),
            StateType::INITIAL(),
            [
                new TransitionId('activate'),
            ]
        );
    }
}
