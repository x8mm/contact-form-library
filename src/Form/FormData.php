<?php

declare(strict_types=1);

namespace ContactForm\Form;

/**
 * Repräsentiert die validierten Formulardaten.
 */
final readonly class FormData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $subject,
        public string $message
    ) {
    }

    /**
     * Liefert die Daten als Array.
     *
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
        ];
    }
}