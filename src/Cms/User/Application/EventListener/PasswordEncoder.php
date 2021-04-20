<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\User;
use Tulia\Cms\User\Application\Event\UserEvent;
use Tulia\Cms\User\Application\Event\UserPreCreateEvent;
use Tulia\Cms\User\Application\Event\UserPreUpdateEvent;

/**
 * @author Adam Banaszkiewicz
 */
class PasswordEncoder implements EventSubscriberInterface
{
    protected EncoderFactoryInterface $encoder;

    public function __construct(EncoderFactoryInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserPreUpdateEvent::class => ['handle', 1000],
            UserPreCreateEvent::class => ['handle', 1000],
        ];
    }

    public function handle(UserEvent $event): void
    {
        $user = $event->getUser();

        if (!$user->getPassword()) {
            return;
        }

        $encoder = $this->encoder->getEncoder(User::class);

        if ($encoder->needsRehash($user->getPassword())) {
            $user->setPassword($encoder->encodePassword($user->getPassword(), null));
        }
    }
}
