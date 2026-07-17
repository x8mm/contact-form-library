<?php

declare(strict_types=1);

namespace ContactForm\Workflow;

use ContactForm\Validation\ValidationResult;

/**
 * Ergebnis eines Workflow-Durchlaufs.
 */
final readonly class WorkflowResult
{
    public function __construct(
        public bool $successful,
        public ?ValidationResult $validation = null,
        public ?string $message = null
    ) {
    }

    public static function success(
        ?string $message = null
    ): self {
        return new self(
            successful: true,
            message: $message
        );
    }

    public static function validationFailed(
        ValidationResult $validation
    ): self {
        return new self(
            successful: false,
            validation: $validation
        );
    }

    public static function failed(
        string $message
    ): self {
        return new self(
            successful: false,
            message: $message
        );
    }
}