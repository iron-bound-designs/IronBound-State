<?php

/**
 * Tests the cached graph loader.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Tests\Graph;

use IronBound\State\Graph\CachedGraphLoader;
use IronBound\State\Graph\Graph;
use IronBound\State\Graph\GraphId;
use IronBound\State\Graph\GraphLoader;
use PHPUnit\Framework\TestCase;

class CachedGraphLoaderTest extends TestCase
{
    public function testSupports(): void
    {
        $graphId = new GraphId('default');
        $loader  = $this->createMock(GraphLoader::class);
        $loader->expects($this->once())->method('supports')->with($graphId)->willReturn(false);

        $cachedLoader = new CachedGraphLoader($loader);
        $this->assertFalse($cachedLoader->supports($graphId));
    }

    public function testMake(): void
    {
        $graphId = new GraphId('default');
        $graph   = $this->createMock(Graph::class);
        $loader  = $this->createMock(GraphLoader::class);
        $loader->expects($this->once())->method('make')->with($graphId)->willReturn($graph);

        $cachedLoader = new CachedGraphLoader($loader);
        $this->assertSame($graph, $cachedLoader->make($graphId));
        $this->assertSame($graph, $cachedLoader->make($graphId));
    }
}
