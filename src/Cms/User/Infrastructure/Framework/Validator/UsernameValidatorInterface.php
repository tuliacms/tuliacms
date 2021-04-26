<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Validator;

/**
 * @author Adam Banaszkiewicz
 */
interface UsernameValidatorInterface
{
    public const OK = 0;
    public const ERR_MIN_LENGTH = 101;
    public const ERR_NOT_ALLOWED_CHARS = 102;

    /**
     * @param string $username
     *
     * @return int
     */
    public function validate(string $username): int;

    /**
     * @return int
     */
    public function getMinLength(): int;

    /**
     * @param int $minLength
     */
    public function setMinLength(int $minLength): void;
}
