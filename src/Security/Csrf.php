<?php

declare(strict_types=1);

namespace ContactForm\Security;

use ContactForm\Exception\SecurityException;
use ContactForm\Http\Session;

/**
 * Verwaltet CSRF-Token.
 *
 * Die Tokens werden in der Session gespeichert und besitzen
 * eine begrenzte Gültigkeitsdauer.
 */
final class Csrf
{
    /**
     * Session-Schlüssel.
     */
    private const SESSION_KEY = '__csrf';

    /**
     * Gültigkeitsdauer in Sekunden.
     */
    private const TOKEN_LIFETIME = 1800;

    private readonly Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Liefert einen gültigen CSRF-Token.
     *
     * Existiert bereits ein gültiger Token,
     * wird dieser wiederverwendet.
     *
     * @throws SecurityException
     */
    public function getToken(): string
    {
        if ($this->session->has(self::SESSION_KEY)) {
            /** @var array<string,mixed> $stored */
            $stored = $this->session->get(self::SESSION_KEY);

            if (
                isset($stored['token'], $stored['created']) &&
                (time() - (int) $stored['created']) < self::TOKEN_LIFETIME
            ) {
                return (string) $stored['token'];
            }
        }

        return $this->generateToken();
    }

    /**
     * Prüft einen Token.
     *
     * @throws SecurityException
     */
    public function validate(string $token): bool
    {
        if (!$this->session->has(self::SESSION_KEY)) {
            throw new SecurityException(
                'CSRF-Token fehlt.'
            );
        }

        /** @var array<string,mixed> $stored */
        $stored = $this->session->get(self::SESSION_KEY);

        if (
            !isset($stored['token'], $stored['created'])
        ) {
            throw new SecurityException(
                'Ungültiger CSRF-Speicher.'
            );
        }

        if (
            (time() - (int) $stored['created']) >
            self::TOKEN_LIFETIME
        ) {
            $this->removeToken();

            throw new SecurityException(
                'CSRF-Token abgelaufen.'
            );
        }

        $valid = hash_equals(
            (string) $stored['token'],
            $token
        );

        if (!$valid) {
            throw new SecurityException(
                'Ungültiger CSRF-Token.'
            );
        }

        return true;
    }

    /**
     * Entfernt den Token.
     */
    public function removeToken(): void
    {
        $this->session->remove(self::SESSION_KEY);
    }

    /**
     * Erzeugt einen neuen Token.
     *
     * @throws SecurityException
     */
    public function regenerateToken(): string
    {
        $this->removeToken();

        return $this->generateToken();
    }

    /**
     * Erzeugt einen kryptographisch sicheren Token.
     *
     * @throws SecurityException
     */
    private function generateToken(): string
    {
        try {
            $token = bin2hex(
                random_bytes(32)
            );
        } catch (\Random\RandomException $exception) {
            throw new SecurityException(
                'CSRF-Token konnte nicht erzeugt werden.',
                previous: $exception
            );
        }

        $this->session->set(
            self::SESSION_KEY,
            [
                'token' => $token,
                'created' => time(),
            ]
        );

        return $token;
    }
}