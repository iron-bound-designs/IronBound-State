<?php

/**
 * Provides a way to configure a suite of State Machine Factories based on an array configuration.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Factory;

use IronBound\State\Graph\{ArrayGraphLoader, CachedGraphLoader, ChainGraphLoader, GraphId};
use IronBound\State\Exception\InvalidArgumentException;
use IronBound\State\StateMediator\{MethodStateMediator,
    PredefinedStateMediatorFactory,
    PropertyStateMediator,
    StateMediator
};

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

            if ($subject['test'] instanceof SupportsTest) {
                $test = $subject['test'];
            } elseif (isset($subject['test']['class'])) {
                $test = ConcreteStateMachineFactory::classTest($subject['test']['class']);
            } else {
                throw new InvalidArgumentException('Config is missing a valid support test.');
            }

            foreach ($subject['graphs'] as $graphName => $graphConfig) {
                $graphId = new GraphId($graphName);

                if ($graphConfig['mediator'] instanceof StateMediator) {
                    $mediator = $graphConfig['mediator'];
                } elseif (isset($graphConfig['mediator']['property'])) {
                    $mediator = new PropertyStateMediator($graphConfig['mediator']['property']);
                } elseif (isset($graphConfig['mediator']['method'])) {
                    $mediator = new MethodStateMediator(
                        $graphConfig['mediator']['method']['get'],
                        $graphConfig['mediator']['method']['set']
                    );
                } else {
                    throw new InvalidArgumentException(
                        sprintf('Config is missing a mediator for the %s graph.', $graphName)
                    );
                }

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
