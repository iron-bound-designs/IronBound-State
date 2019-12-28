<?php

/**
 * Exception thrown if a duplicate transition is encountered.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Exception;

final class DuplicateTransition extends \RuntimeException implements Exception
{

}
