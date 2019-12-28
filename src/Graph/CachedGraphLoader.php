<?php

/**
 * Graph Loader that caches graphs.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Graph;

final class CachedGraphLoader implements GraphLoader
{
    /** @var GraphLoader */
    private $loader;

    /** @var array */
    private $cache = [];

    /**
     * CachedGraphLoader constructor.
     *
     * @param GraphLoader $loader
     */
    public function __construct(GraphLoader $loader)
    {
        $this->loader = $loader;
    }

    public function make(GraphId $graphId): Graph
    {
        if (! isset($this->cache[ $graphId->getName() ])) {
            $this->cache[ $graphId->getName() ] = $this->loader->make($graphId);
        }

        return $this->cache[ $graphId->getName() ];
    }

    public function supports(GraphId $graphId): bool
    {
        return $this->loader->supports($graphId);
    }
}
