<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\Event;

/**
 * @author Adam Banaszkiewicz
 */
class SenderChanged extends DomainEvent
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var null|string
     */
    private $name;

    public function __construct(string $formId, string $email, ?string $name)
    {
        parent::__construct($formId);

        $this->email = $email;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }
}
