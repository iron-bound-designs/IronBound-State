<?php

/**
 * Set of states.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Ds;

use IronBound\State\Exception\UnknownState;
use IronBound\State\State\{State, StateId};

interface StateSet extends \IteratorAggregate, \Countable
{
    /**
     * Check if a state with the given id is contained within the set.
     *
     * @param StateId $id
     *
     * @return bool
     */
    public function contains(StateId $id): bool;

    /**
     * Get a state from the set by id.
     *
     * @param StateId $id
     *
     * @return State
     *
     * @throws UnknownState If the state is not contained within the set.
     */
    public function get(StateId $id): State;

    /**
     * Get an iterator to traverse the set.
     *
     * @return \Traversable|State[]
     */
    public function getIterator();
}
