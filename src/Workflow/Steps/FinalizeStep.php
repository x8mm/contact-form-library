<?php

declare(strict_types=1);

namespace ContactForm\Workflow\Steps;

use ContactForm\Form\FormStatus;
use ContactForm\Submission\SubmissionRepository;
use ContactForm\Workflow\WorkflowContext;
use ContactForm\Workflow\WorkflowStepInterface;

final readonly class FinalizeStep implements WorkflowStepInterface
{
    public function __construct(
        private SubmissionRepository $repository
    ) {
    }

    public function process(
        WorkflowContext $context
    ): void {
        $submission = $context->getSubmission();

        if ($context->isMailSent()) {
            $submission = $submission->withStatus(
                FormStatus::Successful
            );

            $this->repository->markSuccessful(
                $submission
            );

            return;
        }

        $submission = $submission->withStatus(
            FormStatus::Failed
        );

        $this->repository->markFailed(
            $submission
        );
    }
}