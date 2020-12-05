<?php

declare(strict_types=1);

namespace Tulia\Cms\Activity\Application\Model;

use DateTime;

/**
 * @author Adam Banaszkiewicz
 */
class Row
{
    /**
     * @var null|string
     */
    private $id;

    /**
     * @var null|string
     */
    private $websiteId;

    /**
     * @var string
     */
    private $message = '';

    /**
     * @var array
     */
    private $context = [];

    /**
     * @var string
     */
    private $translationDomain = 'messages';

    /**
     * @var DateTime
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public static function buildFromArray(array $data): Row
    {
        $row = new self();

        $data = static::setDatetime($data, 'created_at', new DateTime());

        $row->setId($data['id'] ?? null);
        $row->setwebsiteId($data['website_id'] ?? null);
        $row->setMessage($data['message'] ?? '');
        $row->setContext($data['context'] ?? []);
        $row->setTranslationDomain($data['translation_domain'] ?? 'messages');
        $row->setCreatedAt($data['created_at']);

        return $row;
    }

    private static function setDatetime(array $data, string $key, $default = null): array
    {
        if ($data[$key] instanceof DateTime === false) {
            $data[$key] = new DateTime($data[$key]);
        } else {
            $data[$key] = $default;
        }

        return $data;
    }

    /**
     * @return bool
     */
    public function hasId(): bool
    {
        return (bool) $this->id;
    }

    /**
     * @return null|string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param null|string $id
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return null|string
     */
    public function getWebsiteId(): ?string
    {
        return $this->websiteId;
    }

    /**
     * @return bool
     */
    public function hasWebsiteId(): bool
    {
        return (bool) $this->websiteId;
    }

    /**
     * @param null|string $websiteId
     */
    public function setWebsiteId(?string $websiteId): void
    {
        $this->websiteId = $websiteId;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @param array $context
     */
    public function setContext(array $context): void
    {
        $this->context = $context;
    }

    /**
     * @return string
     */
    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    /**
     * @param string $translationDomain
     */
    public function setTranslationDomain(string $translationDomain): void
    {
        $this->translationDomain = $translationDomain;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
