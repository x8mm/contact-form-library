<?php

declare(strict_types=1);

namespace ContactForm\Workflow;

use Throwable;

/**
 * Führt den kompletten Formular-Workflow aus.
 */
final class SubmissionWorkflow
{
    /**
     * @param iterable<WorkflowStepInterface> $steps
     */
    public function __construct(
        private readonly iterable $steps
    ) {
    }

    /**
     * Führt sämtliche Workflow-Schritte aus.
     */
    public function execute(): WorkflowResult
    {
        $context = new WorkflowContext();

        try {
            foreach ($this->steps as $step) {
                $step->process($context);
            }

            return WorkflowResult::success();
        } catch (WorkflowValidationException $exception) {
            return WorkflowResult::validationFailed(
                $exception->getValidationResult()
            );
        } catch (Throwable $exception) {
            return WorkflowResult::failed(
                $exception->getMessage()
            );
        }
    }
}