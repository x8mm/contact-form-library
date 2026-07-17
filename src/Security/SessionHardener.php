<?php

declare(strict_types=1);

namespace ContactForm\Security;

use ContactForm\Config\Config;
use ContactForm\Exception\SessionException;
use ContactForm\Http\Request;
use ContactForm\Http\Session;

/**
 * Härtet die PHP-Session gegen typische Angriffe.
 *
 * Aufgaben:
 *
 * - sichere INI-Parameter setzen
 * - Session-Fingerprint prüfen
 * - Session-ID regelmäßig erneuern
 * - Inaktivitäts-Timeout überwachen
 * - absolute Sitzungsdauer überwachen
 */
final class SessionHardener
{
    /**
     * Zeit zwischen zwei Session-Regenerationen.
     */
    private const REGENERATE_INTERVAL = 300;

    private readonly Config $config;

    private readonly Session $session;

    private readonly Request $request;

    public function __construct(
        Config $config,
        Session $session,
        Request $request
    ) {
        $this->config = $config;
        $this->session = $session;
        $this->request = $request;
    }

    /**
     * Führt sämtliche Sicherheitsmaßnahmen aus.
     *
     * @throws SessionException
     */
    public function harden(): void
    {
        $this->configureIni();

        $this->session->start();

        $this->initialize();

        $this->validateFingerprint();

        $this->enforceIdleTimeout();

        $this->enforceAbsoluteLifetime();

        $this->regenerateSession();
    }

    /**
     * Setzt sicherheitsrelevante Session-INI-Werte.
     */
    private function configureIni(): void
    {
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_trans_sid', '0');
        ini_set('session.cookie_httponly', '1');
        ini_set(
            'session.cookie_secure',
            $this->request->isSecure() ? '1' : '0'
        );
        ini_set('session.cookie_samesite', 'Strict');
    }

    /**
     * Initialisiert die Session.
     */
    private function initialize(): void
    {
        if (!$this->session->has('__created')) {
            $now = time();

            $this->session->set('__created', $now);
            $this->session->set('__last_activity', $now);
            $this->session->set('__last_regeneration', $now);
            $this->session->set(
                '__fingerprint',
                $this->createFingerprint()
            );
        }
    }

    /**
     * Prüft den Browser-Fingerprint.
     *
     * @throws SessionException
     */
    private function validateFingerprint(): void
    {
        $expected = $this->session->get('__fingerprint');

        if (!is_string($expected)) {
            throw new SessionException('Ungültiger Session-Fingerprint.');
        }

        if (!hash_equals($expected, $this->createFingerprint())) {
            $this->session->destroy();

            throw new SessionException(
                'Die Session wurde aus Sicherheitsgründen beendet.'
            );
        }
    }

    /**
     * Überwacht die Inaktivität.
     *
     * @throws SessionException
     */
    private function enforceIdleTimeout(): void
    {
        $timeout = $this->config->getInt('SESSION_LIFETIME');

        $lastActivity = (int) $this->session->get('__last_activity');

        if ((time() - $lastActivity) > $timeout) {
            $this->session->destroy();

            throw new SessionException(
                'Die Sitzung ist abgelaufen.'
            );
        }

        $this->session->set('__last_activity', time());
    }

    /**
     * Überwacht die maximale Sitzungsdauer.
     *
     * @throws SessionException
     */
    private function enforceAbsoluteLifetime(): void
    {
        $maximumLifetime = 28800;

        $created = (int) $this->session->get('__created');

        if ((time() - $created) > $maximumLifetime) {
            $this->session->destroy();

            throw new SessionException(
                'Die maximale Sitzungsdauer wurde überschritten.'
            );
        }
    }

    /**
     * Erneuert regelmäßig die Session-ID.
     *
     * @throws SessionException
     */
    private function regenerateSession(): void
    {
        $last = (int) $this->session->get('__last_regeneration');

        if ((time() - $last) < self::REGENERATE_INTERVAL) {
            return;
        }

        if (!session_regenerate_id(true)) {
            throw new SessionException(
                'Die Session-ID konnte nicht erneuert werden.'
            );
        }

        $this->session->set(
            '__last_regeneration',
            time()
        );
    }

    /**
     * Erzeugt einen Browser-Fingerprint.
     */
    private function createFingerprint(): string
    {
        return hash(
            'sha256',
            sprintf(
                '%s|%s',
                $this->request->getUserAgent() ?? '',
                $this->request->getHost() ?? ''
            )
        );
    }
}