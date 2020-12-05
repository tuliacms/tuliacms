<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\ValueObject;

use Tulia\Cms\ContactForms\Domain\Exception\InvalidSenderEmailException;

/**
 * @author Adam Banaszkiewicz
 */
final class ReplyTo
{
    /**
     * @var null|string
     */
    private $email;

    /**
     * @param null|string $email
     *
     * @throws InvalidSenderEmailException
     */
    public function __construct(?string $email)
    {
        if ($email && filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidSenderEmailException('Sender\'s email is invalid.');
        }

        $this->email = $email;
    }

    /**
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }
}
