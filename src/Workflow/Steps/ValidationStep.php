<?php

declare(strict_types=1);

namespace ContactForm\Workflow\Steps;

use ContactForm\Form\FormFactory;
use ContactForm\Validation\Validator;
use ContactForm\Workflow\WorkflowContext;
use ContactForm\Workflow\WorkflowStepInterface;
use ContactForm\Workflow\WorkflowValidationException;

final readonly class ValidationStep implements WorkflowStepInterface
{
    public function __construct(
        private FormFactory $factory,
        private Validator $validator
    ) {
    }

    public function process(
        WorkflowContext $context
    ): void {
        $formData = $this->factory->create();

        $context->setFormData($formData);

        $result = $this->validator

            ->required(
                'name',
                $formData->name
            )

            ->lengthBetween(
                'name',
                $formData->name,
                2,
                100
            )

            ->required(
                'email',
                $formData->email
            )

            ->email(
                'email',
                $formData->email
            )

            ->maxLength(
                'email',
                $formData->email,
                255
            )

            ->required(
                'subject',
                $formData->subject
            )

            ->lengthBetween(
                'subject',
                $formData->subject,
                3,
                150
            )

            ->required(
                'message',
                $formData->message
            )

            ->lengthBetween(
                'message',
                $formData->message,
                10,
                5000
            )

            ->validate();

        $context->setValidationResult($result);

        if (!$result->isValid()) {
            throw new WorkflowValidationException(
                $result
            );
        }
    }
}