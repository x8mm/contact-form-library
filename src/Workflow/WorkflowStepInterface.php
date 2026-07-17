<?php

declare(strict_types=1);

namespace ContactForm\Workflow;

/**
 * Schnittstelle aller Workflow-Schritte.
 */
interface WorkflowStepInterface
{
    /**
     * Führt einen Workflow-Schritt aus.
     */
    public function process(
        WorkflowContext $context
    ): void;
}