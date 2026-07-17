<?php

declare(strict_types=1);

namespace ContactForm\Mail;

use RuntimeException;

/**
 * Ausnahme während des Mailversands.
 */
final class MailException extends RuntimeException
{
}