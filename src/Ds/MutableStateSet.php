<?php

/**
 * Set of States.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Ds;

use IronBound\State\Exception\DuplicateState;
use IronBound\State\State\{State, StateId};

final class MutableStateSet implements StateSet
{
    use StateSetTrait;

    /**
     * MutableStateSet constructor.
     *
     * @param State[] $states The initial list of states.
     */
    public function __construct(iterable $states = [])
    {
        $this->addStates($states);
    }

    /**
     * Add a state to the set.
     *
     * @param State $state
     *
     * @return $this
     *
     * @throws DuplicateState Thrown if this state is already contained in this set.
     */
    public function addState(State $state): self
    {
        $this->addStates([ $state ]);

        return $this;
    }

    /**
     * Remove a state from the set.
     *
     * @param StateId $id
     *
     * @return $this
     */
    public function removeState(StateId $id): self
    {
        unset($this->storage[ $id->getName() ]);

        return $this;
    }

    /**
     * Convert this to an immutable set.
     *
     * @return ImmutableStateSet
     */
    public function toImmutable(): ImmutableStateSet
    {
        return new ImmutableStateSet($this->storage);
    }
}
