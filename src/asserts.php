<?php

/**
 * Custom assertion functions.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State;

use IronBound\State\Ds\{MutableStateSet, MutableTransitionSet};
use IronBound\State\State\{State, StateType};
use IronBound\State\Exception\{InitialStateRequired, IsolatedState, UnknownState};
use IronBound\State\Graph\Graph;

/**
 * Asserts that a graph is correctly constructed.
 *
 * Each state must be referenced by at least one transition,
 * and each transition must only reference defined states.
 *
 * @param Graph $graph
 *
 * @throws InitialStateRequired If incorrect initial states.
 * @throws UnknownState If a transition references an unknown state.
 * @throws IsolatedState If a state is not referenced by any transitions.
 */
function assertValidGraph(Graph $graph): void
{
    $initial = filter($graph->getStates(), static function (State $state) {
        return $state->getType()->equals(StateType::INITIAL());
    });

    if (count($initial) === 0) {
        throw new InitialStateRequired(sprintf(
            'The %s graph does not have an initial state.',
            $graph->getId()
        ));
    }

    if (count($initial) > 1) {
        throw new InitialStateRequired(sprintf(
            'The %s graph has %d initial states. Only 1 initial state is allowed.',
            $graph->getId(),
            count($initial)
        ));
    }

    $states      = new MutableStateSet($graph->getStates());
    $transitions = new MutableTransitionSet($graph->getTransitions());

    $seen = [];

    foreach ($transitions as $transition) {
        foreach ($transition->getInitialStates() as $stateId) {
            if (! $states->contains($stateId)) {
                throw new UnknownState(sprintf(
                    "The %s transition references an unknown initial state '%s'.",
                    $transition->getId(),
                    $stateId
                ));
            }

            $seen[ $stateId->getName() ] = true;
        }

        if (! $states->contains($transition->getFinalState())) {
            throw new UnknownState(sprintf(
                "The %s transition references an unknown final state '%s'.",
                $transition->getId(),
                $transition->getFinalState()
            ));
        }

        $seen[ $transition->getFinalState()->getName() ] = true;
    }

    foreach ($states as $state) {
        if (! isset($seen[ $state->getId()->getName() ])) {
            throw IsolatedState::create($state->getId());
        }
    }
}

/**
 * Asserts that every member of the list is an instance of the given class or interface.
 *
 * @param string   $class
 * @param iterable $list
 */
function assertInstancesOf(string $class, iterable $list): void
{
    foreach ($list as $value) {
        if (! $value instanceof $class) {
            throw new \InvalidArgumentException(sprintf(
                'Expected value to be an instance of %s, %s given.',
                $class,
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }
    }
}
