<?php

/**
 * Test the Graph.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Tests\Graph;

use IronBound\State\Graph\Graph;
use IronBound\State\Graph\GraphId;
use IronBound\State\State\StateId;
use IronBound\State\Transition\TransitionId;
use PHPUnit\Framework\TestCase;

abstract class GraphTest extends TestCase
{
    protected $graphId;
    protected $pending;
    protected $active;
    protected $activate;

    protected function setUp(): void
    {
        $this->graphId  = new GraphId('default');
        $this->pending  = new StateId('pending');
        $this->active   = new StateId('active');
        $this->activate = new TransitionId('activate');
    }

    public function testGetId(): void
    {
        $graph = $this->makeGraph();
        $this->assertEquals($this->graphId->getName(), $graph->getId()->getName());
    }

    public function testGetTransitions(): void
    {
        $graph       = $this->makeGraph();
        $transitions = $graph->getTransitions();
        $this->assertTrue($transitions->contains($this->activate));
    }

    public function testGetStates(): void
    {
        $graph  = $this->makeGraph();
        $states = $graph->getStates();
        $this->assertTrue($states->contains($this->pending));
        $this->assertTrue($states->contains($this->active));
    }

    /**
     * Create a graph for testing against.
     *
     * Should use {@see GraphTest::$graphId} for the id,
     * the {@see GraphTest::$pending} and {@see GraphTest::$active} states,
     * and the {@see GraphTest::$activate} transition going between the two.
     *
     * @return Graph
     */
    abstract protected function makeGraph(): Graph;
}
