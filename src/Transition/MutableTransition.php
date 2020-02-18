<?php

/**
 * Mutable Transition.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Transition;

use IronBound\State\State\StateId;

use function IronBound\State\castArray;

final class MutableTransition implements Transition
{
    /** @var TransitionId */
    private $id;

    /** @var StateId[] */
    private $initialStates = [];

    /** @var StateId */
    private $finalState;

    /** @var Guard|null */
    private $guard;

    /** @var array */
    private $attributes;

    /**
     * MutableTransition constructor.
     *
     * @param TransitionId $id
     * @param StateId[]    $initialStates
     * @param StateId      $finalState
     * @param Guard|null   $guard
     * @param iterable     $attributes
     */
    public function __construct(
        TransitionId $id,
        iterable $initialStates,
        StateId $finalState,
        Guard $guard = null,
        iterable $attributes = []
    ) {
        $this->id         = $id;
        $this->finalState = $finalState;
        $this->guard      = $guard;
        $this->attributes = castArray($attributes);

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

    public function getGuard(): ?Guard
    {
        return $this->guard;
    }

    /**
     * Set an attribute's value.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return $this
     */
    public function setAttribute(string $attribute, $value): self
    {
        $this->attributes[ $attribute ] = $value;

        return $this;
    }

    public function getAttributes(): iterable
    {
        return $this->attributes;
    }
}
