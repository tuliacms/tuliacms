<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\Sender;

use Tulia\Cms\ContactForms\Application\FieldType\Core\EmailType;
use Tulia\Cms\ContactForms\Query\Model\Form;
use Tulia\Cms\Platform\Infrastructure\Mail\MailerInterface;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class Sender implements SenderInterface
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var EngineInterface
     */
    private $engine;

    /**
     * @param MailerInterface $mailer
     */
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
            if ($field['type'] === EmailType::class) {
                $possible[] = $data[$field['name']];
            }
        }

        if (empty($possible)) {
            return null;
        }

        if (\count($possible) === 1) {
            return $possible[0];
        }

        foreach ($form->getFields() as $field) {
            if ($field['type'] === EmailType::class && isset($field['options']['sender'])) {
                return $data[$field['name']];
            }
        }

        return null;
    }
}
