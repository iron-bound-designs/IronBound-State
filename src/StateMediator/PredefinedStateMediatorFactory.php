<?php

/**
 * A preconfigured State Mediator Factory.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\StateMediator;

use IronBound\State\Exception\UnsupportedGraph;
use IronBound\State\Graph\GraphId;

final class PredefinedStateMediatorFactory implements StateMediatorFactory
{
    /** @var array */
    private $map = [];

    /**
     * Attach a mediator for a graph.
     *
     * @param GraphId       $graphId
     * @param StateMediator $mediator
     *
     * @return $this
     */
    public function addMediator(GraphId $graphId, StateMediator $mediator): self
    {
        $this->map[ $graphId->getName() ] = $mediator;

        return $this;
    }

    public function make(GraphId $graphId): StateMediator
    {
        if (! $this->supports($graphId)) {
            throw new UnsupportedGraph(sprintf(
                'This factory does not support the graph %s.',
                $graphId
            ));
        }

        return $this->map[ $graphId->getName() ];
    }

    public function supports(GraphId $graphId): bool
    {
        return isset($this->map[ $graphId->getName() ]);
    }
}
