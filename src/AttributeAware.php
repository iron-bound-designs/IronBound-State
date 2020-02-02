<?php

/**
 * Attribute Aware interface.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2020 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State;

interface AttributeAware
{

    /**
     * Get a list of attributes associated with this object.
     *
     * @return iterable
     */
    public function getAttributes(): iterable;
}
