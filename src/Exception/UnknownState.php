<?php

/**
 * Exception thrown when an unknown state is encountered.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Exception;

final class UnknownState extends \LogicException implements Exception
{

}
