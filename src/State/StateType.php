<?php

/**
 * The type of State.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\State;

use MyCLabs\Enum\Enum;

/**
 * Class StateType
 *
 * @package IronBound\State\State
 *
 * @method static StateType INITIAL()
 * @method static StateType NORMAL()
 * @method static StateType FINAL()
 */
final class StateType extends Enum
{
    public const INITIAL = 'initial';
    public const NORMAL = 'normal';
    public const FINAL = 'final';
}
