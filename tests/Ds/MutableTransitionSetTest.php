<?php

/**
 * Test the mutable transition set.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Tests\Ds;

use IronBound\State\Ds\MutableTransitionSet;
use IronBound\State\Ds\TransitionSet;
use IronBound\State\Exception\DuplicateTransition;
use IronBound\State\State\StateId;
use IronBound\State\Transition\MutableTransition;
use IronBound\State\Transition\TransitionId;

class MutableTransitionSetTest extends TransitionSetTest
{
    public function testAddTransition(): void
    {
        $set = $this->makeSet();
        $add = new MutableTransition(
            new TransitionId('deactivate'),
            [],
            new StateId('inactive')
        );

        $set->addTransition($add);
        $this->assertTrue($set->contains($add->getId()));
    }

    public function testAddTransitionRejectsDuplicates(): void
    {
        $this->expectException(DuplicateTransition::class);

        $set = $this->makeSet();
        $set->addTransition(
            new MutableTransition(
                new TransitionId('activate'),
                [
                    new StateId('pending'),
                ],
                new StateId('active')
            )
        );
    }

    public function testRemoveTransition(): void
    {
        $set = $this->makeSet();
        $set->removeTransition(new TransitionId('activate'));

        $this->assertCount(0, iterator_to_array($set->getIterator()));
    }

    public function testRemoveTransitionUnknown(): void
    {
        $set = $this->makeSet();
        $set->removeTransition(new TransitionId('deactivate'));

        $this->assertCount(1, iterator_to_array($set->getIterator()));
    }

    /**
     * @return TransitionSet|MutableTransitionSet
     */
    protected function makeSet(): TransitionSet
    {
        return new MutableTransitionSet(
            [
                new MutableTransition(
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
