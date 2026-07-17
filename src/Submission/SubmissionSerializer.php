<?php

declare(strict_types=1);

namespace ContactForm\Submission;

use JsonException;

/**
 * Serialisiert eine Submission als JSON.
 */
final class SubmissionSerializer
{
    /**
     * Wandelt eine Submission in einen JSON-String um.
     *
     * @throws JsonException
     */
    public function serialize(
        Submission $submission
    ): string {
        return json_encode(
            $submission->toArray(),
            JSON_PRETTY_PRINT
            | JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
            | JSON_THROW_ON_ERROR
        );
    }
}