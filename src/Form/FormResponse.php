<?php

declare(strict_types=1);

namespace ContactForm\Form;

use ContactForm\Http\JsonResponse;
use ContactForm\Validation\ValidationResult;

/**
 * Erzeugt standardisierte JSON-Antworten
 * für das Formular.
 */
final class FormResponse
{
    /**
     * Erfolgreiche Verarbeitung.
     */
    public static function success(
        string $message = 'Ihre Nachricht wurde erfolgreich versendet.'
    ): JsonResponse {
        return new JsonResponse(
            [
                'success' => true,
                'message' => $message,
            ],
            200
        );
    }

    /**
     * Validierungsfehler.
     */
    public static function validationError(
        ValidationResult $result
    ): JsonResponse {
        return new JsonResponse(
            [
                'success' => false,
                'message' => 'Bitte überprüfen Sie Ihre Eingaben.',
                'errors' => $result->getErrors(),
            ],
            422
        );
    }

    /**
     * Sicherheitsfehler.
     */
    public static function securityError(
        string $message
    ): JsonResponse {
        return new JsonResponse(
            [
                'success' => false,
                'message' => $message,
            ],
            403
        );
    }

    /**
     * Interner Fehler.
     */
    public static function serverError(
        string $message = 'Interner Serverfehler.'
    ): JsonResponse {
        return new JsonResponse(
            [
                'success' => false,
                'message' => $message,
            ],
            500
        );
    }
}