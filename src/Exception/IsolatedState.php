<?php

/**
 * Exception thrown if a state is isolated. That is, there are no transitions
 * that go to it or away from it.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Exception;

use IronBound\State\State\StateId;

final class IsolatedState extends \LogicException implements Exception
{
    public static function create(StateId $stateId): self
    {
        return new self(sprintf(
            "The '%s' state is not referenced by any transitions.",
            $stateId
        ));
    }
}
