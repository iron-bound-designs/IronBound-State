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

use IronBound\State\Ds\MutableStateSet;
use IronBound\State\Ds\MutableTransitionSet;
use IronBound\State\State\State;
use IronBound\State\State\StateType;
use IronBound\State\Exception\{IsolatedState, UnknownState};
use IronBound\State\Graph\Graph;

/**
 * Assets that a graph is correctly constructed.
 *
 * Each state must be referenced by at least one transition,
 * and each transition must only reference defined states.
 *
 * @param Graph $graph
 *
 * @throws UnknownState If a transition references an unknown state.
 * @throws IsolatedState If a state is not referenced by any transitions.
 */
function assertValidGraph(Graph $graph): void
{
    $initial = filter($graph->getStates(), static function (State $state) {
        return $state->getType()->equals(StateType::INITIAL());
    });

    if (count($initial) === 0) {
        throw new UnknownState(sprintf(
            'The %s graph does not have an initial state.',
            $graph->getId()
        ));
    }

    if (count($initial) > 1) {
        throw new UnknownState(sprintf(
            'The %s graph has %d initial states. Only 1 initial state is allowed.',
            $graph->getId(),
            $initial
        ));
    }

    $states      = new Ds\MutableStateSet($graph->getStates());
    $transitions = new MutableTransitionSet($graph->getTransitions());

    $seen = new MutableStateSet();

    foreach ($transitions as $transition) {
        foreach ($transition->getInitialStates() as $state) {
            if (! $states->contains($state)) {
                throw new UnknownState(sprintf(
                    'The %s transition referenced an unknown initial state %s.',
                    $transition->getId(),
                    $state
                ));
            }

            $seen->addState($state);
        }

        if (! $states->contains($transition->getFinalState())) {
            throw new UnknownState(sprintf(
                'The %s transition referenced an unknown final state %s.',
                $transition->getId(),
                $transition->getFinalState()
            ));
        }

        $seen->addState($states->get($transition->getFinalState()));
    }

    foreach ($states as $state) {
        if (! $seen->contains($state)) {
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
