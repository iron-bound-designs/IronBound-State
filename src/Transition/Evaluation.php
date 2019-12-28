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
    /** @var bool */
    private $valid;

    /** @var string[] */
    private $reasons;

    /**
     * Evaluation constructor.
     *
     * @param bool     $valid
     * @param string[] $reasons
     */
    private function __construct(bool $valid, array $reasons)
    {
        $this->valid   = $valid;
        $this->reasons = $reasons;
    }

    /**
     * Make a valid Evaluation result.
     *
     * @return Evaluation
     */
    public static function valid(): Evaluation
    {
        return new static(true, []);
    }

    /**
     * Make an invalid Evaluation result.
     *
     * @param string ...$reasons
     *
     * @return Evaluation
     */
    public static function invalid(string ...$reasons): Evaluation
    {
        return new static(false, $reasons);
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
