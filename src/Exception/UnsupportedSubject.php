<?php

/**
 * Exception thrown by the State Machine Factory when trying to build a factory for an
 * unsupported subject.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Exception;

final class UnsupportedSubject extends \InvalidArgumentException implements Exception
{

}
