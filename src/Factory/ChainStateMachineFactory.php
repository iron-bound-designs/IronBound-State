<?php

/**
 * Chain of factories for creating State Machines.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Factory;

use IronBound\State\Exception\UnsupportedSubject;
use IronBound\State\Graph\GraphId;
use IronBound\State\StateMachine;

final class ChainStateMachineFactory implements StateMachineFactory
{
    /** @var StateMachineFactory[] */
    private $factories;

    public function __construct(StateMachineFactory ...$factories)
    {
        $this->factories = $factories;
    }

    /**
     * Add a factory to the chain.
     *
     * @param StateMachineFactory $factory
     *
     * @return $this
     */
    public function addFactory(StateMachineFactory $factory): self
    {
        $this->factories[] = $factory;

        return $this;
    }

    public function make(object $subject, GraphId $graphId): StateMachine
    {
        foreach ($this->factories as $factory) {
            if ($factory->supports($subject)) {
                return $factory->make($subject, $graphId);
            }
        }

        throw new UnsupportedSubject(
            'This state machine factory chain has no factories that support the given subject.'
        );
    }

    public function supports(object $subject): bool
    {
        foreach ($this->factories as $factory) {
            if ($factory->supports($subject)) {
                return true;
            }
        }

        return false;
    }
}
