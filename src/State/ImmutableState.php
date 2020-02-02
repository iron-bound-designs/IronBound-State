<?php

/**
 * Immutable State.
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
use function IronBound\State\uniqueBy;

final class ImmutableState implements State
{
    /** @var StateId */
    private $id;

    /** @var StateType */
    private $type;

    /** @var TransitionId[] */
    private $transitions;

    /** @var array */
    private $attributes;

    /**
     * ImmutableState constructor.
     *
     * @param StateId        $id
     * @param StateType      $type
     * @param TransitionId[] $transitions
     * @param iterable       $attributes
     */
    public function __construct(StateId $id, StateType $type, iterable $transitions = [], iterable $attributes = [])
    {
        $this->id          = $id;
        $this->type        = $type;
        $this->attributes  = castArray($attributes);
        $this->transitions = uniqueBy($transitions, static function (TransitionId $id) {
            return $id->getName();
        });

        if (count($this->transitions) && $this->getType()->equals(StateType::FINAL())) {
            throw CannotAddTransitionToFinalState::create($this->getId());
        }
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

    public function getAttributes(): iterable
    {
        return $this->attributes;
    }
}
