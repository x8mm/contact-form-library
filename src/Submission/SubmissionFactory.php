<?php

declare(strict_types=1);

namespace ContactForm\Submission;

use ContactForm\Config\Config;
use ContactForm\Form\FormData;
use ContactForm\Form\FormStatus;
use ContactForm\Http\Request;
use Symfony\Component\Uid\Ulid;

/**
 * Erzeugt Submission-Objekte.
 */
final class SubmissionFactory
{
    public function __construct(
        private readonly Config $config,
        private readonly Request $request,
        private readonly SubmissionHasher $hasher
    ) {
    }

    /**
     * Erstellt eine neue Submission.
     */
    public function create(
        FormData $formData,
        MailMetadata $mail
    ): Submission {
        $timestamp = gmdate(DATE_ATOM);

        $hash = $this->hasher->hash(
            $formData,
            $timestamp
        );

        return new Submission(
            ulid: (string) new Ulid(),
            timestamp: $timestamp,
            schemaVersion: $this->config->getString('SCHEMA_VERSION'),
            formName: $this->config->getString('FORM_NAME'),
            status: FormStatus::Processing,
            formData: $formData,
            mail: $mail,
            ip: $this->request->getClientIp(),
            userAgent: $this->request->userAgent(),
            referer: $this->request->referer(),
            sha256: $hash
        );
    }
}