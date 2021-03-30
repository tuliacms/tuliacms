<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Framework\Kernel\Event\BootstrapEvent;

/**
 * @author Adam Banaszkiewicz
 */
class LocaleResolver implements EventSubscriberInterface
{
    protected CurrentWebsiteInterface $currentWebsite;

    public function __construct(CurrentWebsiteInterface $currentWebsite)
    {
        $this->currentWebsite = $currentWebsite;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BootstrapEvent::class => [
                ['handle', 9500]
            ],
        ];
    }

    public function handle(BootstrapEvent $event): void
    {
        $request = $event->getRequest();
        $parameters = $this->resolveLocale($request->attributes->all());

        $request->attributes->add($parameters);

        $request->setLocale($result['_locale'] ?? $parameters['_locale'] ?? 'en_US');
        $request->setContentLocale($result['_content_locale'] ?? $parameters['_content_locale'] ?? 'en_US');
        $request->setDefaultLocale($this->currentWebsite->getDefaultLocale()->getCode());
    }

    protected function resolveLocale(array $parameters): array
    {
        $locale = $this->currentWebsite->getLocale();

        $parameters['_locale']            = $locale->getCode();
        $parameters['_content_locale']    = $locale->getCode();
        $parameters['_is_locale_in_path'] = false;

        if ($locale->getLocalePrefix()) {
            $parameters['_is_locale_in_path'] = true;
            $parameters['_content_path'] = substr($parameters['_content_path'], \strlen($locale->getLocalePrefix()));
        }

        return $parameters;
    }
}
