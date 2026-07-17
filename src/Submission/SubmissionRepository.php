<?php

declare(strict_types=1);

namespace ContactForm\Submission;

use ContactForm\Form\FormStatus;
use JsonException;

/**
 * Fachliche API für die Speicherung von Formularanfragen.
 */
final class SubmissionRepository
{
    public function __construct(
        private readonly SubmissionSerializer $serializer,
        private readonly SubmissionStorage $storage
    ) {
    }

    /**
     * Speichert eine neue Anfrage.
     *
     * @throws JsonException
     */
    public function store(
        Submission $submission
    ): void {
        $this->storage->save(
            FormStatus::Processing->value,
            $this->filename($submission),
            $this->serializer->serialize($submission)
        );
    }

    /**
     * Markiert eine Anfrage als erfolgreich.
     */
    public function markSuccessful(
        Submission $submission
    ): void {
        $this->move(
            FormStatus::Processing,
            FormStatus::Successful,
            $submission
        );
    }

    /**
     * Markiert eine Anfrage als fehlgeschlagen.
     */
    public function markFailed(
        Submission $submission
    ): void {
        $this->move(
            FormStatus::Processing,
            FormStatus::Failed,
            $submission
        );
    }

    /**
     * Archiviert eine Anfrage.
     */
    public function archive(
        Submission $submission
    ): void {
        $this->move(
            FormStatus::Successful,
            FormStatus::Processed,
            $submission
        );
    }

    /**
     * Verschiebt eine Datei.
     */
    private function move(
        FormStatus $from,
        FormStatus $to,
        Submission $submission
    ): void {
        $this->storage->move(
            $from->value,
            $to->value,
            $this->filename($submission)
        );
    }

    /**
     * Erzeugt den Dateinamen.
     */
    private function filename(
        Submission $submission
    ): string {
        return sprintf(
            '%s.json',
            $submission->ulid
        );
    }
}