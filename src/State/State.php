<?php

/**
 * State object.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\State;

use IronBound\State\Transition\TransitionId;

interface State
{
    /**
     * Get the state's id.
     *
     * @return StateId
     */
    public function getId(): StateId;

    /**
     * Get the state's type.
     *
     * @return StateType
     */
    public function getType(): StateType;

    /**
     * Gets a list of all available transitions from this state.
     *
     * @return TransitionId[]
     */
    public function getTransitions(): iterable;
}
