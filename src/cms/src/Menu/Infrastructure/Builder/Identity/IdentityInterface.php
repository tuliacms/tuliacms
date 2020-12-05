<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Builder\Identity;

/**
 * @author Adam Banaszkiewicz
 */
interface IdentityInterface
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @param string $id
     */
    public function setId(string $id): void;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param string $type
     */
    public function setType(string $type): void;

    /**
     * @return string
     */
    public function getLink(): string;

    /**
     * @param string $link
     */
    public function setLink(string $link): void;

    /**
     * @return array
     */
    public function getCacheTags(): array;

    /**
     * @param array $cacheTags
     */
    public function setCacheTags(array $cacheTags): void;
}
