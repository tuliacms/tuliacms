<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Translator;

use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;
use Tulia\Framework\Kernel\Event\RequestEvent;

/**
 * Listener sets current locale in Request to logged-in user defined.
 * Works only on backend.
 *
 * @author Adam Banaszkiewicz
 */
class UserLocaleResolver
{
    /**
     * @var AuthenticatedUserProviderInterface
     */
    protected $authenticatedUserProvider;

    /**
     * @param AuthenticatedUserProviderInterface $authenticatedUserProvider
     */
    public function __construct(AuthenticatedUserProviderInterface $authenticatedUserProvider)
    {
        $this->authenticatedUserProvider = $authenticatedUserProvider;
    }

    public function handle(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$request->attributes->get('_is_backend')) {
            return;
        }

        $request->setLocale($this->authenticatedUserProvider->getUser()->getLocale());
    }
}
