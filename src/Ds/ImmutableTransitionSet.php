<?php

/**
 * Immutable transition set.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Ds;

use IronBound\State\Transition\Transition;

final class ImmutableTransitionSet implements TransitionSet
{
    use TransitionSetTrait;

    /**
     * ImmutableTransitionSet constructor.
     *
     * @param Transition[] $transitions The initial set of transitions.
     */
    public function __construct(iterable $transitions = [])
    {
        $this->addTransitions($transitions);
    }
}
