<?php

declare(strict_types=1);

namespace ContactForm\Mail;

use PHPMailer\PHPMailer\Exception as PhpMailerException;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Verantwortlich für den Versand von E-Mails über SMTP.
 */
final class MailService
{
    public function __construct(
        private readonly SmtpConfiguration $configuration
    ) {
    }

    /**
     * Versendet eine E-Mail.
     *
     * @throws MailException
     */
    public function send(MailMessage $message): void
    {
        $mailer = new PHPMailer(true);

        try {
            $this->configure($mailer);

            foreach ($message->recipients as $recipient) {
                $mailer->addAddress($recipient);
            }

            $mailer->setFrom(
                $this->configuration->fromAddress,
                $this->configuration->fromName
            );

            $mailer->addReplyTo($message->replyTo);

            $mailer->Subject = $message->subject;

            $mailer->CharSet = PHPMailer::CHARSET_UTF8;

            $mailer->isHTML(true);

            $mailer->Body = $message->htmlBody;

            $mailer->AltBody = $message->plainBody;

            $mailer->send();
        } catch (PhpMailerException $exception) {
            throw new MailException(
                'Der Mailversand ist fehlgeschlagen.',
                previous: $exception
            );
        }
    }

    /**
     * Konfiguriert den SMTP-Transport.
     */
    private function configure(PHPMailer $mailer): void
    {
        $mailer->isSMTP();

        $mailer->Host = $this->configuration->host;

        $mailer->Port = $this->configuration->port;

        $mailer->SMTPAuth = true;

        $mailer->Username = $this->configuration->username;

        $mailer->Password = $this->configuration->password;

        $mailer->SMTPSecure = $this->configuration->encryption;

        $mailer->CharSet = PHPMailer::CHARSET_UTF8;
    }
}