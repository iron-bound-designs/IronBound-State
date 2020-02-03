<?php

/**
 * Support Test based on class name.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2020 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Factory;

final class ClassSupportsTest implements SupportsTest
{
    /** @var string */
    private $class;

    /**
     * ClassSupportsTest constructor.
     *
     * @param string $class
     */
    public function __construct(string $class)
    {
        $this->class = $class;
    }

    public function __invoke(object $subject): bool
    {
        return $subject instanceof $this->class;
    }
}
