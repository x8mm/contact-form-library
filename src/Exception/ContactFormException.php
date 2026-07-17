<?php

declare(strict_types=1);

namespace ContactForm\Exception;

use RuntimeException;

/**
 * Basisklasse für sämtliche projektspezifischen Exceptions.
 *
 * Alle fachlichen Exceptions der Bibliothek erben von dieser Klasse.
 * Dadurch können sie zentral behandelt werden, ohne allgemeine
 * RuntimeExceptions abfangen zu müssen.
 */
abstract class ContactFormException extends RuntimeException
{
}