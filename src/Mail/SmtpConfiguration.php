<?php

declare(strict_types=1);

namespace ContactForm\Mail;

/**
 * SMTP-Konfiguration.
 */
final readonly class SmtpConfiguration
{
    public function __construct(
        public string $host,
        public int $port,
        public string $username,
        public string $password,
        public string $encryption,
        public string $fromAddress,
        public string $fromName
    ) {
    }
}