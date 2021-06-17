<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Infrastructure\Transport\Email;

use Tulia\Cms\ContactForm\Domain\FieldType\Core\EmailType;
use Tulia\Cms\ContactForm\Domain\ReadModel\Finder\Model\Form;
use Tulia\Cms\ContactForm\Ports\Infrastructure\Transport\Email\SenderInterface;
use Tulia\Cms\Platform\Infrastructure\Mail\MailerInterface;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class Sender implements SenderInterface
{
    private MailerInterface $mailer;

    private EngineInterface $engine;

    public function __construct(MailerInterface $mailer, EngineInterface $engine)
    {
        $this->mailer = $mailer;
        $this->engine = $engine;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Form $form, array $data): int
    {
        $content = $this->engine->render(new View('@backend/forms/mail-template.tpl', [
            'form' => $form,
            'data' => $data,
            'message' => $form->getMessageTemplate(),
        ]));

        $message = $this->mailer->createMessage($form->getSubject());
        $message->setBody($content, 'text/html');
        $message->setFrom($form->getSenderEmail(), $form->getSenderName());
        $message->setSender($form->getSenderEmail(), $form->getSenderName());
        $message->setTo($form->getReceivers());

        $replyTo = $this->findReplyTo($form, $data);

        if ($replyTo) {
            $message->setReplyTo($replyTo);
        }

        return $this->mailer->send($message);
    }

    public function findReplyTo(Form $form, array $data): ?string
    {
        $possible = [];

        foreach ($form->getFields() as $field) {
            if ($field->getType() === EmailType::class) {
                $possible[] = $data[$field->getName()];
            }
        }

        if (empty($possible)) {
            return null;
        }

        if (\count($possible) === 1) {
            return $possible[0];
        }

        foreach ($form->getFields() as $field) {
            if ($field->getType() === EmailType::class && $field->hasOption('sender')) {
                return $data[$field->getName()];
            }
        }

        return null;
    }
}
