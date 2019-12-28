<?php

/**
 * Test the Property State Mediator.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Tests\StateMediator;

use IronBound\State\State\StateId;
use IronBound\State\StateMediator\PropertyStateMediator;
use IronBound\State\StateMediator\StateMediator;

class PropertyStateMediatorTest extends StateMediatorTest
{
    protected function getMediator(): StateMediator
    {
        return new PropertyStateMediator('status');
    }

    protected function getSubject(StateId $withState = null): object
    {
        $subject         = new \stdClass();
        $subject->status = '';

        if ($withState) {
            $subject->status = $withState->getName();
        }

        return $subject;
    }
}
