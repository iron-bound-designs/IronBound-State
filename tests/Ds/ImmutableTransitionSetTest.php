<?php

/**
 * Test the immutable transition set.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Tests\Ds;

use IronBound\State\Ds\ImmutableTransitionSet;
use IronBound\State\Ds\TransitionSet;
use IronBound\State\State\StateId;
use IronBound\State\Transition\ImmutableTransition;
use IronBound\State\Transition\TransitionId;

class ImmutableTransitionSetTest extends TransitionSetTest
{
    protected function makeSet(): TransitionSet
    {
        return new ImmutableTransitionSet(
            [
                new ImmutableTransition(
                    new TransitionId('activate'),
                    [
                        new StateId('pending'),
                    ],
                    new StateId('active')
                )
            ]
        );
    }
}
