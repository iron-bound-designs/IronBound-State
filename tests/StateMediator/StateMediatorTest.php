<?php

/**
 * Abstract test class for all state mediators.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Tests\StateMediator;

use IronBound\State\State\StateId;
use IronBound\State\StateMediator\StateMediator;
use PHPUnit\Framework\TestCase;

abstract class StateMediatorTest extends TestCase
{
    public function testGetState(): void
    {
        $mediator = $this->getMediator();
        $subject  = $this->getSubject(new StateId('active'));
        $this->assertEquals('active', $mediator->getState($subject)->getName());
    }

    public function testGetStateReturnsNullIfNoStateSet(): void
    {
        $mediator = $this->getMediator();
        $this->assertNull($mediator->getState($this->getSubject()));
    }

    /**
     * @depends testGetState
     */
    public function testSetState(): void
    {
        $mediator = $this->getMediator();
        $subject  = $this->getSubject();
        $mediator->setState($subject, new StateId('inactive'));
        $this->assertEquals('inactive', $mediator->getState($subject)->getName());
    }

    /**
     * Get the mediator to test.
     *
     * @return StateMediator
     */
    abstract protected function getMediator(): StateMediator;

    /**
     * Get a subject to test.
     *
     * @param StateId|null $withState Optionally, specify the current state for the subject.
     *
     * @return object
     */
    abstract protected function getSubject(StateId $withState = null): object;
}
