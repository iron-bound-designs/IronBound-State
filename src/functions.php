<?php

/**
 * Utility functions.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State;

use IronBound\State\Exception\InitialStateRequired;
use IronBound\State\Exception\UnknownState;
use IronBound\State\Graph\Graph;
use IronBound\State\State\State;
use IronBound\State\State\StateId;
use IronBound\State\State\StateType;
use IronBound\State\Transition\TransitionId;

/**
 * Check if a transition id is included in a list of transition ids.
 *
 * @param TransitionId $needle
 * @param TransitionId ...$haystack
 *
 * @return bool
 */
function containsTransitionId(TransitionId $needle, TransitionId ...$haystack): bool
{
    return atLeastOne($haystack, static function (TransitionId $maybe) use ($needle) {
        return $needle->equals($maybe);
    });
}

/**
 * Check if a state id is included in a list of state ids.
 *
 * @param StateId $needle
 * @param StateId ...$haystack
 *
 * @return bool
 */
function containsStateId(StateId $needle, StateId ...$haystack): bool
{
    return atLeastOne($haystack, static function (StateId $maybe) use ($needle) {
        return $needle->equals($maybe);
    });
}

/**
 * Get the initial state for a graph.
 *
 * @param Graph $graph
 *
 * @return State
 *
 * @throws InitialStateRequired If no initial state exists.
 */
function getInitialState(Graph $graph): State
{
    foreach ($graph->getStates() as $state) {
        if ($state->getType()->equals(StateType::INITIAL())) {
            return $state;
        }
    }

    throw new InitialStateRequired(sprintf(
        'The %s graph does not have an initial state.',
        $graph->getId()
    ));
}
