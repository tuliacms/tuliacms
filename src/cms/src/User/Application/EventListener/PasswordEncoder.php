<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\EventListener;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\User;
use Tulia\Cms\User\Application\Event\UserEvent;

/**
 * @author Adam Banaszkiewicz
 */
class PasswordEncoder
{
    /**
     * @var EncoderFactoryInterface
     */
    protected $encoder;

    /**
     * @param EncoderFactoryInterface $encoder
     */
    public function __construct(EncoderFactoryInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param UserEvent $event
     */
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
