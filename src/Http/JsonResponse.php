<?php

declare(strict_types=1);

namespace ContactForm\Http;

use JsonException;

/**
 * Repräsentiert eine JSON-HTTP-Antwort.
 *
 * Diese Klasse erweitert die allgemeine Response um die
 * automatische JSON-Kodierung sowie die passenden HTTP-Header.
 */
final class JsonResponse extends Response
{
    /**
     * JSON-Daten.
     *
     * @var array<string, mixed>
     */
    private array $payload = [];

    /**
     * @param array<string, mixed> $payload
     * @param int                  $statusCode
     */
    public function __construct(array $payload = [], int $statusCode = 200)
    {
        parent::__construct($statusCode);

        $this->setHeader(
            'Content-Type',
            'application/json; charset=UTF-8'
        );

        $this->setPayload($payload);
    }

    /**
     * Setzt den JSON-Payload.
     *
     * @param array<string, mixed> $payload
     *
     * @return $this
     */
    public function setPayload(array $payload): static
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * Liefert den JSON-Payload.
     *
     * @return array<string, mixed>
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * Sendet die JSON-Antwort.
     */
    public function send(): void
    {
        try {
            $this->setBody(
                json_encode(
                    $this->payload,
                    JSON_THROW_ON_ERROR
                    | JSON_UNESCAPED_UNICODE
                    | JSON_UNESCAPED_SLASHES
                )
            );
        } catch (JsonException) {
            parent::setStatusCode(500);

            $this->setBody(
                '{"success":false,"message":"Internal Server Error"}'
            );
        }

        parent::send();
    }
}