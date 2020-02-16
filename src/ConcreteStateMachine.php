<?php

/**
 * Concrete state machine.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State;

use IronBound\State\Event\AfterTransitionEvent;
use IronBound\State\Event\BeforeTransitionEvent;
use IronBound\State\Event\TestTransitionEvent;
use IronBound\State\Exception\CannotTransition;
use IronBound\State\Graph\Graph;
use IronBound\State\State\State;
use IronBound\State\Transition\{Evaluation, TransitionId};
use IronBound\State\State\StateType;
use IronBound\State\StateMediator\StateMediator;
use Psr\EventDispatcher\EventDispatcherInterface;

final class ConcreteStateMachine implements StateMachine
{
    /** @var StateMediator */
    private $mediator;

    /** @var Graph */
    private $graph;

    /** @var object */
    private $subject;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * ConcreteStateMachine constructor.
     *
     * @param StateMediator $mediator
     * @param Graph         $graph
     * @param object        $subject
     */
    public function __construct(StateMediator $mediator, Graph $graph, object $subject)
    {
        $this->mediator = $mediator;
        $this->graph    = $graph;
        $this->subject  = $subject;
    }

    public function getSubject(): object
    {
        return $this->subject;
    }

    public function getGraph(): Graph
    {
        return $this->graph;
    }

    public function getCurrentState(): State
    {
        $stateId = $this->mediator->getState($this->getSubject());

        if ($stateId) {
            return $this->getGraph()->getStates()->get($stateId);
        }

        return getInitialState($this->getGraph());
    }

    public function apply(TransitionId $transitionId): void
    {
        $evaluation = $this->evaluate($transitionId);

        if (! $evaluation->isValid()) {
            throw CannotTransition::create($evaluation, $transitionId);
        }

        $transition = $this->getGraph()->getTransitions()->get($transitionId);

        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(new BeforeTransitionEvent($this, $transition));
        }

        $this->mediator->setState($this->getSubject(), $transition->getFinalState());

        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(new AfterTransitionEvent($this, $transition));
        }
    }

    public function evaluate(TransitionId $transitionId): Evaluation
    {
        $transition = $this->getGraph()->getTransitions()->get($transitionId);

        $state = $this->getCurrentState();
        $found = atLeastOne($state->getTransitions(), static function (TransitionId $id) use ($transitionId) {
            return $transitionId->equals($id);
        });

        if (! $found) {
            return Evaluation::invalid(
                sprintf(
                    '%s is not in the list of available transitions.',
                    $transition->getId()
                )
            );
        }

        if ($guard = $transition->getGuard()) {
            $evaluation = $guard($this, $transition);

            if ($evaluation->isInvalid()) {
                return $evaluation;
            }
        }

        if ($this->eventDispatcher) {
            $event = $this->eventDispatcher->dispatch(new TestTransitionEvent($this, $transition));

            if ($reasons = $event->getRejectionReasons()) {
                return Evaluation::invalid(...$reasons);
            }
        }

        return Evaluation::valid();
    }

    public function getAvailableTransitions(): iterable
    {
        if ($this->getCurrentState()->getType()->equals(StateType::FINAL())) {
            return;
        }

        foreach ($this->getGraph()->getTransitions() as $transition) {
            if ($this->evaluate($transition->getId())->isValid()) {
                yield $transition;
            }
        }
    }

    /**
     * Get the event dispatcher instance the State Machine is using.
     *
     * @return EventDispatcherInterface|null
     */
    public function getEventDispatcher(): ?EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    /**
     * Set the Event Dispatcher for the State Machine to dispatch events through.
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }
}
