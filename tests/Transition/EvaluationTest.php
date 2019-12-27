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

use IronBound\State\State\StateId;
use IronBound\State\Transition\Evaluation;
use IronBound\State\Transition\ImmutableTransition;
use IronBound\State\Transition\Transition;
use IronBound\State\Transition\TransitionId;
use PHPUnit\Framework\TestCase;

class EvaluationTest extends TestCase
{
    public function testValid(): void
    {
        $subject    = new \stdClass();
        $transition = $this->makeTransition();
        $evaluation = Evaluation::valid($subject, $transition);

        $this->assertSame($subject, $evaluation->getSubject());
        $this->assertSame($transition, $evaluation->getTransition());
        $this->assertTrue($evaluation->isValid());
        $this->assertFalse($evaluation->isInvalid());
        $this->assertCount(0, $evaluation->getReasons());
    }

    public function testInvalid(): void
    {
        $subject    = new \stdClass();
        $transition = $this->makeTransition();
        $evaluation = Evaluation::invalid($subject, $transition, 'My Reason');

        $this->assertSame($subject, $evaluation->getSubject());
        $this->assertSame($transition, $evaluation->getTransition());
        $this->assertEquals([ 'My Reason' ], $evaluation->getReasons());
        $this->assertTrue($evaluation->isInvalid());
        $this->assertFalse($evaluation->isValid());
    }

    private function makeTransition(): Transition
    {
        return new ImmutableTransition(
            new TransitionId('activate'),
            [
                new StateId('pending'),
            ],
            new StateId('active')
        );
    }
}
