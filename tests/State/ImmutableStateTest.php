<?php

/**
 * Test the Immutable State.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Tests\State;

use IronBound\State\Exception\CannotAddTransitionToFinalState;
use IronBound\State\State\ImmutableState;
use IronBound\State\State\StateId;
use IronBound\State\State\StateType;
use IronBound\State\Transition\TransitionId;
use PHPUnit\Framework\TestCase;
use function IronBound\State\getAttribute;
use function IronBound\State\hasAttribute;

class ImmutableStateTest extends TestCase
{
    public function testGetId(): void
    {
        $state = $this->makeState();
        $this->assertEquals('pending', $state->getId()->getName());
    }

    public function testGetTransitions(): void
    {
        $state       = $this->makeState();
        $transitions = $state->getTransitions();

        $this->assertCount(1, $transitions);
        $this->assertEquals('activate', $transitions[0]->getName());
    }

    public function testGetType(): void
    {
        $state = $this->makeState();
        $this->assertEquals(StateType::INITIAL()->getValue(), $state->getType()->getValue());
    }

    public function testThrowsExceptionIfProvidedTransitionsAndInFinalState(): void
    {
        $this->expectException(CannotAddTransitionToFinalState::class);
        new ImmutableState(new StateId('test'), StateType::FINAL(), [ new TransitionId('transition') ]);
    }

    public function testGetAttributes(): void
    {
        $state = $this->makeState();
        $this->assertTrue(hasAttribute($state, 'label'));
        $this->assertEquals('Pending', getAttribute($state, 'label'));
    }

    private function makeState(): ImmutableState
    {
        return new ImmutableState(
            new StateId('pending'),
            StateType::INITIAL(),
            [
                new TransitionId('activate'),
            ],
            [ 'label' => 'Pending' ]
        );
    }
}
