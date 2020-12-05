<?php

declare(strict_types=1);

namespace Tulia\Framework\Translation;

use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Framework\Kernel\Event\RequestEvent;

/**
 * @author Adam Banaszkiewicz
 */
class LocaleResolver
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function handle(RequestEvent $event): void
    {
        $this->translator->setLocale($event->getRequest()->getLocale());
    }
}
