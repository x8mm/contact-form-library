<?php

declare(strict_types=1);

namespace ContactForm\Workflow\Steps;

use ContactForm\Security\Csrf;
use ContactForm\Security\Honeypot;
use ContactForm\Security\RateLimiter;
use ContactForm\Workflow\WorkflowContext;
use ContactForm\Workflow\WorkflowStepInterface;

/**
 * Führt sämtliche Sicherheitsprüfungen durch.
 */
final readonly class SecurityStep implements WorkflowStepInterface
{
    public function __construct(
        private Csrf $csrf,
        private Honeypot $honeypot,
        private RateLimiter $rateLimiter
    ) {
    }

    /**
     * @inheritDoc
     */
    public function process(WorkflowContext $context): void
    {
        /*
         * CSRF-Token prüfen.
         *
         * Wir gehen davon aus, dass validate()
         * bei einem ungültigen Token eine
         * SecurityException wirft.
         */
        $this->csrf->validate();

        /*
         * Honeypot prüfen.
         */
        $this->honeypot->validate();

        /*
         * Rate Limiter prüfen.
         */
        $this->rateLimiter->validate();
    }
}