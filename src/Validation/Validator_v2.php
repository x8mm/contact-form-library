<?php

declare(strict_types=1);

namespace ContactForm\Validation;

use Closure;

/**
 * Führt serverseitige Formularvalidierungen durch.
 *
 * Der Validator sammelt sämtliche Validierungsfehler und liefert
 * nach Abschluss ein unveränderliches ValidationResult zurück.
 */
final class Validator
{
    /**
     * @var array<string, list<string>>
     */
    private array $errors = [];


    
/** Neu eingeführt aus Kleines "Refactoring des Validators" aus Phase 6.2
/**
 * Normalisiert einen Eingabewert.
 */
private function normalize(?string $value): ?string
{
    if ($value === null) {
        return null;
    }

    return trim($value);
}
    
/**
 * Prüft, ob die Zeichenlänge innerhalb eines Bereichs liegt.
 */
public function lengthBetween(
    string $field,
    ?string $value,
    int $minimum,
    int $maximum,
    ?string $message = null
): self {
    $value = $this->normalize($value);

    if ($value === null || $value === '') {
        return $this;
    }

    $length = mb_strlen($value, 'UTF-8');

    if ($length < $minimum || $length > $maximum) {
        $this->addError(
            $field,
            $message ?? sprintf(
                'Die Eingabe muss zwischen %d und %d Zeichen lang sein.',
                $minimum,
                $maximum
            )
        );
    }

    return $this;
}

/**
 * Prüft, ob ein Feld leer ist.
 *
 * Diese Hilfsmethode dient der besseren Lesbarkeit bei optionalen Feldern.
 */
public function nullable(
    string $field,
    ?string $value
): self {
    return $this;
}

    
    
    /**
     * Prüft, ob ein Pflichtfeld ausgefüllt wurde.
     */
    public function required(
        string $field,
        ?string $value,
        ?string $message = null
    ): self {
        if ($value === null || trim($value) === '') {
            $this->addError(
                $field,
                $message ?? 'Dieses Feld ist erforderlich.'
            );
        }

        return $this;
    }

    /**
     * Prüft eine E-Mail-Adresse.
     */
    public function email(
        string $field,
        ?string $value,
        ?string $message = null
    ): self {
        if ($value === null || $value === '') {
            return $this;
        }

        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->addError(
                $field,
                $message ?? 'Bitte geben Sie eine gültige E-Mail-Adresse ein.'
            );
        }

        return $this;
    }

    /**
     * Prüft die Mindestlänge.
     */
    public function minLength(
        string $field,
        ?string $value,
        int $length,
        ?string $message = null
    ): self {
        if ($value === null || $value === '') {
            return $this;
        }

        if (mb_strlen($value, 'UTF-8') < $length) {
            $this->addError(
                $field,
                $message ?? sprintf(
                    'Mindestens %d Zeichen erforderlich.',
                    $length
                )
            );
        }

        return $this;
    }

    /**
     * Prüft die Maximallänge.
     */
    public function maxLength(
        string $field,
        ?string $value,
        int $length,
        ?string $message = null
    ): self {
        if ($value === null || $value === '') {
            return $this;
        }

        if (mb_strlen($value, 'UTF-8') > $length) {
            $this->addError(
                $field,
                $message ?? sprintf(
                    'Maximal %d Zeichen erlaubt.',
                    $length
                )
            );
        }

        return $this;
    }

    /**
     * Prüft eine exakte Länge.
     */
    public function exactLength(
        string $field,
        ?string $value,
        int $length,
        ?string $message = null
    ): self {
        if ($value === null || $value === '') {
            return $this;
        }

        if (mb_strlen($value, 'UTF-8') !== $length) {
            $this->addError(
                $field,
                $message ?? sprintf(
                    'Es sind genau %d Zeichen erforderlich.',
                    $length
                )
            );
        }

        return $this;
    }

    /**
     * Prüft einen regulären Ausdruck.
     */
    public function matches(
        string $field,
        ?string $value,
        string $pattern,
        ?string $message = null
    ): self {
        if ($value === null || $value === '') {
            return $this;
        }

        if (preg_match($pattern, $value) !== 1) {
            $this->addError(
                $field,
                $message ?? 'Ungültiges Format.'
            );
        }

        return $this;
    }

    /**
     * Vergleicht zwei Werte.
     */
    public function equals(
        string $field,
        ?string $value,
        ?string $other,
        ?string $message = null
    ): self {
        if ($value !== $other) {
            $this->addError(
                $field,
                $message ?? 'Die Werte stimmen nicht überein.'
            );
        }

        return $this;
    }

    /**
     * Prüft auf gültiges UTF-8.
     */
    public function utf8(
        string $field,
        ?string $value,
        ?string $message = null
    ): self {
        if ($value === null || $value === '') {
            return $this;
        }

        if (!mb_check_encoding($value, 'UTF-8')) {
            $this->addError(
                $field,
                $message ?? 'Ungültige UTF-8-Zeichenfolge.'
            );
        }

        return $this;
    }

    /**
     * Führt eine benutzerdefinierte Regel aus.
     *
     * Der Callback muss true zurückgeben, wenn die Prüfung erfolgreich war.
     */
    public function custom(
        string $field,
        mixed $value,
        Closure $callback,
        ?string $message = null
    ): self {
        if (!$callback($value)) {
            $this->addError(
                $field,
                $message ?? 'Ungültiger Wert.'
            );
        }

        return $this;
    }

    /**
     * Erstellt das Validierungsergebnis.
     */
    public function validate(): ValidationResult
    {
        return new ValidationResult(
            valid: $this->errors === [],
            errors: $this->errors
        );
    }

    /**
     * Entfernt alle bisher gesammelten Fehler.
     */
    public function reset(): self
    {
        $this->errors = [];

        return $this;
    }

    /**
     * Fügt einen Fehler hinzu.
     */
    private function addError(
        string $field,
        string $message
    ): void {
        $this->errors[$field] ??= [];
        $this->errors[$field][] = $message;
    }
}