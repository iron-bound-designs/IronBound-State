<?php

/**
 * Exception thrown when a loader tries to load an unsupported graph.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Exception;

final class UnsupportedGraph extends \InvalidArgumentException implements Exception
{

}
