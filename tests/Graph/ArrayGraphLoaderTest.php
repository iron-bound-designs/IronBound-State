<?php

/**
 * Test the Array Graph loader.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Tests\Graph;

use IronBound\State\Graph\ArrayGraphLoader;
use IronBound\State\Graph\GraphId;
use IronBound\State\State\StateId;
use IronBound\State\State\StateType;
use IronBound\State\StateMachine;
use IronBound\State\Transition\Evaluation;
use IronBound\State\Transition\Guard;
use IronBound\State\Transition\TransitionId;
use PHPUnit\Framework\TestCase;

use function IronBound\State\containsTransitionId;
use function IronBound\State\mapMethod;

class ArrayGraphLoaderTest extends TestCase
{
    public function testMake(): void
    {
        $guard   = $this->createMock(Guard::class);
        $graphId = new GraphId('default');
        $loader  = new ArrayGraphLoader($graphId, [
            'states'      => [
                'pending'  => [
                    'type' => StateType::INITIAL,
                ],
                'active',
                'inactive' => [],
            ],
            'transitions' => [
                'activate'   => [
                    'from'  => [ 'pending', 'inactive' ],
                    'to'    => 'active',
                    'guard' => static function () {
                        return Evaluation::invalid('Error');
                    }
                ],
                'deactivate' => [
                    'from'  => 'active',
                    'to'    => 'inactive',
                    'guard' => $guard,
                ],
            ]
        ]);
        $graph   = $loader->make($graphId);

        $this->assertCount(3, $graph->getStates());
        $this->assertCount(2, $graph->getTransitions());

        $pending = $graph->getStates()->get(new StateId('pending'));
        $this->assertEquals(StateType::INITIAL, $pending->getType()->getValue());
        $this->assertTrue(containsTransitionId(
            new TransitionId('activate'),
            ...$pending->getTransitions()
        ));
        $this->assertFalse(containsTransitionId(
            new TransitionId('deactivate'),
            ...$pending->getTransitions()
        ));

        $active = $graph->getStates()->get(new StateId('active'));
        $this->assertEquals(StateType::NORMAL, $active->getType()->getValue());
        $this->assertFalse(containsTransitionId(
            new TransitionId('activate'),
            ...$active->getTransitions()
        ));
        $this->assertTrue(containsTransitionId(
            new TransitionId('deactivate'),
            ...$active->getTransitions()
        ));

        $inactive = $graph->getStates()->get(new StateId('inactive'));
        $this->assertEquals(StateType::NORMAL, $inactive->getType()->getValue());
        $this->assertTrue(containsTransitionId(
            new TransitionId('activate'),
            ...$inactive->getTransitions()
        ));
        $this->assertFalse(containsTransitionId(
            new TransitionId('deactivate'),
            ...$inactive->getTransitions()
        ));

        $activate = $graph->getTransitions()->get(new TransitionId('activate'));
        $this->assertEquals([ 'pending', 'inactive' ], mapMethod($activate->getInitialStates(), 'getName'));
        $this->assertEquals('active', $activate->getFinalState()->getName());
        $this->assertTrue(($activate->getGuard()($this->createMock(StateMachine::class), $activate))->isInvalid());

        $deactivate = $graph->getTransitions()->get(new TransitionId('deactivate'));
        $this->assertEquals([ 'active' ], mapMethod($deactivate->getInitialStates(), 'getName'));
        $this->assertEquals('inactive', $deactivate->getFinalState()->getName());
        $this->assertSame($guard, $deactivate->getGuard());
    }

    public function testSupports(): void
    {
        $loader = new ArrayGraphLoader(new GraphId('default'), []);
        $this->assertTrue($loader->supports(new GraphId('default')));
        $this->assertFalse($loader->supports(new GraphId('status')));
    }
}
