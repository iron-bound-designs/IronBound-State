<?php

/**
 * Graphs define the set of available states and the
 * list of transitions between those states.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Graph;

use IronBound\State\Ds\StateSet;
use IronBound\State\Ds\TransitionSet;

interface Graph
{
    /**
     * Get the graph's id.
     *
     * @return GraphId
     */
    public function getId(): GraphId;

    /**
     * Get a list of all the available states.
     *
     * @return StateSet
     */
    public function getStates(): StateSet;

    /**
     * Get a list of all the available transitions.
     *
     * @return TransitionSet
     */
    public function getTransitions(): TransitionSet;
}
