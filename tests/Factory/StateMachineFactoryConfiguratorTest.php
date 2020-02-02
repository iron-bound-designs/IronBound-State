<?php

/**
 * Test the State Machine configurator.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Tests\Factory;

use IronBound\State\Exception\InvalidArgumentException;
use IronBound\State\Factory\ConcreteStateMachineFactory;
use IronBound\State\Factory\StateMachineFactoryConfigurator;
use IronBound\State\Graph\GraphId;
use IronBound\State\State\StateType;
use IronBound\State\StateMediator\PropertyStateMediator;
use IronBound\State\Transition\TransitionId;
use PHPUnit\Framework\TestCase;

use function IronBound\State\mapMethod;

class StateMachineFactoryConfiguratorTest extends TestCase
{
    public function testConfigure(): void
    {
        $configurator = new StateMachineFactoryConfigurator();
        $factory      = $configurator->configure([
            'subjects' => [
                [
                    'test'   => [
                        'class' => 'stdClass',
                    ],
                    'graphs' => [
                        'status' => [
                            'mediator'    => [
                                'property' => 'status',
                            ],
                            'states'      => [
                                'pending' => [
                                    'type' => StateType::INITIAL,
                                ],
                                'active'  => [],
                            ],
                            'transitions' => [
                                'activate' => [
                                    'from' => 'pending',
                                    'to'   => 'active',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $subject      = new \stdClass();
        $stateMachine = $factory->make($subject, new GraphId('status'));

        $this->assertEquals('pending', $stateMachine->getCurrentState()->getId());
        $this->assertEquals([ 'activate' ], mapMethod($stateMachine->getAvailableTransitions(), 'getId'));

        $stateMachine->apply(new TransitionId('activate'));
        $this->assertEquals('active', $subject->status);
        $this->assertEquals('active', $stateMachine->getCurrentState()->getId());

        $anotherStateMachine = $factory->make($subject, new GraphId('status'));

        $this->assertNotSame($stateMachine, $anotherStateMachine);
        $this->assertSame($stateMachine->getGraph(), $anotherStateMachine->getGraph());
    }

    public function testConfigureShortSyntax(): void
    {
        $configurator = new StateMachineFactoryConfigurator();
        $factory      = $configurator->configure([
            [
                'test'   => [
                    'class' => 'stdClass',
                ],
                'graphs' => [
                    'status' => [
                        'mediator'    => [
                            'property' => 'status',
                        ],
                        'states'      => [
                            'pending' => [
                                'type' => StateType::INITIAL,
                            ],
                            'active'  => [],
                        ],
                        'transitions' => [
                            'activate' => [
                                'from' => 'pending',
                                'to'   => 'active',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $subject      = new \stdClass();
        $stateMachine = $factory->make($subject, new GraphId('status'));

        $this->assertEquals('pending', $stateMachine->getCurrentState()->getId());
    }

    public function testConfigureMethodMediator(): void
    {
        $subject = new class {
            private $status;

            public function get()
            {
                return $this->status;
            }

            public function set($status)
            {
                $this->status = $status;
            }
        };

        $configurator = new StateMachineFactoryConfigurator();
        $factory      = $configurator->configure([
            [
                'test'   => [
                    'class' => get_class($subject),
                ],
                'graphs' => [
                    'status' => [
                        'mediator'    => [
                            'method' => [
                                'get' => 'get',
                                'set' => 'set',
                            ],
                        ],
                        'states'      => [
                            'pending' => [
                                'type' => StateType::INITIAL,
                            ],
                            'active'  => [],
                        ],
                        'transitions' => [
                            'activate' => [
                                'from' => 'pending',
                                'to'   => 'active',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $stateMachine = $factory->make($subject, new GraphId('status'));

        $this->assertEquals('pending', $stateMachine->getCurrentState()->getId());
        $stateMachine->apply(new TransitionId('activate'));
        $this->assertEquals('active', $stateMachine->getCurrentState()->getId());
    }

    public function testConfigureTestInstance(): void
    {
        $configurator = new StateMachineFactoryConfigurator();
        $factory      = $configurator->configure([
            [
                'test'   => ConcreteStateMachineFactory::classTest('stdClass'),
                'graphs' => [
                    'status' => [
                        'mediator'    => [
                            'property' => 'status',
                        ],
                        'states'      => [
                            'pending' => [
                                'type' => StateType::INITIAL,
                            ],
                            'active'  => [],
                        ],
                        'transitions' => [
                            'activate' => [
                                'from' => 'pending',
                                'to'   => 'active',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $subject      = new \stdClass();
        $stateMachine = $factory->make($subject, new GraphId('status'));

        $this->assertEquals('pending', $stateMachine->getCurrentState()->getId());
    }

    public function testConfigureMediatorInstance(): void
    {
        $configurator = new StateMachineFactoryConfigurator();
        $factory      = $configurator->configure([
            [
                'test'   => [
                    'class' => 'stdClass',
                ],
                'graphs' => [
                    'status' => [
                        'mediator'    => new PropertyStateMediator('status'),
                        'states'      => [
                            'pending' => [
                                'type' => StateType::INITIAL,
                            ],
                            'active'  => [],
                        ],
                        'transitions' => [
                            'activate' => [
                                'from' => 'pending',
                                'to'   => 'active',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $subject      = new \stdClass();
        $stateMachine = $factory->make($subject, new GraphId('status'));

        $this->assertEquals('pending', $stateMachine->getCurrentState()->getId());
    }

    public function testConfigureThrowsExceptionIfMissingMediator(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('mediator');

        $configurator = new StateMachineFactoryConfigurator();
        $configurator->configure([
            [
                'test'   => [
                    'class' => 'stdClass',
                ],
                'graphs' => [
                    'status' => [
                        'mediator'    => [],
                        'states'      => [
                            'pending' => [
                                'type' => StateType::INITIAL,
                            ],
                            'active'  => [],
                        ],
                        'transitions' => [
                            'activate' => [
                                'from' => 'pending',
                                'to'   => 'active',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testConfigureThrowsExceptionIfMissingTest(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('support');

        $configurator = new StateMachineFactoryConfigurator();
        $configurator->configure([
            [
                'test'   => [],
                'graphs' => [
                    'status' => [
                        'mediator'    => [
                            'property' => 'status',
                        ],
                        'states'      => [
                            'pending' => [
                                'type' => StateType::INITIAL,
                            ],
                            'active'  => [],
                        ],
                        'transitions' => [
                            'activate' => [
                                'from' => 'pending',
                                'to'   => 'active',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
