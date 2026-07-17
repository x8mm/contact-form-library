<?php

declare(strict_types=1);

namespace ContactForm\Mail;

use ContactForm\Form\FormData;

/**
 * Erstellt eine vollständige MailMessage.
 */
final class MailBuilder
{
    public function __construct(
        private readonly MailTemplateRenderer $renderer,
        private readonly RecipientResolver $resolver
    ) {
    }

    /**
     * Erstellt die Mail.
     */
    public function build(
        FormData $formData
    ): MailMessage {
        return new MailMessage(
            subject: sprintf(
                'Neue Kontaktanfrage von %s',
                $formData->name
            ),
            htmlBody: $this->renderer->render(
                'mail/html.php',
                $formData
            ),
            plainBody: $this->renderer->render(
                'mail/text.php',
                $formData
            ),
            replyTo: $formData->email,
            recipients: $this->resolver->resolve()
        );
    }
}