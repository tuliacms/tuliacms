<?php

declare(strict_types=1);

namespace Tulia\Framework\Translation;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Framework\Kernel\Event\RequestEvent;

/**
 * @author Adam Banaszkiewicz
 */
class LocaleResolver implements EventSubscriberInterface
{
    protected TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => [
                ['handle', 450],
            ],
        ];
    }

    public function handle(RequestEvent $event): void
    {
        $this->translator->setLocale($event->getRequest()->getLocale());
    }
}
