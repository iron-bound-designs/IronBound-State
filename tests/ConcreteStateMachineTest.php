<?php

/**
 * Test the Concrete State Machine.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Tests;

use IronBound\State\ConcreteStateMachine;
use IronBound\State\Event\AfterTransitionEvent;
use IronBound\State\Event\BeforeTransitionEvent;
use IronBound\State\Event\TestTransitionEvent;
use IronBound\State\Exception\CannotTransition;
use IronBound\State\Graph\{Graph, GraphId, ImmutableGraph};
use IronBound\State\State\{ImmutableState, StateId, StateType};
use IronBound\State\StateMachine;
use IronBound\State\StateMediator\{PropertyStateMediator, StateMediator};
use IronBound\State\Transition\{Evaluation, Guard, ImmutableTransition, Transition, TransitionId};
use PHPUnit\Framework\TestCase;

use Psr\EventDispatcher\EventDispatcherInterface;
use function IronBound\State\mapMethod;

class ConcreteStateMachineTest extends TestCase
{
    protected static $graphId;
    protected static $pending;
    protected static $active;
    protected static $inactive;
    protected static $activate;
    protected static $deactivate;

    protected static function setupFixtures(): void
    {
        // We initial these here so we have access to them in Data Providers. Yes this is ugly.
        self::$graphId    = new GraphId('default');
        self::$pending    = new StateId('pending');
        self::$active     = new StateId('active');
        self::$inactive   = new StateId('inactive');
        self::$activate   = new TransitionId('activate');
        self::$deactivate = new TransitionId('deactivate');
    }

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::setupFixtures();
    }

    public function testGetSubject(): void
    {
        $subject = $this->getSubject();
        $machine = new ConcreteStateMachine($this->getMediator(), $this->getGraph(), $subject);

        $this->assertSame($subject, $machine->getSubject());
    }

    public function testGetGraph(): void
    {
        $graph   = $this->getGraph();
        $machine = new ConcreteStateMachine($this->getMediator(), $graph, $this->getSubject());

        $this->assertSame($graph, $machine->getGraph());
    }

    public function testGetCurrentState(): void
    {
        $machine = new ConcreteStateMachine($this->getMediator(), $this->getGraph(), $this->getSubject(self::$active));
        $this->assertEquals(self::$active, $machine->getCurrentState()->getId());
    }

    public function testGetCurrentStateUsesInitialStateIfNoStateSet(): void
    {
        $machine = new ConcreteStateMachine($this->getMediator(), $this->getGraph(), $this->getSubject());
        $this->assertEquals(self::$pending, $machine->getCurrentState()->getId());
    }

    /**
     * @dataProvider dpEvaluate
     *
     * @param bool         $expectedIsValid Whether the evaluation should be valid.
     * @param TransitionId $transitionId    The transition id to test.
     * @param StateId|null $initialState    The initial state of the subject.
     */
    public function testEvaluate(bool $expectedIsValid, TransitionId $transitionId, StateId $initialState = null): void
    {
        $machine = new ConcreteStateMachine($this->getMediator(), $this->getGraph(), $this->getSubject($initialState));

        $evaluation = $machine->evaluate($transitionId);
        $this->assertEquals($expectedIsValid, $evaluation->isValid());

        if ($evaluation->isInvalid()) {
            $this->assertGreaterThanOrEqual(1, count($evaluation->getReasons()));
        }
    }

    public static function dpEvaluate(): array
    {
        self::setupFixtures();

        return [
            [ true, self::$activate ],
            [ true, self::$activate, self::$pending ],
            [ false, self::$activate, self::$active ],
            [ false, self::$activate, self::$inactive ],

            [ false, self::$deactivate ],
            [ false, self::$deactivate, self::$pending ],
            [ true, self::$deactivate, self::$active ],
            [ false, self::$deactivate, self::$inactive ],
        ];
    }

    public function testEvaluateCallsGuard(): void
    {
        $guard = $this->createMock(Guard::class);
        $guard->expects($this->once())
            ->method('__invoke')
            ->with(
                $this->callback(static function (StateMachine $actual) use (&$machine) {
                    return $actual === $machine;
                }),
                $this->callback(static function (Transition $actual) use (&$transition) {
                    return $actual === $transition;
                })
            )
            ->willReturn(Evaluation::invalid('Error'));

        $graph   = new ImmutableGraph(
            self::$graphId,
            [
                new ImmutableState(self::$pending, StateType::INITIAL(), [ self::$activate ]),
                new ImmutableState(self::$active, StateType::NORMAL(), [ self::$deactivate ]),
            ],
            [
                $transition = new ImmutableTransition(self::$activate, [ self::$pending ], self::$active, $guard),
            ]
        );
        $machine = new ConcreteStateMachine($this->getMediator(), $graph, $this->getSubject());

        $evaluation = $machine->evaluate(self::$activate);
        $this->assertTrue($evaluation->isInvalid());
        $this->assertEquals([ 'Error' ], $evaluation->getReasons());
    }

    public function testEvaluateDispatchesEventAndMarksEvaluationAsInvalidIfRejected(): void
    {
        $machine    = new ConcreteStateMachine($this->getMediator(), $this->getGraph(), $this->getSubject());
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(static function (TestTransitionEvent $event) {
                $event->reject('Error');

                return true;
            }))
            ->willReturnArgument(0);

        $machine->setEventDispatcher($dispatcher);
        $evaluation = $machine->evaluate(self::$activate);

        $this->assertTrue($evaluation->isInvalid());
        $this->assertEquals([ 'Error' ], $evaluation->getReasons());
    }

    /**
     * @dataProvider dpGetAvailableTransitions
     *
     * @param array        $expectedAvailable Expected transition ids to be available.
     * @param StateId|null $initialState      The initial state of the subject.
     */
    public function testGetAvailableTransitions(array $expectedAvailable, StateId $initialState = null): void
    {
        $machine = new ConcreteStateMachine($this->getMediator(), $this->getGraph(), $this->getSubject($initialState));

        $available = $machine->getAvailableTransitions();

        $this->assertEquals($expectedAvailable, mapMethod($available, 'getId'));
    }

    public static function dpGetAvailableTransitions(): array
    {
        self::setupFixtures();

        return [
            'no state' => [ [ self::$activate ] ],
            'pending'  => [ [ self::$activate ], self::$pending ],
            'active'   => [ [ self::$deactivate ], self::$active ],
            'inactive' => [ [], self::$inactive ],
        ];
    }

    public function testApply(): void
    {
        $subject  = $this->getSubject();
        $mediator = $this->getMediator();
        $machine  = new ConcreteStateMachine($mediator, $this->getGraph(), $subject);

        $machine->apply(self::$activate);
        $this->assertEquals(self::$active, $mediator->getState($subject));
        $this->assertEquals(self::$active, $machine->getCurrentState()->getId());
    }

    public function testApplyThrowsExceptionIfCannotTransition(): void
    {
        $subject  = $this->getSubject();
        $mediator = $this->getMediator();
        $machine  = new ConcreteStateMachine($mediator, $this->getGraph(), $subject);

        $this->expectException(CannotTransition::class);
        $machine->apply(self::$deactivate);
    }

    public function testApplyFiresEvents(): void
    {
        $machine    = new ConcreteStateMachine($this->getMediator(), $this->getGraph(), $this->getSubject());
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->exactly(3))
            ->method('dispatch')
            ->withConsecutive(
                [ $this->isInstanceOf(TestTransitionEvent::class) ],
                [ $this->isInstanceOf(BeforeTransitionEvent::class) ],
                [ $this->isInstanceOf(AfterTransitionEvent::class) ],
            )
            ->willReturnArgument(0);

        $machine->setEventDispatcher($dispatcher);
        $machine->apply(self::$activate);
    }

    protected function getGraph(): Graph
    {
        return new ImmutableGraph(
            self::$graphId,
            [
                new ImmutableState(self::$pending, StateType::INITIAL(), [ self::$activate ]),
                new ImmutableState(self::$active, StateType::NORMAL(), [ self::$deactivate ]),
                new ImmutableState(self::$inactive, StateType::FINAL(), []),
            ],
            [
                new ImmutableTransition(self::$activate, [ self::$pending ], self::$active),
                new ImmutableTransition(self::$deactivate, [ self::$active ], self::$inactive),
            ]
        );
    }

    protected function getMediator(): StateMediator
    {
        return new PropertyStateMediator('status');
    }

    protected function getSubject(StateId $withState = null): object
    {
        $subject         = new \stdClass();
        $subject->status = '';

        if ($withState) {
            $subject->status = $withState->getName();
        }

        return $subject;
    }
}
