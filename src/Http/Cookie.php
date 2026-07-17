<?php

declare(strict_types=1);

namespace ContactForm\Http;

use ContactForm\Config\Config;
#use InvalidArgumentException;
use ContactForm\Exception\SecurityException;

/**
 * Verwaltet HTTP-Cookies.
 *
 * Diese Klasse kapselt das Setzen, Lesen und Löschen von Cookies
 * unter Verwendung sicherer Standardwerte.
 */
final class Cookie
{
    private readonly Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Setzt ein Cookie.
     *
     * @throws InvalidArgumentException
     */
    public function set(
        string $name,
        string $value,
        ?int $expires = null,
        string $path = '/',
        string $sameSite = 'Strict'
    ): void {
        $sameSite = ucfirst(strtolower($sameSite));

        if (!in_array($sameSite, ['Strict', 'Lax', 'None'], true)) {
            #throw new InvalidArgumentException(
            throw new SecurityException(
                sprintf(
                    'Ungültiger SameSite-Wert "%s".',
                    $sameSite
                )
            );
        }

        $lifetime = $expires
            ?? (time() + $this->config->getInt('SESSION_LIFETIME'));

        setcookie(
            $name,
            $value,
            [
                'expires' => $lifetime,
                'path' => $path,
                'domain' => '',
                'secure' => $this->isSecureConnection(),
                'httponly' => true,
                'samesite' => $sameSite,
            ]
        );

        $_COOKIE[$name] = $value;
    }

    /**
     * Liefert den Wert eines Cookies.
     */
    public function get(string $name, ?string $default = null): ?string
    {
        $value = $_COOKIE[$name] ?? $default;

        if ($value === null) {
            return null;
        }

        return (string) $value;
    }

    /**
     * Prüft, ob ein Cookie existiert.
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $_COOKIE);
    }

    /**
     * Entfernt ein Cookie.
     */
    public function delete(string $name, string $path = '/'): void
    {
        setcookie(
            $name,
            '',
            [
                'expires' => time() - 3600,
                'path' => $path,
                'domain' => '',
                'secure' => $this->isSecureConnection(),
                'httponly' => true,
                'samesite' => 'Strict',
            ]
        );

        unset($_COOKIE[$name]);
    }

    /**
     * Liefert alle Cookies.
     *
     * @return array<string, string>
     */
    public function all(): array
    {
        /** @var array<string, string> $cookies */
        $cookies = $_COOKIE;

        return $cookies;
    }

    /**
     * Ermittelt, ob HTTPS verwendet wird.
     */
    private function isSecureConnection(): bool
    {
        return isset($_SERVER['HTTPS'])
            && strtolower((string) $_SERVER['HTTPS']) !== 'off';
    }
}