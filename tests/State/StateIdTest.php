<?php

/**
 * Test the State Id.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Tests\State;

use IronBound\State\State\StateId;
use PHPUnit\Framework\TestCase;

class StateIdTest extends TestCase
{
    public function testToString(): void
    {
        $this->assertEquals('active', (string) $this->makeState());
    }

    public function testGetName(): void
    {
        $this->assertEquals('active', $this->makeState()->getName());
    }

    public function testEquals(): void
    {
        $this->assertTrue($this->makeState()->equals($this->makeState()));
    }

    public function testEqualsDifferentName(): void
    {
        $other = new StateId('inactive');
        $this->assertFalse($this->makeState()->equals($other));
    }

    private function makeState(): StateId
    {
        return new StateId('active');
    }
}
