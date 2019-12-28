<?php

/**
 * Shared trait for transition sets.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Ds;

use IronBound\State\Exception\{DuplicateTransition, UnknownTransition};
use IronBound\State\Transition\{Transition, TransitionId};

use function IronBound\State\assertInstancesOf;

trait TransitionSetTrait
{
    /** @var Transition[] */
    private $storage = [];

    public function contains(TransitionId $id): bool
    {
        return isset($this->storage[ $id->getName() ]);
    }

    public function get(TransitionId $id): Transition
    {
        if (! isset($this->storage[ $id->getName() ])) {
            throw new UnknownTransition(sprintf(
                'The %s transition is not a member of this set.',
                $id
            ));
        }

        return $this->storage[ $id->getName() ];
    }

    /**
     * Get an iterator to traverse the set.
     *
     * @return Transition[]|\Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator(array_values($this->storage));
    }

    public function count()
    {
        return count($this->storage);
    }

    /**
     * Add transitions to the set.
     *
     * @param Transition[] $transitions
     *
     * @throws DuplicateTransition Thrown if this transition is already contained in this set.
     */
    private function addTransitions(iterable $transitions): void
    {
        assertInstancesOf(Transition::class, $transitions);

        foreach ($transitions as $transition) {
            if ($this->contains($transition->getId())) {
                throw new DuplicateTransition(sprintf(
                    'The transition %s already exists in this set.',
                    $transition->getId()
                ));
            }

            $this->storage[ $transition->getId()->getName() ] = $transition;
        }
    }
}
