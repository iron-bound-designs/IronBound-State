<?php

/**
 * Test the Method State Mediator.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2020 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Tests\StateMediator;

use IronBound\State\State\StateId;
use IronBound\State\StateMediator\MethodStateMediator;
use IronBound\State\StateMediator\StateMediator;

class MethodStateMediatorTest extends StateMediatorTest
{
    protected function getMediator(): StateMediator
    {
        return new MethodStateMediator('get', 'set');
    }

    protected function getSubject(StateId $withState = null): object
    {
        $subject = new class {
            private $status;

            public function get()
            {
                return $this->status;
            }

            public function set($status)
            {
                $this->status = $status;
            }
        };

        if ($withState) {
            $subject->set($withState->getName());
        }

        return $subject;
    }
}
