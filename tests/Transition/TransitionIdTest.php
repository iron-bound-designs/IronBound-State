<?php

/**
 * Test the Transition Id.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Tests\Transition;

use IronBound\State\Transition\TransitionId;
use PHPUnit\Framework\TestCase;

class TransitionIdTest extends TestCase
{
    public function testToString(): void
    {
        $this->assertEquals('activate', (string) $this->makeTransitionId());
    }

    public function testGetName(): void
    {
        $this->assertEquals('activate', $this->makeTransitionId()->getName());
    }

    public function testEquals(): void
    {
        $this->assertTrue($this->makeTransitionId()->equals($this->makeTransitionId()));
    }

    public function testEqualsDifferentName(): void
    {
        $other = new TransitionId('deactivate');
        $this->assertFalse($this->makeTransitionId()->equals($other));
    }

    public function testEqualsDifferentNameAndGraphId(): void
    {
        $other = new TransitionId('deactivate');
        $this->assertFalse($this->makeTransitionId()->equals($other));
    }

    private function makeTransitionId(): TransitionId
    {
        return new TransitionId('activate');
    }
}
