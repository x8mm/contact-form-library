<?php

declare(strict_types=1);

namespace ContactForm\Security;

use ContactForm\Config\Config;
use ContactForm\Exception\SecurityException;
use ContactForm\Http\Request;
use ContactForm\Http\Session;

/**
 * Implementiert einen serverseitigen Honeypot-Schutz.
 *
 * Die Prüfung besteht aus zwei Komponenten:
 *
 * - verstecktes Formularfeld
 * - minimale Ausfüllzeit
 */
final class Honeypot
{
    /**
     * Mindestdauer bis zum Absenden.
     */
    private const MINIMUM_SECONDS = 2;

    /**
     * Session-Schlüssel.
     */
    private const SESSION_KEY = '__honeypot';

    private readonly Config $config;

    private readonly Request $request;

    private readonly Session $session;

    public function __construct(
        Config $config,
        Request $request,
        Session $session
    ) {
        $this->config = $config;
        $this->request = $request;
        $this->session = $session;
    }

    /**
     * Initialisiert den Honeypot.
     */
    public function initialize(): void
    {
        $this->session->set(
            self::SESSION_KEY,
            time()
        );
    }

    /**
     * Führt sämtliche Prüfungen aus.
     *
     * @throws SecurityException
     */
    public function validate(): void
    {
        $this->validateHiddenField();

        $this->validateSubmissionTime();
    }

    /**
     * Prüft das versteckte Feld.
     *
     * @throws SecurityException
     */
    private function validateHiddenField(): void
    {
        $field = $this->config->getString(
            'HONEYPOT_FIELD'
        );

        $value = $this->request->post($field);

        if ($value !== null && $value !== '') {
            throw new SecurityException(
                'Honeypot ausgelöst.'
            );
        }
    }

    /**
     * Prüft die minimale Ausfüllzeit.
     *
     * @throws SecurityException
     */
    private function validateSubmissionTime(): void
    {
        if (!$this->session->has(self::SESSION_KEY)) {
            throw new SecurityException(
                'Honeypot wurde nicht initialisiert.'
            );
        }

        $started = (int) $this->session->get(
            self::SESSION_KEY
        );

        if ((time() - $started) < self::MINIMUM_SECONDS) {
            throw new SecurityException(
                'Formular wurde zu schnell abgesendet.'
            );
        }
    }
}