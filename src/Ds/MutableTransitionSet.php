<?php

/**
 * Set of transitions.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Ds;

use IronBound\State\Exception\DuplicateTransition;
use IronBound\State\Transition\Transition;
use IronBound\State\Transition\TransitionId;

final class MutableTransitionSet implements TransitionSet
{
    use TransitionSetTrait;

    /**
     * MutableTransitionSet constructor.
     *
     * @param Transition[] $transitions The initial set of transitions.
     */
    public function __construct(iterable $transitions = [])
    {
        $this->addTransitions($transitions);
    }

    /**
     * Add a transition to the set.
     *
     * @param Transition $transition
     *
     * @return MutableTransitionSet
     *
     * @throws DuplicateTransition Thrown if this transition is already contained in this set.
     */
    public function addTransition(Transition $transition): self
    {
        $this->addTransitions([ $transition ]);

        return $this;
    }

    /**
     * Remove a transition from the set.
     *
     * @param TransitionId $id
     *
     * @return $this
     */
    public function removeTransition(TransitionId $id): self
    {
        unset($this->storage[ $id->getName() ]);

        return $this;
    }

    /**
     * Convert to an immutable set.
     *
     * @return ImmutableTransitionSet
     */
    public function toImmutable(): ImmutableTransitionSet
    {
        return new ImmutableTransitionSet($this->storage);
    }
}
