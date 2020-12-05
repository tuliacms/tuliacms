<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\FieldsTemplate\EventListener;

use Tulia\Cms\ContactForms\Application\FieldsTemplate\Service\FieldsTemplateViewUpdater;
use Tulia\Cms\ContactForms\Domain\Event\FieldsTemplateChanged;

/**
 * @author Adam Banaszkiewicz
 */
class FieldsTemplateChangedListener
{
    /**
     * @var FieldsTemplateViewUpdater
     */
    private $viewUpdater;

    public function __construct(FieldsTemplateViewUpdater $viewUpdater)
    {
        $this->viewUpdater = $viewUpdater;
    }

    public function __invoke(FieldsTemplateChanged $event): void
    {
        $this->viewUpdater->update($event->getFormId(), $event->getTemplate(), $event->getLocale());
    }
}
