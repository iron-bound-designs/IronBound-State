<?php

/**
 * Concrete state machine.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State;

use IronBound\State\Exception\CannotTransition;
use IronBound\State\Graph\Graph;
use IronBound\State\State\State;
use IronBound\State\Transition\{Evaluation, TransitionId};
use IronBound\State\State\StateType;
use IronBound\State\StateMediator\StateMediator;

final class ConcreteStateMachine implements StateMachine
{
    /** @var StateMediator */
    private $mediator;

    /** @var Graph */
    private $graph;

    /** @var object */
    private $subject;

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
            throw CannotTransition::create($evaluation);
        }

        $transition = $this->getGraph()->getTransitions()->get($transitionId);
        $this->mediator->setState($this->getSubject(), $transition->getFinalState());
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
                $this->getSubject(),
                $transition,
                sprintf(
                    '%s is not in the list of available transitions.',
                    $transition->getId()
                )
            );
        }

        return Evaluation::valid(
            $this->getSubject(),
            $transition
        );
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
}
