<?php

/**
 * State Mediator that uses a method call.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\StateMediator;

use IronBound\State\State\StateId;

final class MethodStateMediator implements StateMediator
{
    /** @var string */
    private $getMethod;

    /** @var string */
    private $setMethod;

    /**
     * MethodStateMediator constructor.
     *
     * @param string $getMethod
     * @param string $setMethod
     */
    public function __construct(string $getMethod, string $setMethod)
    {
        $this->getMethod = $getMethod;
        $this->setMethod = $setMethod;
    }

    public function getState(object $subject): ?StateId
    {
        $value = $subject->{$this->getMethod}();

        if (! $value) {
            return null;
        }

        return new StateId($value);
    }

    public function setState(object $subject, StateId $state): void
    {
        $subject->{$this->setMethod}($state->getName());
    }
}
