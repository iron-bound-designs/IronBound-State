<?php

/**
 * The State Mediator Factory is responsible for creating State Mediators
 * based on the Graph requested. A separate state mediator factory is typically
 * instantiated for each subject type.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\StateMediator;

use IronBound\State\Graph\GraphId;

interface StateMediatorFactory
{
    /**
     * Make a state mediator for the graph.
     *
     * @param GraphId $graphId
     *
     * @return StateMediator
     */
    public function make(GraphId $graphId): StateMediator;

    /**
     * Checks whether there is a state mediator for the graph.
     *
     * @param GraphId $graphId
     *
     * @return bool
     */
    public function supports(GraphId $graphId): bool;
}
