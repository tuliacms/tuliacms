<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\FieldsTemplate\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\ContactForms\Application\FieldsTemplate\Service\FieldsTemplateViewUpdater;
use Tulia\Cms\ContactForms\Domain\Event\FieldsTemplateChanged;

/**
 * @author Adam Banaszkiewicz
 */
class FieldsTemplateChangedListener implements EventSubscriberInterface
{
    private FieldsTemplateViewUpdater $viewUpdater;

    public function __construct(FieldsTemplateViewUpdater $viewUpdater)
    {
        $this->viewUpdater = $viewUpdater;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FieldsTemplateChanged::class => '__invoke',
        ];
    }

    public function __invoke(FieldsTemplateChanged $event): void
    {
        $this->viewUpdater->update($event->getFormId(), $event->getTemplate(), $event->getLocale());
    }
}
