<?php

/**
 * Test custom assert functions.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Tests;

use IronBound\State\Graph\{GraphId, ImmutableGraph, MutableGraph};
use IronBound\State\Exception\InitialStateRequired;
use IronBound\State\Exception\IsolatedState;
use IronBound\State\Exception\UnknownState;
use IronBound\State\Transition\ImmutableTransition;
use IronBound\State\Transition\MutableTransition;
use IronBound\State\Transition\TransitionId;
use IronBound\State\State\{ImmutableState, MutableState, StateId, StateType};
use PHPUnit\Framework\TestCase;

use function IronBound\State\assertInstancesOf;
use function IronBound\State\assertValidGraph;

class AssertsTest extends TestCase
{
    private $graphId;
    private $pending;
    private $active;
    private $activate;
    private $inactive;
    private $deactivate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->graphId    = new GraphId('default');
        $this->pending    = new StateId('pending');
        $this->active     = new StateId('active');
        $this->inactive   = new StateId('inactive');
        $this->activate   = new TransitionId('activate');
        $this->deactivate = new TransitionId('deactivate');
    }

    public function testAssertInstancesOf(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        assertInstancesOf(\stdClass::class, [ $this ]);
    }

    public function testAssertValidGraph(): void
    {
        $graph = new ImmutableGraph(
            $this->graphId,
            [
                new ImmutableState($this->pending, StateType::INITIAL(), [ $this->activate ]),
                new ImmutableState($this->active, StateType::NORMAL(), [ $this->deactivate ]),
                new ImmutableState($this->inactive, StateType::FINAL(), []),
            ],
            [
                new ImmutableTransition($this->activate, [ $this->pending ], $this->active),
                new ImmutableTransition($this->deactivate, [ $this->active ], $this->inactive),
            ]
        );

        assertValidGraph($graph);
        $this->addToAssertionCount(1);
    }

    public function testAssertValidGraphThrowsExceptionIfNoInitialStates(): void
    {
        $graph = new MutableGraph($this->graphId);
        $graph->addState(new MutableState($this->active));

        $this->expectException(InitialStateRequired::class);
        $this->expectExceptionMessage('does not have an initial state');

        assertValidGraph($graph);
    }

    public function testAssertValidGraphThrowsExceptionIfTooManyInitialStates(): void
    {
        $graph = new MutableGraph($this->graphId);
        $graph->addState(new MutableState($this->pending, StateType::INITIAL()));
        $graph->addState(new MutableState($this->active, StateType::INITIAL()));

        $this->expectException(InitialStateRequired::class);
        $this->expectExceptionMessage('has 2 initial states');

        assertValidGraph($graph);
    }

    public function testAssertValidGraphThrowsExceptionIfTransitionReferencesUnknownInitialState(): void
    {
        $graph = new ImmutableGraph(
            $this->graphId,
            [
                new ImmutableState($this->pending, StateType::INITIAL()),
                new ImmutableState($this->inactive, StateType::NORMAL()),
            ],
            [
                new ImmutableTransition($this->deactivate, [ $this->active ], $this->inactive),
            ]
        );

        $this->expectException(UnknownState::class);
        $this->expectExceptionMessage("references an unknown initial state 'active'");

        assertValidGraph($graph);
    }

    public function testAssertValidGraphThrowsExceptionIfTransitionReferencesUnknownFinalState(): void
    {
        $graph = new ImmutableGraph(
            $this->graphId,
            [
                new ImmutableState($this->pending, StateType::INITIAL()),
            ],
            [
                new ImmutableTransition($this->activate, [ $this->pending ], $this->active),
            ]
        );

        $this->expectException(UnknownState::class);
        $this->expectExceptionMessage("references an unknown final state 'active'");

        assertValidGraph($graph);
    }

    public function testAssertValidGraphThrowsExceptionIfStateIsNotReferencedByAnyTransitions(): void
    {
        $graph = new MutableGraph($this->graphId);
        $graph->addState(new MutableState($this->pending, StateType::INITIAL()));
        $graph->addState(new MutableState($this->active));
        $graph->addState(new MutableState($this->inactive));
        $graph->addTransition(new MutableTransition($this->activate, [ $this->pending ], $this->active));

        $this->expectException(IsolatedState::class);
        $this->expectExceptionMessage("The 'inactive' state is not referenced by any transitions.");

        assertValidGraph($graph);
    }
}
