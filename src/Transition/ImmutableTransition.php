<?php

/**
 * Immutable Transition.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Transition;

use IronBound\State\State\StateId;

use function IronBound\State\uniqueBy;

final class ImmutableTransition implements Transition
{
    /** @var TransitionId */
    private $id;

    /** @var StateId[] */
    private $initialStates = [];

    /** @var StateId */
    private $finalState;

    /** @var Guard|null */
    private $guard;

    /**
     * ImmutableTransition constructor.
     *
     * @param TransitionId $id
     * @param StateId[]    $initialStates
     * @param StateId      $finalState
     * @param Guard|null   $guard
     */
    public function __construct(TransitionId $id, iterable $initialStates, StateId $finalState, Guard $guard = null)
    {
        $this->id            = $id;
        $this->finalState    = $finalState;
        $this->guard         = $guard;
        $this->initialStates = uniqueBy($initialStates, static function (StateId $id) {
            return $id->getName();
        });
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
}
