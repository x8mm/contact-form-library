<?php

declare(strict_types=1);

namespace ContactForm\Validation;

/**
 * Enthält das Ergebnis einer Formularvalidierung.
 *
 * Diese Klasse ist unveränderlich und kapselt den
 * Validierungsstatus sowie alle aufgetretenen Fehler.
 */
final readonly class ValidationResult
{
    /**
     * @param array<string, list<string>> $errors
     */
    public function __construct(
        private bool $valid,
        private array $errors = []
    ) {
    }

    /**
     * Gibt zurück, ob die Validierung erfolgreich war.
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * Gibt alle Fehler zurück.
     *
     * @return array<string, list<string>>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Prüft, ob für ein Feld Fehler existieren.
     */
    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    /**
     * Gibt alle Fehler eines Feldes zurück.
     *
     * @return list<string>
     */
    public function getFieldErrors(string $field): array
    {
        return $this->errors[$field] ?? [];
    }

    /**
     * Gibt den ersten Fehler eines Feldes zurück.
     */
    public function getFirstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    /**
     * Prüft, ob überhaupt Fehler vorhanden sind.
     */
    public function hasErrors(): bool
    {
        return !$this->valid;
    }
}