<?php

/**
declare(strict_types=1);

namespace ContactForm\Exception;

use RuntimeException;


 * Exception für Konfigurationsfehler.
 *
 * Diese Exception wird ausgelöst, wenn:
 *
 * - eine erforderliche .env-Variable fehlt
 * - eine Konfiguration ungültig ist
 * - ein Datentyp nicht konvertiert werden kann
 * - eine Konfigurationsdatei nicht geladen werden kann

final class ConfigurationException extends RuntimeException
{
}
 */

declare(strict_types=1);

namespace ContactForm\Exception;

/**
 * Wird ausgelöst, wenn die Anwendung fehlerhaft konfiguriert ist.
 */
final class ConfigurationException extends ContactFormException
{
}