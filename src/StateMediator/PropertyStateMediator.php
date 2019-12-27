<?php

/**
 * State Mediator that gets and sets properties.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\StateMediator;

use IronBound\State\State\StateId;

final class PropertyStateMediator implements StateMediator
{
    /** @var string */
    private $property;

    /**
     * PropertyStateMediator constructor.
     *
     * @param string $property
     */
    public function __construct(string $property)
    {
        $this->property = $property;
    }

    public function getState(object $subject): ?StateId
    {
        if (! isset($subject->{$this->property})) {
            return null;
        }

        $value = $subject->{$this->property};

        if (! $value) {
            return null;
        }

        return new StateId($subject->{$this->property});
    }

    public function setState(object $subject, StateId $state): void
    {
        $subject->{$this->property} = $state->getName();
    }
}
