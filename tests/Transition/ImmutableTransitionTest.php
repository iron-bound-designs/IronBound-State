<?php

/**
 * Test the transition.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Tests\Transition;

use IronBound\State\State\StateId;
use IronBound\State\Transition\{Guard, ImmutableTransition, TransitionId};
use PHPUnit\Framework\TestCase;
use function IronBound\State\getAttribute;
use function IronBound\State\hasAttribute;

class ImmutableTransitionTest extends TestCase
{
    public function testGetId(): void
    {
        $transition = $this->makeTransition();
        $this->assertEquals('activate', $transition->getId()->getName());
    }

    public function testGetInitialStates(): void
    {
        $transition    = $this->makeTransition();
        $initialStates = $transition->getInitialStates();

        $this->assertCount(1, $initialStates);
        $this->assertEquals('pending', $initialStates[0]->getName());
    }

    public function testGetFinalState(): void
    {
        $transition = $this->makeTransition();

        $this->assertEquals('active', $transition->getFinalState()->getName());
    }

    public function testGetGuard(): void
    {
        $guard      = $this->createMock(Guard::class);
        $transition = new ImmutableTransition(
            new TransitionId('activate'),
            [ new StateId('pending') ],
            new StateId('active'),
            $guard
        );

        $this->assertSame($guard, $transition->getGuard());
    }

    public function testGetAttributes(): void
    {
        $transition = $this->makeTransition();
        $this->assertTrue(hasAttribute($transition, 'label'));
        $this->assertEquals('Activate', getAttribute($transition, 'label'));
    }

    private function makeTransition(): ImmutableTransition
    {
        return new ImmutableTransition(
            new TransitionId('activate'),
            [
                new StateId('pending'),
            ],
            new StateId('active'),
            null,
            [ 'label' => 'Activate' ]
        );
    }
}
