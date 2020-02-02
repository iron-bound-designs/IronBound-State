<?php

/**
 * Test utility functions.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Tests;

use IronBound\State\Graph\{GraphId, ImmutableGraph};
use IronBound\State\State\{ImmutableState, StateId, StateType};
use IronBound\State\Transition\{ImmutableTransition, TransitionId};
use IronBound\State\AttributeAware;
use IronBound\State\Exception\InitialStateRequired;
use PHPUnit\Framework\TestCase;

use function IronBound\State\{containsStateId, containsTransitionId, getAttribute, getInitialState, hasAttribute};

class FunctionsTest extends TestCase
{
    public function testContainsTransitionId(): void
    {
        $this->assertTrue(containsTransitionId(
            new TransitionId('t1'),
            new TransitionId('t2'),
            new TransitionId('t1')
        ));
        $this->assertFalse(containsTransitionId(
            new TransitionId('t3'),
            new TransitionId('t2'),
            new TransitionId('t1')
        ));
    }

    public function testContainsStateId(): void
    {
        $this->assertTrue(containsStateId(
            new StateId('t1'),
            new StateId('t2'),
            new StateId('t1')
        ));
        $this->assertFalse(containsStateId(
            new StateId('t3'),
            new StateId('t2'),
            new StateId('t1')
        ));
    }

    public function testGetInitialState(): void
    {
        $graph = new ImmutableGraph(
            new GraphId('graph'),
            [
                new ImmutableState(new StateId('pending'), StateType::INITIAL(), [ new TransitionId('activate') ]),
                new ImmutableState(new StateId('active'), StateType::NORMAL(), [])
            ],
            [
                new ImmutableTransition(new TransitionId('activate'), [ new StateId('pending') ], new StateId('active'))
            ]
        );

        $initialState = getInitialState($graph);

        $this->assertEquals('pending', $initialState->getId());
    }

    public function testGetInitialStateThrowsExceptionIfNoInitialState(): void
    {
        $graph = new ImmutableGraph(
            new GraphId('graph'),
            [
                new ImmutableState(new StateId('pending'), StateType::NORMAL(), [ new TransitionId('activate') ]),
                new ImmutableState(new StateId('active'), StateType::NORMAL(), [])
            ],
            [
                new ImmutableTransition(new TransitionId('activate'), [ new StateId('pending') ], new StateId('active'))
            ]
        );

        $this->expectException(InitialStateRequired::class);
        getInitialState($graph);
    }

    public function testGetAttribute(): void
    {
        $mock = $this->createMock(AttributeAware::class);
        $mock->method('getAttributes')->willReturn([
            'a' => 1,
        ]);

        $this->assertSame(1, getAttribute($mock, 'a'));
        $this->assertNull(getAttribute($mock, 'b'));
        $this->assertSame(2, getAttribute($mock, 'b', 2));
    }

    public function testHasAttribute(): void
    {
        $mock = $this->createMock(AttributeAware::class);
        $mock->method('getAttributes')->willReturn([
            'a' => 1,
        ]);

        $this->assertTrue(hasAttribute($mock, 'a'));
        $this->assertFalse(hasAttribute($mock, 'b'));
    }
}
