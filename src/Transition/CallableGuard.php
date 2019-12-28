<?php

/**
 * Guard implementation that calls any callable. Provided for convenience.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Transition;

use IronBound\State\StateMachine;

final class CallableGuard implements Guard
{
    /** @var callable */
    private $callable;

    /**
     * CallableGuard constructor.
     *
     * @param callable $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public function __invoke(StateMachine $machine, Transition $transition): Evaluation
    {
        $cb = $this->callable;

        return $cb($machine, $transition);
    }
}
