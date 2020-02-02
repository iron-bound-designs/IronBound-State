<?php

/**
 * Mutable State object.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\State;

use IronBound\State\Exception\CannotAddTransitionToFinalState;
use IronBound\State\Transition\TransitionId;
use function IronBound\State\castArray;

final class MutableState implements State
{
    /** @var StateId */
    private $id;

    /** @var StateType */
    private $type;

    /** @var TransitionId[] */
    private $transitions = [];

    /** @var array */
    private $attributes;

    /**
     * ImmutableState constructor.
     *
     * @param StateId        $id          The state id.
     * @param StateType|null $type        Specify the state type. Defaults to {@see StateType::NORMAL()}
     * @param TransitionId[] $transitions The available transitions.
     * @param iterable       $attributes  List of attributes.
     */
    public function __construct(
        StateId $id,
        StateType $type = null,
        iterable $transitions = [],
        iterable $attributes = []
    ) {
        $this->id         = $id;
        $this->type       = $type ?? StateType::NORMAL();
        $this->attributes = castArray($attributes);

        \IronBound\State\each($transitions, [ $this, 'addTransition' ]);
    }

    /**
     * Add a transition to the state.
     *
     * @param TransitionId $transitionId
     *
     * @return $this
     */
    public function addTransition(TransitionId $transitionId): self
    {
        if ($this->getType()->equals(StateType::FINAL())) {
            throw CannotAddTransitionToFinalState::create($this->getId());
        }

        foreach ($this->transitions as $maybeTransition) {
            if ($maybeTransition->equals($transitionId)) {
                return $this;
            }
        }

        $this->transitions[] = $transitionId;

        return $this;
    }

    /**
     * Remove a transition from the state.
     *
     * @param TransitionId $transitionId
     *
     * @return $this
     */
    public function removeTransition(TransitionId $transitionId): self
    {
        $this->transitions = array_filter(
            $this->transitions,
            static function (TransitionId $maybeTransition) use ($transitionId) {
                return ! $maybeTransition->equals($transitionId);
            }
        );

        return $this;
    }

    public function getId(): StateId
    {
        return $this->id;
    }

    public function getType(): StateType
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function getTransitions(): iterable
    {
        return $this->transitions;
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

    /**
     * Convert this to an immutable state.
     *
     * @return ImmutableState
     */
    public function toImmutable(): ImmutableState
    {
        return new ImmutableState($this->getId(), $this->getType(), $this->getTransitions(), $this->getAttributes());
    }
}
