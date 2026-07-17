<?php

declare(strict_types=1);

namespace ContactForm\Form;

use ContactForm\Http\Request;

/**
 * Erstellt FormData-Objekte aus einem HTTP-Request.
 */
final class FormFactory
{
    public function __construct(
        private readonly Request $request
    ) {
    }

    /**
     * Erstellt ein FormData-Objekt.
     */
    public function create(): FormData
    {
        return new FormData(
            name: $this->request->post('name') ?? '',
            email: $this->request->post('email') ?? '',
            subject: $this->request->post('subject') ?? '',
            message: $this->request->post('message') ?? ''
        );
    }
}