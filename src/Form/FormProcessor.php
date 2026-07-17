<?php

declare(strict_types=1);

namespace ContactForm\Form;

use ContactForm\Exception\SecurityException;
use ContactForm\Security\Csrf;
use ContactForm\Security\Honeypot;
use ContactForm\Security\RateLimiter;
use ContactForm\Validation\Validator;

final class FormProcessor
{
    public function __construct(
        private readonly FormFactory $factory,
        private readonly Validator $validator,
        private readonly Csrf $csrf,
        private readonly Honeypot $honeypot,
        private readonly RateLimiter $rateLimiter
    ) {
    }

    /**
     * Verarbeitet das Formular.
     */
    #public function process(): FormResponse|\ContactForm\Http\JsonResponse
    public function process(): JsonResponse
    {
        try {
            /*
             * Sicherheitsprüfungen
             */

            $this->honeypot->validate();

            $this->rateLimiter->validate();

            /*
             * Formulardaten erzeugen
             */

            $formData = $this->factory->create();

            /*
             * Validierung
             */

            $result = $this->validator
                ->required('name', $formData->name)
                ->lengthBetween('name', $formData->name, 2, 100)

                ->required('email', $formData->email)
                ->email('email', $formData->email)
                ->maxLength('email', $formData->email, 255)

                ->required('subject', $formData->subject)
                ->lengthBetween('subject', $formData->subject, 3, 150)

                ->required('message', $formData->message)
                ->lengthBetween('message', $formData->message, 10, 5000)

                ->validate();

            if (!$result->isValid()) {
                return FormResponse::validationError($result);
            }

            /*
             * Phase 8
             *
             * Logger
             * MailService
             */

            return FormResponse::success();
        } catch (SecurityException $exception) {
            return FormResponse::securityError(
                $exception->getMessage()
            );
        } catch (\Throwable) {
            return FormResponse::serverError();
        }
    }
}