<?php

/**
 * Interface to go between the StateMachine and the stateful object.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\StateMediator;

use IronBound\State\Exception\UnsupportedGraph;
use IronBound\State\State\StateId;

interface StateMediator
{
    /**
     * Get a subject's state.
     *
     * @param object $subject The subject to retrieve state information for.
     *
     * @return StateId|null
     */
    public function getState(object $subject): ?StateId;

    /**
     * Set a subject's state.
     *
     * @param object  $subject The subject to set the state information for.
     * @param StateId $state   The state to set.
     *
     * @throws UnsupportedGraph If the StateId belongs to the wrong graph.
     */
    public function setState(object $subject, StateId $state): void;
}
