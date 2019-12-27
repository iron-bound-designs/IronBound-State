<?php

/**
 * Immutable set of states.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Ds;

use IronBound\State\State\State;

final class ImmutableStateSet implements StateSet
{
    use StateSetTrait;

    /**
     * ImmutableStateSet constructor.
     *
     * @param State[] $states The initial list of states.
     */
    public function __construct(iterable $states = [])
    {
        $this->addStates($states);
    }
}
