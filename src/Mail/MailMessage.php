<?php

declare(strict_types=1);

namespace ContactForm\Mail;

/**
 * Repräsentiert eine vollständig aufgebaute E-Mail.
 */
final readonly class MailMessage
{
    /**
     * @param list<string> $recipients
     */
    public function __construct(
        public string $subject,
        public string $htmlBody,
        public string $plainBody,
        public string $replyTo,
        public array $recipients
    ) {
    }
}