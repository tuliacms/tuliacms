<?php

declare(strict_types=1);

namespace Tulia\Cms\Activity\Domain\WriteModel\Model;

use DateTime;

/**
 * @author Adam Banaszkiewicz
 */
class ActivityRow
{
    private string $id;

    private string $websiteId;

    private string $message = '';

    private array $context = [];

    private string $translationDomain = 'messages';

    private DateTime $createdAt;

    private function __construct(string $id, string $websiteId)
    {
        $this->id = $id;
        $this->websiteId = $websiteId;
        $this->createdAt = new DateTime();
    }

    public static function buildFromArray(array $data): ActivityRow
    {
        $row = new self($data['id'], $data['website_id']);

        $row->setMessage($data['message'] ?? '');
        $row->setContext($data['context'] ?? []);
        $row->setTranslationDomain($data['translation_domain'] ?? 'messages');
        $row->setCreatedAt($data['created_at'] ?? new DateTime());

        return $row;
    }

    public static function createNew(string $id, string $websiteId): ActivityRow
    {
        return new self($id, $websiteId);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getWebsiteId(): string
    {
        return $this->websiteId;
    }

    public function setWebsiteId(string $websiteId): void
    {
        $this->websiteId = $websiteId;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function setContext(array $context): void
    {
        $this->context = $context;
    }

    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    public function setTranslationDomain(string $translationDomain): void
    {
        $this->translationDomain = $translationDomain;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
