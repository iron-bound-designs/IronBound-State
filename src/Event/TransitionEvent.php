<?php

/**
 * Event dispatched during transitions.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Event;

use IronBound\State\StateMachine;
use IronBound\State\Transition\Transition;

class TransitionEvent implements Event
{
    /** @var StateMachine */
    private $machine;

    /** @var Transition */
    private $transition;

    /** @var array */
    private $parameters;

    /**
     * TransitionEvent constructor.
     *
     * @param StateMachine $machine
     * @param Transition   $transition
     * @param array        $parameters
     */
    public function __construct(StateMachine $machine, Transition $transition, array $parameters)
    {
        $this->machine    = $machine;
        $this->transition = $transition;
        $this->parameters = $parameters;
    }

    /**
     * Get the State Machine processing the transition.
     *
     * @return StateMachine
     */
    public function getMachine(): StateMachine
    {
        return $this->machine;
    }

    /**
     * Get the transition being processed.
     *
     * @return Transition
     */
    public function getTransition(): Transition
    {
        return $this->transition;
    }

    /**
     * Get the parameters provided to the State Machine.
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
