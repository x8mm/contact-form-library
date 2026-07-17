<?php

declare(strict_types=1);

namespace ContactForm\Mail;

use ContactForm\Config\Config;
use ContactForm\Exception\ConfigurationException;

/**
 * Ermittelt die Empfänger aus der Konfiguration.
 */
final class RecipientResolver
{
    public function __construct(
        private readonly Config $config
    ) {
    }

    /**
     * Liefert alle Empfänger.
     *
     * @return list<string>
     *
     * @throws ConfigurationException
     */
    public function resolve(): array
    {
        $value = $this->config->getString('MAIL_RECIPIENTS');

        $recipients = array_filter(
            array_map(
                static fn(string $mail): string => trim($mail),
                explode(',', $value)
            ),
            static fn(string $mail): bool => $mail !== ''
        );

        foreach ($recipients as $recipient) {
            if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                throw new ConfigurationException(
                    sprintf(
                        'Ungültige E-Mail-Adresse in MAIL_RECIPIENTS: "%s".',
                        $recipient
                    )
                );
            }
        }

        return array_values($recipients);
    }
}
