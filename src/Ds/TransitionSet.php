<?php

/**
 * Transition set interface.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Ds;

use IronBound\State\Exception\UnknownTransition;
use IronBound\State\Transition\{Transition, TransitionId};

interface TransitionSet extends \IteratorAggregate, \Countable
{
    /**
     * Check if a transition with the given id is contained within the set.
     *
     * @param TransitionId $id
     *
     * @return bool
     */
    public function contains(TransitionId $id): bool;

    /**
     * Get a transition from the set by id.
     *
     * @param TransitionId $id
     *
     * @return Transition
     *
     * @throws UnknownTransition If the transition is not contained within the set.
     */
    public function get(TransitionId $id): Transition;

    /**
     * Get an iterator to traverse the set.
     *
     * @return \Traversable|Transition[]
     */
    public function getIterator();
}
