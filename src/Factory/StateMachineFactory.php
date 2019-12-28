<?php

/**
 * Factory interface for building state machines.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Factory;

use IronBound\State\Exception\UnsupportedSubject;
use IronBound\State\Graph\GraphId;
use IronBound\State\StateMachine;

interface StateMachineFactory
{
    /**
     * Make a state machine for the given graph and subject.
     *
     * @param object  $subject The subject to create a state machine for.
     * @param GraphId $graphId Select the particular graph for the subject.
     *
     * @return StateMachine
     *
     * @throws UnsupportedSubject If trying to make a State Machine for a subject this factory does not support.
     */
    public function make(object $subject, GraphId $graphId): StateMachine;

    /**
     * Checks if this factory can create State Machines for the given subject.
     *
     * @param object $subject
     *
     * @return bool
     */
    public function supports(object $subject): bool;
}
