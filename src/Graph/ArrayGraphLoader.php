<?php

/**
 * Factory to create a Graph from an array.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Graph;

use IronBound\State\State\{MutableState, StateId, StateType};
use IronBound\State\Transition\{CallableGuard, Guard, ImmutableTransition, TransitionId};

use function IronBound\State\assertValidGraph;
use function IronBound\State\map;

final class ArrayGraphLoader implements GraphLoader
{
    /** @var GraphId */
    private $graphId;

    /** @var array */
    private $config;

    /**
     * ArrayGraphFactory constructor.
     *
     * @param GraphId $graphId
     * @param array   $config
     */
    public function __construct(GraphId $graphId, array $config)
    {
        $this->graphId = $graphId;
        $this->config  = $config;
    }

    public function supports(GraphId $graphId): bool
    {
        return $this->graphId->equals($graphId);
    }

    public function make(GraphId $graphId): Graph
    {
        $mutable = new MutableGraph($this->graphId);

        /** @var MutableState[] $states */
        $states = [];

        foreach ($this->config['states'] as $name => $config) {
            if (is_string($config)) {
                $name   = $config;
                $config = [];
            }

            $type  = $config['type'] ?? StateType::NORMAL;
            $state = new MutableState(new StateId($name), new StateType($type));

            $states[ $name ] = $state;
            $mutable->addState($state);
        }

        foreach ($this->config['transitions'] as $name => $config) {
            $guard = $config['guard'] ?? null;

            if ($guard && ! $guard instanceof Guard) {
                $guard = new CallableGuard($guard);
            }

            $transition = new ImmutableTransition(
                new TransitionId($name),
                map((array) $config['from'], static function ($name) {
                    return new StateId($name);
                }),
                new StateId($config['to']),
                $guard
            );

            $mutable->addTransition($transition);
        }

        assertValidGraph($mutable);

        return $mutable->toImmutable();
    }
}
