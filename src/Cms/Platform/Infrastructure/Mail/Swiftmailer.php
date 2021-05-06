<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Mail;

use Tulia\Cms\Options\Domain\ReadModel\Options;

/**
 * @author Adam Banaszkiewicz
 */
class Swiftmailer implements MailerInterface
{
    private $mailer;

    /**
     * @var Options
     */
    private $options;

    /**
     * @param Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function createMessage(string $subject): \Swift_Message
    {
        $message = new \Swift_Message($subject);
        $message->setSender($this->options->get('mail.from_email'), $this->options->get('mail.from_name'));

        return $message;
    }

    /**
     * {@inheritdoc}
     */
    public function send(\Swift_Message $message): int
    {
        return $this->createMailer()->send($message);
    }

    public function getMailer(): \Swift_Mailer
    {
        return $this->createMailer();
    }

    private function createMailer(): \Swift_Mailer
    {
        if ($this->mailer instanceof \Swift_Mailer) {
            return $this->mailer;
        }

        return $this->mailer = new \Swift_Mailer($this->createTransport());
    }

    private function createTransport(): \Swift_SmtpTransport
    {
        $this->options->preload([
            'mail.host',
            'mail.port',
            'mail.encryption',
            'mail.username',
            'mail.password',
        ]);

        $transport = new \Swift_SmtpTransport(
            $this->options->get('mail.host'),
            $this->options->get('mail.port'),
            $this->options->get('mail.encryption')
        );

        if ($this->options->get('mail.username')) {
            $transport->setUsername($this->options->get('mail.username'));
        }

        if ($this->options->get('mail.password')) {
            $transport->setUsername($this->options->get('mail.password'));
        }

        return $transport;
    }
}
