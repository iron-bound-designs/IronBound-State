<?php

/**
 * Reusable trait for State Sets.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     MIT
 */

declare(strict_types=1);

namespace IronBound\State\Ds;

use IronBound\State\Exception\{DuplicateState, UnknownState};
use IronBound\State\State\{State, StateId};

use function IronBound\State\assertInstancesOf;

trait StateSetTrait
{
    /** @var State[] */
    private $storage = [];

    public function contains(StateId $id): bool
    {
        return isset($this->storage[ $id->getName() ]);
    }

    public function get(StateId $id): State
    {
        if (! isset($this->storage[ $id->getName() ])) {
            throw new UnknownState(sprintf(
                'The %s state is not a member of this set.',
                $id
            ));
        }

        return $this->storage[ $id->getName() ];
    }

    /**
     * Get an iterator to traverse the set.
     *
     * @return \Traversable|State[]
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
     * Add states to the set.
     *
     * @param State[] $states
     *
     * @throws DuplicateState Thrown if a state is already contained in this set.
     */
    private function addStates(iterable $states): void
    {
        assertInstancesOf(State::class, $states);

        foreach ($states as $state) {
            if ($this->contains($state->getId())) {
                throw new DuplicateState(sprintf(
                    'The state %s already exists in this set.',
                    $state->getId()
                ));
            }

            $this->storage[ $state->getId()->getName() ] = $state;
        }
    }
}
