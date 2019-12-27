<?php

/**
 * Provides a way to configure a suite of State Machine Factories based on an array configuration.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Factory;

use IronBound\State\Graph\{ArrayGraphLoader, CachedGraphLoader, ChainGraphLoader, GraphId};
use IronBound\State\StateMediator\{PredefinedStateMediatorFactory, PropertyStateMediator};

use function IronBound\State\arrayPick;

final class StateMachineFactoryConfigurator
{
    /**
     * Configure a state machine factory based on an array config.
     *
     * @param array $config
     *
     * @return StateMachineFactory
     */
    public function configure(array $config): StateMachineFactory
    {
        $subjects = $config['subjects'] ?? $config;

        $chain = new ChainStateMachineFactory();

        foreach ($subjects as $subject) {
            $mediatorFactory = new PredefinedStateMediatorFactory();
            $graphLoader     = new ChainGraphLoader();
            $test            = ConcreteStateMachineFactory::classTest($subject['test']['class']);

            foreach ($subject['graphs'] as $graphName => $graphConfig) {
                $graphId  = new GraphId($graphName);
                $mediator = new PropertyStateMediator($graphConfig['mediator']['property']);
                $mediatorFactory->addMediator($graphId, $mediator);

                $graphLoader->addLoader(new ArrayGraphLoader(
                    $graphId,
                    arrayPick($graphConfig, [ 'states', 'transitions' ])
                ));
            }

            $chain->addFactory(new ConcreteStateMachineFactory(
                $mediatorFactory,
                new CachedGraphLoader($graphLoader),
                $test
            ));
        }

        return $chain;
    }
}
