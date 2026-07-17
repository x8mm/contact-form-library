<?php

declare(strict_types=1);

namespace ContactForm\Submission;

use ContactForm\Form\FormData;

/**
 * Erzeugt SHA-256-Prüfsummen für Submissions.
 */
final class SubmissionHasher
{
    /**
     * Berechnet den Hash.
     */
    public function hash(
        FormData $formData,
        string $timestamp
    ): string {
        return hash(
            'sha256',
            json_encode(
                [
                    'timestamp' => $timestamp,
                    'data' => $formData->toArray(),
                ],
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
                | JSON_THROW_ON_ERROR
            )
        );
    }
}