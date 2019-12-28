<?php

/**
 * Interface for callbacks that test if the Factory supports a subject.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Factory;

interface SupportsTest
{
    /**
     * Checks if the given subject is supported.
     *
     * {@see StateMachineFactory::supports()}
     *
     * @param object $subject
     *
     * @return bool
     */
    public function __invoke(object $subject): bool;
}
