<?php

declare(strict_types=1);

namespace ContactForm\Workflow\Steps;

use ContactForm\Mail\MailBuilder;
use ContactForm\Mail\MailService;
use ContactForm\Workflow\WorkflowContext;
use ContactForm\Workflow\WorkflowStepInterface;

final readonly class MailStep implements WorkflowStepInterface
{
    public function __construct(
        private MailBuilder $builder,
        private MailService $service
    ) {
    }

    public function process(
        WorkflowContext $context
    ): void {
        $message = $this->builder->build(
            $context->getFormData()
        );

        $context->setMailMessage($message);

        $this->service->send($message);

        $context->markMailSent();
    }
}