<?php

declare(strict_types=1);

namespace ContactForm\Submission;

/**
 * Status des Mailversands.
 */
enum MailStatus: string
{
    /**
     * Mail wurde noch nicht verarbeitet.
     */
    case Pending = 'pending';

    /**
     * Mail wurde erfolgreich versendet.
     */
    case Sent = 'sent';

    /**
     * Mailversand fehlgeschlagen.
     */
    case Failed = 'failed';

    /**
     * Mailversand wurde übersprungen.
     */
    case Skipped = 'skipped';
}