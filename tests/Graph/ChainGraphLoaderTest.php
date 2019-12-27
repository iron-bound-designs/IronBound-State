<?php

/**
 * Test the chain graph loader.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Tests\Graph;

use IronBound\State\Exception\UnsupportedGraph;
use IronBound\State\Graph\ChainGraphLoader;
use IronBound\State\Graph\Graph;
use IronBound\State\Graph\GraphId;
use IronBound\State\Graph\GraphLoader;
use PHPUnit\Framework\TestCase;

class ChainGraphLoaderTest extends TestCase
{
    public function testMake(): void
    {
        $graph   = $this->createMock(Graph::class);
        $graphId = new GraphId('default');

        $unsupported = $this->createMock(GraphLoader::class);
        $unsupported->expects($this->once())->method('supports')->with($graphId)->willReturn(false);
        $unsupported->expects($this->never())->method('make');

        $supported = $this->createMock(GraphLoader::class);
        $supported->expects($this->once())->method('supports')->with($graphId)->willReturn(true);
        $supported->expects($this->once())->method('make')->with($graphId)->willReturn($graph);

        $chainLoader = new ChainGraphLoader($unsupported);
        $chainLoader->addLoader($supported);

        $this->assertSame($graph, $chainLoader->make($graphId));
    }

    public function testMakeThrowsExceptionIfNoneSupport(): void
    {
        $graphId = new GraphId('default');

        $unsupported = $this->createMock(GraphLoader::class);
        $unsupported->method('supports')->with($graphId)->willReturn(false);

        $this->expectException(UnsupportedGraph::class);
        (new ChainGraphLoader())->make($graphId);
    }

    public function testMakeThrowsExceptionIfNoLoadersAdded(): void
    {
        $this->expectException(UnsupportedGraph::class);
        (new ChainGraphLoader())->make(new GraphId('default'));
    }

    public function testSupports(): void
    {
        $graphId = new GraphId('default');

        $unsupported = $this->createMock(GraphLoader::class);
        $unsupported->expects($this->once())->method('supports')->with($graphId)->willReturn(false);

        $supported = $this->createMock(GraphLoader::class);
        $supported->expects($this->once())->method('supports')->with($graphId)->willReturn(true);

        $chainLoader = new ChainGraphLoader($unsupported);
        $chainLoader->addLoader($supported);

        $this->assertTrue($chainLoader->supports($graphId));
    }

    public function testSupportsReturnsFalseIfNoneSupport(): void
    {
        $graphId = new GraphId('default');

        $unsupported = $this->createMock(GraphLoader::class);
        $unsupported->method('supports')->with($graphId)->willReturn(false);

        $this->assertFalse((new ChainGraphLoader())->supports($graphId));
    }

    public function testSupportsReturnsFalseIfNoLoadersAdded(): void
    {
        $this->assertFalse((new ChainGraphLoader())->supports(new GraphId('default')));
    }
}
