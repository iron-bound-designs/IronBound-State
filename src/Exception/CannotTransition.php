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
use IronBound\State\Transition\TransitionId;
use Throwable;

final class CannotTransition extends \RuntimeException implements Exception
{
    /** @var Evaluation */
    private $evaluation;

    /** @var TransitionId */
    private $transitionId;

    public function __construct(
        Evaluation $evaluation,
        TransitionId $transitionId,
        $message = '',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->evaluation   = $evaluation;
        $this->transitionId = $transitionId;
    }

    public static function create(Evaluation $evaluation, TransitionId $transitionId): self
    {
        return new self(
            $evaluation,
            $transitionId,
            sprintf(
                'Cannot apply the %s transition: %s',
                $transitionId,
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

    /**
     * Get the transition that tried to be applied.
     *
     * @return TransitionId
     */
    public function getTransitionId(): TransitionId
    {
        return $this->transitionId;
    }
}
