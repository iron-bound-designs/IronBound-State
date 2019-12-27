<?php

/**
 * Exception thrown when trying to apply an invalid transition.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State\Exception;

use IronBound\State\Transition\Evaluation;
use Throwable;

final class CannotTransition extends \RuntimeException implements Exception
{
    /** @var Evaluation */
    private $evaluation;

    public function __construct(Evaluation $evaluation, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->evaluation = $evaluation;
    }

    public static function create(Evaluation $evaluation): self
    {
        return new self(
            $evaluation,
            sprintf(
                'Cannot apply the %s transition: %s',
                $evaluation->getTransition()->getId(),
                implode(', ', $evaluation->getReasons())
            )
        );
    }

    /**
     * Get the evaluation.
     *
     * @return Evaluation
     */
    public function getEvaluation(): Evaluation
    {
        return $this->evaluation;
    }
}
