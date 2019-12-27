<?php

/**
 * Abstract test for transition sets.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Tests\Ds;

use IronBound\State\Ds\TransitionSet;
use IronBound\State\Exception\UnknownTransition;
use IronBound\State\Transition\TransitionId;
use PHPUnit\Framework\TestCase;

abstract class TransitionSetTest extends TestCase
{
    public function testGetIterator(): void
    {
        $iterator = $this->makeSet()->getIterator();
        $this->assertIsIterable($iterator);
        $array = iterator_to_array($iterator);

        $this->assertCount(1, $array);
        $this->assertArrayHasKey(0, $array);
        $this->assertEquals('activate', $array[0]->getId()->getName());
    }

    public function testGet(): void
    {
        $set        = $this->makeSet();
        $transition = $set->get(new TransitionId('activate'));

        $this->assertEquals('activate', $transition->getId()->getName());
    }

    public function testGetThrowsExceptionIfDifferentName(): void
    {
        $this->expectException(UnknownTransition::class);
        $this->makeSet()->get(new TransitionId('deactivate'));
    }

    public function testContains(): void
    {
        $this->assertTrue($this->makeSet()->contains(new TransitionId('activate')));
    }

    public function testContainsDifferentName(): void
    {
        $this->assertFalse($this->makeSet()->contains(new TransitionId('deactivate')));
    }

    /**
     * Make a transition set for testing.
     *
     * It should be for the graph 'status' and have a 'activate' transition
     * with a 'pending' initial state and an 'active' final state.
     *
     * @return TransitionSet
     */
    abstract protected function makeSet(): TransitionSet;
}
