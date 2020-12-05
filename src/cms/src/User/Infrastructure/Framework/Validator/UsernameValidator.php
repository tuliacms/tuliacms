<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Validator;

/**
 * @author Adam Banaszkiewicz
 */
class UsernameValidator implements UsernameValidatorInterface
{
    protected $minLength = 8;

    /**
     * {@inheritdoc}
     */
    public function validate(string $username): int
    {
        if (mb_strlen($username) < $this->minLength) {
            return UsernameValidatorInterface::ERR_MIN_LENGTH;
        }

        if (! preg_match('/^[a-z0-9\-_@.]+$/i', $username)) {
            return UsernameValidatorInterface::ERR_NOT_ALLOWED_CHARS;
        }

        return UsernameValidatorInterface::OK;
    }

    /**
     * {@inheritdoc}
     */
    public function getMinLength(): int
    {
        return $this->minLength;
    }

    /**
     * {@inheritdoc}
     */
    public function setMinLength(int $minLength): void
    {
        $this->minLength = $minLength;
    }
}
