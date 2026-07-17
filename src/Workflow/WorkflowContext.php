<?php

declare(strict_types=1);

namespace ContactForm\Workflow;

use ContactForm\Form\FormData;
use ContactForm\Mail\MailMessage;
use ContactForm\Submission\Submission;
use ContactForm\Validation\ValidationResult;

/**
 * Gemeinsamer Kontext für den Formular-Workflow.
 *
 * Das Objekt wird von allen Workflow-Schritten gemeinsam genutzt
 * und transportiert den aktuellen Bearbeitungszustand.
 */
final class WorkflowContext
{
    private ?FormData $formData = null;

    private ?Submission $submission = null;

    private ?ValidationResult $validationResult = null;

    private ?MailMessage $mailMessage = null;

    /* Anpassung aus Phase 10.3 "Anpassung 1"
    private bool $mailSent = false;
    */

    public function setFormData(FormData $formData): void
    {
        $this->formData = $formData;
    }

    public function getFormData(): ?FormData
    {
        return $this->formData;
    }

    public function setSubmission(Submission $submission): void
    {
        $this->submission = $submission;
    }

    public function getSubmission(): ?Submission
    {
        return $this->submission;
    }

    public function setValidationResult(
        ValidationResult $result
    ): void {
        $this->validationResult = $result;
    }

    public function getValidationResult(): ?ValidationResult
    {
        return $this->validationResult;
    }

    public function setMailMessage(
        MailMessage $message
    ): void {
        $this->mailMessage = $message;
    }

    public function getMailMessage(): ?MailMessage
    {
        return $this->mailMessage;
    }

    /* Anpassung aus Phase 10.3 "Anpassung 1"
    public function markMailSent(): void
    {
        $this->mailSent = true;
    }
    */
    /* Anpassung aus Phase 10.3 "Anpassung 1"
    public function isMailSent(): bool
    {
        return $this->mailSent;
    }
    */
}