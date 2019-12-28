<?php

/**
 * Exception thrown if an initial state is required, but none or too many are provided.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Exception;

final class InitialStateRequired extends \LogicException implements Exception
{

}
