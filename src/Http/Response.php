<?php

declare(strict_types=1);

namespace ContactForm\Http;

/**
 * Repräsentiert eine HTTP-Antwort.
 *
 * Diese Klasse kapselt Statuscode, Header und Response-Body.
 * Sie dient als Basisklasse für spezialisierte Response-Typen
 * wie JsonResponse.
 */
class Response
{
    /**
     * HTTP-Statuscode.
     */
    protected int $statusCode;

    /**
     * HTTP-Header.
     *
     * @var array<string, string>
     */
    protected array $headers = [];

    /**
     * Response-Body.
     */
    protected string $body = '';

    /**
     * @param int $statusCode HTTP-Statuscode
     */
    public function __construct(int $statusCode = 200)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * Setzt den HTTP-Statuscode.
     *
     * @return $this
     */
    public function setStatusCode(int $statusCode): static
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Liefert den HTTP-Statuscode.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Fügt einen Header hinzu oder überschreibt ihn.
     *
     * @return $this
     */
    public function setHeader(string $name, string $value): static
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * Entfernt einen Header.
     *
     * @return $this
     */
    public function removeHeader(string $name): static
    {
        unset($this->headers[$name]);

        return $this;
    }

    /**
     * Prüft, ob ein Header existiert.
     */
    public function hasHeader(string $name): bool
    {
        return array_key_exists($name, $this->headers);
    }

    /**
     * Liefert alle Header.
     *
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Setzt den Response-Body.
     *
     * @return $this
     */
    public function setBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Liefert den Response-Body.
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Sendet die HTTP-Antwort.
     */
    public function send(): void
    {
        if (!headers_sent()) {
            http_response_code($this->statusCode);

            foreach ($this->headers as $name => $value) {
                header(sprintf('%s: %s', $name, $value));
            }
        }

        echo $this->body;
    }
}