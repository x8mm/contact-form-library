<?php

declare(strict_types=1);

namespace ContactForm\Submission;

use ContactForm\Form\FormData;
use ContactForm\Form\FormStatus;

/**
 * Repräsentiert eine vollständige Formularanfrage.
 */
final readonly class Submission
{
    /**
     * @param list<string> $recipients
     */
    public function __construct(
        public string $ulid,
        public string $timestamp,
        public string $schemaVersion,
        public string $formName,
        public FormStatus $status,
        public FormData $formData,
        #public bool $mailSent, /*Anpassung aus Phase 8.3*/
        #public array $recipients, /*Anpassung aus Phase 8.3*/
        public MailMetadata $mail, /*Anpassung aus Phase 8.3*/
        public ?string $ip,
        public ?string $userAgent,
        public ?string $referer,
        public string $sha256
    ) {
    }

    /**
     * Konvertiert das Objekt in ein Array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'ulid' => $this->ulid,
            'timestamp' => $this->timestamp,
            'schemaVersion' => $this->schemaVersion,
            'formName' => $this->formName,
            'status' => $this->status->value,
            'formData' => $this->formData->toArray(),
            #'mailSent' => $this->mailSent,  /*Anpassung aus Phase 8.3*/
            #'recipients' => $this->recipients,  /*Anpassung aus Phase 8.3*/
            'mail' => $this->mail->toArray(),  /*Anpassung aus Phase 8.3*/
            'ip' => $this->ip,
            'userAgent' => $this->userAgent,
            'referer' => $this->referer,
            'sha256' => $this->sha256,
        ];
    }
}