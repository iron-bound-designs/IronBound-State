<?php

/**
 * Immutable Graph.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Graph;

use IronBound\State\Ds\{ImmutableStateSet, StateSet, ImmutableTransitionSet, TransitionSet};
use IronBound\State\State\State;
use IronBound\State\Transition\Transition;

use function IronBound\State\assertInstancesOf;

final class ImmutableGraph implements Graph
{
    /** @var GraphId */
    private $id;

    /** @var ImmutableStateSet */
    private $states;

    /** @var ImmutableTransitionSet */
    private $transitions;

    /**
     * ImmutableGraph constructor.
     *
     * @param GraphId      $id
     * @param State[]      $states
     * @param Transition[] $transitions
     */
    public function __construct(GraphId $id, iterable $states, iterable $transitions)
    {
        assertInstancesOf(State::class, $states);
        assertInstancesOf(Transition::class, $transitions);

        $this->id          = $id;
        $this->states      = new ImmutableStateSet($states);
        $this->transitions = new ImmutableTransitionSet($transitions);
    }

    public function getId(): GraphId
    {
        return $this->id;
    }

    public function getStates(): StateSet
    {
        return $this->states;
    }

    public function getTransitions(): TransitionSet
    {
        return $this->transitions;
    }
}
