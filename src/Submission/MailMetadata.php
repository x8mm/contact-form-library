<?php

declare(strict_types=1);

namespace ContactForm\Submission;

/**
 * Enthält sämtliche Informationen zum Mailversand.
 */
final readonly class MailMetadata
{
    /**
     * @param list<string> $recipients
     */
    public function __construct(
        public MailStatus $status,
        public array $recipients,
        public ?string $error = null
    ) {
    }

    /**
     * Wandelt die Informationen in ein Array um.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'status' => $this->status->value,
            'recipients' => $this->recipients,
            'error' => $this->error,
        ];
    }
}

/* Anpassungs aus 10.2 sieht vor "Anpassung 2"
**
final readonly class MailMetadata
{
    public function __construct(
        public MailStatus $status,
        public ?string $sentAt,
        public ?string $error
    ) {
    }

    public static function pending(): self
    {
        return new self(
            MailStatus::Pending,
            null,
            null
        );
    }

    public static function sent(): self
    {
        return new self(
            MailStatus::Sent,
            gmdate(DATE_ATOM),
            null
        );
    }

    public static function failed(
        string $message
    ): self {
        return new self(
            MailStatus::Failed,
            null,
            $message
        );
    }
}
*/