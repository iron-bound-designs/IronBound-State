<?php

/**
 * Test the Evaluation.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Tests\Transition;

use IronBound\State\Transition\Evaluation;
use PHPUnit\Framework\TestCase;

class EvaluationTest extends TestCase
{
    public function testValid(): void
    {
        $evaluation = Evaluation::valid();

        $this->assertTrue($evaluation->isValid());
        $this->assertFalse($evaluation->isInvalid());
        $this->assertCount(0, $evaluation->getReasons());
    }

    public function testInvalid(): void
    {
        $evaluation = Evaluation::invalid('My Reason');

        $this->assertEquals([ 'My Reason' ], $evaluation->getReasons());
        $this->assertTrue($evaluation->isInvalid());
        $this->assertFalse($evaluation->isValid());
    }
}
