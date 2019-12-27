<?php

/**
 * Graph Factory interface.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Graph;

interface GraphLoader
{
    /**
     * Make the graph.
     *
     * @param GraphId $graphId
     *
     * @return Graph
     */
    public function make(GraphId $graphId): Graph;

    /**
     * Does this loader support the given graph.
     *
     * @param GraphId $graphId
     *
     * @return bool
     */
    public function supports(GraphId $graphId): bool;
}
