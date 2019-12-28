<?php

/**
 * Exception thrown if a state definition is incomplete.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Exception;

use IronBound\State\State\StateId;
use IronBound\State\Transition\TransitionId;

final class IncompleteStateDefinition extends \LogicException implements Exception
{
    public static function missingTransition(StateId $stateId, TransitionId $missingTransition): self
    {
        return new self(sprintf(
            'The state %s is missing the %s transition.',
            $stateId,
            $missingTransition
        ));
    }
}
