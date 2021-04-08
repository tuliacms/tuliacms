<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\ValueObject;

use Tulia\Cms\ContactForms\Domain\Exception\InvalidSenderEmailException;

/**
 * @author Adam Banaszkiewicz
 */
final class Sender
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @param string $email
     * @param string|null $name
     *
     * @throws InvalidSenderEmailException
     */
    public function __construct(string $email, ?string $name)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidSenderEmailException('Sender\'s email is invalid.');
        }

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
