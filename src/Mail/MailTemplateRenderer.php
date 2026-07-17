<?php

declare(strict_types=1);

namespace ContactForm\Mail;

use ContactForm\Form\FormData;
use RuntimeException;

/**
 * Rendert Mail-Templates.
 */
final class MailTemplateRenderer
{
    public function __construct(
        private readonly string $templateDirectory
    ) {
    }

    /**
     * Rendert ein Template.
     */
    public function render(
        string $template,
        FormData $formData
    ): string {
        $file = rtrim(
            $this->templateDirectory,
            DIRECTORY_SEPARATOR
        ) . DIRECTORY_SEPARATOR . $template;

        if (!is_file($file)) {
            throw new RuntimeException(
                sprintf(
                    'Template "%s" wurde nicht gefunden.',
                    $template
                )
            );
        }

        ob_start();

        $data = $formData;

        require $file;

        return (string) ob_get_clean();
    }
}