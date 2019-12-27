<?php

/**
 * Test the Immutable Graph.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Tests\Graph;

use IronBound\State\Graph\{Graph, MutableGraph};
use IronBound\State\Exception\{DuplicateState, IncompleteStateDefinition, UnknownState};
use IronBound\State\Transition\{ImmutableTransition, MutableTransition, Transition, TransitionId};
use IronBound\State\State\{ImmutableState, MutableState, State, StateId, StateType};

use function IronBound\State\{containsTransitionId, map};

class MutableGraphTest extends GraphTest
{
    protected $inactive;
    protected $deactivate;

    protected function setUp(): void
    {
        parent::setUp();
        $this->inactive   = new StateId('inactive');
        $this->deactivate = new TransitionId('deactivate');
    }

    public function testAddState(): void
    {
        $graph = $this->makeGraph();
        $graph->addState(new MutableState($this->inactive, StateType::NORMAL()));

        $this->assertTrue($graph->getStates()->contains($this->inactive));
    }

    public function testAddStateThrowsExceptionForDuplicateState(): void
    {
        $graph = $this->makeGraph();

        $this->expectException(DuplicateState::class);
        $graph->addState(new MutableState($this->active, StateType::NORMAL()));
    }

    public function testAddTransition(): void
    {
        $graph = $this->makeGraph();
        $graph->addState(new MutableState($this->inactive, StateType::NORMAL()));
        $graph->addTransition(new MutableTransition($this->deactivate, [ $this->active ], $this->inactive));

        $this->assertTrue($graph->getTransitions()->contains($this->deactivate));
    }

    public function testAddTransitionAddsTransitionToInitialStates(): void
    {
        $graph = $this->makeGraph();
        $graph->addState(new MutableState($this->inactive, StateType::NORMAL()));
        $graph->addTransition(new MutableTransition($this->deactivate, [ $this->active ], $this->inactive));

        $this->assertTrue(containsTransitionId(
            $this->deactivate,
            ...$graph->getStates()->get($this->active)->getTransitions()
        ));
    }

    public function testAddTransitionThrowsExceptionIfCannotAddTransitionToInitialState(): void
    {
        $graph = $this->makeGraph();
        $graph->addState(new ImmutableState(
            new StateId('trash'),
            StateType::NORMAL(),
            []
        ));

        $this->expectException(IncompleteStateDefinition::class);
        $graph->addTransition(new MutableTransition(
            new TransitionId('restore'),
            [ new StateId('trash') ],
            $this->active
        ));
    }

    public function testAddTransitionCleansUpModifiedStateIfEncountersImmutableState(): void
    {
        $graph = $this->makeGraph();
        $graph->addState(new MutableState(
            new StateId('archived'),
            StateType::NORMAL(),
            []
        ));
        $graph->addState(new ImmutableState(
            new StateId('trash'),
            StateType::NORMAL(),
            []
        ));

        $this->expectException(IncompleteStateDefinition::class);
        $graph->addTransition(new MutableTransition(
            new TransitionId('restore'),
            [ new StateId('archived'), new StateId('trash') ],
            $this->active
        ));

        $this->assertFalse(containsTransitionId(
            new TransitionId('restore'),
            ...$graph->getStates()->get(new StateId('archived'))->getTransitions()
        ));
    }

    public function testAddTransitionDoesNotThrowExceptionIfImmutableStateAlreadyContainsTransition(): void
    {
        $graph = $this->makeGraph();
        $graph->addState(new ImmutableState(
            new StateId('trash'),
            StateType::NORMAL(),
            [
                new TransitionId('restore')
            ]
        ));
        $graph->addTransition(new MutableTransition(
            new TransitionId('restore'),
            [ new StateId('trash') ],
            $this->active
        ));

        $this->addToAssertionCount(1);
    }

    public function testAddTransitionThrowsExceptionIfInitialStateNotFound(): void
    {
        $graph = $this->makeGraph();
        $this->expectException(UnknownState::class);
        $graph->addTransition(new MutableTransition(
            $this->deactivate,
            [ new StateId('unknown') ],
            $this->active
        ));
    }

    public function testAddTransitionThrowsExceptionIfFinalStateNotFound(): void
    {
        $graph = $this->makeGraph();
        $this->expectException(UnknownState::class);
        $graph->addTransition(new MutableTransition($this->deactivate, [ $this->active ], $this->inactive));
    }

    public function testToImmutable(): void
    {
        $graph     = $this->makeGraph();
        $immutable = $graph->toImmutable();

        $this->assertEquals($graph->getId()->getName(), $immutable->getId()->getName());

        $this->assertEquals(
            map($graph->getStates(), static function (State $state) {
                return $state->getId()->getName();
            }),
            map($immutable->getStates(), static function (State $state) {
                return $state->getId()->getName();
            })
        );
        $this->assertEquals(
            map($graph->getTransitions(), static function (Transition $transition) {
                return $transition->getId()->getName();
            }),
            map($immutable->getTransitions(), static function (Transition $transition) {
                return $transition->getId()->getName();
            })
        );

        foreach ($immutable->getStates() as $state) {
            $this->assertInstanceOf(ImmutableState::class, $state);
        }

        foreach ($immutable->getTransitions() as $transition) {
            $this->assertInstanceOf(ImmutableTransition::class, $transition);
        }
    }

    /**
     * @return Graph|MutableGraph
     */
    protected function makeGraph(): Graph
    {
        return (new MutableGraph($this->graphId))
            ->addState(new MutableState($this->pending, StateType::INITIAL(), [ $this->activate ]))
            ->addState(new MutableState($this->active, StateType::NORMAL(), []))
            ->addTransition(new MutableTransition($this->activate, [ $this->pending ], $this->active));
    }
}
