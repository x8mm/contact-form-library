<?php

declare(strict_types=1);

namespace ContactForm\Security;

use ContactForm\Config\Config;
use ContactForm\Exception\SecurityException;
use ContactForm\Http\Request;
use ContactForm\Http\Session;

/**
 * Mehrstufiger Rate Limiter.
 *
 * Begrenzt Formularanfragen anhand von
 * - Client-IP
 * - Session
 * - konfigurierbarem Zeitfenster
 */
final class RateLimiter
{
    private const SESSION_KEY = '__rate_limit';

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
     * Prüft das aktuelle Request-Limit.
     *
     * @throws SecurityException
     */
    public function validate(): void
    {
        $window = $this->config->getInt('RATE_LIMIT_WINDOW');
        $maximum = $this->config->getInt('RATE_LIMIT_MAX');

        $identifier = $this->createIdentifier();

        /** @var array<string, array<int,int>> $storage */
        $storage = $this->session->get(self::SESSION_KEY, []);

        $timestamps = $storage[$identifier] ?? [];

        $now = time();

        $timestamps = array_values(
            array_filter(
                $timestamps,
                static fn (int $timestamp): bool =>
                    ($now - $timestamp) < $window
            )
        );

        if (count($timestamps) >= $maximum) {
            throw new SecurityException(
                'Zu viele Formularanfragen. Bitte versuchen Sie es später erneut.'
            );
        }

        $timestamps[] = $now;

        $storage[$identifier] = $timestamps;

        $this->session->set(
            self::SESSION_KEY,
            $storage
        );
    }

    /**
     * Entfernt alle gespeicherten Limits.
     */
    public function reset(): void
    {
        $this->session->remove(self::SESSION_KEY);
    }

    /**
     * Erzeugt einen eindeutigen Request-Identifier.
     */
    private function createIdentifier(): string
    {
        return hash(
            'sha256',
            sprintf(
                '%s|%s',
                $this->request->getClientIp() ?? '',
                session_id()
            )
        );
    }
}