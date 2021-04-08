<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Validator;

/**
 * @author Adam Banaszkiewicz
 */
interface PasswordValidatorInterface
{
    public const OK = 0;
    public const ERR_MIN_LENGTH        = 101;
    public const ERR_MIN_DIGITS        = 102;
    public const ERR_MIN_SPECIALS      = 103;
    public const ERR_MIN_BIG_LETTERS   = 104;
    public const ERR_MIN_SMALL_LETTERS = 105;

    /**
     * @param string $password
     *
     * @return int
     */
    public function validate(string $password): int;

    /**
     * @return int
     */
    public function getMinLength(): int;

    /**
     * @param int $minLength
     */
    public function setMinLength(int $minLength): void;

    /**
     * @return int
     */
    public function getMinDigits(): int;

    /**
     * @param int $minDigits
     */
    public function setMinDigits(int $minDigits): void;

    /**
     * @return int
     */
    public function getMinSpecialChars(): int;

    /**
     * @param int $minSpecialChars
     */
    public function setMinSpecialChars(int $minSpecialChars): void;

    /**
     * @return int
     */
    public function getMinBigLetters(): int;

    /**
     * @param int $minBigLetters
     */
    public function setMinBigLetters(int $minBigLetters): void;

    /**
     * @return int
     */
    public function getMinSmallLetters(): int;

    /**
     * @param int $minSmallLetters
     */
    public function setMinSmallLetters(int $minSmallLetters): void;
}
