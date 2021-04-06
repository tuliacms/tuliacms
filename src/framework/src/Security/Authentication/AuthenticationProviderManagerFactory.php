<?php

declare(strict_types=1);

namespace Tulia\Framework\Security\Authentication;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;

/**
 * @author Adam Banaszkiewicz
 */
class AuthenticationProviderManagerFactory
{
    public static function factory(iterable $providers, bool $debug, EventDispatcherInterface $eventDispatcher): AuthenticationProviderManager
    {
        $manager = new AuthenticationProviderManager($providers, ! $debug);
        $manager->setEventDispatcher($eventDispatcher);

        return $manager;
    }
}
