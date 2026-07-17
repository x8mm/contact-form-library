<?php

declare(strict_types=1);

namespace ContactForm\Submission;

use ContactForm\Exception\LoggingException;

/**
 * Verwaltet die Speicherung von JSON-Dateien.
 */
final class SubmissionStorage
{
    public function __construct(
        private readonly string $baseDirectory
    ) {
    }

    /**
     * Speichert eine JSON-Datei.
     *
     * @throws LoggingException
     */
    public function save(
        string $directory,
        string $filename,
        string $json
    ): void {
        $path = $this->buildDirectory($directory);

        if (!is_dir($path) && !mkdir($path, 0755, true) && !is_dir($path)) {
            throw new LoggingException(
                sprintf(
                    'Verzeichnis "%s" konnte nicht erstellt werden.',
                    $path
                )
            );
        }

        $file = $path . DIRECTORY_SEPARATOR . $filename;

        if (file_put_contents($file, $json, LOCK_EX) === false) {
            throw new LoggingException(
                sprintf(
                    'Datei "%s" konnte nicht geschrieben werden.',
                    $file
                )
            );
        }
    }

    /**
     * Verschiebt eine Datei.
     *
     * @throws LoggingException
     */
    public function move(
        string $fromDirectory,
        string $toDirectory,
        string $filename
    ): void {
        $source = $this->buildDirectory($fromDirectory)
            . DIRECTORY_SEPARATOR
            . $filename;

        $targetDirectory = $this->buildDirectory($toDirectory);

        if (
            !is_dir($targetDirectory)
            && !mkdir($targetDirectory, 0755, true)
            && !is_dir($targetDirectory)
        ) {
            throw new LoggingException(
                sprintf(
                    'Verzeichnis "%s" konnte nicht erstellt werden.',
                    $targetDirectory
                )
            );
        }

        $target = $targetDirectory
            . DIRECTORY_SEPARATOR
            . $filename;

        if (!rename($source, $target)) {
            throw new LoggingException(
                sprintf(
                    'Datei "%s" konnte nicht verschoben werden.',
                    $filename
                )
            );
        }
    }

    /**
     * Löscht eine gespeicherte Datei.
     *
     * Existiert die Datei nicht mehr, wird dies als erfolgreicher Zustand
     * behandelt. Nur ein fehlgeschlagener Löschvorgang einer vorhandenen Datei
     * führt zu einer Exception.
     *
     * @throws LoggingException
     */
    public function delete(
        string $directory,
        string $filename
    ): void {
        $file = $this->buildDirectory($directory)
            . DIRECTORY_SEPARATOR
            . $filename;

        if (!file_exists($file)) {
            return;
        }

        if (!unlink($file)) {
            throw new LoggingException(
                sprintf(
                    'Datei "%s" konnte nicht gelöscht werden.',
                    $file
                )
            );
        }
    }

    /**
     * Erstellt den vollständigen Verzeichnispfad.
     */
    private function buildDirectory(
        string $directory
    ): string {
        return rtrim(
            $this->baseDirectory,
            DIRECTORY_SEPARATOR
        ) . DIRECTORY_SEPARATOR . trim(
            $directory,
            DIRECTORY_SEPARATOR
        );
    }
}