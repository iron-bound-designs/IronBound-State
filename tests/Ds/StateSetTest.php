<?php

/**
 * Abstract test for state sets.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Tests\Ds;

use IronBound\State\Ds\StateSet;
use IronBound\State\Exception\UnknownState;
use IronBound\State\State\StateId;
use PHPUnit\Framework\TestCase;

abstract class StateSetTest extends TestCase
{
    public function testGetIterator(): void
    {
        $iterator = $this->makeSet()->getIterator();
        $this->assertIsIterable($iterator);
        $array = iterator_to_array($iterator);

        $this->assertCount(1, $array);
        $this->assertArrayHasKey(0, $array);
        $this->assertEquals('pending', $array[0]->getId()->getName());
    }

    public function testGet(): void
    {
        $set   = $this->makeSet();
        $state = $set->get(new StateId('pending'));

        $this->assertEquals('pending', $state->getId()->getName());
    }

    public function testGetThrowsExceptionIfDifferentName(): void
    {
        $this->expectException(UnknownState::class);
        $this->makeSet()->get(new StateId('active'));
    }

    public function testContains(): void
    {
        $this->assertTrue($this->makeSet()->contains(new StateId('pending')));
    }

    public function testContainsDifferentName(): void
    {
        $this->assertFalse($this->makeSet()->contains(new StateId('active')));
    }

    /**
     * Make a state set for testing.
     *
     * It should be for the graph 'status' and have a 'pending' state with an 'activate' transition.
     *
     * @return StateSet
     */
    abstract protected function makeSet(): StateSet;
}
