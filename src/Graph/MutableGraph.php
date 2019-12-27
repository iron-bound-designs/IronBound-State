<?php

/**
 * Mutable graph.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Graph;

use IronBound\State\Exception\{DuplicateState, IncompleteStateDefinition, UnknownState};
use IronBound\State\State\{MutableState, State};
use IronBound\State\Transition\{MutableTransition, Transition};
use IronBound\State\Ds\{StateSet, TransitionSet, MutableStateSet, MutableTransitionSet};

use function IronBound\State\containsTransitionId;

final class MutableGraph implements Graph
{
    /** @var GraphId */
    private $id;

    /** @var MutableStateSet */
    private $states;

    /** @var \IronBound\State\Ds\MutableTransitionSet */
    private $transitions;

    /**
     * MutableGraph constructor.
     *
     * @param GraphId $id
     */
    public function __construct(GraphId $id)
    {
        $this->id          = $id;
        $this->states      = new MutableStateSet();
        $this->transitions = new MutableTransitionSet();
    }

    /**
     * Add a state to this graph.
     *
     * @param State $state
     *
     * @return $this
     *
     * @throws DuplicateState If a state with the same id already exists.
     */
    public function addState(State $state): self
    {
        $this->states->addState($state);

        return $this;
    }

    /**
     * Add a transition to this graph.
     *
     * @param Transition $transition
     *
     * @return $this
     *
     * @throws UnknownState If the transition references a state that is not defined.
     * @throws IncompleteStateDefinition If a transition references a state that does not reference the transition
     *                                   and the state is not mutable.
     */
    public function addTransition(Transition $transition): self
    {
        /** @var MutableState[] $added */
        $added = [];

        foreach ($transition->getInitialStates() as $stateId) {
            $state = $this->getStates()->get($stateId);

            if (containsTransitionId($transition->getId(), ...$state->getTransitions())) {
                continue;
            }

            if ($state instanceof MutableState) {
                $state->addTransition($transition->getId());
                $added[] = $state;

                continue;
            }

            foreach ($added as $state) {
                $state->removeTransition($transition->getId());
            }

            throw IncompleteStateDefinition::missingTransition($stateId, $transition->getId());
        }

        // Trigger an Exception if the final state cannot be found.
        $this->getStates()->get($transition->getFinalState());

        $this->transitions->addTransition($transition);

        return $this;
    }

    /**
     * Construct an immutable graph from this mutable graph.
     *
     * Additionally, converts each state and transition to an immutable variant.
     *
     * @return ImmutableGraph
     */
    public function toImmutable(): ImmutableGraph
    {
        $states      = [];
        $transitions = [];

        foreach ($this->getStates() as $state) {
            if ($state instanceof MutableState) {
                $state = $state->toImmutable();
            }

            $states[] = $state;
        }

        foreach ($this->getTransitions() as $transition) {
            if ($transition instanceof MutableTransition) {
                $transition = $transition->toImmutable();
            }

            $transitions[] = $transition;
        }

        return new ImmutableGraph($this->getId(), $states, $transitions);
    }

    public function getId(): GraphId
    {
        return $this->id;
    }

    public function getStates(): StateSet
    {
        return $this->states->toImmutable();
    }

    public function getTransitions(): TransitionSet
    {
        return $this->transitions->toImmutable();
    }
}
