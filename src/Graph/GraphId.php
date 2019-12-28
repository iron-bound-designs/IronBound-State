<?php

/**
 * GraphID.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Graph;

final class GraphId
{
    /** @var string */
    private $name;

    /**
     * GraphId constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Get the graph name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function equals(GraphId $id): bool
    {
        return $this->name === $id->name;
    }

    public function __toString()
    {
        return $this->name;
    }
}
