<?php

declare(strict_types=1);

namespace ContactForm\Http;

use ContactForm\Config\Config;
# use RuntimeException;
use ContactForm\Exception\SessionException;

/**
 * Verwaltet die PHP-Session.
 *
 * Diese Klasse kapselt die Initialisierung und den Zugriff auf die
 * Session. Sie setzt sichere Cookie-Parameter und stellt eine
 * typisierte API für Session-Werte bereit.
 */
final class Session
{
    private readonly Config $config;

    /**
     * Gibt an, ob die Session über diese Instanz gestartet wurde.
     */
    private bool $started = false;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Initialisiert und startet die Session.
     *
     * @throws RuntimeException
     */
    public function start(): void
    {
        if ($this->started || session_status() === PHP_SESSION_ACTIVE) {
            $this->started = true;

            return;
        }

        session_name(
            $this->config->getString('SESSION_NAME')
        );

        session_set_cookie_params([
            'lifetime' => $this->config->getInt('SESSION_LIFETIME'),
            'path' => '/',
            'domain' => '',
            'secure' => $this->isSecureConnection(),
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        if (!session_start()) {
            #throw new RuntimeException(
            #throw new SessionException (
            throw new SessionException(
                'Die Session konnte nicht gestartet werden.'
            );
        }

        $this->started = true;
    }

    /**
     * Prüft, ob die Session aktiv ist.
     */
    public function isStarted(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    /**
     * Schreibt einen Wert in die Session.
     */
    public function set(string $key, mixed $value): void
    {
        $this->ensureStarted();

        $_SESSION[$key] = $value;
    }

    /**
     * Liest einen Wert aus der Session.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $this->ensureStarted();

        return $_SESSION[$key] ?? $default;
    }

    /**
     * Prüft, ob ein Schlüssel existiert.
     */
    public function has(string $key): bool
    {
        $this->ensureStarted();

        return array_key_exists($key, $_SESSION);
    }

    /**
     * Entfernt einen Session-Wert.
     */
    public function remove(string $key): void
    {
        $this->ensureStarted();

        unset($_SESSION[$key]);
    }

    /**
     * Löscht sämtliche Session-Daten.
     */
    public function clear(): void
    {
        $this->ensureStarted();

        $_SESSION = [];
    }

    /**
     * Beendet die Session vollständig.
     */
    public function destroy(): void
    {
        if (!$this->isStarted()) {
            return;
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                [
                    'expires' => time() - 3600,
                    'path' => $params['path'],
                    'domain' => $params['domain'],
                    'secure' => $params['secure'],
                    'httponly' => $params['httponly'],
                    'samesite' => $params['samesite'] ?? 'Strict',
                ]
            );
        }

        session_destroy();

        $this->started = false;
    }

    /**
     * Stellt sicher, dass die Session aktiv ist.
     *
     * @throws RuntimeException
     */
    private function ensureStarted(): void
    {
        if (!$this->isStarted()) {
            #throw new RuntimeException(
            #throw new SessionException (
            throw new SessionException(
                'Die Session wurde noch nicht gestartet.'
            );
        }
    }

    /**
     * Ermittelt, ob die aktuelle Verbindung HTTPS verwendet.
     */
    private function isSecureConnection(): bool
    {
        return isset($_SERVER['HTTPS'])
            && strtolower((string) $_SERVER['HTTPS']) !== 'off';
    }
}