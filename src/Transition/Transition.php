<?php

/**
 * Transition.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Transition;

use IronBound\State\State\StateId;

interface Transition
{
    /**
     * Get the transition's id.
     *
     * @return TransitionId
     */
    public function getId(): TransitionId;

    /**
     * Get the initial states the subject can be in to undergo this transition.
     *
     * @return StateId[]
     */
    public function getInitialStates(): iterable;

    /**
     * Get the final state the subject will be in after completing this transition.
     *
     * @return StateId
     */
    public function getFinalState(): StateId;

    /**
     * Get the Guard for a transition.
     *
     * This will be called when evaluating the transition to determine
     * if the subject meets all the requirements.
     *
     * @return Guard|null
     */
    public function getGuard(): ?Guard;
}
