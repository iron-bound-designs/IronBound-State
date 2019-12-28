<?php

/**
 * Event fired when testing if a transition can be applied.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Event;

final class TestTransitionEvent extends TransitionEvent
{
    /** @var string[] */
    private $rejections = [];

    /**
     * Prevent the transition from being applied.
     *
     * @param string $reason Reason why the transition could not be applied.
     *
     * @return $this
     */
    public function reject(string $reason): self
    {
        $this->rejections[] = $reason;

        return $this;
    }

    /**
     * Get all the reasons the transition was rejected.
     *
     * @return string[]
     */
    public function getRejectionReasons(): array
    {
        return $this->rejections;
    }
}
