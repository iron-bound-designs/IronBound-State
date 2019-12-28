<?php

/**
 * Exception thrown when trying to add transitions to final states.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Exception;

use IronBound\State\State\StateId;

final class CannotAddTransitionToFinalState extends \LogicException implements Exception
{
    public static function create(StateId $state): self
    {
        return new self(sprintf(
            'Cannot add transition to final state %s.',
            $state
        ));
    }
}
