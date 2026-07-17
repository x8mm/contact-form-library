<?php

declare(strict_types=1);

namespace ContactForm\Security;

use ContactForm\Http\Request;
use ContactForm\Http\Response;

/**
 * Fügt einer HTTP-Antwort sicherheitsrelevante Header hinzu.
 */
final class SecurityHeaders
{
    private readonly Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Wendet sämtliche Sicherheitsheader an.
     */
    public function apply(Response $response): void
    {
        $response->setHeader(
            'X-Frame-Options',
            'DENY'
        );

        $response->setHeader(
            'X-Content-Type-Options',
            'nosniff'
        );

        $response->setHeader(
            'Referrer-Policy',
            'strict-origin-when-cross-origin'
        );

        $response->setHeader(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=()'
        );

        $response->setHeader(
            'Cross-Origin-Opener-Policy',
            'same-origin'
        );

        $response->setHeader(
            'Cross-Origin-Resource-Policy',
            'same-origin'
        );

        $response->setHeader(
            'Content-Security-Policy',
            $this->buildContentSecurityPolicy()
        );

        if ($this->request->isSecure()) {
            $response->setHeader(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }
    }

    /**
     * Erstellt die Content-Security-Policy.
     */
    private function buildContentSecurityPolicy(): string
    {
        return implode(
            '; ',
            [
                "default-src 'self'",
                "base-uri 'self'",
                "object-src 'none'",
                "frame-ancestors 'none'",
                "form-action 'self'",
                "img-src 'self' data:",
                "style-src 'self'",
                "script-src 'self'",
                "connect-src 'self'",
                "font-src 'self'",
            ]
        );
    }
}