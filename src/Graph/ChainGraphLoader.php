<?php

/**
 * Chain Loader.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Graph;

use IronBound\State\Exception\UnsupportedGraph;

final class ChainGraphLoader implements GraphLoader
{
    /** @var GraphLoader[] */
    private $loaders;

    /**
     * ChainGraphLoader constructor.
     *
     * @param GraphLoader[] $loaders
     */
    public function __construct(GraphLoader ...$loaders)
    {
        $this->loaders = $loaders;
    }

    /**
     * Add a graph loader to the chain.
     *
     * @param GraphLoader $loader
     *
     * @return $this
     */
    public function addLoader(GraphLoader $loader): self
    {
        $this->loaders[] = $loader;

        return $this;
    }

    public function make(GraphId $graphId): Graph
    {
        foreach ($this->loaders as $loader) {
            if ($loader->supports($graphId)) {
                return $loader->make($graphId);
            }
        }

        throw new UnsupportedGraph(sprintf(
            'This loader does not support the graph %s.',
            $graphId
        ));
    }

    public function supports(GraphId $graphId): bool
    {
        foreach ($this->loaders as $loader) {
            if ($loader->supports($graphId)) {
                return true;
            }
        }

        return false;
    }
}
