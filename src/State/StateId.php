<?php

/**
 * StateId.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\State;

final class StateId
{
    /** @var string */
    private $name;

    /**
     * StateId constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name  = $name;
    }

    /**
     * Get the name identifier.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function equals(StateId $id): bool
    {
        return $this->name === $id->name;
    }

    public function __toString()
    {
        return $this->name;
    }
}
