<?php

/**
 * Guard a transition from completing.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Transition;

use IronBound\State\StateMachine;

interface Guard
{
    /**
     * Prevent a transition from being applied.
     *
     * This method is called after determining that the transition
     * is available to the subject's current state.
     *
     * @param StateMachine $machine    The state machine evaluating the guard. Use it to
     *                                 gain access to the subject and current state.
     * @param Transition   $transition The transition being applied. Provided in case the
     *                                 guard cannot maintain a reference to it's transition.
     *
     * @return Evaluation The evaluation result.
     */
    public function __invoke(StateMachine $machine, Transition $transition): Evaluation;
}
