<?php

/**
 * Test the Mmutable State Set.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Tests\Ds;

use IronBound\State\Exception\DuplicateState;
use IronBound\State\Ds\{MutableStateSet, StateSet};
use IronBound\State\State\{MutableState, StateId, StateType};
use IronBound\State\Transition\TransitionId;

class MutableStateSetTest extends StateSetTest
{
    public function testAddState(): void
    {
        $set = $this->makeSet();
        $add = new MutableState(new StateId('active'), StateType::NORMAL());

        $set->addState($add);
        $this->assertTrue($set->contains($add->getId()));
    }

    public function testAddStateRejectsDuplicates(): void
    {
        $this->expectException(DuplicateState::class);

        $set = $this->makeSet();
        $set->addState(
            new MutableState(new StateId('pending'), StateType::INITIAL())
        );
    }

    public function testRemoveState(): void
    {
        $set = $this->makeSet();
        $set->removeState(new StateId('pending'));

        $this->assertCount(0, iterator_to_array($set->getIterator()));
    }

    public function testRemoveStateUnknown(): void
    {
        $set = $this->makeSet();
        $set->removeState(new StateId('active'));

        $this->assertCount(1, iterator_to_array($set->getIterator()));
    }

    /**
     * Make the state set to test.
     *
     * @return MutableStateSet|StateSet
     */
    protected function makeSet(): StateSet
    {
        return new MutableStateSet(
            [
                new MutableState(
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
