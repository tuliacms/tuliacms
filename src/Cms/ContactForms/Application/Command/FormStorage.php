<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\Command;

use Tulia\Cms\ContactForms\Application\Event\FormCreatedEvent;
use Tulia\Cms\ContactForms\Application\Event\FormDeletedEvent;
use Tulia\Cms\ContactForms\Application\Event\FormPreCreateEvent;
use Tulia\Cms\ContactForms\Application\Event\FormPreDeleteEvent;
use Tulia\Cms\ContactForms\Application\Event\FormPreUpdateEvent;
use Tulia\Cms\ContactForms\Application\Event\FormUpdatedEvent;
use Tulia\Cms\ContactForms\Application\Model\Form as ApplicationForm;
use Tulia\Cms\ContactForms\Domain\Event\FormDeleted;
use Tulia\Cms\ContactForms\Domain\Exception\FormNotFoundException;
use Tulia\Cms\ContactForms\Domain\Policy\FieldsTemplatePolicyInterface;
use Tulia\Cms\ContactForms\Domain\RepositoryInterface;
use Tulia\Cms\ContactForms\Domain\Aggregate\Form as Aggregate;
use Tulia\Cms\ContactForms\Domain\ValueObject\AggregateId;
use Tulia\Cms\Platform\Infrastructure\Bus\Event\EventBusInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FormStorage
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var EventBusInterface
     */
    private $eventDispatcher;

    /**
     * @var FieldsTemplatePolicyInterface
     */
    private $fieldsTemplatePolicy;

    /**
     * @param RepositoryInterface $repository
     * @param EventBusInterface $eventDispatcher
     * @param FieldsTemplatePolicyInterface $fieldsTemplatePolicy
     */
    public function __construct(
        RepositoryInterface $repository,
        EventBusInterface $eventDispatcher,
        FieldsTemplatePolicyInterface $fieldsTemplatePolicy
    ) {
        $this->repository      = $repository;
        $this->eventDispatcher = $eventDispatcher;
        $this->fieldsTemplatePolicy = $fieldsTemplatePolicy;
    }

    public function save(ApplicationForm $form): void
    {
        $aggregateExists = false;

        try {
            $aggregate = $this->repository->find(new AggregateId($form->getId()), $form->getLocale());

            // We can assign $aggregateExists only after call find() in repository,
            // to handle exception when node not exists, and perform proper action when node not exists.
            $aggregateExists = true;
        } catch (FormNotFoundException $exception) {
            $aggregate = new Aggregate(
                new AggregateId($form->getId()),
                $form->getWebsiteId(),
                $form->getLocale()
            );
        }

        if ($aggregateExists) {
            $event = new FormPreUpdateEvent($form);
            $this->eventDispatcher->dispatch($event);
        } else {
            $event = new FormPreCreateEvent($form);
            $this->eventDispatcher->dispatch($event);
        }

        if ($event->isPropagationStopped()) {
            return;
        }

        $this->updateAggregate($form, $aggregate);

        $this->repository->save($aggregate);
        $this->eventDispatcher->dispatchCollection($aggregate->collectDomainEvents());

        if ($aggregateExists) {
            $this->eventDispatcher->dispatch(new FormUpdatedEvent($form));
        } else {
            $this->eventDispatcher->dispatch(new FormCreatedEvent($form));
        }
    }

    public function delete(ApplicationForm $form): void
    {
        try {
            $aggregate = $this->repository->find(new AggregateId($form->getId()), $form->getLocale());
        } catch (FormNotFoundException $exception) {
            return;
        }

        $event = new FormPreDeleteEvent($form);
        $this->eventDispatcher->dispatch($event);

        if ($event->isPropagationStopped()) {
            return;
        }

        $this->repository->delete($aggregate);
        $this->eventDispatcher->dispatch(new FormDeleted($aggregate->getId()));
        $this->eventDispatcher->dispatch(new FormDeletedEvent($form));
    }

    private function updateAggregate(ApplicationForm $form, Aggregate $aggregate): void
    {
        $aggregate->changeReceivers($form->getReceivers());
        $aggregate->changeSender($form->getSenderEmail(), $form->getSenderName());
        $aggregate->replyTo($form->getReplyTo());
        $aggregate->rename($form->getName());
        $aggregate->setMessageSubject($form->getSubject());
        $aggregate->changeFieldsTemplate($this->fieldsTemplatePolicy, $form->getFieldsTemplate());
        $aggregate->changeMessageTemplate($form->getMessageTemplate());
    }
}
