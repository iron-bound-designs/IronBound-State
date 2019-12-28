<?php

/**
 * Test the Immutable State Set.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Tests\Ds;

use IronBound\State\Ds\{ImmutableStateSet, StateSet};
use IronBound\State\State\{ImmutableState, StateId, StateType};
use IronBound\State\Transition\TransitionId;

class ImmutableStateSetTest extends StateSetTest
{
    /**
     * Make the state set to test.
     *
     * @return ImmutableState|StateSet
     */
    protected function makeSet(): StateSet
    {
        return new ImmutableStateSet(
            [
                new ImmutableState(
                    new StateId('pending'),
                    StateType::INITIAL(),
                    [
                        new TransitionId('activate')
                    ]
                )
            ]
        );
    }
}
