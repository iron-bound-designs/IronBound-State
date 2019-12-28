<?php

/**
 * Exception thrown when an unknown transition is encountered.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Exception;

final class UnknownTransition extends \LogicException implements Exception
{

}
