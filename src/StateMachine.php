<?php

/**
 * The State Machine interface. A new state machine is created for every stateful
 * object to be evaluated.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State;

use IronBound\State\Exception\CannotTransition;
use IronBound\State\Graph\Graph;
use IronBound\State\State\State;
use IronBound\State\Transition\{Evaluation, Transition, TransitionId};

interface StateMachine
{
    /**
     * Get the subject of the state machine.
     *
     * @return object
     */
    public function getSubject(): object;

    /**
     * Get the state graph being used.
     *
     * @return Graph
     */
    public function getGraph(): Graph;

    /**
     * Get the current state of the object.
     *
     * @return State
     */
    public function getCurrentState(): State;

    /**
     * Apply a transition.
     *
     * @param TransitionId $transitionId The transition to apply.
     * @param array        $parameters   Parameters to customize behavior or provide additional context.
     */
    public function apply(TransitionId $transitionId, array $parameters = []): void;

    /**
     * Evaluate a transition.
     *
     * @param TransitionId $transitionId The transition to apply.
     * @param array        $parameters   Parameters to customize behavior or provide additional context.
     *
     * @return Evaluation
     */
    public function evaluate(TransitionId $transitionId, array $parameters = []): Evaluation;

    /**
     * Get a list of the available transitions to the subject.
     *
     * @return Transition[]
     */
    public function getAvailableTransitions(): iterable;
}
