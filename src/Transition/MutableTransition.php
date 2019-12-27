<?php

/**
 * Mutable Transition.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Transition;

use IronBound\State\State\StateId;

final class MutableTransition implements Transition
{
    /** @var TransitionId */
    private $id;

    /** @var StateId[] */
    private $initialStates = [];

    /** @var StateId */
    private $finalState;

    /**
     * MutableTransition constructor.
     *
     * @param TransitionId $id
     * @param StateId[]    $initialStates
     * @param StateId      $finalState
     */
    public function __construct(TransitionId $id, iterable $initialStates, StateId $finalState)
    {
        $this->id         = $id;
        $this->finalState = $finalState;

        \IronBound\State\each($initialStates, [ $this, 'addInitialState' ]);
    }

    /**
     * Add another initial state to this transition.
     *
     * @param StateId $stateId
     *
     * @return $this
     */
    public function addInitialState(StateId $stateId): self
    {
        foreach ($this->initialStates as $maybeState) {
            if ($maybeState->equals($stateId)) {
                return $this;
            }
        }

        $this->initialStates[] = $stateId;

        return $this;
    }

    /**
     * Convert the transition to an immutable transition.
     *
     * @return ImmutableTransition
     */
    public function toImmutable(): ImmutableTransition
    {
        return new ImmutableTransition($this->getId(), $this->getInitialStates(), $this->getFinalState());
    }

    public function getId(): TransitionId
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getInitialStates(): iterable
    {
        return $this->initialStates;
    }

    public function getFinalState(): StateId
    {
        return $this->finalState;
    }
}
