<?php

/**
 * The result of evaluating if a transition can be completed.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Transition;

final class Evaluation
{
    /** @var object */
    private $subject;

    /** @var Transition */
    private $transition;

    /** @var bool */
    private $valid;

    /** @var string[] */
    private $reasons;

    /**
     * Evaluation constructor.
     *
     * @param object     $subject
     * @param Transition $transition
     * @param bool       $valid
     * @param string[]   $reasons
     */
    private function __construct(object $subject, Transition $transition, bool $valid, array $reasons)
    {
        $this->subject    = $subject;
        $this->transition = $transition;
        $this->valid      = $valid;
        $this->reasons    = $reasons;
    }

    /**
     * Make a valid Evaluation result.
     *
     * @param object     $subject
     * @param Transition $transition
     *
     * @return Evaluation
     */
    public static function valid(object $subject, Transition $transition): Evaluation
    {
        return new static($subject, $transition, true, []);
    }

    /**
     * Make an invalid Evaluation result.
     *
     * @param object     $subject
     * @param Transition $transition
     * @param string     ...$reasons
     *
     * @return Evaluation
     */
    public static function invalid(object $subject, Transition $transition, string ...$reasons): Evaluation
    {
        return new static($subject, $transition, false, $reasons);
    }

    /**
     * Get the subject the transition is being evaluated for.
     *
     * @return object
     */
    public function getSubject(): object
    {
        return $this->subject;
    }

    /**
     * Get the transition being evaluated.
     *
     * @return Transition
     */
    public function getTransition(): Transition
    {
        return $this->transition;
    }

    /**
     * Is this a valid transition for the subject.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * Is this an invalid transition for the subject.
     *
     * @return bool
     */
    public function isInvalid(): bool
    {
        return ! $this->valid;
    }

    /**
     * Get the reasons the transition could not be applied.
     *
     * @return string[]
     */
    public function getReasons(): array
    {
        return $this->reasons;
    }
}
