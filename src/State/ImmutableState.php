<?php

/**
 * Immutable State.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\State;

use IronBound\State\Exception\CannotAddTransitionToFinalState;
use IronBound\State\Transition\TransitionId;

use function IronBound\State\uniqueBy;

final class ImmutableState implements State
{
    /** @var StateId */
    private $id;

    /** @var StateType */
    private $type;

    /** @var TransitionId[] */
    private $transitions;

    /**
     * ImmutableState constructor.
     *
     * @param StateId        $id
     * @param StateType      $type
     * @param TransitionId[] $transitions
     */
    public function __construct(StateId $id, StateType $type, iterable $transitions = [])
    {
        $this->id          = $id;
        $this->type        = $type;
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
}
