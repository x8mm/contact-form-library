<?php

declare(strict_types=1);

namespace ContactForm\Workflow\Steps;

use ContactForm\Mail\MailMetadata;
use ContactForm\Submission\SubmissionFactory;
use ContactForm\Submission\SubmissionRepository;
use ContactForm\Workflow\WorkflowContext;
use ContactForm\Workflow\WorkflowStepInterface;

final readonly class PersistenceStep implements WorkflowStepInterface
{
    public function __construct(
        private SubmissionFactory $factory,
        private SubmissionRepository $repository
    ) {
    }

    public function process(
        WorkflowContext $context
    ): void {
        $submission = $this->factory->create(
            $context->getFormData(),
            MailMetadata::pending()
        );

        $this->repository->store($submission);

        $context->setSubmission($submission);
    }
}