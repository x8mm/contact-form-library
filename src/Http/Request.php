<?php

declare(strict_types=1);

namespace ContactForm\Http;

/**
 * Kapselt den aktuellen HTTP-Request.
 *
 * Diese Klasse stellt einen zentralen und typisierten Zugriff auf
 * Request-Daten bereit. Direkte Zugriffe auf Superglobals außerhalb
 * dieser Klasse sollen vermieden werden.
 */
final class Request
{
    /**
     * Liefert den HTTP-Request-Methodennamen.
     */
    public function getMethod(): string
    {
        return strtoupper(
            (string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')
        );
    }

    /**
     * Prüft, ob der Request per POST erfolgt ist.
     */
    public function isPost(): bool
    {
        return $this->getMethod() === 'POST';
    }

    /**
     * Prüft, ob der Request per GET erfolgt ist.
     */
    public function isGet(): bool
    {
        return $this->getMethod() === 'GET';
    }

    /**
     * Liefert einen POST-Wert.
     */
    public function post(string $key, ?string $default = null): ?string
    {
        $value = $_POST[$key] ?? $default;

        if ($value === null) {
            return null;
        }

        return trim((string) $value);
    }

    /**
     * Liefert einen GET-Wert.
     */
    public function query(string $key, ?string $default = null): ?string
    {
        $value = $_GET[$key] ?? $default;

        if ($value === null) {
            return null;
        }

        return trim((string) $value);
    }

    /**
     * Prüft, ob ein POST-Wert vorhanden ist.
     */
    public function hasPost(string $key): bool
    {
        return isset($_POST[$key]);
    }

    /**
     * Prüft, ob ein GET-Wert vorhanden ist.
     */
    public function hasQuery(string $key): bool
    {
        return isset($_GET[$key]);
    }

    /**
     * Liefert alle POST-Daten.
     *
     * @return array<string, mixed>
     */
    public function allPost(): array
    {
        return $_POST;
    }

    /**
     * Liefert alle GET-Daten.
     *
     * @return array<string, mixed>
     */
    public function allQuery(): array
    {
        return $_GET;
    }

    /**
     * Liefert die Client-IP.
     *
     * Es wird bewusst REMOTE_ADDR verwendet.
     * Header wie X-Forwarded-For werden nicht ausgewertet,
     * da deren Vertrauenswürdigkeit von der Infrastruktur abhängt.
     */
    public function getClientIp(): ?string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;

        if (!is_string($ip) || $ip === '') {
            return null;
        }

        return $ip;
    }

    /**
     * Liefert den User-Agent.
     */
    public function getUserAgent(): ?string
    {
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        if (!is_string($agent) || $agent === '') {
            return null;
        }

        return $agent;
    }

    /**
     * Liefert den Referer.
     */
    public function getReferer(): ?string
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? null;

        if (!is_string($referer) || $referer === '') {
            return null;
        }

        return $referer;
    }

    /**
     * Prüft, ob es sich um einen XMLHttpRequest handelt.
     */
    public function isAjax(): bool
    {
        return strtolower(
            (string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')
        ) === 'xmlhttprequest';
    }

    /**
     * Liefert den Hostnamen.
     */
    public function getHost(): ?string
    {
        $host = $_SERVER['HTTP_HOST'] ?? null;

        if (!is_string($host) || $host === '') {
            return null;
        }

        return $host;
    }

    /**
     * Liefert das verwendete URI.
     */
    public function getUri(): ?string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? null;

        if (!is_string($uri) || $uri === '') {
            return null;
        }

        return $uri;
    }

    /**
     * Prüft, ob HTTPS verwendet wird.
     */
    public function isSecure(): bool
    {
        if (
            isset($_SERVER['HTTPS']) &&
            strtolower((string) $_SERVER['HTTPS']) !== 'off'
        ) {
            return true;
        }

        return (int) ($_SERVER['SERVER_PORT'] ?? 80) === 443;
    }

    /**
     * Liefert das komplette REQUEST-Array.
     *
     * Diese Methode sollte möglichst selten verwendet werden,
     * da POST und GET getrennt verarbeitet werden sollten.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $_REQUEST;
    }
}