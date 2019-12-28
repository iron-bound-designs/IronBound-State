<?php

/**
 * Test the Immutable Graph.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Tests\Graph;

use IronBound\State\Graph\{Graph, ImmutableGraph};
use IronBound\State\State\{ImmutableState, StateType};
use IronBound\State\Transition\ImmutableTransition;

class ImmutableGraphTest extends GraphTest
{
    protected function makeGraph(): Graph
    {
        return new ImmutableGraph(
            $this->graphId,
            [
                new ImmutableState($this->pending, StateType::INITIAL(), [ $this->activate ]),
                new ImmutableState($this->active, StateType::NORMAL(), [])
            ],
            [
                new ImmutableTransition($this->activate, [ $this->pending ], $this->active)
            ]
        );
    }
}
