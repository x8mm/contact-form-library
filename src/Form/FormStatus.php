<?php

declare(strict_types=1);

namespace ContactForm\Form;

/**
 * Status einer Formularanfrage.
 *
 * Diese Werte werden sowohl für die JSON-Ablage als auch für
 * den Mailversand verwendet.
 */
enum FormStatus: string
{
    /**
     * Anfrage wird verarbeitet.
     */
    case Processing = 'processing';

    /**
     * Anfrage erfolgreich abgeschlossen.
     */
    case Successful = 'successful';

    /**
     * Fehler beim Verarbeiten.
     */
    case Failed = 'failed';

    /**
     * Anfrage archiviert.
     */
    case Processed = 'processed';
}